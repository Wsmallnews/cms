<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
     * 解析导航类型：缺失时不抛错（navigationType 置空），取树请用 getNavigationTree()。
     */
    public function mountNavigationable()
    {
        $this->navigationType = Utils::getNavigationTypeModel()::scopeable(...$this->getScopeable())->when($this->navigationTypeId, function ($query) {
            $query->where('id', $this->navigationTypeId);
        })->first();

        $this->navigationTypeId = $this->navigationType?->id;
    }

    /**
     * 当前 scope 的正常状态导航树（normal + 树序全量）。
     * 导航类型不存在时不发起查询，直接返回空集合。
     *
     * @return Collection<int, mixed>
     */
    public function getNavigationTree(): Collection
    {
        if (! $this->navigationType) {
            return collect([]);
        }

        return $this->getScopedQuery()->normal()->defaultOrder()->get()->toTree();
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
     * 导航类型不存在时返回恒空查询（where 1=0）兜底：type_id 为 null 时走 scoped()
     * 会被 Laravel 转成 whereNull，反而匹配到无类型的数据。标准取树请优先用
     * getNavigationTree()（类型缺失时连查询都不发起）。
     */
    protected function getScopedQuery(): string | Builder
    {
        if (! $this->navigationType) {
            return Utils::getNavigationModel()::query()->whereRaw('1 = 0');
        }

        return Utils::getNavigationModel()::scoped($this->getScoped());
    }
}
