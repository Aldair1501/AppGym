<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoResource\Pages;
use App\Models\Gasto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Gastos';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              Forms\Components\TextInput::make('concepto')
                    ->label('Concepto')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ejemplo: Pago a técnico o compra de insumos')
                    ->helperText('Descripción breve del gasto'),

                Forms\Components\TextInput::make('monto')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->placeholder('Ejemplo: 150.00')
                    ->helperText('Monto en quetzales'),

                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->required()
                    ->helperText('Fecha en que se realizó el gasto'),

               Forms\Components\Select::make('tipo')
                    ->label('Tipo de Gasto')
                    ->options([
                        'salario' => 'Salario',
                        'servicio' => 'Servicio',
                        'mantenimiento' => 'Mantenimiento',
                        'suministro' => 'Suministro',
                        'servicio_basico' => 'Servicios Básicos',
                        'otro' => 'Otro',
                    ])
                    ->required()
                    ->placeholder('Selecciona el tipo de gasto')
                    ->reactive() // necesario para que cambie el helperText dinámicamente
                    ->helperText(fn ($get) => match ($get('tipo')) {
                        'salario' => '💼 Pago a empleados o entrenadores fijos.',
                        'servicio' => '🤝 Servicios contratados temporalmente.',
                        'mantenimiento' => '🔧 Reparaciones de equipo o instalaciones.',
                        'suministro' => '📦Compra de insumos consumibles y de reposición.',
                        'servicio_basico' => '💡 Luz, agua, internet u otros servicios fijos.',
                        'otro' => '📌 Gastos no clasificados en las otras categorías.',
                        default => 'Categoría del gasto',
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->wrap()
                    ->tooltip(fn ($record) => $record->concepto),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => 'Q ' . number_format($state, 2))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->colors([
                        'teal' => 'salario',
                        'warning' => 'servicio',
                        'gray' => 'mantenimiento',
                        'success' => 'suministro',
                        'info' => 'servicio_basico',
                        'purple' => 'otro',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'salario' => 'Salario',
                        'servicio' => 'Servicio',
                        'mantenimiento' => 'Mantenimiento',
                        'suministro' => 'Suministro',
                        'servicio_basico' => 'Servicios Básicos',
                        'otro' => 'Otro',
                        default => ucfirst($state),
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo de Gasto')
                    ->options([
                        'salario' => 'Salario',
                        'servicio' => 'Servicio',
                        'mantenimiento' => 'Mantenimiento',
                        'suministro' => 'Suministro',
                        'servicio_basico' => 'Servicios Básicos',
                        'otro' => 'Otro',
                    ]),

                Filter::make('fecha')
                    ->label('Rango de Fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                 Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Eliminar Seleccionados'),


            ])
            ->defaultSort('fecha', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGastos::route('/'),
            'create' => Pages\CreateGasto::route('/create'),
            'edit' => Pages\EditGasto::route('/{record}/edit'),
        ];
    }
}
