@php
$record = $getRecord();
$changedFields = $record->changed_fields ?? [];
$oldValues = $record->old_values ?? [];
$newValues = $record->new_values ?? [];

$fieldLabels = [
    'sop_name' => 'Nama SOP',
    'sk_number' => 'Nomor SK',
    'id_unit' => 'Unit',
    'type_sop' => 'Tipe SOP',
    'status' => 'Status',
    'file_path' => 'File Dokumen',
    'approval_date' => 'Tanggal Pengesahan',
    'start_date' => 'Tanggal Berlaku',
    'expired' => 'Tanggal Kadaluarsa',
    'desc' => 'Deskripsi',
    'days_left' => 'Sisa Hari',
    'feedback' => 'Feedback',
];
@endphp

<div class="space-y-4">
    @if(empty($changedFields))
        <div class="text-gray-500 dark:text-gray-400 text-center py-4">
            <x-heroicon-o-information-circle class="w-8 h-8 mx-auto mb-2 opacity-50" />
            <p>Tidak ada detail perubahan field</p>
        </div>
    @else
        @foreach($changedFields as $field)
            @php
                $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
                $oldValue = $oldValues[$field] ?? '-';
                $newValue = $newValues[$field] ?? '-';
                
                // Format values for better readability
                if (is_null($oldValue) || $oldValue === '') $oldValue = '(kosong)';
                if (is_null($newValue) || $newValue === '') $newValue = '(kosong)';
                
                // Truncate long text
                if (is_string($oldValue) && strlen($oldValue) > 100) {
                    $oldValue = substr($oldValue, 0, 100) . '...';
                }
                if (is_string($newValue) && strlen($newValue) > 100) {
                    $newValue = substr($newValue, 0, 100) . '...';
                }
            @endphp
            
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
                {{-- Header --}}
                <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-pencil-square class="w-4 h-4 text-primary-500" />
                        <span class="font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                    </div>
                </div>
                
                {{-- Content --}}
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">
                    {{-- Before --}}
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30">
                                <x-heroicon-s-minus class="w-3 h-3 text-red-600 dark:text-red-400" />
                            </span>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400">Sebelum</span>
                        </div>
                        <div class="pl-8">
                            <div class="text-sm text-gray-700 dark:text-gray-300 bg-red-50 dark:bg-red-950/20 rounded-lg px-3 py-2 border border-red-200 dark:border-red-900">
                                {{ $oldValue }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- After --}}
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30">
                                <x-heroicon-s-plus class="w-3 h-3 text-green-600 dark:text-green-400" />
                            </span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">Sesudah</span>
                        </div>
                        <div class="pl-8">
                            <div class="text-sm text-gray-700 dark:text-gray-300 bg-green-50 dark:bg-green-950/20 rounded-lg px-3 py-2 border border-green-200 dark:border-green-900">
                                {{ $newValue }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
