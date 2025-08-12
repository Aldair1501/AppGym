<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ClienteMembresia;
use App\Models\Pago;
use App\Models\Factura;
use Filament\Forms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\ToggleButtons;


class PuntoDeVenta extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $title = 'Punto de Venta';
    protected static string $view = 'filament.pages.punto-de-venta';

    // Variables públicas para formulario
    public $cliente_tipo = 'existente';
    public $cliente_id = null;
    public $cliente_nombre = '';
    public $cliente_apellido = '';
    public $cliente_genero = null;
    public $cliente_fecha_nacimiento = null;
    public $cliente_telefono = '';
    public $cliente_direccion = '';
    public $cliente_email = '';
    public $membresia_id = null;
    public $fecha_inicio;
    public $metodo_pago = null;
    public $monto = 0;
    public $monto_recibido = 0;
    public $cambio = 0;
    public $saldo_pendiente = null;

    public function mount(): void
    {
        $this->fecha_inicio = today();
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Cliente')
                    ->description('Datos del cliente')

                    ->schema([

                        ToggleButtons::make('cliente_tipo')
                            ->label('Tipo de operación')
                            ->options([
                                'existente' => 'Renovar membresía.',
                                'nuevo' => 'Registrar cliente nuevo',
                            ])
                            ->icons([
                                'existente' => 'heroicon-o-user',
                                'nuevo' => 'heroicon-o-user-plus',
                            ])
                            ->colors([
                                'existente' => 'success',
                                'nuevo' => 'info',
                            ])
                            ->default('existente')
                            ->grouped()
                            ->reactive()
                            ->required(),
                        

                        Select::make('cliente_id')
                            ->label('Buscar cliente existente')
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => Cliente::where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellido', 'like', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn($cliente) => [$cliente->id => "{$cliente->nombre} {$cliente->apellido}"]))
                            ->getOptionLabelUsing(fn($value) => Cliente::find($value)?->nombre . ' ' . Cliente::find($value)?->apellido ?? 'Desconocido')
                            ->nullable()
                            ->visible(fn($get) => $get('cliente_tipo') === 'existente')
                            ->required(fn($get) => $get('cliente_tipo') === 'existente')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('cliente_nombre', null);
                                    $set('cliente_apellido', null);
                                    $set('cliente_genero', null);
                                    $set('cliente_fecha_nacimiento', null);
                                    $set('cliente_telefono', null);
                                    $set('cliente_direccion', null);
                                    $set('cliente_email', null);
                                }
                            }),

                        Grid::make(2)->schema([
                            TextInput::make('cliente_nombre')
                                ->label('Nombre')
                                ->required(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->maxLength(100),

                            TextInput::make('cliente_apellido')
                                ->label('Apellido')
                                ->required(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->maxLength(100),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('cliente_genero')
                                ->label('Género')
                                ->options([
                                    'Masculino' => 'Masculino',
                                    'Femenino' => 'Femenino',
                                    'Indefinido' => 'Indefinido',
                                ])
                                ->required(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo'),

                            DatePicker::make('cliente_fecha_nacimiento')
                                ->label('Fecha de nacimiento')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo'),
                        ]),

                        Grid::make(1)->schema([
                            TextInput::make('cliente_telefono')
                                ->label('Teléfono')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->maxLength(20),

                            TextInput::make('cliente_direccion')
                                ->label('Dirección')
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo'),

                            TextInput::make('cliente_email')
                                ->label('Correo electrónico')
                                ->email()
                                ->visible(fn($get) => $get('cliente_tipo') === 'nuevo')
                                ->maxLength(255),
                        ]),

                        Placeholder::make('estado_membresia')
                            ->label('Estado de membresía actual')
                            ->visible(fn($get) => $get('cliente_tipo') === 'existente' && $get('cliente_id') !== null)
                            ->content(function ($state, $get) {
                                $clienteId = $get('cliente_id');
                                if (!$clienteId) {
                                    return 'Sin cliente seleccionado';
                                }

                                $membresia = ClienteMembresia::where('cliente_id', $clienteId)
                                    ->orderByDesc('fecha_fin')
                                    ->first();

                                if (!$membresia) {
                                    return 'Sin membresía activa';
                                }

                                $fechaFin = Carbon::parse($membresia->fecha_fin);
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

                        Placeholder::make('mensaje_sin_cliente')
                            ->label('Estado de membresía actual')
                            ->visible(fn($get) => !($get('cliente_tipo') === 'existente' && $get('cliente_id') !== null))
                            ->content('Registra un cliente para ver el estado de membresía'),
                    ]),

                Step::make('Membresía')
                    ->description('Asigna una nueva membresía o renueva la existente')
                    ->schema([
                        Select::make('membresia_id')
                            ->label('Tipo de membresía')
                            ->options(Membresia::all()->pluck('nombre', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('monto', Membresia::find($state)?->precio ?? 0)),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->default(today())
                            ->required(),
                    ]),

                Step::make('Pago')
                    ->description('Detalles de pago')
                    ->schema([
                        Select::make('metodo_pago')
                            ->label('Método de pago')
                            ->options([
                                'efectivo' => 'Efectivo',
                                'transferencia' => 'Transferencia',
                                'otros' => 'Otros',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($state !== 'efectivo') {
                                    $set('monto_recibido', 0);
                                    $set('cambio', 0);
                                }
                            }),

                        TextInput::make('monto')
                            ->label('Monto a pagar')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                if ($get('metodo_pago') === 'efectivo') {
                                    $montoRecibido = floatval($get('monto_recibido') ?? 0);
                                    $cambio = $montoRecibido - floatval($state);
                                    $set('cambio', $cambio > 0 ? $cambio : 0);
                                } else {
                                    $set('cambio', 0);
                                }
                            }),

                        TextInput::make('monto_recibido')
                            ->label('Monto recibido')
                            ->numeric()
                            ->required(fn ($get) => $get('metodo_pago') === 'efectivo')
                            ->visible(fn ($get) => $get('metodo_pago') === 'efectivo')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                $monto = floatval($get('monto') ?? 0);
                                $diferencia = floatval($state ?? 0) - $monto;

                                if ($diferencia >= 0) {
                                    $set('cambio', $diferencia);
                                    $set('saldo_pendiente', null);
                                } else {
                                    $set('cambio', null);
                                    $set('saldo_pendiente', abs($diferencia));
                                }
                            }),

                        Placeholder::make('cambio_placeholder')
                            ->label('Cambio a devolver')
                            ->content(fn ($get) => 'Q' . number_format($get('cambio') ?? 0, 2))
                            ->visible(fn ($get) => $get('cambio') !== null)
                            ->extraAttributes(fn () => [
                                'style' => 'color: #16a34a; font-weight: 600; font-size: 1.125rem;',
                            ]),

                        Placeholder::make('saldo_pendiente_placeholder')
                            ->label('Saldo pendiente')
                            ->content(fn ($get) => 'Q' . number_format($get('saldo_pendiente') ?? 0, 2))
                            ->visible(fn ($get) => $get('saldo_pendiente') !== null)
                            ->extraAttributes(fn () => [
                                'style' => 'color: #dc2626; font-weight: 600; font-size: 1.125rem;',
                            ]),
                    ]),
            ])
          ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
    <x-filament::button
        type="submit"
        size="sm"
        color="primary"
    >
        Finalizar Venta
    </x-filament::button>
BLADE
)))

        ];
    }
   

    public function submit()
    {
       $this->registrarVenta();
   }

    public function registrarVenta()
    {
        try {


   $this->validate([
            'cliente_tipo' => ['required', 'in:existente,nuevo'],

            // Cliente existente
            'cliente_id' => ['required_if:cliente_tipo,existente', 'nullable', 'integer', 'exists:cliente,id'],

            // Cliente nuevo
            'cliente_nombre' => ['required_if:cliente_tipo,nuevo', 'nullable', 'string', 'max:255'],
            'cliente_apellido' => ['required_if:cliente_tipo,nuevo', 'nullable', 'string', 'max:255'],
            'cliente_genero' => ['required_if:cliente_tipo,nuevo', 'nullable', 'in:Masculino,Femenino,Indefinido'],
            'cliente_fecha_nacimiento' => ['nullable', 'date'],
            'cliente_telefono' => ['nullable', 'string', 'max:20'],
            'cliente_direccion' => ['nullable', 'string', 'max:255'],
            'cliente_email' => ['nullable', 'email', 'max:255'],

            // Membresía
            'membresia_id' => ['required', 'integer', 'exists:membresia,id'],
            'fecha_inicio' => ['required', 'date'],

            // Pago
            'metodo_pago' => ['required', 'string', 'in:efectivo,tarjeta,transferencia'],
            'monto' => ['required', 'numeric', 'min:0'],
            'monto_recibido' => ['required', 'numeric', 'min:0'],
        ]);

            $data = $this->only([
                'cliente_tipo',
                'cliente_id',
                'cliente_nombre',
                'cliente_apellido',
                'cliente_genero',
                'cliente_fecha_nacimiento',
                'cliente_telefono',
                'cliente_direccion',
                'cliente_email',
                'membresia_id',
                'fecha_inicio',
                'metodo_pago',
                'monto',
                'monto_recibido',
            ]);

            DB::transaction(function () use ($data) {
                if ($data['cliente_tipo'] === 'nuevo') {
                    $cliente = Cliente::create([
                        'nombre' => $data['cliente_nombre'],
                        'apellido' => $data['cliente_apellido'],
                        'genero' => $data['cliente_genero'],
                        'fecha_nacimiento' => $data['cliente_fecha_nacimiento'] ?? null,
                        'telefono' => $data['cliente_telefono'] ?? null,
                        'direccion' => $data['cliente_direccion'] ?? null,
                        'email' => $data['cliente_email'] ?? null,
                    ]);
                    $clienteId = $cliente->id;
                } else {
                    $clienteId = $data['cliente_id'];
                }

                $membresia = Membresia::findOrFail($data['membresia_id']);

                $existe = ClienteMembresia::where('cliente_id', $clienteId)
                    ->where('membresia_id', $membresia->id)
                    ->whereDate('fecha_inicio', $data['fecha_inicio'])
                    ->exists();

                if ($existe) {
                    throw new \Exception('Este cliente ya tiene esa membresía registrada para esa fecha.');
                }

                ClienteMembresia::create([
                    'cliente_id' => $clienteId,
                    'membresia_id' => $membresia->id,
                    'fecha_inicio' => $data['fecha_inicio'],
                    'fecha_fin' => Carbon::parse($data['fecha_inicio'])->addDays($membresia->duracion_dias),
                ]);

                $pago = Pago::create([
                    'cliente_id' => $clienteId,
                    'membresia_id' => $membresia->id,
                    'monto' => $data['monto'],
                    'fecha_pago' => now(),
                    'metodo_pago' => $data['metodo_pago'],
                ]);

                Factura::create([
                    'pago_id' => $pago->id,
                    'numero_factura' => 'F-' . now()->format('YmdHis') . '-' . $pago->id,
                    'fecha_emision' => now(),
                    'total' => $data['monto'],
                ]);
            });

            Notification::make()
                ->title('Venta registrada con éxito')
                ->success()
                ->send();

            $this->reset([
                'cliente_tipo',
                'cliente_id',
                'cliente_nombre',
                'cliente_apellido',
                'cliente_genero',
                'cliente_fecha_nacimiento',
                'cliente_telefono',
                'cliente_direccion',
                'cliente_email',
                'membresia_id',
                'fecha_inicio',
                'metodo_pago',
                'monto',
                'monto_recibido',
                'cambio',
                'saldo_pendiente',
            ]);

            $this->fecha_inicio = today();

            $this->form->fill();

            return redirect()->route('filament.admin.pages.punto-de-venta');


        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al registrar la venta')
                ->body($e->getMessage())
                ->danger()
                ->send();

        }
    }
}
