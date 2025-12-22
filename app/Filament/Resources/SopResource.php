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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['Admin', 'Verifikator', 'Unit']);
    }

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
                                                ->helperText('Contoh: SK/001/2024 (tanpa spasi)')
                                                ->required()
                                                ->maxLength(100)
                                                ->regex('/^[^\s]+$/')
                                                ->validationMessages([
                                                    'regex' => 'Nomor SK tidak boleh mengandung spasi.',
                                                ])
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
                                                ->prefixIcon('heroicon-o-building-office-2')
                                                ->live(),
                                            Forms\Components\Select::make('type_sop')
                                                ->label('Tipe SOP')
                                                ->placeholder('Pilih tipe SOP...')
                                                ->options([
                                                    'NonAP' => '📄 Non AP',
                                                    'AP' => '🛡️ AP (Audit Prosedur)',
                                                ])
                                                ->required()
                                                ->native(false)
                                                ->prefixIcon('heroicon-o-tag')
                                                ->live(),
                                        ]),

                                    // Unit Terkait - hanya muncul jika tipe AP
                                    Forms\Components\Select::make('collabUnits')
                                        ->label('Unit Terkait (Kolaborasi)')
                                        ->placeholder('Pilih unit yang terkait...')
                                        ->relationship('collabUnits', 'unit_name')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-group')
                                        ->helperText('Pilih unit-unit lain yang terkait dengan SOP AP ini')
                                        ->visible(fn (callable $get) => $get('type_sop') === 'AP')
                                        ->options(function (callable $get) {
                                            $selectedUnitId = $get('id_unit');
                                            return \App\Models\Unit::query()
                                                ->when($selectedUnitId, fn ($query) => $query->where('id_unit', '!=', $selectedUnitId))
                                                ->pluck('unit_name', 'id_unit');
                                        }),
                                ]),

                            // Hidden fields
                            Forms\Components\Hidden::make('user_id')
                                ->default(fn () => auth()->id()),
                            Forms\Components\Hidden::make('status')
                                ->default('Aktif'),
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
                                        ->required()
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
                            // Forms\Components\Section::make('✅ Konfirmasi')
                            //     ->description('Pastikan semua data sudah benar sebelum menyimpan')
                            //     ->icon('heroicon-o-check-circle')
                            //     ->schema([
                            //         Forms\Components\Placeholder::make('info')
                            //             ->label('')
                            //             ->content('Setelah menyimpan, dokumen SOP akan berstatus "Aktif" dan dapat dilihat oleh Unit terkait.')
                            //             ->columnSpanFull(),
                            //     ]),

                            // Hidden feedback field
                            Forms\Components\Hidden::make('feedback'),
                        ]),
                ])
                ->skippable()
                ->persistStepInQueryString()
                ->columnSpanFull()
                ->submitAction(view('filament.components.wizard-submit-action')),
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
                                Components\TextEntry::make('unit.unit_name')
                                    ->label('Unit Kerja')
                                    ->icon('heroicon-o-building-office'),
                                Components\TextEntry::make('unit.directorate.dir_name')
                                    ->label('Direktorat')
                                    ->icon('heroicon-o-building-office'),
                            ]),
                            Components\Group::make([
                                Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->size(Components\TextEntry\TextEntrySize::Large)
                                    ->color(fn (string $state): string => match ($state) {
                                        'Aktif' => 'success',
                                        'Kadaluarsa' => 'danger',
                                        default => 'secondary',
                                    })
                                    ->icon(fn (string $state): string => match ($state) {
                                        'Aktif' => 'heroicon-o-check-circle',
                                        'Kadaluarsa' => 'heroicon-o-exclamation-triangle',
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
                                Components\TextEntry::make('collabUnits.unit_name')
                                    ->label('Unit Kolaborasi')
                                    ->icon('heroicon-o-user-group')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Tidak ada unit kolaborasi')
                                    ->visible(fn ($record) => $record->collabUnits->isNotEmpty()),
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
                                    ->formatStateUsing(function (int $state): string {
                                        if ($state <= 0) {
                                            return 'Sudah Kadaluarsa';
                                        }
                                        
                                        $years = floor($state / 365);
                                        $remainingDays = $state % 365;
                                        $months = floor($remainingDays / 30);
                                        $days = $remainingDays % 30;
                                        
                                        $parts = [];
                                        if ($years > 0) {
                                            $parts[] = $years . ' tahun';
                                        }
                                        if ($months > 0) {
                                            $parts[] = $months . ' bulan';
                                        }
                                        if ($days > 0 || empty($parts)) {
                                            $parts[] = $days . ' hari';
                                        }
                                        
                                        return implode(' ', $parts);
                                    })
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
                // === Kolom Utama ===
                Tables\Columns\TextColumn::make('id_sop')
                    ->label('ID SOP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('ID SOP disalin!')
                    ->copyMessageDuration(1500)
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-hashtag')
                    ->tooltip('Klik untuk menyalin ID')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sop_name')
                    ->label('Nama SOP')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap()
                    ->weight('medium')
                    ->description(fn ($record) => $record->sk_number)
                    ->tooltip(fn ($record) => "📄 {$record->sop_name}\n📋 SK: {$record->sk_number}"),

                Tables\Columns\TextColumn::make('unit.unit_name')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('gray')
                    ->toggleable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->unit?->unit_name),

                Tables\Columns\TextColumn::make('type_sop')
                    ->label('Tipe')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'AP' => 'success',
                        'NonAP' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'AP' => 'heroicon-o-shield-check',
                        'NonAP' => 'heroicon-o-document',
                        default => 'heroicon-o-document',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'AP' => 'AP',
                        'NonAP' => 'Non-AP',
                        default => $state,
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        'AP' => 'Audit Prosedur',
                        'NonAP' => 'Non Audit Prosedur',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Kadaluarsa' => 'danger',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Aktif' => 'heroicon-o-check-circle',
                        'Kadaluarsa' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Aktif' => 'Aktif',
                        'Kadaluarsa' => 'Kadaluarsa',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('days_left')
                    ->label('Masa Berlaku')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(function (int $state): string {
                        if ($state <= 0) {
                            return 'Kadaluarsa';
                        } elseif ($state <= 30) {
                            return "{$state} hari";
                        } elseif ($state <= 90) {
                            return round($state / 30) . ' bulan';
                        } else {
                            return round($state / 365, 1) . ' tahun';
                        }
                    })
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 30 => 'danger',
                        $state <= 90 => 'warning',
                        default => 'success',
                    })
                    ->icon(fn (int $state): string => match (true) {
                        $state <= 0 => 'heroicon-o-x-circle',
                        $state <= 30 => 'heroicon-o-exclamation-triangle',
                        $state <= 90 => 'heroicon-o-clock',
                        default => 'heroicon-o-check-circle',
                    })
                    ->tooltip(fn ($record) => "Berlaku: " . \Carbon\Carbon::parse($record->start_date)->format('d M Y') .
                        "\nBerakhir: " . \Carbon\Carbon::parse($record->expired)->format('d M Y')),

                // === Kolom Tanggal (Toggleable) ===
                Tables\Columns\TextColumn::make('approval_date')
                    ->label('Tgl Pengesahan')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-check-badge')
                    ->iconColor('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tgl Berlaku')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-play')
                    ->iconColor('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('expired')
                    ->label('Tgl Kadaluarsa')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color(fn ($record) => $record->expired < now() ? 'danger' : 'warning')
                    ->toggleable(isToggledHiddenByDefault: true),

                // === Kolom Pembuat (Toggleable) ===
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // === Kolom Hidden ===
                Tables\Columns\TextColumn::make('sk_number')
                    ->label('Nomor SK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Data Terhapus'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Aktif' => '✅ Aktif',
                        'Kadaluarsa' => '⚠️ Kadaluarsa',
                    ])
                    ->placeholder('Semua Status')
                    ->indicator('Status'),

                Tables\Filters\SelectFilter::make('type_sop')
                    ->label('Tipe SOP')
                    ->options([
                        'AP' => '🛡️ AP (Audit Prosedur)',
                        'NonAP' => '📄 Non AP',
                    ])
                    ->placeholder('Semua Tipe')
                    ->indicator('Tipe'),

                Tables\Filters\SelectFilter::make('id_unit')
                    ->label('Unit Kerja')
                    ->relationship('unit', 'unit_name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Unit')
                    ->indicator('Unit'),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Segera Kadaluarsa')
                    ->query(fn (Builder $query): Builder => $query->where('days_left', '<=', 30)->where('days_left', '>', 0))
                    ->toggle()
                    ->indicator('Segera Kadaluarsa'),

                Tables\Filters\Filter::make('already_expired')
                    ->label('Sudah Kadaluarsa')
                    ->query(fn (Builder $query): Builder => $query->where('days_left', '<=', 0))
                    ->toggle()
                    ->indicator('Kadaluarsa'),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->tooltip('Lihat Detail SOP'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->tooltip('Edit SOP'),
                    Tables\Actions\Action::make('nonaktifkan')
                        ->label('Non-aktifkan')
                        ->requiresConfirmation()
                        ->visible(fn (Sop $record) => auth()->user()->hasRole('Verifikator') && $record->status === 'Aktif')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->modalIcon('heroicon-o-x-circle')
                        ->modalHeading('Konfirmasi Non-aktifkan SOP')
                        ->modalDescription(fn (Sop $record) => "Anda akan menonaktifkan SOP ini:\n\n📄 {$record->sop_name}\n📋 SK: {$record->sk_number}")
                        ->modalSubmitActionLabel('Ya, Non-aktifkan')
                        ->tooltip('Non-aktifkan SOP ini')
                        ->action(function (Sop $record) {
                            $record->update([
                                'status' => 'Kadaluarsa',
                                'days_left' => 0,
                            ]);
                        }),
                    Tables\Actions\Action::make('download')
                        ->label('Download PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('gray')
                        ->url(fn (Sop $record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                        ->openUrlInNewTab()
                        ->visible(fn (Sop $record) => !empty($record->file_path))
                        ->tooltip('Download dokumen PDF'),
                ])
                ->icon('heroicon-o-ellipsis-vertical')
                ->tooltip('Aksi')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->icon('heroicon-o-arrow-path'),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Sop $record): string => static::getUrl('view', ['record' => $record]))
            ->poll('60s')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Belum Ada Dokumen SOP')
            ->emptyStateDescription('Mulai dengan membuat dokumen SOP baru.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat SOP Baru')
                    ->icon('heroicon-o-plus'),
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
            'index' => Pages\ListSops::route('/'),
            'create' => Pages\CreateSop::route('/create'),
            'view' => Pages\ViewSop::route('/{record}'),
            'edit' => Pages\EditSop::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        // Unit hanya bisa melihat SOP dengan status Aktif dan yang terkait dengan unit mereka
        if (auth()->check() && auth()->user()->hasRole('Unit')) {
            $userIdUnit = auth()->user()->id_unit;

            $query->where('status', 'Aktif');

            // Filter by user's id_unit
            if ($userIdUnit) {
                $query->where('id_unit', $userIdUnit);
            } else {
                // If user has no associated unit, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}

