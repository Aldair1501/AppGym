<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteMembresiaResource\Pages;
use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ClienteMembresia;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;
use Filament\Forms\Components\{Select, DatePicker, Toggle, Grid};

class ClienteMembresiaResource extends Resource
{
    protected static ?string $model = ClienteMembresia::class;

    protected static ?string $navigationGroup = 'Gestión de Clientes y Membresías';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Asignar Membresías';
    protected static ?string $pluralModelLabel = 'Clientes con Membresía';
    protected static ?string $modelLabel = 'Asignación de Membresía';

     protected static ?int $navigationSort = 1;// ordena el grupo  para elegir cual va arriba del otro

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                Select::make('cliente_id')
                ->label('Cliente')
                ->placeholder('Selecciona el cliente')
                ->searchable()
                ->getSearchResultsUsing(fn(string $search) =>
                    Cliente::query()
                        ->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->limit(100)
                        ->get()
                        ->mapWithKeys(fn(Cliente $cliente) => [
                            $cliente->id => $cliente->nombre_completo,
                        ])
                )
                ->getOptionLabelUsing(fn($value) => Cliente::find($value)?->nombre_completo ?? 'Desconocido')
                ->required(),

                    Select::make('membresia_id')
                        ->label('Membresía')
                        ->relationship('membresia', 'nombre')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $membresia = Membresia::find($state);
                            if ($membresia) {
                                $inicio = Carbon::now();
                                $fin = $inicio->copy()->addDays($membresia->duracion_dias);
                                $set('fecha_inicio', $inicio->toDateString());
                                $set('fecha_fin', $fin->toDateString());
                            }
                        })
                        ->helperText(fn ($state) => $state ? Membresia::find($state)?->duracion_dias . ' días' : null),
                ]),

                Grid::make(2)->schema([
    
                    DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->reactive() // importante para que escuche cambios
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $membresiaId = $get('membresia_id');
                        $membresia = \App\Models\Membresia::find($membresiaId);

                        if ($membresia && $state) {
                            $fechaInicio = Carbon::parse($state);
                            $fechaFin = $fechaInicio->copy()->addDays($membresia->duracion_dias);
                            $set('fecha_fin', $fechaFin->format('Y-m-d'));
                        }
                    }),


                    DatePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->required()
                        ->disabled()
                        ->dehydrated(),
]),


            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nombre_completo')
                ->label('Cliente')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('membresia.nombre')
                    ->label('Membresía')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('membresia.tipo')
                    ->label('Tipo de Membresía')
                    ->searchable()
                    ->sortable(),


               Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d M Y'),

               Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días Restantes')
                    ->icon(function ($record) {
                        $hoy = now()->startOfDay();
                        $fin = \Illuminate\Support\Carbon::parse($record->fecha_fin)->startOfDay();
                        $dias = $hoy->diffInDays($fin, false);

                        return match (true) {
                    $dias > 3 => 'heroicon-o-check-circle',          
                    $dias >= 0 => 'heroicon-o-exclamation-circle',   
                    default => 'heroicon-o-x-circle',                
                };
                    })
                    ->color(function ($record) {
                        $hoy = now()->startOfDay();
                        $fin = \Illuminate\Support\Carbon::parse($record->fecha_fin)->startOfDay();
                        $dias = $hoy->diffInDays($fin, false);

                        return match (true) {
                            $dias > 3 => 'success',
                            $dias >= 0 => 'warning',
                            $dias < 0 => 'danger',
                            default => 'danger',
                        };
                    })
                    ->getStateUsing(function ($record) {
                        $hoy = now()->startOfDay();
                        $fin = \Illuminate\Support\Carbon::parse($record->fecha_fin)->startOfDay();

                        // Usamos isToday para asegurar detectar "vence hoy" correctamente
                        if ($fin->isToday()) {
                            return "Vence hoy";
                        }

                        if ($hoy->lessThan($fin)) {
                            $dias = $hoy->diffInDays($fin);
                            return $dias . " día(s)";
                        }

                        if ($hoy->greaterThan($fin)) {
                            $dias = $fin->diffInDays($hoy);
                            return "Hace " . $dias . " día(s)";
                        }

                        return "";
                    })
                    ->sortable(),

                    
               Tables\Columns\BadgeColumn::make('estado')
                        ->label('Estado')
                        ->getStateUsing(function ($record) {
                            $hoy = now()->startOfDay();
                            $inicio = \Illuminate\Support\Carbon::parse($record->fecha_inicio)->startOfDay();
                            $fin = \Illuminate\Support\Carbon::parse($record->fecha_fin)->startOfDay();

                            if ($record->fecha_cancelacion !== null) {
                                return 'cancelado';
                            }

                            if ($hoy->between($inicio, $fin)) {
                                return 'activo';
                            }

                            if ($hoy->greaterThan($fin)) {
                                return 'vencido';
                            }

                            // Opcional, por si la fecha inicio es futura
                            return 'pendiente';
                        })
                        ->colors([
                            'success' => 'activo',
                            'danger' => 'vencido',
                            'gray' => 'cancelado',
                            'warning' => 'pendiente',
                        ])
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'activo' => 'Activo',
                            'vencido' => 'Vencido',
                            'cancelado' => 'Cancelado',
                            'pendiente' => 'Pendiente',
                            default => ucfirst($state),
                        })
                        ->sortable(),
            ])
            ->filters([
                 Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'vencido' => 'Vencido',
                        'cancelado' => 'Cancelado',
                        'pendiente' => 'Pendiente',
                    ])
                    ->query(function ($query, $state) {
                        $hoy = now()->startOfDay();

                        if ($state === 'activo') {
                            $query->whereNull('fecha_cancelacion')
                                ->whereDate('fecha_inicio', '<=', $hoy)
                                ->whereDate('fecha_fin', '>=', $hoy);
                        } elseif ($state === 'vencido') {
                            $query->whereNull('fecha_cancelacion')
                                ->whereDate('fecha_fin', '<', $hoy);
                        } elseif ($state === 'cancelado') {
                            $query->whereNotNull('fecha_cancelacion');
                        } elseif ($state === 'pendiente') {
                            $query->whereNull('fecha_cancelacion')
                                ->whereDate('fecha_inicio', '>', $hoy);
                        }
                    }),
   
            ])


            ->actions([

                // Acción personalizada para renovar membresía en el recurso de ClienteMembresia
                Tables\Actions\Action::make('renovar')
                    ->label('Renovar')
                     ->icon('heroicon-o-arrow-path')
                    ->color('info')

                    // Mostrar solo si la membresía ya venció o vence hoy
                    ->visible(fn ($record) => now()->startOfDay()->greaterThanOrEqualTo(
                        \Illuminate\Support\Carbon::parse($record->fecha_fin)->startOfDay()
                    ))

                    // Ventana de confirmación antes de ejecutar la acción
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar renovación')
                    ->modalSubheading('¿Estás seguro de que deseas renovar esta membresía?')
                    ->modalButton('Sí, renovar')

                    // Lógica que se ejecuta al hacer clic en "Renovar"
                   ->action(function (ClienteMembresia $record) {
                    try {
                        $membresia = $record->membresia;
                        $hoy = now();

                        $record->fecha_inicio = $hoy;
                        $record->fecha_fin = $hoy->copy()->addDays($membresia->duracion_dias);
                        $record->fecha_cancelacion = null;

                        $record->save(); // Guarda los cambios

                        Notification::make()
                            ->title('Membresía renovada con éxito')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Error al renovar membresía')
                            ->body('Ocurrió un error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

                Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Tables\Actions\Action::make('cancelar')
                ->label('Cancelar membresía')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) =>
                    $record->fecha_cancelacion === null &&
                    now()->between($record->fecha_inicio, $record->fecha_fin)
                )
                ->requiresConfirmation()
                ->modalHeading('¿Cancelar membresía?')
                ->modalSubheading('Esta acción marcará la membresía como cancelada.')
                ->modalButton('Sí, cancelar')
                ->action(function ($record) {
                    $record->fecha_cancelacion = now();
                    $record->save();

                    Notification::make()
                        ->title('Membresía cancelada')
                        ->success()
                        ->send();
                }),
            ])
            ])


            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClienteMembresias::route('/'),
            'create' => Pages\CreateClienteMembresia::route('/create'),
            'edit'   => Pages\EditClienteMembresia::route('/{record}/edit'),
        ];
    }
}
