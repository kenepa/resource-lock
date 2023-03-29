<?php

namespace Kenepa\ResourceLock\Http\Livewire;

use Illuminate\Support\Facades\Gate;
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
        if (!config('resource-lock.unlocker.limited_access')) {
            $this->isAllowedToUnlock = true;
        } else if (config('resource-lock.unlocker.limited_access') && Gate::allows(config('resource-lock.unlocker.gate'))) {
            $this->isAllowedToUnlock = true;
        }
    }
    
}
