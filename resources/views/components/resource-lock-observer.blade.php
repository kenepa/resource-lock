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
        displayClasses="block"
        :closeButton="false"
        :disabled="true"
        :closeByClickingAway="false"
        :closeByEscaping="false"
    >
        <div x-data="{ resourceLockOwner: null}"  @open-modal.window="(event) => { resourceLockOwner = event.detail.resourceLockOwner}">
            <div class="flex justify-center ">
                <x-filament::icon-button icon="heroicon-s-lock-closed" size="lg" tabindex="-1"/>
            </div>
            <p x-show="resourceLockOwner" class="text-center pt-2">
                <span  x-text="resourceLockOwner" class="font-bold"></span> {{ __('resource-lock::modal.locked_notice_user') }}
            </p>
            <p x-show="resourceLockOwner === null" class="text-center pt-2">
                {{ __('resource-lock::modal.locked_notice') }}
            </p>
        </div>

        <div x-data="{
        url: '/',
        unlock() {
            Livewire.dispatch('resourceLockObserver::unlock')
            Livewire.dispatch('close-modal', {id: 'resourceIsLockedNotice'})
        }
        }" @open-modal.window="(event) => { url = event.detail.returnUrl}" class="flex flex-col justify-center space-y-2">

            @if ($isAllowedToUnlock)
                <button x-on:click="unlock()" style="--c-400:var(--danger-400);--c-500:var(--danger-500);--c-600:var(--danger-600);" class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70 rounded-lg fi-btn-color-danger gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus:ring-custom-500/50 dark:focus:ring-custom-400/50 fi-ac-btn-action" tabindex="-1">
                    {{ __('resource-lock::modal.unlock_button') }}
                </button>
            @endif

            <a style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
               class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 disabled:pointer-events-none disabled:opacity-70 rounded-lg fi-btn-color-primary gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus:ring-custom-500/50 dark:focus:ring-custom-400/50 fi-ac-btn-action"
               :href="url" tabindex="-1">
                <span>
                    {{ __('resource-lock::modal.return_button') }}
                </span>
            </a>

        </div>
    </x-filament::modal>
</div>
