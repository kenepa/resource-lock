<?php

declare(strict_types=1);

use Kenepa\ResourceLock\ResourceLockPlugin;

function getResourceLockNavigationItem($panel, string $group = 'Settings', string $label = 'Resource Lock Manager')
{
    $navigationItems = $panel->getNavigation();

    $navigationItem = array_find($navigationItems, function ($value, $key) use ($group) {
       return str_contains($key, $group);
    });

    return $navigationItem->getItems()[$label];
}

describe('Navigation Registration', function () {
    it('registers navigation item in panel by default', function () {
        $panel = filament()->getDefaultPanel();
        $resourceLockNavigationItem = getResourceLockNavigationItem($panel);

        expect($resourceLockNavigationItem->isVisible())->toBeTrue();
    });

    it('hides navigation item when configured to be hidden', function () {
        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->registerNavigation(false));
        $navigationItems = $panel->getNavigation();

        expect($navigationItems)->toBeEmpty();
    });
});

describe('Navigation Customization', function () {
    it('uses custom navigation label when configured', function () {
        $customLabel = 'Custom Lock Manager';

        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->navigationLabel($customLabel));

        $resourceLockNavigationItem = getResourceLockNavigationItem($panel, 'Settings', $customLabel);

        expect($resourceLockNavigationItem->getLabel())->toBe($customLabel);
    });

    it('uses custom navigation icon when configured', function () {
        $customIcon = 'heroicon-o-shield-check';

        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->navigationIcon($customIcon));

        $resourceLockNavigationItem = getResourceLockNavigationItem($panel);

        expect($resourceLockNavigationItem->getIcon())->toBe($customIcon);
    });

    it('uses custom navigation group when configured', function () {
        $customGroup = 'Custom Settings';

        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->navigationGroup($customGroup));

        $resourceLockNavigationItem = getResourceLockNavigationItem($panel, $customGroup);

        expect($resourceLockNavigationItem)->not->toBeNull();
    });

    it('uses custom navigation sort when configured', function () {
        $customSort = 5;

        $panel = filament()->getDefaultPanel();
        $panel->plugin(ResourceLockPlugin::make()
            ->navigationSort($customSort));

        $resourceLockNavigationItem = getResourceLockNavigationItem($panel);

        expect($resourceLockNavigationItem->getSort())->toBe($customSort);
    });
});

describe('Polling Configuration', function () {
    it('uses default keep-alive value when not configured', function () {
        $plugin = ResourceLockPlugin::make();

        expect($plugin->shouldUsePollingKeepAlive())->toBeFalse();
    });

    it('uses custom keep-alive value when configured via plugin', function () {
        $plugin = ResourceLockPlugin::make()
            ->pollingKeepAlive(true);

        expect($plugin->shouldUsePollingKeepAlive())->toBeTrue();
    });


    it('uses default visible value when not configured', function () {
        $plugin = ResourceLockPlugin::make();

        expect($plugin->shouldUsePollingVisible())->toBeFalse();
    });

    it('uses custom visible value when configured via plugin', function () {
        $plugin = ResourceLockPlugin::make()
            ->pollingVisible(true);

        expect($plugin->shouldUsePollingVisible())->toBeTrue();
    });

});
