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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('dir_id')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('dir_name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('dir_head_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('dir_telp')
                    ->tel()
                    ->required()
                    ->maxLength(255),
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
                    ->label('Kepala Direktorat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dir_telp')
                    ->label('Nomor Telp')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
