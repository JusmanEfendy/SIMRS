<x-filament-widgets::widget>
    <x-filament::section>
        @if($unit)
            <div>
                {{-- Info Unit --}}
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $unit->unit_name }}
                        </h2>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400">
                            Aktif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        {{-- ID Unit --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center">
                                <x-heroicon-o-identification class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">ID Unit</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $unit->id_unit }}</p>
                            </div>
                        </div>

                        {{-- Kepala Unit --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-warning-100 dark:bg-warning-500/20 flex items-center justify-center">
                                <x-heroicon-o-user-circle class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kepala Unit</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $unit->unit_head_name }}</p>
                            </div>
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-success-100 dark:bg-success-500/20 flex items-center justify-center">
                                <x-heroicon-o-phone class="w-5 h-5 text-success-600 dark:text-success-400" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Nomor Telepon</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $unit->unit_telp }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Direktorat Info --}}
                    @if($directorate)
                        <div class="mt-6 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                                    <x-heroicon-o-building-office-2 class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Di bawah Direktorat</p>
                                    <p class="font-semibold text-blue-900 dark:text-blue-100">{{ $directorate->dir_name }}</p>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">{{ $directorate->dir_head_name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <x-heroicon-o-exclamation-circle class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Unit Belum Ditetapkan</h3>
                <p class="text-gray-500 dark:text-gray-400">Akun Anda belum dikaitkan dengan unit kerja manapun. Silakan hubungi administrator.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
