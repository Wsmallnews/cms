<?php

namespace Wsmallnews\Cms;

use Illuminate\Support\Collection;

class FlagRegistry
{
    /**
     * 存储所有范围内容类型信息的集合
     */
    protected ?Collection $flags;

    public function __construct()
    {
        $this->flags = collect();
    }

    /**
     * 注册范围类型
     *
     * @param  string  $scopeType  范围类型
     * @param  array  $flagInfo  flag 类型信息数组
     */
    public function register(string $scopeType, array $flagInfo): static
    {
        $types = $this->getTypes($scopeType);
        $type = $flagInfo['type'];

        $this->flags->put($scopeType, $types->put($type, $flagInfo));

        return $this;
    }

    /**
     * 注册多个范围类型
     *
     * @param  string  $scopeType  范围类型
     * @param  array  $typeInfos  flag 类型信息数组，每个元素为一个 flag 类型信息数组
     */
    public function registers(string $scopeType, array $flagInfos): static
    {
        foreach ($flagInfos as $flagInfo) {
            $this->register($scopeType, $flagInfo);
        }

        return $this;
    }

    /**
     * 获取所有范围
     *
     * @return Collection 所有范围
     */
    public function getFlags(): Collection
    {
        return $this->flags;
    }

    /**
     * 获取指定范围的所有内容类型
     *
     * @param  string  $scopeType  范围类型
     * @return Collection flag 类型信息集合
     */
    public function getTypes(string $scopeType): Collection
    {
        return $this->flags->get($scopeType, collect());
    }

    /**
     * 获取指定范围的指定内容类型
     *
     * @param  string  $scopeType  范围类型
     * @param  string  $type  flag 类型标识
     * @return array|null flag 类型信息数组，如果不存在则返回 null
     */
    public function getType(string $scopeType, string $type): ?array
    {
        return $this->getTypes($scopeType)->firstWhere('type', $type);
    }

    /**
     * 获取指定范围的内容类型选项，用于 options 选择
     *
     * @param  string  $scopeType  范围类型
     * @return array flag 类型选项数组，键为类型标识，值为类型标签
     */
    public function getTypesOptions(string $scopeType): array
    {
        return $this->getTypes($scopeType)->mapWithKeys(function ($flagInfo) {
            return [$flagInfo['type'] => $flagInfo['label']];
        })->toArray();
    }

    /**
     * 获取指定范围的内容类型颜色，用于显示 选项颜色
     *
     * @param  string  $scopeType  范围类型
     * @return array flag 类型选项数组，键为类型标识，值为类型颜色
     */
    public function getTypesColors(string $scopeType): array
    {
        return $this->getTypes($scopeType)->mapWithKeys(function ($flagInfo) {
            return [$flagInfo['type'] => $flagInfo['color']];
        })->toArray();
    }

    /**
     * 获取指定范围的内容类型icon，用于显示 选项图标
     *
     * @param  string  $scopeType  范围类型
     * @return array flag 类型选项数组，键为类型标识，值为类型icon
     */
    public function getTypesIcons(string $scopeType): array
    {
        return $this->getTypes($scopeType)->mapWithKeys(function ($flagInfo) {
            return [$flagInfo['type'] => $flagInfo['icon']];
        })->toArray();
    }
}
