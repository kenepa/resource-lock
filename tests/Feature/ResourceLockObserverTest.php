<?php

declare(strict_types=1);

use Kenepa\ResourceLock\Http\Livewire\ResourceLockObserver;
use Kenepa\ResourceLock\ResourceLockPlugin;
use Livewire\Livewire;

describe('ResourceLockObserver Keep-Alive Configuration', function () {
    it('uses keep-alive modifier when enabled via plugin', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(true));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');

        expect($component->get('pollingKeepAlive'))->toBeTrue();
        expect($component->get('usesPollingToDetectPresence'))->toBeTrue();
    });

    it('does not use keep-alive modifier when disabled via plugin', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(false));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');

        expect($component->get('pollingKeepAlive'))->toBeFalse();
        expect($component->get('usesPollingToDetectPresence'))->toBeTrue();
    });

    it('resets keep-alive when disabling polling', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(true));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');
        expect($component->get('pollingKeepAlive'))->toBeTrue();

        $component->call('disablePolling');
        expect($component->get('pollingKeepAlive'))->toBeFalse();
        expect($component->get('usesPollingToDetectPresence'))->toBeFalse();
    });

    it('renders correct wire:poll directive with keep-alive modifier', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(true)
            ->presencePollingInterval(10));

        $component = Livewire::test(ResourceLockObserver::class);
        $component->call('enablePolling');

        $html = $component->viewData('usesPollingToDetectPresence')
            ? $component->viewData('pollingKeepAlive')
                ? 'wire:poll.keep-alive.' . $component->viewData('presencePollingInterval') . 's'
                : 'wire:poll.' . $component->viewData('presencePollingInterval') . 's'
            : null;

        expect($html)->toBe('wire:poll.keep-alive.10s');
    });

    it('renders correct wire:poll directive without keep-alive modifier', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(false)
            ->presencePollingInterval(15));

        $component = Livewire::test(ResourceLockObserver::class);
        $component->call('enablePolling');

        $html = $component->viewData('usesPollingToDetectPresence')
            ? $component->viewData('pollingKeepAlive')
                ? 'wire:poll.keep-alive.' . $component->viewData('presencePollingInterval') . 's'
                : 'wire:poll.' . $component->viewData('presencePollingInterval') . 's'
            : null;

        expect($html)->toBe('wire:poll.15s');
    });

    it('uses visible modifier when enabled via plugin', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingVisible(true));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');

        expect($component->get('pollingVisible'))->toBeTrue();
        expect($component->get('usesPollingToDetectPresence'))->toBeTrue();
    });

    it('does not use visible modifier when disabled via plugin', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingVisible(false));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');

        expect($component->get('pollingVisible'))->toBeFalse();
        expect($component->get('usesPollingToDetectPresence'))->toBeTrue();
    });


    it('resets visible when disabling polling', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingVisible(true));

        $component = Livewire::test(ResourceLockObserver::class);

        $component->call('enablePolling');
        expect($component->get('pollingVisible'))->toBeTrue();

        $component->call('disablePolling');
        expect($component->get('pollingVisible'))->toBeFalse();
        expect($component->get('usesPollingToDetectPresence'))->toBeFalse();
    });

    it('renders correct wire:poll directive with visible modifier', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingVisible(true)
            ->presencePollingInterval(20));

        $component = Livewire::test(ResourceLockObserver::class);
        $component->call('enablePolling');

        $html = $component->viewData('usesPollingToDetectPresence') 
            ? 'wire:poll' . 
              ($component->viewData('pollingKeepAlive') ? '.keep-alive' : '') .
              ($component->viewData('pollingVisible') ? '.visible' : '') .
              '.' . $component->viewData('presencePollingInterval') . 's'
            : null;

        expect($html)->toBe('wire:poll.visible.20s');
    });

    it('renders correct wire:poll directive with both keep-alive and visible modifiers', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->usesPollingToDetectPresence(true)
            ->pollingKeepAlive(true)
            ->pollingVisible(true)
            ->presencePollingInterval(30));

        $component = Livewire::test(ResourceLockObserver::class);
        $component->call('enablePolling');

        $html = $component->viewData('usesPollingToDetectPresence') 
            ? 'wire:poll' . 
              ($component->viewData('pollingKeepAlive') ? '.keep-alive' : '') .
              ($component->viewData('pollingVisible') ? '.visible' : '') .
              '.' . $component->viewData('presencePollingInterval') . 's'
            : null;

        expect($html)->toBe('wire:poll.keep-alive.visible.30s');
    });
});
