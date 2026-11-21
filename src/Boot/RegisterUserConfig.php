<?php

namespace Wsmallnews\Cms\Boot;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Wsmallnews\Cms\CmsPlugin;
use Wsmallnews\Support\Features\Site\Site;
use Wsmallnews\Support\Features\Site\SiteService;
use Wsmallnews\User\Facades\UserConfig as UserConfigFacade;

/**
 * Auth config of one cms site (guard, two-factor, frontend URLs).
 *
 * The stored value is a closure so URLs resolve per call: route generation
 * needs the request's tenant, which does not exist at registration time.
 */
final class RegisterUserConfig extends SiteService
{
    public function register(Site $site): void
    {
        $module = app(CmsPlugin::class)->getId();

        UserConfigFacade::config($module, function () use ($site): array {
            return [
                'guard' => $site->getOption('auth.guard', 'web'),
                'two_factor' => $site->getOption('two_factor', []),
                'urls' => [
                    'index' => $site->route('index'),
                    'login' => $site->route('login'),
                    'register' => $site->route('register'),
                    'profile' => $site->route('profile'),
                    'forgot-password' => $site->route('forgot.password'),
                    'reset-password' => fn ($params) => $site->route('reset.password', $params),
                    'verify-email' => $site->route('verify.email'),
                    'verify-email-verification' => function ($parameters) use ($site) {
                        // 租户参数只在存在当前租户时补，避免单租户下塞入 null
                        if (($tenant = current_tenant()) && ! isset($parameters['tenant'])) {
                            $parameters['tenant'] = $tenant;
                        }

                        return URL::temporarySignedRoute(
                            $site->routeName('verify.email.verification'),
                            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                            $parameters
                        );
                    },
                    'password-confirm' => $site->route('password.confirm'),
                ],
            ];
        }, $site);
    }
}
