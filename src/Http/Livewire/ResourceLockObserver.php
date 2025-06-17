<?php

namespace Kenepa\ResourceLock\Http\Livewire;

use Illuminate\Support\Facades\Gate;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Livewire\Attributes\On;
use Livewire\Component;

class ResourceLockObserver extends Component
{
    public bool $isAllowedToUnlock = false;
    public bool $usesPollingToDetectPresence = false;
    public int $presencePollingInterval = 15;

    public function render()
    {
        return view('resource-lock::components.resource-lock-observer');
    }

    public function mount()
    {
        if (! ResourceLockPlugin::get()->shouldLimitUnlockerAccess()) {
            $this->isAllowedToUnlock = true;
        } elseif (ResourceLockPlugin::get()->shouldLimitUnlockerAccess() && Gate::allows(ResourceLockPlugin::get()->getUnlockerGate())) {
            $this->isAllowedToUnlock = true;
        }
    }

    public function sendPresenceHeartbeat()
    {
        $this->dispatch('resourceLockObserver::renewLock');
    }

    #[On('enablePollingInResourceLockObserver')]
    public function enablePolling()
    {
        $this->presencePollingInterval = ResourceLockPlugin::get()->getPresencePollingInterval();
        $this->usesPollingToDetectPresence = ResourceLockPlugin::get()->shouldUsePollingToDetectPresence();
    }

    #[On('disablePollingInResourceLockObserver')]
    public function disablePolling()
    {
        $this->usesPollingToDetectPresence = false;
        $this->presencePollingInterval = 0;
    }
}
