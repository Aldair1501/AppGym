<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadoResource\Pages;
use App\Models\Empleado;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;

class EmpleadoResource extends Resource
{
    protected static ?string $model = Empleado::class;

    protected static ?string $navigationGroup = 'Personal';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; // Icono más representativo
    protected static ?string $navigationLabel = 'Empleados'; // Label personalizado
    protected static ?string $pluralModelLabel = 'Empleados';
    protected static ?string $modelLabel = 'Empleado';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Información del Empleado')
                ->description('Completa los datos del empleado')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('nombre')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('puesto')
                            ->label('Puesto')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),

                        DatePicker::make('fecha_contratacion')
                            ->label('Fecha de contratación')
                            ->closeOnDateSelection(),
                    ]),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('puesto')
                    ->label('Puesto')
                    ->sortable(),

                TextColumn::make('telefono')
                    ->label('Teléfono'),

                TextColumn::make('email')
                    ->label('Correo')
                    ->url(fn ($record) => 'mailto:' . $record->email, true)
                    ->openUrlInNewTab(),

                TextColumn::make('fecha_contratacion')
    ->label('Fecha de contratación')
    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d M. Y'))
    ->sortable(),

                
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Puedes agregar filtros personalizados si deseas
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay empleados registrados')
            ->emptyStateDescription('Crea un nuevo registro para comenzar.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleado::route('/create'),
            'edit' => Pages\EditEmpleado::route('/{record}/edit'),
        ];
    }
}
