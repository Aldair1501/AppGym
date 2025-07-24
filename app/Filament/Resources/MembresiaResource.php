<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembresiaResource\Pages;
use App\Filament\Resources\MembresiaResource\RelationManagers;
use App\Models\Membresia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;


class MembresiaResource extends Resource
{
    protected static ?string $model = Membresia::class;

    protected static ?string $navigationGroup = 'Membresías';
    protected static ?string $navigationIcon = 'heroicon-o-gift-top';
    protected static ?string $navigationLabel = 'Planes de Membresía'; // Menú lateral
    protected static ?string $pluralLabel = 'Membresías';     // Encabezado de la tabla
    protected static ?string $modelLabel = 'Membresía';       // En formularios y botones

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
             TextInput::make('nombre')
                ->label('Nombre de la membresía')
                ->required()
                ->maxLength(100),

            TextInput::make('precio')
                ->label('Precio')
                ->numeric()
                ->minValue(0)
                ->prefix('Q')
                ->required(),

           TextInput::make('duracion_dias')
                ->label('Duración (días)')
                ->numeric()
                ->minValue(1)
                ->required(),

                 Select::make('tipo')
                ->label('Tipo de Membresía')
                ->options([
                    'Gimnasio' => 'Gimnasio',
                    'Box' => 'Boxeo',
                    'Mixto' => 'Gimnasio + Boxeo',
                ])
                ->required()
                ->native(false),

            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(4)
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('nombre')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

                TextColumn::make('tipo')
                ->label('Tipo')
                ->badge()
                ->sortable()
                ->color(fn (string $state): string => match ($state) {
                    'Gimnasio' => 'info',
                    'Box' => 'warning',
                    'Mixto' => 'success',
                    default => 'gray',
                }),

            TextColumn::make('precio')
                ->label('Precio')
                 ->formatStateUsing(fn ($state) => 'Q' . number_format($state, 2))
                ->sortable(),

            TextColumn::make('duracion_dias')
                ->label('Duración (días)')
                ->icon('heroicon-o-clock')
                ->sortable(),

            TextColumn::make('descripcion')
                ->label('Descripción')
                ->limit(30),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                 Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembresias::route('/'),
            'create' => Pages\CreateMembresia::route('/create'),
            'edit' => Pages\EditMembresia::route('/{record}/edit'),
        ];
    }
}
