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

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Unit Kerja';

    protected static ?string $modelLabel = 'Unit Kerja';

    protected static ?string $pluralModelLabel = 'Unit Kerja';

    protected static ?string $navigationGroup = 'Struktur Organisasi';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['Admin', 'Direksi']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_unit')
                    ->label('ID Unit')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(50)
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'ID Unit tidak dapat diubah karena sudah terhubung dengan data lain.' : null),
                Forms\Components\TextInput::make('unit_name')
                    ->required()
                    ->label('Nama Unit')
                    ->maxLength(100),
                Forms\Components\TextInput::make('unit_head_name')
                    ->required()
                    ->label('Kepala Unit')
                    ->maxLength(100),
                Forms\Components\TextInput::make('unit_telp')
                    ->tel()
                    ->label('Nomor Telepon')
                    ->rules([
                            'regex:/^(?:\+62|08)[0-9]{8,11}$/',
                        ])
                    ->validationMessages([
                        'regex' => 'Nomor telepon harus diawali +62 atau 08 (contoh: +628123456789 atau 08123456789)',
                    ])
                    ->required()
                    ->maxLength(20),
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
                    ->label('Nomor Telepon')
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
