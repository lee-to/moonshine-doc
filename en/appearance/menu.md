# Menu

- [Basics](#basics)
- [Groups](#groups)
- [Divider](#divider)
- [Icon](#icon)
- [Badge](#badge)
- [Translation](#translation)
- [Open in a new tab](#target-blank)
- [Display condition](#condition)
- [Active item](#active)
- [Attributes](#attributes)
- [Change button](#change-button)
- [Custom view](#custom-view)

---

<a name="basics"></a>
## Basics

**Menu** is the foundation for navigation in the admin panel, so we have tried to create a flexible system
that allows you to fully customize the menu for different pages and users.

The navigation menu is configured in a class that extends `MoonShine\Laravel\Layouts\AppLayout` through the `menu()` method.

During the installation of the admin panel, depending on the configurations you choose, a class **App\MoonShine\Layouts\MoonShineLayout** will be created,
which already contains the `menu()` method.

In the future, if necessary, you can create other ***Layouts*** for specific pages.

To add a menu item, you need to use the class **MoonShine\Menu\MenuItem** and its static method `make()`.

```php
MenuItem::make(Closure|string $label, Closure|MenuFillerContract|string $filler, string $icon = null, Closure|bool $blank = false)
```

- `$label` - the name of the menu item,
- `$filler` - an element for generating the URL,
- `$icon` - an icon for the menu item,
- `$blank` - open in a new tab.

> [!TIP]
> You can pass [ModelResource](/docs/{{version}}/model-resource/index), [Page](/docs/{{version}}/page/index) or [CrudResource](/docs/{{version}}/advanced/crud-resource) as the second parameter.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:5]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Admins', MoonShineUserResource::class),
            MenuItem::make('Home', fn() => route('home')),
            MenuItem::make('Docs', 'https://moonshine-laravel.com/docs'),
            MenuItem::make('Laravel Docs', 'https://laravel.com/docs', blank: true)
        ];
    }
}
```

> [!TIP]
> If the menu is created for [ModelResource](/docs/{{version}}/model-resource/index) or [CrudResource](/docs/{{version}}/advanced/crud-resource), the first page declared in the `pages()` method will be used for the menu item.

<a name="groups"></a>
## Groups

Menu items can be grouped together.
For this, the class `MoonShine\MenuManager\MenuGroup` is used with the static method `make()`.

```php
MenuGroup::make(Closure|string $label, iterable $items, string|null $icon = null)
```

- `$label` - the name of the group,
- `$items` - an array of menu components,
- `$icon` - an icon for the group.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('System', [
                MenuItem::make('Admins', MoonShineUserResource::class),
                MenuItem::make('Roles', MoonShineUserRoleResource::class),
            ])
        ];
    }
}
```

You can also add items to the group using the `setItems()` method.

```php
setItems(iterable $items)
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('System')->setItems([
                MenuItem::make('Admins', MoonShineUserResource::class),
                MenuItem::make('Roles', MoonShineUserRoleResource::class),
            ])
        ];
    }
}
```

> [!TIP]
> To create a multi-level menu, groups can be nested.

<a name="divider"></a>
## Divider

Menu items can be visually separated using `MenuDivider`.

```php
/**
 * @param  (Closure(MenuElementContract $context): string)|string  $label
 */
MenuDivider::make(Closure|string $label = '')
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuDivider;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Admins', MoonShineUserResource::class),
            MenuDivider::make(),
            MenuItem::make('Roles', MoonShineUserRoleResource::class)
        ];
    }
}
```

<a name="icon"></a>
## Icon

An icon can be assigned to both a menu item and a group. This can be implemented in several ways.

### Through parameter
An icon can be set by passing the name as the third parameter in the static method `make()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Admins', MoonShineUserResource::class, 'users'),
            MenuItem::make('Roles', MoonShineUserRoleResource::class, 'hashtag')
        ];
    }
}
```

### Through method

You can use the `icon()` method.

```php
icon(string $icon, bool $custom = false, ?string $path = null)
```

- `$icon` - the name of the icon or HTML (if custom mode is used),
- `$custom` - custom mode,
- `$path` - the path to the directory where the **blade** templates of icons are stored.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('System', [
                MenuItem::make('Admins', MoonShineUserResource::class)
                    ->icon('users'),
                MenuItem::make('Roles', MoonShineUserRoleResource::class)
                    ->icon(svg('path-to-icon-pack')->toHtml(), custom: true),
            ])
                ->icon('cog', path: 'icons')
        ];
    }
}
```

### Through attribute

An icon will be displayed for the menu item if the **ModelResource**, **Page**, or **Resource** class has the `Icon` attribute set and the icon has not been overridden by other means.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
namespace MoonShine\Resources;

#[Icon('users')]
class MoonShineUserResource extends ModelResource
{
    // ...
}
```

> [!TIP]
> For more detailed information, refer to the section [Icons](/docs/{{version}}/appearance/icons).

<a name="badge"></a>
## Badge

You can also add a badge to a menu item.

### Through menu item

To add a badge to a menu item, use the `badge()` method, which takes a closure as a parameter.

```php
/**
 * @param  Closure(MenuElementContract $context): string|int|float|null  $value
 */
badge(Closure|string|int|float|null $value)
```

```php
namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\CommentResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{

    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Comments', CommentResource::class)
                ->badge(fn() => Comment::count())
        ];
    }
}
```

<a name="translation"></a>
## Translation

To translate menu items, you need to pass the translation key as the name and add the `translatable()` method.

```php
translatable(string $key = '')
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:5]
namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\CommentResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('menu.Comments', CommentResource::class)
                ->translatable()
            // or
            MenuItem::make('Comments', CommentResource::class)
                ->translatable('menu')
        ];
    }
}
```

```php
// lang/en/menu.php

return [
    'Comments' => 'Comments',
];
```

You can use Laravel's translation features for translating menu badges.

```php
namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\CommentResource;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Comments', CommentResource::class)
                ->badge(fn() => __('menu.badge.new'))
        ];
    }
}
```

<a name="target-blank"></a>
## Open in a new tab

You can specify a flag for the menu item to indicate whether to open the link in a new tab. This can be implemented in several ways.

### Through parameter

The flag can be set by passing the fourth parameter `true/false` or a closure in the static method `make()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('MoonShine Docs', 'https://moonshine-laravel.com/docs', 'heroicons.arrow-up', true),
            MenuItem::make('Laravel Docs', 'https://laravel.com/docs', blank: fn() => true),
        ];
    }
}
```

### Through method

You can use the `blank()` method.

```php
/**
 * @param  (Closure(MenuElementContract $context): bool)|bool  $blankCondition
 */
blank(Closure|bool $blankCondition = true)
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('MoonShine Docs', 'https://moonshine-laravel.com/docs', 'heroicons.arrow-up', true),
            MenuItem::make('Laravel Docs', 'https://laravel.com/docs')->blank(fn() => true),
        ];
    }
}
```

<a name="condition"></a>
## Display condition

You can display menu items based on a condition using the `canSee()` method.

```php
/**
 * @param  Closure(MenuElementContract $context): bool  $callback
 */
canSee(Closure $callback)
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\Providers;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuDivider;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('System', [
                MenuItem::make('Admins', MoonShineUserResource::class),
                MenuDivider::make()
                    ->canSee(fn() => true),
                MenuItem::make('Roles', MoonShineUserRoleResource::class)
                    ->canSee(fn() => false)
            ])
                ->canSee(static fn(): bool => request()->user('moonshine')?->id === 1)
        ];
    }
}
```

<a name="active"></a>
## Active item

A menu item becomes active if it matches the ***url***, but the `forceActive()` method allows you to forcibly make an item active.

```php
/**
 * @param  Closure(string $path, string $host, MenuElementContract $context): bool  $when
 */
whenActive(Closure $when)
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Label', '/endpoint')
                ->whenActive(fn() => request()->fullUrlIs('*admin/endpoint/*')),
        ];
    }
}
```

<a name="attributes"></a>
## Attributes

Groups and menu items, like other components, can have custom attributes assigned.

> [!TIP]
> For more detailed information, refer to the section [Component Attributes](/docs/{{version}}/components/attributes).

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('System')->setItems([
                MenuItem::make('Admins', MoonShineUserResource::class),
                MenuItem::make('Roles', MoonShineUserRoleResource::class)
                    ->customAttributes(['class' => 'group-li-custom-class'])
            ])
                ->setAttribute('data-id', '123')
                ->class('group-li-custom-class')
        ];
    }
}
```

<a name="change-button"></a>
## Change button

A menu item is an [ActionButton](/docs/{{version}}/components/action-button) and you can change its attributes using the `changeButton` method.

```php
/**
 * @param  Closure(ActionButtonContract $button): ActionButtonContract  $callback
 */
changeButton(Closure $callback)
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:5]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\ActionButton;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuItem::make('Label', '/endpoint')
                ->changeButton(static fn(ActionButton $button) => $button->class('new-item')),
        ];
    }
}
```

> [!WARNING]
> Some parameters of **ActionButton**, such as `url`, `badge`, `icon`, and others are overridden in the system.
> To change them, use the corresponding methods.

<a name="custom-view"></a>
## Custom view

If you need to change the **view** using a *fluent interface*, you can use the `customView()` method.

```php
customView(string $path)
```

- `$path` - the path to the **blade** template.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:5]
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

final class MoonShineLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make('Group', [
                MenuItem::make('Label', '/endpoint')
                    ->customView('admin.custom-menu-item'),
            ])
                ->customView('admin.custom-menu-group'),
        ];
    }
}
```
