<?php

namespace Wsmallnews\Cms;

use Closure;
use Illuminate\Support\Collection;

class ContentRegistry
{
    protected ?Collection $types;

    public function __construct()
    {
        $this->types = collect();
    }

    /**
     * 注册内容类型
     *
     * @param  array  $typeInfo  内容类型信息
     */
    public function register(array $typeInfo): static
    {
        $type = $typeInfo['type'];
        $this->types->put($type, $typeInfo);

        return $this;
    }

    /**
     * 注册多个内容类型
     *
     * @param  array  $typeInfos  内容类型信息数组
     */
    public function registers(array $typeInfos): static
    {
        foreach ($typeInfos as $typeInfo) {
            $this->register($typeInfo);
        }

        return $this;
    }

    /**
     * 获取所有内容类型
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    /**
     * 获取指定内容类型
     *
     * @param  string  $type  内容类型
     */
    public function getType(string $type): array
    {
        return $this->types->firstWhere('type', $type);
    }

    /**
     * 获取内容类型选项 select
     */
    public function getOptions(): array
    {
        return $this->types->mapWithKeys(function ($typeInfo) {
            return [$typeInfo['type'] => $typeInfo['label']];
        })->toArray();
    }

    /**
     * 检查内容类型是否有表单
     *
     * @param  string  $type  内容类型
     * @param  array  $arguments  表单参数
     */
    public function hasForms(string $type, array $arguments = []): bool
    {
        $forms = $this->getTypeForms($type, $arguments);

        return $forms && count($forms) > 0;
    }

    /**
     * 获取内容类型表单
     *
     * @param  string  $type  内容类型
     * @param  array  $arguments  表单参数
     */
    public function getTypeForms(string $type, array $arguments = []): array
    {
        $typeInfo = $this->types->firstWhere('type', $type);

        $forms = $typeInfo['forms'] ?? [];

        return $forms instanceof Closure ? app()->call($forms, $arguments) : $forms;
    }
}
