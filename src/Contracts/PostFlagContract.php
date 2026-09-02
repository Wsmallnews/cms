<?php

namespace Wsmallnews\Cms\Contracts;

/**
 * 图文 flag 契约
 *
 * 自定义 flag enum 必须实现本接口，并保证契约常量对应的值存在（值是数据库与查询逻辑的稳定契约）。
 * 推荐实现 Filament 的 HasLabel / HasColor / HasIcon 接口，直接用于表单、表格与前端徽章渲染。
 */
interface PostFlagContract
{
    /** 热门 */
    public const HOT = 'hot';

    /** 新 */
    public const NEW = 'new';

    /** 推荐 */
    public const RECOMMEND = 'recommend';

    /** 置顶（列表 SQL 排序依赖此值，必须存在） */
    public const TOP = 'top';
}
