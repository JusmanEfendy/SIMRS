<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SopResource\Pages;
use App\Filament\Resources\SopResource\RelationManagers;
use App\Models\Sop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Dokumen SOP';
    
    protected static ?string $modelLabel = 'SOP';
    
    protected static ?string $pluralModelLabel = 'Dokumen SOP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    // ========== STEP 1: Informasi Dasar ==========
                    Forms\Components\Wizard\Step::make('Informasi Dasar')
                        ->description('Data utama dokumen SOP')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Section::make('📋 Identitas SOP')
                                ->description('Informasi identitas dokumen SOP')
                                ->icon('heroicon-o-identification')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('id_sop')
                                                ->label('ID SOP')
                                                ->placeholder('Akan terisi otomatis')
                                                ->helperText('ID SOP akan digenerate otomatis saat menyimpan')
                                                ->disabled()
                                                ->dehydrated(false)
                                                ->prefixIcon('heroicon-o-hashtag'),
                                            Forms\Components\TextInput::make('sk_number')
                                                ->label('Nomor SK')
                                                ->placeholder('Masukkan nomor SK...')
                                                ->helperText('Contoh: SK/001/2024')
                                                ->required()
                                                ->maxLength(100)
                                                ->prefixIcon('heroicon-o-document-duplicate'),
                                        ]),
                                    Forms\Components\TextInput::make('sop_name')
                                        ->label('Nama SOP')
                                        ->placeholder('Masukkan nama lengkap SOP...')
                                        ->helperText('Nama SOP yang jelas dan deskriptif')
                                        ->required()
                                        ->maxLength(100)
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('🏢 Unit & Tipe')
                                ->description('Klasifikasi dokumen SOP')
                                ->icon('heroicon-o-building-office')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('id_unit')
                                                ->label('Unit Kerja')
                                                ->placeholder('Pilih unit kerja...')
                                                ->relationship('unit', 'unit_name')
                                                ->searchable()
                                                ->preload()
                                                ->required()
                                                ->prefixIcon('heroicon-o-building-office-2'),
                                            Forms\Components\Select::make('type_sop')
                                                ->label('Tipe SOP')
                                                ->placeholder('Pilih tipe SOP...')
                                                ->options([
                                                    'NonAP' => '📄 Non AP',
                                                    'AP' => '🛡️ AP (Audit Prosedur)',
                                                ])
                                                ->required()
                                                ->native(false)
                                                ->prefixIcon('heroicon-o-tag'),
                                        ]),
                                ]),

                            // Hidden fields
                            Forms\Components\Hidden::make('user_id')
                                ->default(fn () => auth()->id()),
                            Forms\Components\Hidden::make('status')
                                ->default('Pending'),
                        ]),

                    // ========== STEP 2: Dokumen & Deskripsi ==========
                    Forms\Components\Wizard\Step::make('Dokumen')
                        ->description('Upload file dan deskripsi')
                        ->icon('heroicon-o-paper-clip')
                        ->schema([
                            Forms\Components\Section::make('📎 Upload Dokumen')
                                ->description('Unggah file dokumen SOP dalam format PDF')
                                ->icon('heroicon-o-arrow-up-tray')
                                ->schema([
                                    Forms\Components\FileUpload::make('file_path')
                                        ->label('File Dokumen SOP')
                                        ->directory('sop-files')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->maxSize(10240) // 10MB
                                        ->helperText('Format: PDF, Maksimal ukuran: 10MB')
                                        ->downloadable()
                                        ->openable()
                                        ->previewable(true)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('📝 Deskripsi')
                                ->description('Penjelasan singkat tentang SOP')
                                ->icon('heroicon-o-pencil-square')
                                ->collapsible()
                                ->schema([
                                    Forms\Components\RichEditor::make('desc')
                                        ->label('')
                                        ->placeholder('Tuliskan deskripsi atau ringkasan SOP di sini...')
                                        ->helperText('Opsional - Jelaskan tujuan dan ruang lingkup SOP')
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'underline',
                                            'bulletList',
                                            'orderedList',
                                        ])
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ========== STEP 3: Periode Berlaku ==========
                    Forms\Components\Wizard\Step::make('Periode Berlaku')
                        ->description('Tanggal pengesahan dan masa berlaku')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Forms\Components\Section::make('📅 Tanggal Penting')
                                ->description('Atur tanggal pengesahan dan masa berlaku SOP')
                                ->icon('heroicon-o-calendar-days')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\DatePicker::make('approval_date')
                                                ->label('Tanggal Pengesahan')
                                                ->placeholder('Pilih tanggal...')
                                                ->helperText('Tanggal SOP disahkan')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d F Y')
                                                ->prefixIcon('heroicon-o-check-badge'),
                                            Forms\Components\DatePicker::make('start_date')
                                                ->label('Tanggal Berlaku')
                                                ->placeholder('Pilih tanggal...')
                                                ->helperText('SOP mulai berlaku sejak tanggal ini')
                                                ->required()
                                                ->native(false)
                                                ->displayFormat('d F Y')
                                                ->prefixIcon('heroicon-o-play')
                                                ->live()
                                                ->afterStateUpdated(function (callable $set, $state) {
                                                    if ($state) {
                                                        $expiredDate = Carbon::parse($state)->addYears(3);
                                                        $set('expired', $expiredDate->toDateString());
                                                        $set('days_left', (int) now()->diffInDays($expiredDate, false));
                                                    }
                                                }),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\DatePicker::make('expired')
                                                ->label('Tanggal Kadaluarsa')
                                                ->helperText('Otomatis: 3 tahun setelah tanggal berlaku')
                                                ->disabled()
                                                ->dehydrated()
                                                ->native(false)
                                                ->displayFormat('d F Y')
                                                ->prefixIcon('heroicon-o-exclamation-triangle'),
                                            Forms\Components\TextInput::make('days_left')
                                                ->label('Sisa Masa Berlaku')
                                                ->helperText('Otomatis terhitung dari tanggal berlaku')
                                                ->disabled()
                                                ->dehydrated()
                                                ->suffix(' hari')
                                                ->prefixIcon('heroicon-o-clock'),
                                        ]),
                                ]),

                            // Ringkasan sebelum submit
                            Forms\Components\Section::make('✅ Konfirmasi')
                                ->description('Pastikan semua data sudah benar sebelum menyimpan')
                                ->icon('heroicon-o-check-circle')
                                ->schema([
                                    Forms\Components\Placeholder::make('info')
                                        ->label('')
                                        ->content('Setelah menyimpan, dokumen SOP akan berstatus "Pending" dan menunggu proses review dari Verifikator.')
                                        ->columnSpanFull(),
                                ]),

                            // Hidden feedback field
                            Forms\Components\Hidden::make('feedback'),
                        ]),
                ])
                ->skippable()
                ->persistStepInQueryString()
                ->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Header Section dengan Status Badge
                Components\Section::make()
                    ->schema([
                        Components\Split::make([
                            Components\Group::make([
                                Components\TextEntry::make('sop_name')
                                    ->label('')
                                    ->size(Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpanFull(),
                                Components\TextEntry::make('id_sop')
                                    ->label('')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-o-identification'),
                            ]),
                            Components\Group::make([
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->size(Components\TextEntry\TextEntrySize::Large)
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'In Review' => 'info',
                                        'Approve' => 'success',
                                        'Rejected' => 'danger',
                                        'Expired' => 'gray',
                                        default => 'secondary',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Pending' => 'heroicon-o-clock',
                                        'In Review' => 'heroicon-o-magnifying-glass',
                                        'Approve' => 'heroicon-o-check-circle',
                                        'Rejected' => 'heroicon-o-x-circle',
                                        'Expired' => 'heroicon-o-exclamation-triangle',
                                        default => 'heroicon-o-question-mark-circle',
                                    }),
                            ])->grow(false),
                        ])->from('md'),
                    ])
                    ->extraAttributes(['class' => 'bg-gradient-to-r from-primary-50 to-transparent dark:from-primary-950']),

                // Grid dengan 2 kolom untuk informasi utama
                Components\Grid::make(2)
                    ->schema([
                        // Kolom Kiri - Informasi Dokumen
                        Components\Section::make('📄 Informasi Dokumen')
                            ->description('Detail dokumen SOP')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Components\TextEntry::make('sk_number')
                                    ->label('Nomor SK')
                                    ->icon('heroicon-o-hashtag')
                                    ->copyable()
                                    ->copyMessage('Nomor SK disalin!')
                                    ->copyMessageDuration(1500),
                                Components\TextEntry::make('type_sop')
                                    ->label('Tipe SOP')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'AP' => 'success',
                                        'NonAP' => 'info',
                                        default => 'gray',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'AP' => 'heroicon-o-shield-check',
                                        'NonAP' => 'heroicon-o-document',
                                        default => 'heroicon-o-document',
                                    }),
                                Components\TextEntry::make('unit.unit_name')
                                    ->label('Unit Kerja')
                                    ->icon('heroicon-o-building-office'),
                                Components\TextEntry::make('user.name')
                                    ->label('Dibuat Oleh')
                                    ->icon('heroicon-o-user'),
                            ])
                            ->columns(2)
                            ->collapsible(),

                        // Kolom Kanan - Tanggal & Waktu
                        Components\Section::make('📅 Periode Berlaku')
                            ->description('Informasi masa berlaku SOP')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Components\TextEntry::make('approval_date')
                                    ->label('Tanggal Pengesahan')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-check-badge')
                                    ->color('success'),
                                Components\TextEntry::make('start_date')
                                    ->label('Tanggal Berlaku')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-play'),
                                Components\TextEntry::make('expired')
                                    ->label('Tanggal Kadaluarsa')
                                    ->date('d F Y')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->color(fn ($record) => $record->expired < now() ? 'danger' : 'warning'),
                                Components\TextEntry::make('days_left')
                                    ->label('Sisa Masa Berlaku')
                                    ->suffix(' hari')
                                    ->icon('heroicon-o-clock')
                                    ->color(fn (int $state): string => match (true) {
                                        $state <= 30 => 'danger',
                                        $state <= 90 => 'warning',
                                        default => 'success',
                                    })
                                    ->weight(FontWeight::Bold),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),

                // Deskripsi SOP
                Components\Section::make('📝 Deskripsi')
                    ->description('Penjelasan tentang SOP ini')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('desc')
                            ->label('')
                            ->markdown()
                            ->placeholder('Tidak ada deskripsi untuk SOP ini.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(fn ($record) => empty($record->desc)),

                // Feedback (jika status Rejected)
                Components\Section::make('⚠️ Catatan Penolakan')
                    ->description('Alasan SOP ditolak oleh verifikator')
                    ->icon('heroicon-o-exclamation-circle')
                    ->schema([
                        Components\TextEntry::make('feedback')
                            ->label('')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->status === 'Rejected' && !empty($record->feedback))
                    ->extraAttributes(['class' => 'border-danger-500 bg-danger-50 dark:bg-danger-950']),

                // Metadata
                Components\Section::make('🕐 Metadata')
                    ->description('Informasi sistem')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-plus-circle'),
                        Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diubah')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-pencil')
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_sop')
                    ->label('ID SOP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('ID SOP disalin!'),
                Tables\Columns\TextColumn::make('sk_number')
                    ->label('Nomor SK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sop_name')
                    ->label('Nama SOP')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->sop_name),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_unit')
                    ->hidden()
                    // ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_sop')
                    ->label('Jenis SOP')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'AP' => 'success',
                        'NonAP' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('file_path')
                    ->searchable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('approval_date')
                    ->label('Tanggal Pengesahan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Berlaku')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired')
                    ->label('Tanggal Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expired < now() ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'In Review' => 'info',
                        'Approve' => 'success',
                        'Rejected' => 'danger',
                        'Expired' => 'gray',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-clock',
                        'In Review' => 'heroicon-o-magnifying-glass',
                        'Approve' => 'heroicon-o-check-circle',
                        'Rejected' => 'heroicon-o-x-circle',
                        'Expired' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                Tables\Columns\TextColumn::make('days_left')
                    ->label('Masa Berlaku')
                    ->suffix(' hari')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 30 => 'danger',
                        $state <= 90 => 'warning',
                        default => 'success',
                    }),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'In Review' => 'In Review',
                        'Approve' => 'Disetujui',
                        'Rejected' => 'Ditolak',
                        'Expired' => 'Kadaluarsa',
                    ]),
                Tables\Filters\SelectFilter::make('type_sop')
                    ->label('Tipe SOP')
                    ->options([
                        'AP' => 'AP',
                        'NonAP' => 'Non AP',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\Action::make('review')
                ->label('Review')
                ->requiresConfirmation()
                ->visible(fn (Sop $record) => auth()->user()->hasRole('Verifikator') && $record->status === 'Pending')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->modalHeading('Konfirmasi Review')
                ->modalDescription('Lanjutkan ke halaman review?')
                ->modalSubmitActionLabel('Ya, Lanjut')
                ->action(function (Sop $record) {
                    $record->update(['status' => 'In Review']);
                    return redirect(static::getUrl('view', ['record' => $record]));
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSops::route('/'),
            'create' => Pages\CreateSop::route('/create'),
            'view' => Pages\ViewSop::route('/{record}'),
            'edit' => Pages\EditSop::route('/{record}/edit'),
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

