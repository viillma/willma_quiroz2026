export type CatalogItem = {
    id: number
    imagen: string
    nombre: string
    descripcion: string
    categoria: string
    precio: number
}

export const categorias = [
    'Todos',
    'Logos',
    'Flyers',
    'Banners',
    'Redes Sociales',
    'Fotografía',
    'Edición',
    'Otros',
] as const

export type Categoria = (typeof categorias)[number]

export const catalogItems: CatalogItem[] = [
    {
        id: 1,
        imagen: 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=600&h=400&fit=crop',
        nombre: 'Identidad Visual Corporativa',
        descripcion: 'Paquete completo de branding con logotipo principal, variantes cromáticas, papelería corporativa y guía de uso.',
        categoria: 'Logos',
        precio: 350,
    },
    {
        id: 2,
        imagen: 'https://images.unsplash.com/photo-1586339949916-3e5457d58f4c?w=600&h=400&fit=crop',
        nombre: 'Flyer Evento Especial',
        descripcion: 'Diseño promocional para conciertos y eventos con tipografía moderna, efectos de luz y paleta vibrante.',
        categoria: 'Flyers',
        precio: 120,
    },
    {
        id: 3,
        imagen: 'https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=600&h=400&fit=crop',
        nombre: 'Banner Web Principal',
        descripcion: 'Banner hero para sitio web corporativo con animaciones CSS, gradientes personalizados y llamado a la acción.',
        categoria: 'Banners',
        precio: 180,
    },
    {
        id: 4,
        imagen: 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=600&h=400&fit=crop',
        nombre: 'Pack Redes Sociales',
        descripcion: 'Kit de 10 piezas para Instagram, Facebook y LinkedIn con línea gráfica unificada y optimizadas para cada plataforma.',
        categoria: 'Redes Sociales',
        precio: 250,
    },
    {
        id: 5,
        imagen: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=600&h=400&fit=crop',
        nombre: 'Sesión Producto',
        descripcion: 'Fotografía profesional de producto con iluminación de estudio, fondo neutro y retoque digital incluido.',
        categoria: 'Fotografía',
        precio: 200,
    },
    {
        id: 6,
        imagen: 'https://images.unsplash.com/photo-1550684376-efcbd6e3f031?w=600&h=400&fit=crop',
        nombre: 'Logotipo Minimalista',
        descripcion: 'Diseño de marca con enfoque minimalista, geometría limpia y símbolo versátil para uso digital e impreso.',
        categoria: 'Logos',
        precio: 250,
    },
    {
        id: 7,
        imagen: 'https://images.unsplash.com/photo-1600132806370-bf17e65e942f?w=600&h=400&fit=crop',
        nombre: 'Flyer Lanzamiento',
        descripcion: 'Flyer promocional para lanzamiento de producto con efectos de gradiente, tipografía bold y CTA destacado.',
        categoria: 'Flyers',
        precio: 130,
    },
    {
        id: 8,
        imagen: 'https://images.unsplash.com/photo-1563986768609-322da13575f2?w=600&h=400&fit=crop',
        nombre: 'Banner Oferta Especial',
        descripcion: 'Banner promocional para campaña de descuentos con diseño llamativo, temporizador visual y precio destacado.',
        categoria: 'Banners',
        precio: 160,
    },
    {
        id: 9,
        imagen: 'https://images.unsplash.com/photo-1611926653458-09294b3142bf?w=600&h=400&fit=crop',
        nombre: 'Contenido Historias IG',
        descripcion: 'Pack de 5 historias animadas para Instagram con motion graphics, stickers personalizados y enlace swipe-up.',
        categoria: 'Redes Sociales',
        precio: 190,
    },
    {
        id: 10,
        imagen: 'https://images.unsplash.com/photo-1554048612-b6a482bc67e2?w=600&h=400&fit=crop',
        nombre: 'Edición Retrato',
        descripcion: 'Edición profesional de retrato con corrección de color, retoque facial, eliminación de imperfecciones y fondo.',
        categoria: 'Edición',
        precio: 80,
    },
    {
        id: 11,
        imagen: 'https://images.unsplash.com/photo-1536240478700-b869070f9279?w=600&h=400&fit=crop',
        nombre: 'Fotografía Corporativa',
        descripcion: 'Sesión de fotografía corporativa para equipo de trabajo con fondo personalizado y edición profesional.',
        categoria: 'Fotografía',
        precio: 350,
    },
    {
        id: 12,
        imagen: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=400&fit=crop',
        nombre: 'Mockup Empaque',
        descripcion: 'Diseño de empaque personalizado con mockup 3D, lista para impresión con sangrados y guías técnicas.',
        categoria: 'Otros',
        precio: 220,
    },
    {
        id: 13,
        imagen: 'https://images.unsplash.com/photo-1599305445671-ac291c95aaa9?w=600&h=400&fit=crop',
        nombre: 'Logotipo Tipográfico',
        descripcion: 'Marca tipográfica personalizada con lettering único, kerning ajustado y variantes para fondo claro y oscuro.',
        categoria: 'Logos',
        precio: 200,
    },
    {
        id: 14,
        imagen: 'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?w=600&h=400&fit=crop',
        nombre: 'Flyer Restaurante',
        descripcion: 'Flyer gastronómico con fotografía de plating, tipografía elegante y promoción del menú ejecutivo.',
        categoria: 'Flyers',
        precio: 110,
    },
    {
        id: 15,
        imagen: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&h=400&fit=crop',
        nombre: 'Edición Fotografía Boda',
        descripcion: 'Edición completa de álbum de boda con corrección de color, efectos dreamy y formato para impresión profesional.',
        categoria: 'Edición',
        precio: 150,
    },
]
