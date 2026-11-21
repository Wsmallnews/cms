<?php

use Wsmallnews\Category\Filament\Pages\Category\CategoryPage as PostCategoryPage;
use Wsmallnews\Category\Filament\Resources\CategoryTypes\CategoryTypeResource;
use Wsmallnews\Cms\Enums;
use Wsmallnews\Cms\Filament\Pages\GeneralSetting as GeneralSettingPage;
use Wsmallnews\Cms\Filament\Pages\Navigation\Footer\FooterNavigationPage;
use Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage;
use Wsmallnews\Cms\Filament\Resources\Links\LinkResource;
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource;
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;
use Wsmallnews\Cms\Models;
use Wsmallnews\Comment\Enums\CommentStatus;
use Wsmallnews\Comment\Filament\Pages\Comment\CommentPage;
use Wsmallnews\Comment\Filament\Resources\Comments\CommentResource;
use Wsmallnews\Support\Enums\ContentType;
use Wsmallnews\Support\Filament\Resources\Compositions\CompositionResource;
use Wsmallnews\Support\Filament\Resources\Pages\PageResource;

return [

    /**
     * Custom models
     */
    'models' => [
        'navigation' => Models\Navigation::class,
        'navigation_type' => Models\NavigationType::class,
        'post' => Models\Post::class,
        'link' => Models\Link::class,
    ],

    /**
     * 后台默认注册的资源与页面（每个站点一份实例）。
     * 注释掉条目即全局下线；应用侧经 CmsPlugin::make()->resources()/pages()
     * 追加或按 class 覆盖（overrides 合并）。
     * key 一律按 panel + plugin + site + class 派生，这里禁止写 key。
     */
    'panel_register' => [
        /**
         * 条目共享默认值（合并进每个条目）。
         * navigation_group 用于单站点后台分组；多站点用 site label。
         */
        'global_default' => [
            'navigation_group' => 'sn-cms::cms.global_default.navigation_group',
        ],

        'resources' => [
            NavigationTypeResource::class,
            LinkResource::class,
            PostResource::class,
            // support 的编排/站点页资源零代码注册：module_id 由注册插件注入，默认主 scope。
            CompositionResource::class,
            PageResource::class,

            // 能力包资源：宿主站点派生 scope，注册即归属（module_id = sn-cms）
            // CategoryTypeResource::class,
            // CommentResource::class,
        ],
        'pages' => [
            // Category 提供页面类；cms 持有 cms 站点维度的实例。
            PostCategoryPage::class => [
                'navigation_parent_item' => 'sn-cms::cms.post_resource.navigation_label',
            ],
            GeneralSettingPage::class,
            NavigationPage::class,
            // Footer 导航的子 scope 由页面类 getScopeSuffix() 自述（返回 'footer'）。
            FooterNavigationPage::class,

            // comment 提供页面类；cms 持有 cms 站点维度的实例。
            // CommentPage::class,
        ],
    ],

    /**
     * 文件基础目录，会自动拼接当前年月日 (仅用于 filament 默认上传组件 (Forms\Components\FileUpload))
     */
    'file_directory' => 'sn/cms/',

    /**
     * 内容表单配置（FormComponents::contentTypeGroup）
     * types: 允许的内容类型；default_type: 默认内容类型
     */
    'contents' => [
        'post' => [
            'types' => null,
            'default_type' => ContentType::Richtext,
        ],
    ],

    /**
     * 路由相关配置
     *
     * prefix/domain/middleware/routeNamePrefix/frontend 由 CmsPlugin::site() 在面板注册时声明，
     * 这里只保留模型路由键；站点 URI 统一放在 options.route_uris。
     */
    'routes' => [
        /**
         * Default route key name for the cms models.
         */
        'route_key_name' => [
            'post' => 'slug',
        ],
    ],

    /**
     * 可自定义的 enum
     *
     * 替换类必须实现对应契约接口，并保证契约常量对应的值存在（值是数据库与查询逻辑的稳定契约）。
     * 只有"展示元数据型" enum 才登记在此（label/color/icon 随站点变化、业务行为不依赖 case 身份），
     * 业务状态机型 enum（如 PostStatus）不开放配置。
     */
    'enums' => [
        // 图文 flag，须实现 Wsmallnews\Cms\Contracts\PostFlagContract
        'post_flag' => Enums\PostFlag::class,
    ],

    /**
     * 站点行为默认值。站点通过 CmsPlugin::site()->options() 显式覆盖；
     * 未覆盖的键由 SiteContext::getConfig('sn-cms', ...) 从这里兜底。
     */
    'options' => [
        /**
         * 认证配置（站点可通过 options.auth.* 覆盖）
         */
        'auth' => [
            // 当前站点使用的 guard
            'guard' => 'web',

            // 认证主体类型：member（wsmallnews/member）或 user（wsmallnews/user）
            'user_type' => 'member',
        ],

        /**
         * 各站共用的默认 URI；站点可在 site()->options(['route_uris' => [...]]) 覆盖单条。
         */
        'route_uris' => [
            'index' => '/',
            'posts' => 'posts',
            'posts-show' => 'posts/{slug}',
            'pages-show' => 'pages/{slug}',

            // 全局搜索结果页（search.display = 'page' 时搜索框回车跳转目标）
            'search' => 'search',

            'login' => 'login',
            'register' => 'register',
            'profile' => 'profile',
            'profile-views' => 'profile/views',
            'forgot-password' => 'forgot-password',
            'reset-password' => 'reset-password/{token}',
            'verify-email' => 'verify-email',
            'verify-email-verification' => 'verify-email/{id}/{hash}',
            'password-confirm' => 'password-confirm',

            // 用户设置
            'settings' => [
                'profile' => 'settings/profile',
                'password' => 'settings/password',
                'two-factor' => 'settings/two-factor',
            ],
        ],

        /**
         * RSS 订阅（cms 的 posts 内容流）
         *
         * 站点端点由 CmsFrontendRoutes 按 site 注册（/{prefix}/feed）。
         * enabled 控制是否注册流与模块端点路由；limit 为本流输出条数上限。
         */
        'feed' => [
            'enabled' => true,
            'limit' => 50,
        ],

        /**
         * 2FA 配置
         */
        'two_factor' => [
            /**
             * 是否启用双因素认证
             */
            'enabled' => true,

            /**
             * 在启用双因素认证时，必须确认一次，否则启动失败
             * two_factor_confirmed_at: 记录启用确认时间，如果为null, two_factor_secret， two_factor_recovery_codes 会被清空，用户双因素启用失败
             */
            'confirm' => true,

            /**
             * 验证窗口，单位：分钟
             */
            'window' => 1,
        ],

        /**
         * 是否支持评论
         */
        'comments' => [
            /**
             * post comment
             */
            'post' => [
                /**
                 * 是否启用评论
                 */
                'enable' => true,
                /**
                 * 是否启用添加评论
                 */
                'can_add_comment' => true,
                /**
                 * 评论内容类型
                 */
                'content_type' => ContentType::Textarea,
                /**
                 * 默认评论状态
                 */
                'comment_status' => CommentStatus::Normal,
            ],
        ],

        /**
         * 全局搜索（前端搜索框仅在本模块启用时渲染，来源也仅在启用时参与搜索）
         *
         * 本节除 enabled 外的键整节透传到搜索注册表（Search::config）：模块想覆盖哪些
         * search 配置就写哪些键（声明 null = 不覆盖），未写的键走全局兜底
         * config('sn-support.search.*')。可覆盖键清单见 support 包 config/sn-support.php
         * 的 search 节，如：engine / display / terms_operator / show_search_button /
         * limit / split_terms / case_insensitive / debounce
         * （page 由本包结果页路由闭包兜底，通常无需声明）
         */
        'search' => [
            /**
             * 是否启用本模块的全局搜索
             */
            'enabled' => true,
        ],

        /**
         * 站点 sitemap（{prefix}/sitemap.xml 端点与来源注册）
         *
         * enabled 之外的键整节透传到 sitemap 注册表（Sitemap::config）：
         * site options 或本节的键覆盖 support 兜底 config('sn-support.sitemap.*')，
         * 可覆盖键见 support 包 config/sn-support.php 的 sitemap 节（如 cache_ttl）。
         */
        'sitemap' => [
            /**
             * 是否启用本站点的 sitemap 端点
             */
            'enabled' => true,
        ],

        /**
         * 前台导航（tradition 主题导航条）
         *
         * 注意：与 contents.navigation（内容表单）、models.navigation（模型映射）是不同层级的既有键，互不冲突。
         */
        'navigation' => [
            // 整体风格：primary = 主题色面板+白字（默认）；minimal = 白底简约，默认黑字、hover/选中转主题色
            // 注意：主行背景由调用方设置——primary 文字为白色，调用处必须提供深色背景（如 sn-primary-bg），否则文字不可见
            'style' => 'primary',

            // PC 主行（lg+）子菜单展开形式：cascade = 级联（一级向下，深层向左/右弹）| accordion = 手风琴
            'desktop_submenu_style' => 'cascade',

            // PC 主行一级导航的展开触发：hover | click（cascade 深层子菜单跟随同一触发；
            // accordion 仅对一级生效，面板内部的手风琴层级固定 click）
            'desktop_submenu_trigger' => 'hover',

            // PC 一级导航 hover/选中形态：flush = 通栏着色（默认）| rounded = 圆角胶囊（上下留呼吸边）
            'desktop_item_style' => 'flush',

            // 仅「hover 级联」生效：父项是否可点击（直达第一个可用叶子）
            'parent_clickable' => true,

            // "更多"下拉（溢出折叠）里的展开形式：accordion（默认，窄面板更稳）| cascade
            'more_submenu_style' => 'accordion',

            // "更多"下拉里 cascade 形式的触发（accordion 时此值忽略）
            'more_submenu_trigger' => 'click',

            // "更多"按钮仅图标（⋯），不显示文字
            'more_icon_only' => true,

            // 同级导航（brothers）：当前页面匹配到二级及以下导航时，展示其所在二级分组的兄弟节点
            // 是否显示（false 时骨架不注入；匹配不到二级分组时组件自身也不渲染）
            'brothers_enabled' => true,
            // 展示形态：top = 页面内容上方一排按钮（多级以 hover 下拉展开）| sidebar = 左侧手风琴卡片（左侧分栏形态落地前，非 top 时骨架不注入）
            'brothers_layout' => 'sidebar',
            // PC 按钮的下拉展开形式（仅 brothers_layout = top 时生效，sidebar 恒为手风琴）：
            // cascade = 级联（深层向右弹）| accordion = 手风琴（下拉面板内点击展开层级）
            'brothers_submenu_style' => 'cascade',

            // top 形态下按钮排的水平对齐：left 居左 | center 居中 | right 居右（sidebar 形态不适用）
            'brothers_align' => 'left',
        ],

        /**
         * themes
         */
        'themes' => [
            // 是否启用暗黑模式
            'dark_mode' => true,

            // 默认主题模式
            'default_dark_mode' => 'system',

            // 强制暗黑主题
            'dark_mode_forced' => false,

            'layout' => 'sn-cms::components.layouts.app',

            'theme' => 'tradition',

            // 页面容器
            'page_container' => 'sn-cms::container.page',
        ],
    ],
];
