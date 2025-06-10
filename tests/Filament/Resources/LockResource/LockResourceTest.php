<?php

declare(strict_types=1);

use Kenepa\ResourceLock\Resources\LockResource\ManageResourceLocks;

use function Pest\Livewire\livewire;

it('can render lock resource index page', function () {
    livewire(ManageResourceLocks::class)
        ->assertSuccessful();
});

it('can render the unlock all resources button', function () {
    livewire(ManageResourceLocks::class)
        ->assertSee('Unlock all resources');
});
