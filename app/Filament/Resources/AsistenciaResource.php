<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsistenciaResource\Pages;
use App\Models\Asistencia;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class AsistenciaResource extends Resource
{
    protected static ?string $model = Asistencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Control de Accesos';
    protected static ?string $navigationLabel = 'Asistencias';
    protected static ?string $pluralLabel = 'Asistencias';
    protected static ?string $modelLabel = 'Asistencia';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => 
                        Cliente::where(function ($query) use ($search) {
                            $query->where('nombre', 'like', "%{$search}%")
                                ->orWhere('apellido', 'like', "%{$search}%");
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($cliente) => [
                            $cliente->id => "{$cliente->nombre} {$cliente->apellido}",
                        ])
                    )
                    ->getOptionLabelUsing(fn ($value) => 
                        Cliente::find($value)
                            ?->nombre . ' ' . Cliente::find($value)?->apellido
                    )
                    ->required(),

                Forms\Components\DateTimePicker::make('fecha_hora_entrada')
                    ->label('Fecha y Hora de Entrada')
                    ->default(now()) // Valor por defecto
                    ->required()
                    ->seconds(false),

            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('Fecha')  // nombre personalizado, no existe en BD
                ->label('Fecha')
                 ->icon('heroicon-o-calendar-days')
                ->getStateUsing(fn ($record) => Carbon::parse($record->fecha_hora_entrada)->format('d/m/Y'))
                ->sortable(query: fn ($query, $direction) =>
                    $query->orderBy('fecha_hora_entrada', $direction)
                ),

               Tables\Columns\TextColumn::make('Hora de entrada')  // nombre personalizado, no existe en BD
                ->label('Hora de entrada')
                ->getStateUsing(fn ($record) => Carbon::parse($record->fecha_hora_entrada)->format('h:i A'))
                ->sortable(query: fn ($query, $direction) =>
                    $query->orderBy('fecha_hora_entrada', $direction)
                ),


                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable(['cliente.nombre', 'cliente.apellido']),


            ])

            ->filters([
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->options(Cliente::all()->pluck('nombre', 'id'))
                    ->searchable(),
            ])
            ->defaultSort('fecha_hora_entrada', 'desc')

            ->actions([

                ActionGroup::make([
                    Action::make('view')
                        ->label('Ver')
                        ->color('info')
                        ->icon('heroicon-o-eye'),
                    Action::make('edit')
                        ->label('Editar')
                        ->color('warning')
                        ->icon('heroicon-o-pencil'),
                    Action::make('delete')
                        ->label('Eliminar')
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ]),

            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencia::route('/create'),
            'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}
