<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pago;
use App\Models\Gasto;
use Illuminate\Support\Carbon;

class IngresosVsGastosChart extends ChartWidget
{
    protected static ?int $sort = 2;
    //protected int|string|array $columnSpan = 1;   //esto es para aumentar el tamaño del grafico
    protected static ?string $heading = 'Ingresos vs Gastos mensuales';

   protected function getData(): array
{
    $year = now()->year;

    $labels = [];
    $ingresos = [];
    $gastos = [];

    for ($i = 1; $i <= 12; $i++) {
        $inicio = Carbon::create($year, $i, 1)->startOfMonth();
        $fin = $inicio->copy()->endOfMonth();

        // Ingresos del mes filtrando pagos que tengan factura con estado 'emitida'
        $totalIngresos = Pago::whereBetween('fecha_pago', [$inicio, $fin])
            ->whereHas('factura', function ($query) {
                $query->where('estado', 'emitida');
            })
            ->sum('monto');

        // Gastos del mes
        $totalGastos = Gasto::whereBetween('fecha', [$inicio, $fin])->sum('monto');

        $labels[] = ucfirst(mb_substr($inicio->translatedFormat('F'), 0, 3));
        $ingresos[] = round($totalIngresos, 2);
        $gastos[] = round($totalGastos, 2);
    }

    return [
        'datasets' => [
            [
                'label' => 'Ingresos (Q)',
                'data' => $ingresos,
                'backgroundColor' => 'rgba(34, 197, 94, 0.7)', // Verde
                'borderColor' => 'rgba(34, 197, 94, 1)',
                'borderWidth' => 1,
            ],
            [
                'label' => 'Gastos (Q)',
                'data' => $gastos,
                'backgroundColor' => 'rgba(239, 68, 68, 0.7)', // Rojo
                'borderColor' => 'rgba(239, 68, 68, 1)',
                'borderWidth' => 1,
            ],
        ],
        'labels' => $labels,
    ];
}

    protected function getType(): string
    {
        return 'bar';
    }


   
}
