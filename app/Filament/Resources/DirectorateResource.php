<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorateResource\Pages;
use App\Filament\Resources\DirectorateResource\RelationManagers;
use App\Models\Directorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DirectorateResource extends Resource
{
    protected static ?string $model = Directorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Direktorat';

    protected static ?string $modelLabel = 'Direktorat';

    protected static ?string $pluralModelLabel = 'Direktorat';

    protected static ?string $navigationGroup = 'Struktur Organisasi';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('dir_id')
                    ->label('ID Direktorat')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'ID Direktorat sudah digunakan.',
                    ])
                    ->required()
                    ->maxLength(50)
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'ID Direktorat tidak dapat diubah karena sudah terhubung dengan data lain.' : null),
                Forms\Components\TextInput::make('dir_name')
                    ->label('Nama Direktorat')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('dir_head_name')
                    ->label('Direksi')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('dir_telp')
                    ->label('Nomor Telepon')
                    ->rules([
                            'regex:/^(?:\+62|08)[0-9]{8,11}$/',
                        ])
                    ->validationMessages([
                        'regex' => 'Nomor telepon harus diawali +62 atau 08 (contoh: +628123456789 atau 08123456789)',
                    ])
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->disabled(fn (string $context) => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dir_id')
                ->label('ID Direktorat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dir_name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dir_head_name')
                    ->label('Direksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dir_telp')
                    ->label('Nomor Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDirectorates::route('/'),
            'create' => Pages\CreateDirectorate::route('/create'),
            'view' => Pages\ViewDirectorate::route('/{record}'),
            'edit' => Pages\EditDirectorate::route('/{record}/edit'),
        ];
    }
}
