<div x-init="resourceLockObserverInit" class="resource-lock-wrapper">
    <script>
        function resourceLockObserverInit() {
            Livewire.dispatch('resourceLockObserver::init')
        }

        // Listen for events triggered by closing modal with 'close' button in footer
        function trackModalContainers() {
            document.querySelectorAll('div[x-ref="modalContainer"]:not([data-modal-tracked])').forEach(container => {
                container.setAttribute('data-modal-tracked', 'true');

                ['modal-closed'].forEach(eventType => {
                    container.addEventListener(eventType, event => {
                        if (event.detail.id.endsWith('-table-action')) {
                            Livewire.dispatch('resourceLockObserver::unload')
                        }
                    });
                });
            });
        }

        function startObserving() {
            // Initial
            trackModalContainers();

            const observer = new MutationObserver(function () {
                trackModalContainers()
            })

            observer.observe(document.body, {
                childList: true,
                subtree: true,
            })
        }

        startObserving();

        window.onbeforeunload = function () {
            Livewire.dispatch('resourceLockObserver::unload')
        };

        // Listen for events triggered by closing modal with close icon/click outside, save button in footer
        window.addEventListener('close-modal', event => {

            if (event.detail.id.endsWith('-table-action')) {
                Livewire.dispatch('resourceLockObserver::unload')
            }
        });
    </script>

    <style>
        .resource-lock-wrapper .fi-modal-close-overlay, .resource-lock-wrapper .fi-modal-close-overlay + div {
            z-index: 9999;
        }
    </style>


    @if ($usesPollingToDetectPresence)
        <div wire:poll{{ $pollingKeepAlive ? '.keep-alive' : '' }}{{ $pollingVisible ? '.visible' : '' }}.{{ $presencePollingInterval }}s="sendPresenceHeartbeat"></div>
    @endif

    <x-filament::modal
        id="resourceIsLockedNotice"
        displayClasses="block"
        :closeButton="false"
        :disabled="true"
        :closeByClickingAway="false"
        :closeByEscaping="false"
    >
        <div x-data="{ resourceLockOwner: null}"  @open-modal.window="(event) => { resourceLockOwner = event.detail.resourceLockOwner}">
            <div style="display:flex; justify-content:center; margin-bottom: 0.5rem">
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
        }" @open-modal.window="(event) => { url = event.detail.returnUrl}" style="display:flex; flex-direction:column; justify-content:center;">

            @if ($isAllowedToUnlock)
                <button x-on:click="unlock()" style="margin-bottom: 0.5rem" class="fi-color fi-color-primary fi-bg-color-600 hover:fi-bg-color-500 dark:fi-bg-color-600 dark:hover:fi-bg-color-500 fi-text-color-0 hover:fi-text-color-0 dark:fi-text-color-0 dark:hover:fi-text-color-0 fi-btn fi-size-md" tabindex="-1">
                    {{ __('resource-lock::modal.unlock_button') }}
                </button>
            @endif

            <a
               class="fi-btn fi-size-md"
               :href="url" tabindex="-1">
                <span>
                    {{ __('resource-lock::modal.return_button') }}
                </span>
            </a>

        </div>
    </x-filament::modal>
</div>
