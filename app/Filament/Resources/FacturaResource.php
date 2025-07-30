<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Filament\Resources\FacturaResource\RelationManagers;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 4 ;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('numero_factura')
                    ->label('Número de Factura')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\DatePicker::make('fecha_emision')
                    ->label('Fecha de Emisión')
                    ->required(),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->prefix('Q')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('numero_factura')
                    ->label('Factura')
                    ->searchable(),

                 Tables\Columns\TextColumn::make('pago.cliente.nombre_completo')
                    ->label('Cliente')
                    ->getStateUsing(fn ($record) => "{$record->pago->cliente->nombre} {$record->pago->cliente->apellido}")
                    ->searchable(['pago.cliente.nombre', 'pago.cliente.apellido'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('pago.membresia.nombre')
                    ->label('Membresía')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_emision')
                    ->label('Fecha de Emisión')
                    ->date(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Q' . number_format($state, 2))
                    ->sortable(),
            ])
            ->filters([
               
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListFacturas::route('/'),
           // 'create' => Pages\CreateFactura::route('/create'),
            'edit' => Pages\EditFactura::route('/{record}/edit'),
        ];
    }
}
