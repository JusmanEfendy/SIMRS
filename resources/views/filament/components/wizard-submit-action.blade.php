<div class="flex items-center gap-3">
    <x-filament::button
        tag="a"
        href="{{ \App\Filament\Resources\SopResource::getUrl('index') }}"
        color="gray"
        size="md"
    >
        <x-slot name="icon">
            <x-heroicon-o-x-mark class="h-5 w-5" />
        </x-slot>
        Batal
    </x-filament::button>

    <x-filament::button
        type="submit"
        size="md"
        wire:loading.attr="disabled"
        wire:target="create"
    >
        <x-slot name="icon">
            <x-heroicon-o-check class="h-5 w-5" />
        </x-slot>
        Simpan SOP
    </x-filament::button>
</div>
