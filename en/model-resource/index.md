# Basics

- [Basics](#basics)
- [Creating](#creating)
- [Basic Properties](#basic-properties)
- [Declaring in the System](#declaring-in-the-system)
- [Adding to the Menu](#adding-to-the-menu)
    - [Alias](#alias)
- [Current Element/Model](#current-element-model)
- [Modal Windows](#modal-windows)
- [Redirects](#redirects)
- [Active Actions](#active-actions)
- [Buttons](#buttons)
    - [Display](#display)
- [Modifiers](#modifiers)
- [Components](#components)
- [Lifecycle](#lifecycle)
    - [Active Resource](#on-load)
    - [Creating an Instance](#on-boot)
- [Assets](#assets)

---

<a name="basics"></a>
## Basics

`ModelResource` extends `CrudResource` and provides functionality for working with Eloquent models. It serves as a foundation for creating resources associated with database models.
`ModelResource` offers methods for performing CRUD operations, managing relationships, applying filters, and much more.

> [!TIP]
> You can also refer to the section on [CrudResource](/docs/{{version}}/advanced/crud-resource).
> `CrudResource` is an abstract class providing a basic interface for `CRUD` operations without binding to a storage and data type.

Under the hood, `ModelResource` extends `CrudResource` and immediately includes the capability to work with `Eloquent`.
If you delve into the details of MoonShine, you will see all the standard `Controller`, `Model`, and `blade views`.

If you were developing independently, you could create resource controllers and resource routes as follows:

```shell
php artisan make:controller Controller --resource
```

```php
use Illuminate\Support\Facades\Route;

Route::resource('resources', Controller::class);
```

But this work can be entrusted to the admin panel `MoonShine`, which will generate and declare them automatically.

`ModelResource` is the primary component for creating a section in the admin panel when working with databases.

<a name="creating"></a>
## Creating

```shell
php artisan moonshine:resource Post
```

> [!NOTE]
> For more details, refer to the [Commands](/docs/{{version}}/advanced/commands#resource).

<a name="basic-properties"></a>
## Basic Properties

Basic parameters that can be changed for a resource to customize its functionality.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    // Model
    protected string $model = Post::class;

    // Section title
    protected string $title = 'Posts';

    // Eager load
    protected array $with = ['category'];

    // Field for displaying values in relationships and breadcrumbs
    protected string $column = 'id';

    // ...
}
```

![resource_paginate](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_paginate.png)
![resource_paginate_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_paginate_dark.png)

<a name="declaring-in-the-system"></a>
## Declaring in the System

The resource is automatically registered in `MoonShineServiceProvider` when executing the command `php artisan moonshine:resource`.
However, if you create a section manually, you need to declare it in the system within `MoonShineServiceProvider`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\Providers;

use App\MoonShine\Resources\ArticleResource;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator; // [tl! collapse:end]

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(
        CoreContract $core,
        ConfiguratorContract $config,
    ): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                ArticleResource::class,
                // ...
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
```

<a name="adding-to-the-menu"></a>
## Adding to the Menu

All pages in `MoonShine` have a `Layout`, and each page can have its own.
By default, when `MoonShine` is installed, a base `MoonShineLayout` is added to the directory `app/MoonShine/Layouts`.
In `Layout`, everything related to the appearance of your pages, including navigation, is customized.

To add a section to the menu, you need to declare it via the `menu()` method in `Layout`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\PostResource;

use MoonShine\Laravel\Layouts\CompactLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem; // [tl! collapse:end]

final class MoonShineLayout extends CompactLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make(__('moonshine::ui.resource.system'), [
                MenuItem::make(
                    __('moonshine::ui.resource.admins_title'),
                    MoonShineUserResource::class
                ),
                MenuItem::make(
                    __('moonshine::ui.resource.role_title'),
                    MoonShineUserRoleResource::class
                ),
            ]),
            MenuItem::make('Posts', PostResource::class),
            // ...
        ];
    }
}
```

> [!TIP]
> You can learn about advanced `Layout` settings in the section [Layout](/docs/{{version}}/appearance/layout).

> [!TIP]
> You can learn about advanced `MenuManager` settings in the section [Menu](/docs/{{version}}/appearance/menu).

<a name="alias"></a>
### Alias

By default, the alias of the resource used in the `url` is generated based on the class name in `kebab-case`, for example:
`MoonShineUserResource` -> `moon-shine-user-resource`.

To change the `alias`, you can use the resource property `$alias` or the method `getAlias`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected ?string $alias = 'custom-alias';

    // ...
}
```

or

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    public function getAlias(): ?string
    {
        return 'custom-alias';
    }
}
```

<a name="current-element-model"></a>
## Current Element/Model

If the `resourceItem` parameter is present in the `url` of the detail or editing page, you can access the current element in the resource using the `getItem()` method.

```php
$this->getItem();
```

You can access the model through the `getModel()` method.

```php
$this->getModel();
```

<a name="modal-windows"></a>
## Modal Windows

You can add, edit, and view records directly on the listing page in a modal window.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected bool $createInModal = true;

    protected bool $editInModal = true;

    protected bool $detailInModal = true;

    // ...
}
```

<a name="redirects"></a>
## Redirects

By default, when creating and editing a record, a redirect to the form page is performed, but this behavior can be controlled.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Support\Enums\PageType;

// Through a property in the resource
protected ?PageType $redirectAfterSave = PageType::FORM;

// or through methods (redirect after deletion is also available)

public function getRedirectAfterSave(): string
{
    return '/';
}

public function getRedirectAfterDelete(): string
{
    return $this->getIndexPageUrl();
}
```

<a name="active-actions"></a>
## Active Actions

Often, it is necessary to create a resource where the ability to delete, add, or edit is excluded.
This is not about authorization, but rather a global exclusion of these sections.
This can be done easily through the `activeActions` method in the resource.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"} 
// [tl! collapse:5]
namespace App\MoonShine\Resources;

use MoonShine\Support\ListOf;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::VIEW, Action::MASS_DELETE)
            // ->only(Action::VIEW)
        ;
    }
}
```

You can also create a new list, for example:
```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Laravel\Enums\Action;
use MoonShine\Support\ListOf;

protected function activeActions(): ListOf
{
    return new ListOf(Action::class, [Action::VIEW, Action::UPDATE]);
}
```

<a name="buttons"></a>
## Buttons

By default, the index page of the resource model contains only a button for creation.
The `actions()` method allows you to add additional [buttons](/docs/{{version}}/action-button/index).

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton; // [tl! collapse:end]

class PostResource extends ModelResource
{
    // ...

    protected function topButtons(): ListOf
    {
        return parent::topButtons()->add(
            ActionButton::make('Refresh', '#')
                ->dispatchEvent(AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName()))
        );
    }
}
```

<a name="display"></a>
#### Display

You can also change the button display, showing them inline or in a dropdown menu to save space.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Resources;

use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

class PostResource extends ModelResource
{
    // ...

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()->prepend(
            ActionButton::make('Button 1', '/')
                ->showInLine(),
            ActionButton::make('Button 2', '/')
                ->showInDropdown(),
        );
    }
}
```

<a name="modifiers"></a>
## Modifiers

To modify the main component of `IndexPage`, `FormPage`, or `DetailPage` from the resource, you can override the corresponding methods `modifyListComponent()`, `modifyFormComponent()`, and `modifyDetailComponent()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ComponentContract;

public function modifyListComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyListComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\FlexibleRender;

public function modifyFormComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyFormComponent($component)->fields([
        FlexibleRender::make('Top'),
        ...parent::modifyFormComponent($component)->getFields()->toArray(),
        FlexibleRender::make('Bottom'),
    ])->submit('Go');
}
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ComponentContract;

public function modifyDetailComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyDetailComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

<a name="components"></a>
## Components

The best way to change page components is to publish the pages and interact through them.
But, if you want to quickly add components to pages, you can use the resource methods `pageComponents`, `indexPageComponents`, `formPageComponents` and `detailPageComponents`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
use MoonShine\Core\Collections\Components;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Text;

// or indexPageComponents/formPageComponents/detailPageComponents
protected function pageComponents(): array
{
    return [
        Modal::make(
            'My Modal'
            components: Components::make([
                FormBuilder::make()->fields([
                    Text::make('Title')
                ])
            ])
        )
        ->name('demo-modal')
    ];
}
```

> [!TIP]
> Components will be added to `bottomLayer`.

<a name="lifecycle"></a>
## Lifecycle

`Resource` has several different methods to connect to various parts of its lifecycle. Let's walk through them:

<a name="on-load"></a>
### Active Resource

The `onLoad` method allows integration at the moment when the resource is loaded and currently active.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected function onLoad(): void
    {
        // ...
    }
}
```

> [!TIP]
> Recipe: [Changing breadcrumbs from a resource](/docs/{{version}}/recipes/custom-breadcrumbs).

You can also attach a `trait` to the resource and within the `trait`, add a method according to the naming convention - `load{TraitName}` and use the trait to access the `onLoad` of the resource.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Resources;

use App\Traits\WithPermissions;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    use WithPermissions;

    // ...
}
```

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Support\Enums\Layer;
use MoonShine\Support\Enums\PageType;

trait WithPermissions
{
    protected function loadWithPermissions(): void
    {
        $this->getPages()
            ->findByUri(PageType::FORM->value)
            ->pushToLayer(
                layer: Layer::BOTTOM,
                component: Permissions::make(
                    label: 'Permissions',
                    resource: $this,
                )
            );
    }
}
```

<a name="on-boot"></a>
### Creating an Instance

The `onBoot` method allows integration at the moment when MoonShine is creating an instance of the resource within the system.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected function onBoot(): void
    {
        // ...
    }
}
```

You can also attach a `trait` to the resource and within the `trait`, add a method according to the naming convention - `boot{TraitName}` and use the trait to access the `onBoot` of the resource.

<a name="assets"></a>
## Assets

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

protected function onLoad(): void
{
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```