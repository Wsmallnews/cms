<?php

namespace Wsmallnews\Cms;

use Closure;
use Illuminate\Support\Collection;

class ContentRegistry
{
    /**
     * 存储所有范围内容类型信息的集合
     */
    protected ?Collection $scopes;

    public function __construct()
    {
        $this->scopes = collect();
    }

    /**
     * 注册范围类型
     *
     * @param  string  $scopeType  范围类型
     * @param  array  $typeInfo  内容类型信息数组
     */
    public function register(string $scopeType, array $typeInfo): static
    {
        $types = $this->getTypes($scopeType);
        $type = $typeInfo['type'];

        $this->scopes->put($scopeType, $types->put($type, $typeInfo));

        return $this;
    }

    /**
     * 注册多个范围类型
     *
     * @param  string  $scopeType  范围类型
     * @param  array  $typeInfos  内容类型信息数组，每个元素为一个内容类型信息数组
     */
    public function registers(string $scopeType, array $typeInfos): static
    {
        foreach ($typeInfos as $typeInfo) {
            $this->register($scopeType, $typeInfo);
        }

        return $this;
    }

    /**
     * 获取所有范围
     *
     * @return Collection 所有范围
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    /**
     * 获取指定范围的所有内容类型
     *
     * @param  string  $scopeType  范围类型
     * @return Collection 内容类型信息集合
     */
    public function getTypes(string $scopeType): Collection
    {
        return $this->scopes->get($scopeType, collect());
    }

    /**
     * 获取指定范围的指定内容类型
     *
     * @param  string  $scopeType  范围类型
     * @param  string  $type  内容类型标识
     * @return array|null 内容类型信息数组，如果不存在则返回 null
     */
    public function getType(string $scopeType, string $type): ?array
    {
        return $this->getTypes($scopeType)->firstWhere('type', $type);

    }

    /**
     * 获取指定范围的内容类型选项，用于下拉选择
     *
     * @param  string  $scopeType  范围类型
     * @return array 内容类型选项数组，键为类型标识，值为类型标签
     */
    public function getTypesOptions(string $scopeType): array
    {
        return $this->getTypes($scopeType)->mapWithKeys(function ($typeInfo) {
            return [$typeInfo['type'] => $typeInfo['label']];
        })->toArray();
    }

    /**
     * 检查指定范围的指定内容类型是否有表单配置
     *
     * @param  string  $scopeType  范围类型
     * @param  string  $type  内容类型标识
     * @param  array  $arguments  表单参数，当表单配置为闭包时使用
     * @return bool 是否有表单配置
     */
    public function hasTypeForms(string $scopeType, string $type, array $arguments = []): bool
    {
        $forms = $this->getTypeForms($scopeType, $type, $arguments);

        return $forms && count($forms) > 0;
    }

    /**
     * 获取指定范围的指定内容类型的表单配置
     *
     * @param  string  $scopeType  范围类型
     * @param  string  $type  内容类型标识
     * @param  array  $arguments  表单参数，当表单配置为闭包时使用
     * @return array 表单配置数组
     */
    public function getTypeForms(string $scopeType, string $type, array $arguments = []): array
    {
        $typeInfo = $this->getType($scopeType, $type);

        $forms = $typeInfo['forms'] ?? [];

        return $forms instanceof Closure ? app()->call($forms, $arguments) : $forms;
    }
}
