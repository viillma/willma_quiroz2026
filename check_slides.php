<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$slides = App\Models\CarouselSlide::all();
foreach ($slides as $slide) {
    echo "Title: " . $slide->titulo . "\n";
    echo "  Imagen: " . $slide->imagen . "\n";
    echo "  Imagen URL: " . $slide->imagen_url . "\n";
    echo "  Active: " . ($slide->activo ? "yes" : "no") . "\n";
    echo "  Order: " . $slide->orden . "\n";
    echo "\n";
}