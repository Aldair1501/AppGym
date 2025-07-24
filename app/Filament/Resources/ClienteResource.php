<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\ForceDeleteAction;



use Illuminate\Support\Facades\DB;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\Factura;




class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationGroup = 'Gestión de Clientes';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';

  public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Información personal')
            ->description('Datos generales del cliente')
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('apellido')
                        ->label('Apellido')
                        ->required()
                        ->maxLength(100),
                ]),

                Grid::make(2)->schema([
                Select::make('genero')
                        ->label('Género')
                        ->options([
                            'Masculino' => 'Masculino',
                            'Femenino' => 'Femenino',
                            'Indefinido' => 'Indefinido',
                        ])
                        ->native(false)
                        ->required(),
                       

                    DatePicker::make('fecha_nacimiento')
                        ->label('Fecha de nacimiento')
                        ->nullable(),
                ]),

                Grid::make(1)->schema([
                    TextInput::make('telefono')
                        ->label('Teléfono')
                        ->maxLength(20)
                        ->nullable(),

                    TextInput::make('direccion')
                        ->label('Dirección')
                        ->nullable(),

                        TextInput::make('email')
                        ->label('Correo electrónico')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->nullable(),
                ]),
            ]),

        
    ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')
        ->label('Nombre')
        ->getStateUsing(fn ($record) => "{$record->nombre} {$record->apellido}")
        ->searchable(['nombre', 'apellido'])
        ->sortable()
        ->icon('heroicon-o-user'),


         TextColumn::make('genero')
        ->label('Género')
        ->badge()
        ->color(fn ($record) => match($record->genero) {
            'Masculino' => 'info',
            'Femenino' => 'pink',
            'Indefinido' => 'gray',
            default => 'secondary',
        }),

    TextColumn::make('email')
        ->label('Correo')
        ->sortable()
        ->searchable()
        ->tooltip(fn ($record) => $record->email ?? 'Sin correo'),

    TextColumn::make('telefono')
        ->label('Teléfono')
        ->tooltip(fn ($record) => $record->telefono ?? 'No registrado'),

    TextColumn::make('edad')
        ->label('Edad')
        ->getStateUsing(fn ($record) => $record->fecha_nacimiento
            ? Carbon::parse($record->fecha_nacimiento)->age . ' años'
            : 'No registrada'),


    // (toggleables)
    TextColumn::make('direccion')
        ->label('Dirección')
        ->limit(20)
        ->tooltip(fn ($record) => $record->direccion)
        ->toggleable(),

    TextColumn::make('fecha_nacimiento')
        ->label('Nacimiento')
        ->date('d M Y')
        ->icon('heroicon-o-calendar')
        ->toggleable(),

    TextColumn::make('created_at')
        ->label('Fecha de registro')
        ->date('d M Y')
        ->icon('heroicon-o-clock')
        ->sortable()
        ->toggleable(),
            ])
            ->filters([
                    SelectFilter::make('genero')
                ->label('Filtrar por género')
                ->options([
                    'Masculino' => 'Masculino',
                    'Femenino' => 'Femenino',
                    'Indefinido' => 'Indefinido',
                ]),

                Filter::make('created_at')
                    ->label('Registrado entre')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),

                    TrashedFilter::make(),

            ])
            ->actions([
                    \Filament\Tables\Actions\EditAction::make(),
                    \Filament\Tables\Actions\DeleteAction::make(),
                     RestoreAction::make(),
                     ForceDeleteAction::make(),





            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 25, 50, 100])
            ->striped();
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }
}
