<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'pregunta' => '¿Qué servicios ofrece CREAPIX?',
                'respuesta' => 'Somos un estudio creativo especializado en diseño gráfico, fotografía, filmación, producción audiovisual, publicidad e impresión. Podés ver todos nuestros servicios en la sección Servicios de esta página.',
            ],
            [
                'pregunta' => '¿Cómo puedo solicitar una cotización?',
                'respuesta' => 'La forma más rápida es escribirnos por WhatsApp (botón flotante en toda la página), completar el formulario de contacto o enviarnos un correo. Recibirás una propuesta personalizada sin compromiso.',
            ],
            [
                'pregunta' => '¿Cuánto tarda la entrega de un trabajo?',
                'respuesta' => 'Depende del tipo de proyecto. Cada servicio indica un tiempo estimado; en general los trabajos de diseño se entregan en pocos días y los proyectos audiovisuales pueden tomar más tiempo. Lo confirmamos al acordar la cotización.',
            ],
            [
                'pregunta' => '¿Qué formas de pago aceptan?',
                'respuesta' => 'Aceptamos pagos en efectivo, QR, transferencia bancaria y tarjeta. Para cada proyecto acordamos un adelanto y el saldo al momento de la entrega.',
            ],
            [
                'pregunta' => '¿Trabajan con clientes de todo el país?',
                'respuesta' => 'Sí. Podemos coordinar proyectos a distancia para clientes fuera de nuestra ciudad y entregar los archivos digitales finales en alta calidad.',
            ],
        ];

        foreach ($faqs as $orden => $faq) {
            Faq::firstOrCreate(
                ['pregunta' => $faq['pregunta']],
                [
                    'respuesta' => $faq['respuesta'],
                    'orden' => $orden,
                    'publicado' => true,
                ]
            );
        }
    }
}
