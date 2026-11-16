<?php

namespace Database\Seeders;

use App\Models\CatalogoDiseno;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        if (CatalogoDiseno::exists()) {
            return;
        }

        $items = [
            ['Identidad Visual Corporativa', 'Logos', 'Paquete completo de branding con logotipo principal, variantes cromáticas, papelería corporativa y guía de uso.', 350, null, 0],
            ['Flyer Evento Especial', 'Flyers', 'Diseño promocional para conciertos y eventos con tipografía moderna, efectos de luz y paleta vibrante.', 120, null, 0],
            ['Banner Web Principal', 'Banners', 'Banner hero para sitio web corporativo con animaciones CSS, gradientes personalizados y llamado a la acción.', 180, null, 0],
            ['Pack Redes Sociales', 'Redes Sociales', 'Kit de 10 piezas para Instagram, Facebook y LinkedIn con línea gráfica unificada y optimizadas para cada plataforma.', 250, null, 0],
            ['Sesión Producto', 'Fotografía', 'Fotografía profesional de producto con iluminación de estudio, fondo neutro y retoque digital incluido.', 200, null, 0],
            ['Logotipo Minimalista', 'Logos', 'Diseño de marca con enfoque minimalista, geometría limpia y símbolo versátil para uso digital e impreso.', 250, null, 0],
            ['Flyer Lanzamiento', 'Flyers', 'Flyer promocional para lanzamiento de producto con efectos de gradiente, tipografía bold y CTA destacado.', 130, null, 0],
            ['Banner Oferta Especial', 'Banners', 'Banner promocional para campaña de descuentos con diseño llamativo, temporizador visual y precio destacado.', 160, null, 0],
            ['Contenido Historias IG', 'Redes Sociales', 'Pack de 5 historias animadas para Instagram con motion graphics, stickers personalizados y enlace swipe-up.', 190, null, 0],
            ['Edición Retrato', 'Edición', 'Edición profesional de retrato con corrección de color, retoque facial, eliminación de imperfecciones y fondo.', 80, null, 0],
            ['Fotografía Corporativa', 'Fotografía', 'Sesión de fotografía corporativa para equipo de trabajo con fondo personalizado y edición profesional.', 350, null, 0],
            ['Mockup Empaque', 'Otros', 'Diseño de empaque personalizado con mockup 3D, lista para impresión con sangrados y guías técnicas.', 220, null, 0],
            ['Logotipo Tipográfico', 'Logos', 'Marca tipográfica personalizada con lettering único, kerning ajustado y variantes para fondo claro y oscuro.', 200, null, 0],
            ['Flyer Restaurante', 'Flyers', 'Flyer gastronómico con fotografía de plating, tipografía elegante y promoción del menú ejecutivo.', 110, null, 0],
            ['Edición Fotografía Boda', 'Edición', 'Edición completa de álbum de boda con corrección de color, efectos dreamy y formato para impresión profesional.', 150, null, 0],
        ];

        $imagenes = [
            'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1586339949916-3e5457d58f4c?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1550684376-efcbd6e3f031?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1563986768609-322da13575f2?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1611926653458-09294b3142bf?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1554048612-b6a482bc67e2?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1536240478700-b869070f9279?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop',
        ];

        foreach ($items as $index => [$nombre, $categoria, $descripcion, $precio, $precioAnterior, $orden]) {
            CatalogoDiseno::create([
                'nombre' => $nombre,
                'categoria' => $categoria,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'precio_anterior' => $precioAnterior,
                'estado' => 'activo',
                'orden' => $orden,
                'imagen' => $imagenes[$index],
            ]);
        }
    }
}
