<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Kalnoy\Nestedset\QueryBuilder;
use Livewire\Attributes\Locked;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;

trait Navigationable
{
    #[Locked]
    public ?int $navigationTypeId = null;

    public ?NavigationTypeModel $navigationType = null;

    /**
     * 解析导航类型：缺失时不抛错（navigationType 置空）
     */
    public function mountNavigationable()
    {
        $this->navigationType = Utils::getNavigationTypeModel()::scopeable(...$this->getScopeable())->when($this->navigationTypeId, function ($query) {
            $query->where('id', $this->navigationTypeId);
        })->first();

        $this->navigationTypeId = $this->navigationType?->id;
    }


    /**
     * 导航类型是否存在（缺失时调用方应短路，不发起导航查询）
     */
    public function hasNavigationType(): bool
    {
        return ! is_null($this->navigationType);
    }

    public function getScoped()
    {
        $scoped = [
            ...$this->getScopeable(),
            'type_id' => $this->navigationType?->id,
        ];
        has_tenancy() && $scoped['team_id'] = current_tenant()?->id;

        return $scoped;
    }

    /**
     * queryBuilder 不支持调用 Nestedset 的 scoped 方法
     *
     * 仅返回已限定 scope 的查询起点，查询条件（normal/defaultOrder 等）由调用方控制；
     * 导航类型不存在时返回 null，调用方应短路返回空集合，不要拿空 type_id 去查库。
     */
    protected function getScopedQuery(): ?QueryBuilder
    {
        if (! $this->hasNavigationType()) {
            return null;
        }

        return Utils::getNavigationModel()::scoped($this->getScoped());
    }
}
