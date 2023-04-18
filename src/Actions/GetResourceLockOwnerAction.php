<?php

namespace Kenepa\ResourceLock\Actions;

class GetResourceLockOwnerAction
{
    /*
     * This action handles what name will be displayed when a locked resource notice appears
     */
    public function execute($userModel): string|null
    {
        return $userModel->name;
    }
}
