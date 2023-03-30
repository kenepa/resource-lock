<div x-init="resourceLockObserverInit" class="bg-red-500">

    <script>
        function resourceLockObserverInit() {
            Livewire.emit('resourceLockObserver::init')
        }

        window.onbeforeunload = function () {
            Livewire.emit('resourceLockObserver::unload')
        };
    </script>

    <x-filament::modal
            id="resourceIsLockedNotice"
            :closeButton="false"
            :disabled="true"
            :closeByClickingAway="false"
    >
        <div>
            <div class="flex justify-center ">
                <x-filament::icon-button icon="heroicon-s-lock-closed" size="lg"/>
            </div>
            <p class="text-center">
                {{ __('resource-lock::modal.locked_notice') }}
            </p>
        </div>

        <div class="flex flex-col justify-center space-y-2">

            @if ($isAllowedToUnlock)
                <button wire:click="$emit('resourceLockObserver::unlock')" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-page-button-action">
                    {{ __('resource-lock::modal.unlock_button') }}
                </button>
            @endif

            <a class="block filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action"
               href="/">

            <span class="">
                {{ __('resource-lock::modal.return_button') }}
            </span>

            </a>
        </div>
    </x-filament::modal>
</div>