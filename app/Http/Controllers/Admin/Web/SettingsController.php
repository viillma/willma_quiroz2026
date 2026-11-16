<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionWeb;
use App\Models\LandingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $settings = LandingSetting::allJson();

        $config = ConfiguracionWeb::primera();
        $config->refresh();

        $defaults = [
            'hero_titulo_linea1' => 'CREAMOS IDEAS,',
            'hero_titulo_linea2' => 'CAPTURAMOS MOMENTOS,',
            'hero_titulo_linea3' => 'TRANSFORMAMOS MARCAS',
            'hero_subtitulo' => 'Soluciones creativas en diseño gráfico, fotografía y producción audiovisual para impulsar tu marca.',
            'hero_imagen' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=1600&q=80&auto=format&fit=crop',
            'boton_primario_texto' => 'Ver servicios',
            'boton_secundario_texto' => 'Contactar por WhatsApp',
            'stats_proyectos' => '150+',
            'stats_clientes' => '100+',
            'stats_anios' => '8',
            'contacto_whatsapp' => '6404467',
            'contacto_correo' => 'hola@creapix.com',
            'contacto_facebook' => 'https://facebook.com/creapix',
            'contacto_instagram' => 'https://instagram.com/creapix',
            'contacto_tiktok' => 'https://tiktok.com/@creapix',
            'nosotros_texto' => 'Somos un estudio creativo especializado en transformar ideas en piezas visuales que conectan con las personas.',
        ];

        return Inertia::render('web/settings/Index', [
            'settings' => array_merge($defaults, $settings),
            'configuracion' => [
                'logo' => $config->logo,
                'logo_url' => $config->logo_url,
                'nombre_empresa' => $config->nombre_empresa,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_titulo_linea1' => ['nullable', 'string', 'max:255'],
            'hero_titulo_linea2' => ['nullable', 'string', 'max:255'],
            'hero_titulo_linea3' => ['nullable', 'string', 'max:255'],
            'hero_subtitulo' => ['nullable', 'string', 'max:2000'],
            'hero_imagen' => ['nullable', 'string', 'max:1000'],
            'boton_primario_texto' => ['nullable', 'string', 'max:255'],
            'boton_secundario_texto' => ['nullable', 'string', 'max:255'],
            'stats_proyectos' => ['nullable', 'string', 'max:100'],
            'stats_clientes' => ['nullable', 'string', 'max:100'],
            'stats_anios' => ['nullable', 'string', 'max:100'],
            'contacto_whatsapp' => ['nullable', 'string', 'max:100'],
            'contacto_correo' => ['nullable', 'email', 'max:255'],
            'contacto_facebook' => ['nullable', 'string', 'max:500'],
            'contacto_instagram' => ['nullable', 'string', 'max:500'],
            'contacto_tiktok' => ['nullable', 'string', 'max:500'],
            'nosotros_texto' => ['nullable', 'string', 'max:2000'],
            'nombre_empresa' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('hero_imagen_archivo')) {
            $request->validate([
                'hero_imagen_archivo' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            ]);
            $validated['hero_imagen'] = $request->file('hero_imagen_archivo')->store('hero', 'public');
        }

        foreach ($validated as $key => $value) {
            if ($key === 'hero_imagen_archivo') {
                continue;
            }
            LandingSetting::set($key, is_string($value) ? $value : json_encode($value));
        }

        $config = ConfiguracionWeb::primera();

        if ($request->has('nombre_empresa')) {
            $config->nombre_empresa = $request->input('nombre_empresa');
        }

        if ($request->hasFile('logo_archivo')) {
            $request->validate([
                'logo_archivo' => ['file', 'mimes:jpg,jpeg,png,webp,svg', 'max:10240'],
            ]);
            if ($config->logo && ! filter_var($config->logo, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($config->logo);
            }
            $config->logo = $request->file('logo_archivo')->store('logos', 'public');
        }

        $config->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Configuración guardada correctamente.']);

        return back();
    }

    public function destroyLogo(): RedirectResponse
    {
        $config = ConfiguracionWeb::primera();

        if ($config->logo && ! filter_var($config->logo, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($config->logo);
        }

        $config->logo = null;
        $config->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Logotipo eliminado.']);

        return back();
    }
}
