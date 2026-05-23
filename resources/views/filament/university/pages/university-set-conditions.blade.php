<x-filament-panels::page>
    <div class="space-y-4">
        {{ $this->form }}

        <div class="flex justify-end" style="margin-top:2rem;">
            <x-filament::button wire:click="save" color="primary" size="lg">
                حفظ
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
