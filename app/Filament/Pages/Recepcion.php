<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ClienteMembresia;
use App\Models\Pago;
use App\Models\Factura;
use App\Models\Asistencia;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;

class Recepcion extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $title = 'Punto de Recepción';
    protected static string $view = 'filament.pages.recepcion';

    public $cliente_id = null;
    public $cliente_nombre = '';
    public $membresia_id = null;
    public $fecha_inicio;
    public $metodo_pago = null;
    public $monto = 0;

    public function mount(): void
    {
        $this->fecha_inicio = today();
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Section::make('Cliente')
                    ->description('Busca un cliente o registra uno nuevo')
                    
                    ->schema([
                    Select::make('cliente_id')
                        ->label('Buscar cliente existente')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return Cliente::where('nombre', 'like', "%{$search}%")
                                ->limit(50)
                                ->pluck('nombre', 'id');
                        })
                        ->getOptionLabelUsing(fn($value) => Cliente::find($value)?->nombre ?? 'Desconocido')
                        ->nullable()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('cliente_nombre', '')),

                        TextInput::make('cliente_nombre')
                            ->label('Nombre cliente (si es nuevo)')
                            ->placeholder('Escribe nombre si es cliente nuevo')
                            ->required(fn (callable $get) => !$get('cliente_id'))
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('cliente_id', null)),

                    Placeholder::make('estado_membresia')
                    ->label('Estado membresía actual')
                    ->content(function ($state, $get) {
                        $clienteId = $get('cliente_id');
                        if (!$clienteId) return 'Sin cliente seleccionado';

                        $membresia = ClienteMembresia::where('cliente_id', $clienteId)
                            ->orderByDesc('fecha_fin')
                            ->first();

                        if (!$membresia) return 'Sin membresía activa';

                        $fechaFin = \Illuminate\Support\Carbon::parse($membresia->fecha_fin);
                        $hoy = now();
                        $dias = (int) $hoy->diffInDays($fechaFin, false);

                        if ($dias > 0) {
                            return "✅ Activa hasta {$fechaFin->format('d/m/Y')} (faltan {$dias} días)";
                        } elseif ($dias === 0) {
                            return "❗ Vence hoy ({$fechaFin->format('d/m/Y')})";
                        } else {
                            return "❌ Vencida el {$fechaFin->format('d/m/Y')} (hace " . abs($dias) . " días)";
                        }
                    }),

                    ])->columnSpan(1),

                Section::make('Membresía')
                    ->description('Selecciona la membresía para vender o renovar')
                    ->schema([
                        Select::make('membresia_id')
                            ->label('Tipo de membresía')
                            ->options(Membresia::all()->pluck('nombre', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('monto', Membresia::find($state)?->precio ?? 0)),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->default(today())
                            ->required(),
                    ])->columnSpan(1),

                Section::make('Pago')
                    ->description('Datos del pago')
                    ->schema([
                        Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'tarjeta' => 'Tarjeta',
                                'transferencia' => 'Transferencia',
                            ])
                            ->required(),

                        TextInput::make('monto')
                            ->label('Monto a pagar')
                            ->numeric()
                            ->required(),
                    ])->columnSpan(1),
            ]),
        ];
    }

    public function registrarVenta()
    {
        $data = $this->form->getState();

        if (!$data['membresia_id'] || !$data['monto']) {
            Notification::make()
                ->title('Error: Selecciona una membresía y especifica el monto')
                ->danger()
                ->send();
            return;
        }

        // Crear cliente nuevo
        if (!$data['cliente_id']) {
            if (empty(trim($data['cliente_nombre']))) {
                Notification::make()
                    ->title('Error: Escribe un nombre para el nuevo cliente')
                    ->danger()
                    ->send();
                return;
            }

            $cliente = Cliente::create([
                'nombre' => $data['cliente_nombre'],
            ]);
            $clienteId = $cliente->id;
        } else {
            $clienteId = $data['cliente_id'];
        }

        $membresia = Membresia::findOrFail($data['membresia_id']);

        // Registrar cliente_membresia
                $existe = ClienteMembresia::where('cliente_id', $clienteId)
                ->where('membresia_id', $membresia->id)
                ->whereDate('fecha_inicio', $data['fecha_inicio'])
                ->exists();

            if ($existe) {
                Notification::make()
                    ->title('Registro duplicado')
                    ->body('Este cliente ya tiene esa membresía registrada para esa fecha.')
                    ->danger()
                    ->send();
                return;
            }

            // Registrar cliente_membresia
            ClienteMembresia::create([
                'cliente_id' => $clienteId,
                'membresia_id' => $membresia->id,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => Carbon::parse($data['fecha_inicio'])->addDays($membresia->duracion_dias),
            ]);
        // Registrar pago
        $pago = Pago::create([
    'cliente_id' => $clienteId,
    'membresia_id' => $membresia->id,  // <-- Aquí agregamos este campo
    'monto' => $data['monto'],
    'fecha_pago' => now(),
    'metodo_pago' => $data['metodo_pago'],
]);

        // Registrar factura
        Factura::create([
            'pago_id' => $pago->id,
            'numero_factura' => 'F-' . now()->format('YmdHis'),
            'fecha_emision' => now(),
            'total' => $data['monto'],
        ]);

        // Registrar asistencia si es diario
        if ($membresia->duracion_dias === 1) {
            Asistencia::create([
                'cliente_id' => $clienteId,
                'fecha' => now(),
            ]);
        }

        Notification::make()
            ->title('Venta registrada con éxito')
            ->success()
            ->send();

        // Reset formulario
        $this->cliente_id = null;
        $this->cliente_nombre = '';
        $this->membresia_id = null;
        $this->fecha_inicio = today();
        $this->metodo_pago = null;
        $this->monto = 0;

        $this->form->fill();
    }
}
