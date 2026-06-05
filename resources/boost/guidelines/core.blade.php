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

### 正确命名空间速查

| 类别 | 命名空间 |
|---|---|
| Page 基类 | `Wsmallnews\Cms\Filament\Pages\Navigation\Base` |
| Page 实现 | `Wsmallnews\Cms\Filament\Pages\Navigation\NavigationPage` |
| Widget | `Wsmallnews\Cms\Filament\Pages\Navigation\Widgets\Navigation` |
| Schema Form | `Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationForm` |
| Schema Infolist | `Wsmallnews\Cms\Filament\Pages\Navigation\Schemas\NavigationInfolist` |
| 模型 | `Wsmallnews\Cms\Models\Navigation` |
| 导航类型模型 | `Wsmallnews\Cms\Models\NavigationType` |
| 文章模型 | `Wsmallnews\Cms\Models\Post` |
| Plugin | `Wsmallnews\Cms\CmsPlugin` |
| ServiceProvider | `Wsmallnews\Cms\CmsServiceProvider` |

### 常见错误

- **模型必须 use `NodeTrait`**，否则 `mount()` 抛出 `NestedsetException`。
- **`$level` 设置为 `1` 时只能有根节点**，至少 `2` 才能选择父级。
- **`$scopeType` 必须设置**，否则无法正确过滤导航数据。
- **多租户 scope 需要模型定义 `getScopeAttributes()`**，返回的字段必须包含 `team_id`。
