<div x-init="resourceLockObserverInit">

    <script>
        function resourceLockObserverInit() {
            Livewire.emit('resourceLockObserver::init')
        }

        window.onbeforeunload = function () {
            Livewire.emit('resourceLockObserver::unload')
        };

        window.addEventListener('close-modal', event => {
            if (event.detail.id.endsWith('-table-action')) {
                Livewire.emit('resourceLockObserver::unload')
            }
        })
    </script>

    <x-filament::modal
            id="resourceIsLockedNotice"
            :closeButton="false"
            :disabled="true"
            :closeByClickingAway="false"
    >
        <div x-data="{ resourceLockOwner: null}"  @open-modal.window="(event) => { resourceLockOwner = event.detail.resourceLockOwner}">
            <div class="flex justify-center ">
                <x-filament::icon-button icon="heroicon-s-lock-closed" size="lg"/>
            </div>
            <p x-show="resourceLockOwner" class="text-center">
                <span  x-text="resourceLockOwner" class="font-bold"></span> {{ __('resource-lock::modal.locked_notice_user') }}
            </p>
            <p x-show="resourceLockOwner === null" class="text-center">
                {{ __('resource-lock::modal.locked_notice') }}
            </p>
        </div>

        <div x-data="{url: '/'}" @open-modal.window="(event) => { url = event.detail.returnUrl}" class="flex flex-col justify-center space-y-2">

            @if ($isAllowedToUnlock)
                <button wire:click="$emit('resourceLockObserver::unlock')" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-page-button-action">
                    {{ __('resource-lock::modal.unlock_button') }}
                </button>
            @endif

            <a class="block filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action"
               :href="url">

            <span>
                {{ __('resource-lock::modal.return_button') }}
            </span>

            </a>
        </div>
    </x-filament::modal>
</div>