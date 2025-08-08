<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Cliente;
use App\Models\ClienteMembresia;
use App\Models\Pago;
use Illuminate\Support\Carbon;
use App\Models\Gasto;
use App\Models\Factura;


class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        // Guardamos el mes y año actual para usarlos varias veces
        $mes = now()->month;
        $anio = now()->year;

        // Calculamos los ingresos del mes actual (sumando todos los pagos realizados)
        $ingresos = Pago::whereMonth('fecha_pago', $mes)
            ->whereYear('fecha_pago', $anio)
            ->whereHas('factura', function ($query) {
                $query->where('estado', 'emitida');
            })
            ->sum('monto');

        // Calculamos los gastos del mes actual (sumando todos los gastos registrados)
        $gastos = Gasto::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->sum('monto');

        // Calculamos la utilidad como ingresos - gastos
        $utilidad = $ingresos - $gastos;


        return [
            
    // Total de clientes
    Stat::make('Clientes registrados', Cliente::count())
        ->description('Total de clientes en el sistema')
        ->icon('heroicon-o-user-group')
        ->color('cyan'),

    // Clientes con membresía vigente
    Stat::make('Clientes activos', ClienteMembresia::whereDate('fecha_inicio', '<=', now())
        ->whereDate('fecha_fin', '>=', now())
        ->count())
        ->description('Membresías vigentes hoy')
        ->icon('heroicon-o-user')
        ->color('yellow'),

    // Membresías vencidas
    Stat::make('Membresías vencidas', ClienteMembresia::whereDate('fecha_fin', '<', now())
        ->count())
        ->description('Clientes con membresía vencida')
        ->icon('heroicon-o-exclamation-circle')
        ->color('danger'),

   // Ingresos del mes
            Stat::make('Ingresos del mes', 'Q' . number_format($ingresos, 2))
                ->description('Pagos recibidos en ' . now()->translatedFormat('F'))
                ->icon('heroicon-o-currency-dollar')
                ->color('lime'),

            // Gastos del mes
            Stat::make('Gastos del mes', 'Q' . number_format($gastos, 2))
                ->description('Gastos registrados en ' . now()->translatedFormat('F'))
                ->icon('heroicon-o-receipt-refund')
                ->color('pink'),

            // Utilidad del mes
            Stat::make('Utilidad del mes', 'Q' . number_format($utilidad, 2))
                ->description('Diferencia entre ingresos y gastos en' . now()->translatedFormat('F'))
                ->icon('heroicon-o-calculator')
                ->color($utilidad >= 0 ? 'success' : 'danger'),
        
    

        ];
    }
}
