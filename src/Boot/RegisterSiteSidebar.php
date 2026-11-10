<?php

namespace Wsmallnews\Cms\Boot;

use Filament\Support\Icons\Heroicon;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;
use Wsmallnews\User\Facades\SidebarMenuRegistry as SidebarMenuRegistryFacade;

/**
 * Profile sidebar of one site. A site may replace the whole list through the
 * `sidebar.entries` option (an empty array hides the sidebar entirely).
 */
final class RegisterSiteSidebar extends SiteService
{
    public function register(Site $site): void
    {
        $entries = $site->getOption('sidebar.entries', null);

        if (is_callable($entries)) {
            $entries = $entries($site);
        }

        SidebarMenuRegistryFacade::registers(
            app(CmsPlugin::class)->getId(),
            $entries ?? $this->defaultEntries($site),
            $site,
        );
    }

    /** @return array<int, callable> */
    protected function defaultEntries(Site $site): array
    {
        return [
            fn () => [
                'key' => 'profile',
                'label' => __('sn-cms::cms.sidebar.profile'),
                'url' => $site->route('profile'),
                'icon' => Heroicon::OutlinedUser,
                'active_icon' => Heroicon::User,
            ],
            fn () => [
                'key' => 'profile-views',
                'label' => __('sn-cms::cms.sidebar.profile_views'),
                'url' => $site->route('profile.views'),
                'icon' => Heroicon::OutlinedEye,
                'active_icon' => Heroicon::Eye,
            ],
            fn () => [
                'key' => 'settings-profile',
                'label' => __('sn-cms::cms.sidebar.settings_profile'),
                'url' => $site->route('settings.profile'),
                'icon' => Heroicon::OutlinedPencilSquare,
                'active_icon' => Heroicon::PencilSquare,
            ],
            fn () => [
                'key' => 'settings-password',
                'label' => __('sn-cms::cms.sidebar.settings_password'),
                'url' => $site->route('settings.password'),
                'icon' => Heroicon::OutlinedLockClosed,
                'active_icon' => Heroicon::LockClosed,
            ],
            fn () => [
                'key' => 'settings-two-factor',
                'label' => __('sn-cms::cms.sidebar.settings_two_factor'),
                'url' => fn () => $site->route('settings.two-factor'),
                'icon' => Heroicon::OutlinedKey,
                'active_icon' => Heroicon::Key,
                'hidden' => fn () => ! $site->getOption('two_factor.enabled', false),
            ],
        ];
    }
}
