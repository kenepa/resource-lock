<?php

namespace Kenepa\ResourceLock\Http\Livewire;

use Illuminate\Support\Facades\Gate;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Livewire\Component;

class ResourceLockObserver extends Component
{
    public bool $isAllowedToUnlock = false;

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
}
