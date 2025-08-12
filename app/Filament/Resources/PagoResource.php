<?php

namespace App\Filament\Resources;

use App\Models\Pago;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PagoResource\Pages;
use Filament\Tables\Filters\DateFilter;


class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $pluralModelLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';

    protected static ?int $navigationSort = 4 ;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make([
                Grid::make(2)->schema([
                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->placeholder('Selecciona el cliente')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            // Evita buscar si hay menos de 3 caracteres para no sobrecargar la DB
                            if (strlen($search) < 3) {
                                return collect();
                            }

                            return Cliente::query()
                                ->where('nombre', 'like', "{$search}%")    // Busca que el nombre empiece con el texto
                                ->orWhere('apellido', 'like', "{$search}%") // Igual para apellido
                                ->limit(1000)
                                ->get()
                                ->mapWithKeys(fn(Cliente $cliente) => [
                                    $cliente->id => $cliente->nombre_completo,
                                ]);
                        })
                        ->getOptionLabelUsing(fn($value) => Cliente::find($value)?->nombre_completo ?? 'Desconocido')
                        ->required(),


                    Select::make('membresia_id')
                    ->label('Membresía')
                    ->placeholder('Selecciona la membresía')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Membresia::pluck('nombre', 'id');
                    })
                    ->reactive()  // Muy importante para que actualice monto al cambiar membresía
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Cuando cambie la membresía, busca el precio y asigna al monto
                        $membresia = \App\Models\Membresia::find($state);
                        $set('monto', $membresia?->precio ?? 0);
                    })
                    ->required(),

                TextInput::make('monto')
                    ->label('Monto')
                    ->prefix('Q')
                    ->numeric()
                    ->readonly()  
                    ->required(),



                    DatePicker::make('fecha_pago')
                        ->label('Fecha de pago')
                        ->required()
                        ->closeOnDateSelection()
                        ->displayFormat('d/m/Y'),

                    Select::make('metodo_pago')
                        ->label('Método de pago')
                        ->options([
                            'efectivo' => '🟢 Efectivo',
                            'transferencia' => '💸 Transferencia',
                            'otros' => '📦 Otros',
                        ])
                        ->placeholder('Selecciona un método')
                        ->helperText('¿Cómo se realizó el pago?'),
                ]),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.nombre_completo')
                ->label('Cliente')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-user'),

                TextColumn::make('membresia.nombre')
                    ->label('Membresía')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => 'Q' . number_format($state, 2))
                    ->sortable(),

                  BadgeColumn::make('metodo_pago')
                    ->label('Método')
                    ->colors([
                        'gray' => 'otros',
                        'warning' => 'transferencia',
                        'green' => 'efectivo',
                    ])
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),


                TextColumn::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days'),

            ])
            ->defaultSort('fecha_pago', 'desc')


            ->filters([
             Tables\Filters\SelectFilter::make('metodo_pago')
        ->options([
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'otros' => 'Otros',
        ])
        ->label('Método de Pago'),

            ])


            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('Ver detalles del pago'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                  
                ]),
            ])
            ->emptyStateHeading('No hay pagos registrados')
            ->emptyStateDescription('Empieza creando un nuevo registro de pago.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Nuevo Pago'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Próximamente: relación con factura (hasOne)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
        ];
    }
}
