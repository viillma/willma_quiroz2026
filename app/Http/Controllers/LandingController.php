<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use App\Models\ConfiguracionWeb;
use App\Models\LandingSetting;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class LandingController extends Controller
{
    public function index(): Response
    {
        $config = ConfiguracionWeb::primera();

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'carousel' => CarouselSlide::activos()->get(),
            'landing' => LandingSetting::allJson(),
            'configuracion' => [
                'logo' => $config->logo,
                'logo_url' => $config->logo_url,
                'nombre_empresa' => $config->nombre_empresa,
            ],
        ]);
    }
}
