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

    public function mountNavigationable()
    {
        $this->navigationType = Utils::getNavigationTypeModel()::scopeable(...$this->getScopeable())->when($this->navigationTypeId, function ($query) {
            $query->where('id', $this->navigationTypeId);
        })->firstOrFail();

        $this->navigationTypeId = $this->navigationType->id;
    }

    public function getScoped()
    {
        $scoped = [
            ...$this->getScopeable(),
            'type_id' => $this->navigationType->id,
        ];
        has_tenancy() && $scoped['team_id'] = current_tenant()?->id;

        return $scoped;
    }

    /**
     * queryBuilder 不支持调用 Nestedset 的 scoped 方法
     */
    protected function getScopedQuery(): string | QueryBuilder
    {
        return Utils::getNavigationModel()::scoped($this->getScoped());
    }
}
