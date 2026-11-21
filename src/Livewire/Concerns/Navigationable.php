<?php

namespace Wsmallnews\Cms\Livewire\Concerns;

use Kalnoy\Nestedset\QueryBuilder;
use Livewire\Attributes\Locked;
use Wsmallnews\Cms\Models\NavigationType as NavigationTypeModel;
use Wsmallnews\Cms\Support\Utils;
use Wsmallnews\Support\Livewire\Concerns\HasScopedType;

/**
 * Navigation tree helpers for a component: resolves its container type and
 * returns a query already constrained to one data space.
 *
 * Requires ScopedBySite on the host component. The data space defaults to the
 * site base scope; a host with its own partition overrides typeScopeable().
 */
trait Navigationable
{
    use HasScopedType;

    #[Locked]
    public ?int $navigationTypeId = null;

    public ?NavigationTypeModel $navigationType = null;

    public function mountNavigationable(): void
    {
        $this->navigationType = $this->resolveType();
        $this->navigationTypeId = $this->navigationType?->getKey();
    }

    /** Data space of the navigation tree; override to follow a partition. */
    protected function typeScopeable(): array
    {
        return $this->getBaseScopeable();
    }

    protected function typeModel(): string
    {
        return Utils::getNavigationTypeModel();
    }

    protected function typeIdValue(): ?int
    {
        return $this->navigationTypeId;
    }

    /** Scope + type id shared by the type and its rows. */
    public function getScoped()
    {
        return $this->scopedType();
    }

    /**
     * Query starting point of the navigation tree; null when the type is
     * missing, so callers must short-circuit instead of querying an empty tree.
     */
    protected function getScopedQuery(): ?QueryBuilder
    {
        return $this->hasType()
            ? Utils::getNavigationModel()::scoped($this->scopedType())
            : null;
    }
}
