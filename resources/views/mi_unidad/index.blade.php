<x-layouts.app>

    <x-slot name="title">
        {{ __('My Unit') }}

           <div class="flex justify-end">
            <x-button wire:navigate 
            href="{{}}"
             class="mb-4">
                <x-icon name="folder" class="mr-2" />
                {{ __('Create Unit') }}
            </x-button>
        </div>
    </x-layouts.app>

