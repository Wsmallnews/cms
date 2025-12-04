<?php

namespace Wsmallnews\Cms\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Wsmallnews\Support\Support\Utils as SupportUtils;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SupportUtils::isTenancyEnabled()) {
            return $next($request);
        }

        // if (! $request->route()->hasParameter('tenant')) {
        //     return $next($request);
        // }

        $tenantId = $request->route()->parameter('tenant');
        $tenant = $this->getTenant($tenantId);

        $request->attributes->set('has_tenancy', true);
        $request->attributes->set('current_tenant', $tenant);

        return $next($request);
    }

    /**
     * 通过 id 获取租户
     *
     * @param  int  $tenantId
     * @return void
     */
    protected function getTenant($tenantId)
    {
        $tenantModel = SupportUtils::getTenantModel();

        $record = app($tenantModel)
            ->resolveRouteBinding($tenantId, 'slug');

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($tenantModel, [$tenantId]);
        }

        return $record;
    }
}
