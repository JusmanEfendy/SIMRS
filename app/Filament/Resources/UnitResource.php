<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_unit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unit_name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('unit_head_name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('unit_telp')
                    ->tel()
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('dir_id')
                    ->relationship('directorate', 'dir_name')
                    ->label('Direktorat')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id())
                    ->disabled(fn (string $context) => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_unit')
                    ->label('ID Unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_head_name')
                    ->label('Kepala Unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_telp')
                    ->label('Nomor Telp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('directorate.dir_name')
                    ->label('Direktorat')
                    ->sortable()
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view' => Pages\ViewUnit::route('/{record}'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Filter untuk role Direksi/Direktorat - tampilkan unit yang terhubung dengan direktorat mereka
        if (auth()->check() && (auth()->user()->hasRole('Direksi') || auth()->user()->hasRole('Direktorat'))) {
            // Ambil dir_id dari user yang sedang login
            $userDirId = auth()->user()->dir_id;
            
            if ($userDirId) {
                // Filter unit berdasarkan dir_id yang sesuai dengan user's dir_id
                $query->where('dir_id', $userDirId);
            } else {
                // Jika user tidak memiliki dir_id, tampilkan kosong
                $query->whereRaw('1 = 0');
            }
        }
        
        return $query;
    }
}
