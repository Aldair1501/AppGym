<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; // Para manejar fechas fácilmente

class MembresiasPorTipoChart extends ChartWidget
{
    // Título del widget más claro para el dashboard
    protected static ?string $heading = 'Membresías vendidas en el mes actual por tipo';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;


    /**
     * Método principal para obtener los datos que alimentan el gráfico.
     * Aquí hacemos la consulta para obtener el total de membresías vendidas
     * agrupadas por nombre, solo para el mes en curso.
     *
     * @return array Datos formateados para el gráfico Pie de Filament
     */
    protected function getData(): array
    {
        // Obtenemos las membresías vendidas durante el mes actual
        $data = DB::table('cliente_membresia')
            ->join('membresia', 'cliente_membresia.membresia_id', '=', 'membresia.id')
            ->select('membresia.nombre', DB::raw('COUNT(*) as total'))
            // Filtramos para que solo cuente las del mes actual
            ->whereBetween('cliente_membresia.created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->groupBy('membresia.nombre')
            ->orderByDesc('total')
            ->get();

        // Si no hay datos para este mes, mostramos un gráfico vacío con etiqueta "Sin datos"
        if ($data->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'data' => [1], // Valor mínimo para que se muestre el gráfico
                        'backgroundColor' => ['#D1D5DB'], // Color gris claro para indicar vacío
                    ],
                ],
                'labels' => ['Sin datos'],
            ];
        }

        // Extraemos los nombres de membresía y la cantidad total de cada una
        $labels = $data->pluck('nombre');
        $values = $data->pluck('total');

        // Retornamos los datos formateados con colores generados dinámicamente
        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $this->generateColors($labels->count()),
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Definimos que el tipo de gráfico será "pie" (gráfico de torta)
     *
     * @return string Tipo de gráfico
     */
    protected function getType(): string
    {
        return 'pie';
    }

    /**
     * Genera un arreglo de colores hex para las secciones del gráfico.
     * Usa un conjunto base y, si hay más categorías que colores base,
     * genera colores aleatorios con brillo medio para mejor visibilidad.
     *
     * @param int $count Número de colores a generar
     * @return array Arreglo de colores hexadecimales
     */
    private function generateColors(int $count): array
    {
        $baseColors = [
            '#3B82F6', // azul
            '#F59E0B', // amarillo
            '#10B981', // verde
            '#EF4444', // rojo
            '#8B5CF6', // morado
            '#EC4899', // rosa fuerte
            '#6366F1', // azul violeta
            '#F472B6', // rosa claro
            '#22D3EE', // cian
            '#A78BFA', // lila
        ];

        if ($count <= count($baseColors)) {
            return array_slice($baseColors, 0, $count);
        }

        $colors = $baseColors;

        // Generar colores adicionales aleatorios para los casos donde hay más tipos
        for ($i = count($baseColors); $i < $count; $i++) {
            $colors[] = $this->randomColor();
        }

        return $colors;
    }

    /**
     * Genera un color hexadecimal aleatorio con valores RGB entre 100 y 200
     * para evitar colores muy oscuros o muy claros, buscando buena visibilidad.
     *
     * @return string Color hexadecimal
     */
    private function randomColor(): string
    {
        $r = rand(100, 200);
        $g = rand(100, 200);
        $b = rand(100, 200);

        return sprintf("#%02X%02X%02X", $r, $g, $b);
    }

    protected function getOptions(): array
{
    return [
        'scales' => [
            'x' => [
                'grid' => ['display' => false],
                'ticks' => ['display' => false], // Oculta los números del eje X
            ],
            'y' => [
                'grid' => ['display' => false],
                'ticks' => ['display' => false], // Oculta los números del eje Y
            ],
        ],
        'responsive' => true,
        'maintainAspectRatio' => false,


        
    ];
}



    
}
