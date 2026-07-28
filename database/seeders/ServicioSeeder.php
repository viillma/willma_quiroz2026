<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Servicio::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $servicios = [
            [
                'nombre' => 'Logotipos',
                'descripcion' => 'Diseño de logotipos profesionales para tu marca, incluye propuestas conceptuales y archivo vectorial editable.',
                'precio' => 150.00,
                'tiempo_estimado' => 5,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Banners Publicitarios',
                'descripcion' => 'Banners y piezas gráficas para campañas publicitarias digitales e impresas.',
                'precio' => 80.00,
                'tiempo_estimado' => 2,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Tarjetas de Invitación',
                'descripcion' => 'Diseño personalizado de tarjetas de invitación para eventos sociales y corporativos.',
                'precio' => 60.00,
                'tiempo_estimado' => 3,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Fotografía',
                'descripcion' => 'Sesiones fotográficas profesionales para productos, retratos y eventos.',
                'precio' => 250.00,
                'tiempo_estimado' => 7,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Edición de Fotografías',
                'descripcion' => 'Edición profesional de fotografías: corrección de color, retoque básico y mejora de imagen.',
                'precio' => 40.00,
                'tiempo_estimado' => 2,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Reparación de Fotografías',
                'descripcion' => 'Restauración y reparación de fotografías antiguas o dañadas: eliminación de rasguños, manchas y reconstrucción.',
                'precio' => 60.00,
                'tiempo_estimado' => 4,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Afiches Publicitarios',
                'descripcion' => 'Diseño de afiches y flyers publicitarios para impresión y redes sociales.',
                'precio' => 90.00,
                'tiempo_estimado' => 3,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Tarjetas de Presentación',
                'descripcion' => 'Diseño de tarjetas de presentación corporativas listas para impresión.',
                'precio' => 50.00,
                'tiempo_estimado' => 2,
                'estado' => 'activo',
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}
