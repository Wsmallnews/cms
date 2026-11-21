## CMS 包（wsmallnews/cms）

`wsmallnews/cms` 是基于 `wsmallnews/filament-nestedset` 的内容管理系统插件，支持导航管理、文章管理、页面管理和多租户。命名空间根为 `Wsmallnews\Cms`，Blade 视图前缀为 `sn-cms`，配置文件为 `config/sn-cms.php`。

### 核心架构

- 依赖 `wsmallnews/filament-nestedset`（`NestedsetPage` 基类）
- **Base**（`Wsmallnews\Cms\Filament\Pages\Navigation\Base`）：继承 `Wsmallnews\FilamentNestedset\Filament\Pages\NestedsetPage` 的抽象页面类，负责配置、schema 定义、导航类型管理
- **NavigationPage**（`Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage`）：继承 Base 的具体页面类，注册到 Filament 面板
- **Navigation Widget**（`Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation`）：Filament Widget 变体

### 导航类型（NavigationType）

每个导航页面绑定一个 `NavigationType`，定义导航的层级限制和作用域：

```php
// 自动创建导航类型（当 canManage = false 时）
$navigationType = NavigationType::create([
    'name' => Str::title($scopeType),
    'level' => $level,
    'status' => NavigationTypeStatus::Normal,
    'scope_type' => $scopeType,
    'scope_id' => $scopeId,
    'team_id' => $tenantId,
]);
```

### 创建导航页面

```bash
php artisan make:filament-nestedset-page
```

生成的页面类继承 `Base`，需设置 `$model` 和 `$scopeType`。

#### 静态属性

| 属性 | 类型 | 默认值 | 说明 |
|---|---|---|---|
| `$model` | `?string` | `null` | 导航模型类名，**必须设置** |
| `$scopeType` | `?string` | `null` | 作用域类型，**必须设置** |
| `$scopeId` | `int` | `0` | 作用域 ID（0 = 全局） |
| `$level` | `?int` | `null` | 嵌套层级限制 |
| `$canManage` | `bool` | `false` | 是否显示导航类型管理表单 |
| `$navigationIcon` | `string\|BackedEnum\|null` | `Heroicon::OutlinedBars3BottomLeft` | 导航图标 |
| `$navigationSort` | `?int` | `1` | 导航排序 |

#### 可覆盖方法

```php
// 自定义 schema（create 和 edit 共用）
public function schema(array $arguments): array { return []; }

// create 和 edit 分别定义
public function createSchema(array $arguments): array { return []; }
public function editSchema(array $arguments): array { return []; }

// Infolist 附加属性展示
public function infolistSchema(): array { return []; }

// 自定义节点标签
public function getRecordLabel(Model $record): HtmlString|string { ... }

// 自定义嵌套集查询条件
public function getEloquentQuery($query) { return $query; }

// 额外的 scope 参数
public function nestedScoped(): array { return []; }
```

### 关键可覆盖方法

Base 页面自动通过 `nestedScoped()` 将 `scope_type`、`scope_id`、`type_id` 注入 nestedset 查询，不要手动重复添加这些 scope。`$navigationType` 会自动从配置的 `scopeType` / `scopeId` 解析或创建。

Base 页面覆盖了 `getRecordLabel()`（返回 `$record->name_label`）、`getHeaderActions()` / `getNestedsetActions()`（仅返回 createAction 和 fixNestedsetAction）、以及 `getEloquentQuery()`（追加 `->with(['content'])`）。

### 模型要求

模型必须 use `Kalnoy\Nestedset\NodeTrait`，并且实现 `getScopeAttributes()`：

```php
use Kalnoy\Nestedset\NodeTrait;

class Navigation extends Model
{
    use NodeTrait;

    public function getScopeAttributes(): array
    {
        return ['team_id', 'scope_type', 'scope_id', 'type_id'];
    }
}
```

`Navigation` 模型的 `getScopeAttributes()` 返回 `['scope_type', 'scope_id', 'type_id']`，多租户时追加 `'team_id'`。不要将 `type_id` 忽略，否则 scoped 查询会遗漏导航类型过滤。

### Post 资源

PostResource 提供文章的 CRUD 管理，支持 Scopeable、定时调度和评论/点赞集成：

```php
use Wsmallnews\Cms\Filament\Resources\Posts\BaseResource;

// BaseResource 已提供：
// - use ScopedQueries（applyScopeToQuery 自动过滤）
// - form() → PostForm（含 mediaImageUpload、richEditor、markdownEditor）
// - table() → PostsTable（含 modelColumn、morphColumn、ScheduledTask 相关操作）
// - 图标、slug、导航排序、翻译标签
```

可配置的具体实现：

```php
use Wsmallnews\Cms\Filament\Resources\Posts\PostResource;

// 在 PanelProvider 中注册
$panel->resources([PostResource::class]);
```

### NavigationType 资源

NavigationTypeResource 提供导航类型的 CRUD 管理：

```php
use Wsmallnews\Cms\Filament\Resources\NavigationTypes\BaseResource;

// BaseResource 已提供：
// - use ScopedQueries（applyScopeToQuery 自动过滤）
// - form() → NavigationTypeForm
// - table() → NavigationTypesTable
// - getWidgets() → Navigation Widget
```

### Tags 资源

CMS 包继承 support 包的 Tags 资源，按 `article` 类型过滤：

```php
use Wsmallnews\Cms\Filament\Resources\Tags\TagResource;

// 继承自 Wsmallnews\Support\Filament\Resources\Tags\BaseResource
// getTagType() 返回 'article'
```

### 文章模型

`Post` 继承 `SupportModel`，集成评论、点赞、浏览、媒体库和标签：

```php
use Wsmallnews\Cms\Models\Post;

// 核心特性：
// - extends SupportModel（scopeTenant、snScope）
// - implements HasSnSubject（preference 包集成）
// - use Commentable（评论系统）
// - use Preferenceable + Viewable（点赞/浏览）
// - use HasActivityLog（活动日志）
// - use InteractsWithMedia（Spatie 媒体库）
// - use HasTags（Spatie 标签）
// - use SoftDeletes
```

### Livewire 组件

CMS 包提供丰富的前端 Livewire 组件：

| 组件 | 注册名 | 说明 |
|---|---|---|
| `Livewire\Components\Navigation\Navigation` | `sn-cms-navigation` | 导航菜单 |
| `Livewire\Components\Navigation\Brothers` | `sn-cms-navigation-brothers` | 同级导航 |
| `Livewire\Components\Navigation\Content` | `sn-cms-navigation-content` | 导航内容 |
| `Livewire\Components\Navigation\Breadcrumb` | `sn-cms-navigation-breadcrumb` | 面包屑 |
| `Livewire\Components\Post\Post` | `sn-cms-post` | 文章详情 |
| `Livewire\Components\Post\Posts` | `sn-cms-posts` | 文章列表（分页） |
| `Livewire\Components\Post\IndexPosts` | `sn-cms-index-posts` | 首页文章列表 |

所有组件继承 `Wsmallnews\Cms\Livewire\Components\Base`（→ `Wsmallnews\Support\Livewire\Base`），使用 `ScopedBySite` trait。

### NavigationContext（导航分区上下文）

`Cms\Support\NavigationContext` 提供「当前请求命中的导航节点」：分区 banner、兄弟导航判据、节点路径。

- **生命周期**：容器 scoped 单例（`CmsServiceProvider` 注册），每请求一份，请求结束由框架释放；Octane / Swoole 下不会跨请求串站点或租户。
- **缓存分区**：key 含站点 + 租户 + scope，同一请求内 `SiteContext::run()` 切换上下文（测试、多站点嵌入）不会互相污染；要重解析调 `flush()`。
- **匹配规则**：scope 内 normal 且可寻址的节点；精确 path 优先（首页 `/` 只精确匹配），其次最长前缀 + 段边界（防 `/posts-x` 吃掉 `/posts`）；同一 URL 被多个节点引用取树序第一个；站外链接不参与。
- **只在初始页面请求用**：Livewire update 的 path 是 `/livewire/update`，不要在组件 update 生命周期里调用；`hasBrothers()` 与 Brothers 组件 mount 共用同一判断。
- `nodePath()` 只对 `Url` 类型判 host：Route / Page / Child 的 URL 由当前站点实时生成，恒为本站，换域名后 path 仍有效。

### 站点前台路由扩展

站点前台路由由 `Cms\Support\CmsFrontendRoutes::register($site)` 注册。宿主要追加自己的页面（例如带 `scope_suffix` 的文章分区 `aaa`），用 support 的 `SiteRoutes` 注册进站点分组，前缀 / 域名 / 中间件 / 路由名前缀自动继承：

```php
use Wsmallnews\Cms\Support\CmsFrontendRoutes;
use Wsmallnews\Support\Facades\SiteRoutes;
use Wsmallnews\Support\Features\Site\Site;

// AppServiceProvider::boot()
SiteRoutes::extend('sn-cms', CmsFrontendRoutes::SLOT_AUTH, function (Site $site): void {
    Route::get('aaa/my-posts', AaaMyPostsPage::class)->name('aaa.my-posts');
});
```

| 槽位常量 | 进入的中间件组 | 用途 |
|---|---|---|
| `SLOT_PUBLIC` | 站点组（含 `sn-resolve-site-context` 等） | 公开页面，无需登录 |
| `SLOT_GUEST` | `cms-guest:{guard}` | 仅未登录用户 |
| `SLOT_AUTH` | `cms-auth:{guard}` | 必须登录 |

- 回调**每个站点执行一次**，拿到当前 `Site`；路由名最终是 `sn-cms.{siteKey}.aaa.my-posts`；
- `SLOT_PUBLIC` 在 `pages/{slug}` 和搜索页之前执行，**不要注册 catch-all**，也不要复用内置路由名；
- 槽位词表属于 cms（shop 等模块定义自己的槽位），support 只负责按 `module + slot` 存回调。

### 分区内容组件

公共文章组件接受两个 scope 参数，由页面组件显式传入：

- `scope-suffix`：文章自身的数据分区（不传 = 站点 base）；
- `related-scope-suffix`：`关联key => suffix`，如 `['categories' => 'aaa']`；未传的 key 回落站点 base。

```blade
<livewire:sn-cms::components.post.posts
    scope-suffix="aaa"
    :related-scope-suffix="['categories' => 'aaa']" />
```

内置页面（`Livewire\Post\Posts` / `Post`）不传，全部走 base。`related-scope-suffix` 与后台条目的 `related_scope_suffix` 是同一语义，但两端各自声明：前端没有「资源实例」可继承，分区页面由宿主自己负责。

导航树（`components.navigation.navigation` / `components.footer`）用 `typeScopeable()` 决定数据空间：页头跟自身 `getScopeable()`，footer 固定 `::footer`。

### 定时调度

文章支持定时发布/下架，通过 ScheduledTask Facade 注册：

```php
use Wsmallnews\Support\Facades\ScheduledTask;

// 在 ServiceProvider 中注册
ScheduledTask::registers('sn_post', [
    'publish' => ['label' => '发布', 'handler' => PublishHandler::class],
    'unpublish' => ['label' => '下架', 'handler' => UnpublishHandler::class],
]);

// 在表单中嵌入调度器
ScheduledTask::scheduleRepeater('sn_post');
```

### Utils 工具类

`Wsmallnews\Cms\Support\Utils` — 全部为静态方法：

| 方法 | 说明 |
|---|---|
| `getConfig(string $name, $default)` | 只读配置文件，必须写完整路径（`options.feed.limit` / `contents.post.types`）；根取 `getPluginId()` |
| `getPluginId()` | 模块 id / 配置根（`config/{pluginId}.php`） |
| `getScopeableContext()` | 由当前 Site 派生 ScopeableContext 值对象 |
| `getScopeable()` | 返回 `['scope_type' => '...', 'scope_id' => 0]` |
| `getScopeType()` | 获取默认 scope_type |
| `getScopeId()` | 获取默认 scope_id |
| `getModel(string $name, bool $shouldException = true)` | 获取配置的模型类名 |
| `getPostModel()` | `getModel('post')` 快捷方式 |
| `getNavigationModel()` | `getModel('navigation')` 快捷方式 |
| `getNavigationTypeModel()` | `getModel('navigation_type')` 快捷方式 |
| `getFileDirectory(?string $type)` | 获取文件目录（自动追加日期） |

`Wsmallnews\Cms\Support\SiteUtils` — 当前站点（无站点上下文抛 `InvalidSiteException`）。**只用于没有 Livewire 组件实例的地方**（布局、Blade 容器组件、中间件、模型、注册期闭包）；Livewire 组件类及其内容视图优先 `$this->siteOption()` / `$this->themeOption()` / `$this->searchOption()` / `$this->getLayout()` / `$this->site()?->route()`。

| 方法 | 说明 |
|---|---|
| `getConfig(string $name, $default)` | 站点 options > `config("{pluginId}.options.*")` |
| `getSite()` | 当前 cms 站点（跨模块站点抛异常） |
| `getCommentConfig(string $module, ?string $key, $default)` | comments 节的模块配置 |
| `getDefaultDarkMode()` / `hasDarkMode()` / `hasDarkModeForced()` | 主题暗黑开关（布局视图用） |
| `route(string $name, $params, $absolute)` | 当前站点 URL（路由前缀 + 租户参数） |

`HasCmsConfig`（Livewire 组件内）另提供 `navigationOption()` / `themeOption()` / `searchOption()` / `commentOption()` / `getLayout()` / `getPageContainer()` / `isDesktopParentClickable()`。

注册期/命令已持有 `Site` 时，直接 `$site->route()` / `$site->getOption()`，不要依赖当前上下文。

### 正确命名空间速查

| 类别 | 命名空间 |
|---|---|
| Navigation Page 基类 | `Wsmallnews\Cms\Filament\Pages\Navigation\Base` |
| Navigation Page 实现 | `Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage` |
| Navigation Widget | `Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation` |
| Navigation Schema Form | `Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm` |
| Post Resource | `Wsmallnews\Cms\Filament\Resources\Posts\PostResource` |
| Post BaseResource | `Wsmallnews\Cms\Filament\Resources\Posts\BaseResource` |
| NavigationType Resource | `Wsmallnews\Cms\Filament\Resources\NavigationTypes\NavigationTypeResource` |
| Tags Resource | `Wsmallnews\Cms\Filament\Resources\Tags\TagResource` |
| GeneralSetting Page | `Wsmallnews\Cms\Filament\Pages\GeneralSetting` |
| 导航模型 | `Wsmallnews\Cms\Models\Navigation` |
| 导航类型模型 | `Wsmallnews\Cms\Models\NavigationType` |
| 文章模型 | `Wsmallnews\Cms\Models\Post` |
| CmsPlugin | `Wsmallnews\Cms\CmsPlugin` |
| Utils | `Wsmallnews\Cms\Support\Utils` |
| ServiceProvider | `Wsmallnews\Cms\CmsServiceProvider` |

### 常见错误

- **模型必须 use `NodeTrait`**，否则 `mount()` 抛出 `NestedsetException`。
- **`$level` 设置为 `1` 时只能有根节点**，至少 `2` 才能选择父级。
- **`$scopeType` 必须设置**，否则无法正确过滤导航数据。
- **多租户 scope 需要模型定义 `getScopeAttributes()`**，返回的字段必须包含 `team_id`。
- **`CanPagination` 已包含 `WithPagination`**，不要在 Livewire 组件中重复 `use WithPagination`。
- **counter 字段使用 JSON 格式**，模型中需配合 support 包的 `CounterCast` 使用。
- **`Utils` 所有方法都是静态的**；文件配置用 `Utils::getConfig()`。请求期站点配置/路由：Livewire 组件类与内容视图用 `$this->siteOption()` / `$this->themeOption()` / `$this->getLayout()` / `$this->site()?->route()`；布局、Blade 容器组件（`<x-dynamic-component>`）、中间件、模型等无组件实例处才用 `SiteUtils::getConfig()` / `SiteUtils::route()`（见 `Cms\Support\SiteUtils`）。已持有 Site 时直接 `$site->getOption()` / `$site->route()`。`Utils::route()` 已删除，删掉 firstSite 兜底后无站点上下文会直接抛异常。
