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
                    'imagen' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'BANNERS PUBLICITARIOS',
                    'descripcion' => 'Diseños impactantes para hacer crecer tu negocio.',
                    'imagen' => 'https://images.unsplash.com/photo-1770017863955-56a1501a3c93?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'TARJETAS DE PRESENTACIÓN',
                    'descripcion' => 'Una primera impresión profesional para tu marca.',
                    'imagen' => 'https://images.unsplash.com/photo-1558655146-d09347e92766?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'TARJETAS DE INVITACIÓN',
                    'descripcion' => 'Diseños personalizados para momentos especiales.',
                    'imagen' => 'https://images.unsplash.com/photo-1742581659446-6260fc707e7d?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'EDICIÓN DE FOTOS',
                    'descripcion' => 'Transformamos tus imágenes con acabado profesional.',
                    'imagen' => 'https://images.unsplash.com/photo-1744948961024-66baaafbb5dc?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'RESTAURACIÓN DE FOTOS',
                    'descripcion' => 'Recuperamos recuerdos que parecían perdidos.',
                    'imagen' => 'https://images.unsplash.com/photo-1695921957609-42b27aee3285?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'EDICIÓN DE VIDEOS',
                    'descripcion' => 'Convertimos tus videos en historias profesionales.',
                    'imagen' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'FILMACIÓN PROFESIONAL',
                    'descripcion' => 'Capturamos momentos con calidad cinematográfica.',
                    'imagen' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=1600&q=85&auto=format&fit=crop',
                ],
                [
                    'titulo' => 'SESIONES DE FOTOS',
                    'descripcion' => 'Momentos únicos capturados profesionalmente.',
                    'imagen' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=1600&q=85&auto=format&fit=crop',
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
