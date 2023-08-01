<div x-init="resourceLockObserverInit">

    <script>
        function resourceLockObserverInit() {
            Livewire.dispatch('resourceLockObserver::init')
        }

        window.onbeforeunload = function () {
            Livewire.dispatch('resourceLockObserver::unload')
        };

        window.addEventListener('close-modal', event => {
            if (event.detail.id.endsWith('-table-action')) {
                Livewire.dispatch('resourceLockObserver::unload')
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
                <button wire:click="$dispatch('resourceLockObserver::unlock')" class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70 rounded-lg fi-btn-color-danger gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus:ring-custom-500/50 dark:focus:ring-custom-400/50 fi-ac-btn-action">
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