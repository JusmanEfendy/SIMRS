<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100),
                Forms\Components\DateTimePicker::make('email_verified_at')->hidden(),
                Forms\Components\TextInput::make('password')
                    ->visibleOn('create')
                    ->revealable()
                    ->password()
                    ->required()
                    ->maxLength(50)
                    ->rule(
                        Password::min(8)
                        ->letters()
                        ->mixedCase()
                    )
                    ->validationMessages([
                        'min' => 'Password minimal 8 karakter.',
                        'regex' => 'Password harus mengandung huruf besar, dan huruf kecil.',
                    ]),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options(\Spatie\Permission\Models\Role::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                        if ($record) {
                            $component->state($record->roles->first()?->id);
                        }
                    })
                    ->dehydrated(false),
                Select::make('id_unit')
                    ->label('Unit Kerja')
                    ->relationship('unit', 'unit_name')
                    ->searchable()
                    ->preload()
                    ->visible(function (callable $get) {
                        $roleId = $get('role');
                        if (!$roleId) return false;
                        $role = \Spatie\Permission\Models\Role::find($roleId);
                        return $role && $role->name === 'Unit';
                    }),
                Select::make('dir_id')
                    ->label('Direktorat yang Dikelola')
                    ->relationship('managedDirectorate', 'dir_name')
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih direktorat yang akan dikelola oleh user dengan role Direksi')
                    ->visible(function (callable $get) {
                        $roleId = $get('role');
                        if (!$roleId) return false;
                        $role = \Spatie\Permission\Models\Role::find($roleId);
                        return $role && in_array($role->name, ['Direksi', 'Direktorat']);
                    }),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->hidden()
                    ->dateTime()
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
