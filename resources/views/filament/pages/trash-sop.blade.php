<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Info --}}
        <div class="p-4 bg-danger-50 dark:bg-danger-950 border border-danger-200 dark:border-danger-800 rounded-xl">
            <div class="flex items-center gap-3">
                <x-heroicon-o-trash class="w-8 h-8 text-danger-500" />
                <div>
                    <h3 class="text-lg font-semibold text-danger-700 dark:text-danger-300">Sampah SOP</h3>
                    <p class="text-sm text-danger-600 dark:text-danger-400">
                        Dokumen SOP yang dihapus akan tersimpan di sini. Anda dapat memulihkan atau menghapus permanen.
                    </p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
