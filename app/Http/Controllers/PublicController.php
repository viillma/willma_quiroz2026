<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactLeadRequest;
use App\Models\BusinessSetting;
use App\Models\CatalogoDiseno;
use App\Models\CarouselSlide;
use App\Models\ConfiguracionWeb;
use App\Models\ContactLead;
use App\Models\Faq;
use App\Models\LandingSetting;
use App\Models\Portafolio;
use App\Models\Servicio;
use App\Models\Testimonio;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function index(): Response
    {
        $servicios = Servicio::publicados()->activos()->orderBy('orden')->orderByDesc('created_at')->get();

        $destacados = $servicios->filter(fn ($s) => $s->destacado)->take(6)->values();
        if ($destacados->isEmpty()) {
            $destacados = $servicios->take(6)->values();
        }

        $portafolios = Portafolio::with(['images'])
            ->where('publicar', true)
            ->orderByDesc('created_at')
            ->take(6)
            ->get()
            ->map(fn ($p) => $this->portafolioArray($p));

        $testimonios = Testimonio::publicados()
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->get();

        $carousel = $this->construirCarouselDesdeServicios($servicios);

        return Inertia::render('public/Home', [
            'site' => $this->site(),
            'carousel' => $carousel,
            'servicios_destacados' => $destacados->map(fn ($s) => $this->servicioArray($s)),
            'portafolios' => $portafolios,
            'testimonios' => $testimonios->map(fn ($t) => $this->testimonioArray($t)),
        ]);
    }

    private function construirCarouselDesdeServicios(): array
    {
        $slides = CarouselSlide::activos()
            ->orderBy('orden')
            ->get()
            ->map(fn ($s) => (object) [
                'id' => $s->id,
                'titulo' => $s->titulo,
                'descripcion' => $s->descripcion,
                'boton_texto' => $s->boton_texto ?? 'Solicitar servicio',
                'boton_enlace' => $s->boton_enlace ?? '#',
                'imagen' => $s->imagen_url,
                'imagen_url' => $s->imagen_url,
                'duracion' => $s->duracion ?? 5000,
            ]);

        return $slides->toArray();
    }

    public function servicios(): Response
    {
        $servicios = Servicio::publicados()->activos()
            ->orderBy('orden')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => $this->servicioArray($s));

        return Inertia::render('public/Servicios', [
            'site' => $this->site(),
            'servicios' => $servicios,
            'categorias' => $this->categoriasDe($servicios),
        ]);
    }

    public function servicio(string $slug): Response
    {
        $servicio = Servicio::publicados()->activos()->where('slug', $slug)->firstOrFail();

        $relacionados = Servicio::publicados()->activos()
            ->where('id', '!=', $servicio->id)
            ->where('categoria', $servicio->categoria)
            ->orderBy('orden')
            ->take(3)
            ->get()
            ->map(fn ($s) => $this->servicioArray($s));

        return Inertia::render('public/ServicioDetalle', [
            'site' => $this->site(),
            'servicio' => $this->servicioArray($servicio),
            'relacionados' => $relacionados,
        ]);
    }

    public function catalogo(): Response
    {
        $disenos = CatalogoDiseno::publicados()
            ->where('estado', 'activo')
            ->orderBy('orden')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($d) => $this->catalogoArray($d));

        return Inertia::render('public/Catalogo', [
            'site' => $this->site(),
            'disenos' => $disenos,
            'categorias' => $this->categoriasDe($disenos),
        ]);
    }

    public function portafolio(): Response
    {
        $portafolios = Portafolio::with(['images'])
            ->where('publicar', true)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => $this->portafolioArray($p));

        return Inertia::render('public/Portafolio', [
            'site' => $this->site(),
            'portafolios' => $portafolios,
            'categorias' => $this->categoriasDe($portafolios),
        ]);
    }

    public function nosotros(): Response
    {
        return Inertia::render('public/Nosotros', [
            'site' => $this->site(),
        ]);
    }

    public function contacto(): Response
    {
        return Inertia::render('public/Contacto', [
            'site' => $this->site(),
        ]);
    }

    public function contactoEnvio(ContactLeadRequest $request): RedirectResponse
    {
        ContactLead::create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '¡Gracias! Tu mensaje fue enviado correctamente. Te responderemos a la brevedad.',
        ]);

        return back();
    }

    public function faq(): Response
    {
        $faqs = Faq::publicados()
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->map(fn ($f) => [
                'id' => $f->id,
                'pregunta' => $f->pregunta,
                'respuesta' => $f->respuesta,
            ]);

        return Inertia::render('public/PreguntasFrecuentes', [
            'site' => $this->site(),
            'faqs' => $faqs,
        ]);
    }

    public function robots(): \Illuminate\Http\Response
    {
        $base = rtrim(config('app.url'), '/');

        $body = "User-agent: *\n"
            ."Allow: /\n"
            ."Disallow: /login\n"
            ."Disallow: /register\n"
            ."Disallow: /panel\n"
            ."Disallow: /admin\n"
            ."Disallow: /recepcion\n"
            ."Disallow: /diseno\n"
            ."Sitemap: {$base}/sitemap.xml\n";

        return response($body)->header('Content-Type', 'text/plain');
    }

    public function sitemap(): \Illuminate\Http\Response
    {
        $base = rtrim(config('app.url'), '/');

        $urls = ['/', '/servicios', '/catalogo', '/portafolio', '/nosotros', '/contacto', '/preguntas-frecuentes'];

        foreach (Servicio::publicados()->activos()->whereNotNull('slug')->get() as $servicio) {
            $urls[] = '/servicios/'.urlencode($servicio->slug);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $loc = htmlspecialchars($base.$url, ENT_XML1, 'UTF-8');
            $xml .= "  <url><loc>{$loc}</loc></url>\n";
        }

        $xml .= '</urlset>';

        return response($xml)->header('Content-Type', 'application/xml');
    }

    private function site(): array
    {
        $config = ConfiguracionWeb::primera();
        $landing = LandingSetting::allJson();
        $business = BusinessSetting::allJson();

        $social = [
            'instagram' => (string) ($landing['contacto_instagram'] ?? $business['instagram'] ?? ''),
            'facebook' => (string) ($landing['contacto_facebook'] ?? $business['facebook'] ?? ''),
            'tiktok' => (string) ($landing['contacto_tiktok'] ?? $business['tiktok'] ?? ''),
        ];

        $nombre = (string) ($config->nombre_empresa
            ?: ($business['business_name'] ?? 'CREAPIX'));

        $heroImagen = (string) ($landing['hero_imagen'] ?? '');

        return [
            'nombre' => $nombre,
            'eslogan' => (string) ($business['business_description'] ?? 'CREAMOS · CAPTURAMOS · TRANSFORMAMOS'),
            'logo' => $config->logo_url,
            'favicon' => $config->logo_url,
            'whatsapp' => $this->whatsappIntl($landing, $business),
            'whatsapp_local' => (string) ($landing['contacto_whatsapp'] ?? $business['business_whatsapp'] ?? '64904467'),
            'email' => (string) ($landing['contacto_correo'] ?? $business['business_email'] ?? ''),
            'telefono' => (string) ($business['business_phone'] ?? ''),
            'direccion' => (string) ($business['business_address'] ?? ''),
            'ciudad' => (string) ($business['business_city'] ?? ''),
            'nombre_pais' => (string) ($business['business_country'] ?? 'Bolivia'),
            'social' => array_filter($social, fn ($v) => str_contains($v, 'http')),
            'hero' => [
                'linea1' => (string) ($landing['hero_titulo_linea1'] ?? 'CREAMOS IDEAS,'),
                'linea2' => (string) ($landing['hero_titulo_linea2'] ?? 'CAPTURAMOS MOMENTOS,'),
                'linea3' => (string) ($landing['hero_titulo_linea3'] ?? 'TRANSFORMAMOS MARCAS'),
                'subtitulo' => (string) ($landing['hero_subtitulo'] ?? 'Soluciones creativas en diseño, fotografía y producción audiovisual para impulsar tu marca.'),
                'imagen' => $heroImagen,
                'boton_primario' => (string) ($landing['boton_primario_texto'] ?? 'Ver servicios'),
                'boton_secundario' => (string) ($landing['boton_secundario_texto'] ?? 'Contactar por WhatsApp'),
            ],
            'stats' => [
                'proyectos' => (string) ($landing['stats_proyectos'] ?? '150+'),
                'clientes' => (string) ($landing['stats_clientes'] ?? '100+'),
                'anios' => (string) ($landing['stats_anios'] ?? '8'),
            ],
            'nosotros' => (string) ($landing['nosotros_texto'] ?? 'Somos un estudio creativo especializado en transformar ideas en piezas visuales que conectan con las personas.'),
            'currency_symbol' => (string) ($business['currency_symbol'] ?? 'Bs'),
            'canonical' => request()->url(),
            'servicios' => Servicio::publicados()->activos()->orderBy('orden')->take(4)->get()
                ->map(fn ($s) => ['nombre' => $s->nombre, 'slug' => $s->slug]),
        ];
    }

    private function whatsappIntl(array $landing, array $business): string
    {
        $pais = trim((string) ($landing['whatsapp_pais'] ?? $business['whatsapp_pais'] ?? '591'));
        $local = trim((string) ($landing['contacto_whatsapp'] ?? $business['business_whatsapp'] ?? '64904467'));
        $digits = preg_replace('/\D+/', '', $local);

        if ($digits === '') {
            $digits = '64904467';
        }

        $digits = ltrim($digits, '0');

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (! str_starts_with($digits, $pais)) {
            $digits = $pais.$digits;
        }

        return $digits;
    }

    private function categoriasDe($items): array
    {
        return collect($items)->pluck('categoria')->filter()->unique()->values()->all();
    }

    private function servicioArray(Servicio $s): array
    {
        return [
            'id' => $s->id,
            'codigo' => $s->codigo,
            'slug' => $s->slug,
            'nombre' => $s->nombre,
            'categoria' => $s->categoria,
            'descripcion' => $s->descripcion,
            'precio' => (float) $s->precio,
            'unidad' => $s->unidad,
            'tiempo_estimado' => $s->tiempo_estimado,
            'imagen' => $s->imagen_url,
            'estado' => $s->estado,
            'publicado' => $s->publicado,
            'destacado' => $s->destacado,
            'caracteristicas' => $s->caracteristicas ?? [],
            'incluye' => $s->incluye ?? [],
            'seo_title' => $s->seo_title,
            'seo_description' => $s->seo_description,
        ];
    }

    private function catalogoArray(CatalogoDiseno $d): array
    {
        return [
            'id' => $d->id,
            'nombre' => $d->nombre,
            'categoria' => $d->categoria,
            'descripcion' => $d->descripcion,
            'precio' => (float) $d->precio,
            'precio_anterior' => $d->precio_anterior !== null ? (float) $d->precio_anterior : null,
            'estado' => $d->estado,
            'publicado' => $d->publicado,
            'destacado' => $d->destacado,
            'imagen' => $d->imagen_url,
        ];
    }

    private function portafolioArray(Portafolio $p): array
    {
        return [
            'id' => $p->id,
            'titulo' => $p->titulo,
            'categoria' => $p->categoria,
            'descripcion' => $p->descripcion,
            'imagen' => $p->imagen_url,
            'fecha' => $p->fecha?->toDateString(),
            'images' => $p->images->map(fn ($i) => [
                'id' => $i->id,
                'url' => $i->url,
            ]),
            'servicio' => $p->servicio?->nombre,
        ];
    }

    private function testimonioArray(Testimonio $t): array
    {
        return [
            'id' => $t->id,
            'nombre' => $t->nombre,
            'texto' => $t->texto,
            'calificacion' => $t->calificacion,
            'servicio' => $t->servicio,
            'foto' => $t->foto_url,
        ];
    }
}
