<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Colors\Color;

class FilamentColorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        FilamentColor::register([
    'red' => Color::hex('#ef4444'),        // rojo 500
    'orange' => Color::hex('#f97316'),     // naranja 500
    'amber' => Color::hex('#f59e0b'),      // ámbar 500
    'yellow' => Color::hex('#eab308'),     // amarillo 500
    'lime' => Color::hex('#84cc16'),       // lima 500
    'green' => Color::hex('#22c55e'),      // verde 500
    'emerald' => Color::hex('#10b981'),    // esmeralda 500
    'teal' => Color::hex('#14b8a6'),       // teal 500
    'cyan' => Color::hex('#06b6d4'),       // cian 500
    'sky' => Color::hex('#0ea5e9'),        // cielo 500
    'purple' => Color::hex('#a855f7'),     // púrpura 500
    'fuchsia' => Color::hex('#d946ef'),    // fucsia 500
    'pink' => Color::hex('#ec4899'),       // rosa 500
    'slate' => Color::hex('#8e18c0ff'),      // pizarra 500
    'gray' => Color::hex('#6b7280'),       // gris 500
        ]);
    }
}
