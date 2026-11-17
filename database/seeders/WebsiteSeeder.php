<?php

namespace Database\Seeders;

use App\Models\CarouselSlide;
use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        if (CarouselSlide::count() === 0) {
            $slides = [
                [
                    'titulo' => 'DISEÑO DE LOGOTIPOS',
                    'descripcion' => 'Creamos identidades visuales únicas para tu marca.',
                    'imagen' => 'logotipos.jpg',
                ],
                [
                    'titulo' => 'BANNERS PUBLICITARIOS',
                    'descripcion' => 'Diseños impactantes para hacer crecer tu negocio.',
                    'imagen' => 'banners-publicitarios.jpg',
                ],
                [
                    'titulo' => 'TARJETAS DE INVITACIÓN',
                    'descripcion' => 'Diseños personalizados para momentos especiales.',
                    'imagen' => 'tarjetas-invitacion.jpg',
                ],
                [
                    'titulo' => 'EDICIÓN DE FOTOGRAFÍAS',
                    'descripcion' => 'Transformamos tus imágenes con acabado profesional.',
                    'imagen' => 'edicion-fotografias.jpg',
                ],
                [
                    'titulo' => 'AFICHES PUBLICITARIOS',
                    'descripcion' => 'Diseño de afiches y flyers publicitarios para impresión y redes sociales.',
                    'imagen' => 'afiches-publicitarios.jpg',
                ],
                [
                    'titulo' => 'REPARACIÓN DE FOTOS',
                    'descripcion' => 'Restauramos recuerdos que parecían perdidos.',
                    'imagen' => 'reparacion-fotos.jpg',
                ],
                [
                    'titulo' => 'FOTOGRAFÍA PROFESIONAL',
                    'descripcion' => 'Sesiones fotográficas profesionales para productos, retratos y eventos.',
                    'imagen' => 'fotografia-profesional.jpg',
                ],
                [
                    'titulo' => 'FILMACIÓN PROFESIONAL',
                    'descripcion' => 'Capturamos momentos con calidad cinematográfica.',
                    'imagen' => 'filmacion-profesional.jpg',
                ],
                [
                    'titulo' => 'SESIONES DE FOTOS',
                    'descripcion' => 'Momentos únicos capturados profesionalmente.',
                    'imagen' => 'sesiones-fotos.jpg',
                ],
            ];

            foreach ($slides as $position => $slide) {
                CarouselSlide::create(array_merge($slide, [
                    'boton_texto' => 'Solicitar servicio',
                    'boton_enlace' => '#contacto',
                    'duracion' => 5,
                    'orden' => $position,
                    'activo' => true,
                ]));
            }
        }

        $settings = [
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

        foreach ($settings as $key => $value) {
            if (! LandingSetting::where('key', $key)->exists()) {
                LandingSetting::create(['key' => $key, 'value' => $value]);
            }
        }
    }
}
