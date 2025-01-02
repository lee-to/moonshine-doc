# Pages

- [Basics](#basics)
- [Creating a Page](#create)
- [Title](#title)
- [Components](#components)
- [Breadcrumbs](#breadcrumbs)
- [Layout](#layout)
    - [Modifying Layout](#modify-layout)
- [Alias](#alias)
- [Rendering](#render)
- [Before Rendering](#before-render)
- [Response Modifier](#modify-response)
- [Lifecycle](#lifecycle)
    - [Active Page](#on-load)
    - [Booting Instance](#on-boot)
- [Creating a Link to a Page from a Resource](#link-from-resource)
- [Assets](#assets)

---

<a name="basics"></a>
## Basics

*Page* is the foundation of the `MoonShine` admin panel. The main purpose of `Page` is to display components.

Pages with similar logic can be combined into a `Resource`.

<a name="create"></a>
## Creating a Page

To create a page class, you can use the console command:

```php
php artisan moonshine:page
```

After entering the class name, a file will be created that serves as the basis for the page in the admin panel. By default, it is located in the `app/MoonShine/Pages` directory.

> [!NOTE]
> Pages are automatically registered in the system when the command is executed, but if you create a page manually, it must be registered in the `MoonShineServiceProvider` in the `$core->pages()` method.

You can also specify the class name and the directory location in the command.

```php
php artisan moonshine:page OrderStatistics --dir=Pages/Statistics
```

The `OrderStatistics` file will be created in the `app/MoonShine/Pages/Statistics` directory.

<a name="title"></a>
## Title

The page title can be set through the `title` property, and the subtitle can be set through `subtitle`:

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected string $title = 'CustomPage';

    protected string $subtitle = 'Subtitle';

    //...
}
```

If some logic is required for the title and subtitle, the `title()` and `subtitle()` methods allow you to implement it:

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    // ...

    public function getTitle(): string
    {
        return $this->title ?: 'CustomPage';
    }

    public function getSubtitle(): string
    {
        return $this->subtitle ?: 'Subtitle';
    }

    //...
}
```

<a name="components"></a>
## Components

To register the components of the page, the `components()` method is used.

```php
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;

class CustomPage extends Page
{
    // ...

    protected function components(): iterable
    {
        return [
            Grid::make([
                Column::make([
                    Box::make([
                        //
                    ])
                ])->columnSpan(6),
                Column::make([
                    Box::make([
                        //
                    ])
                ])->columnSpan(6),
            ])
        ];
    }

    //...
}
```

> [!NOTE]
> For more detailed information, refer to the [Components](/docs/{{version}}/components/index) section.

<a name="breadcrumbs"></a>
## Breadcrumbs

The `getBreadcrumbs()` method is responsible for generating the breadcrumbs.

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    // ...

    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    //...
}
```

<a name="layout"></a>
## Layout

By default, pages use the `AppLayout` or `CompactLayout` display template. For more information about templates, see the [Layout](/docs/{{version}}/appearance/layout) section.

```php
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected ?string $layout = AppLayout::class;

    //...
}
```

<a name="modify-layout"></a>
### Modifying Layout

When developing an admin panel using `MoonShine`, there is often a need for flexible management of templates. Instead of creating numerous separate templates for various situations, `MoonShine` provides an opportunity to dynamically modify existing templates. This is achieved through the `modifyLayout` method.

The `modifyLayout` method allows you to access the template after creating its instance and make necessary changes. This is especially useful when you need to adapt the template to specific conditions or add dynamic content.

Example usage
Consider an example from the `moonshine-software/two-factor` package that demonstrates how to use `modifyLayout` for customizing the authentication template:

```php
use MoonShine\Contracts\UI\LayoutContract;

/**
 * @param  LoginLayout  $layout
 */
protected function modifyLayout(LayoutContract $layout): LayoutContract
{
    return $layout->title(
        __('moonshine-two-factor::ui.2fa')
    )->description(
        __('moonshine-two-factor::ui.confirm')
    );
}
```

<a name="alias"></a>
## Alias

If you need to change the page alias, this can be done through the `alias` property.

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected ?string $alias = null;

    //...
}
```

You can also override the `getAlias()` method.

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    public function getAlias(): ?string
    {
        return 'custom_page';
    }

    //...
}
```

<a name="render"></a>
## Rendering

You can display the page outside of `MoonShine` by simply returning it in a controller:

```php
class ProfileController extends Controller
{
    public function __invoke(ProfilePage $page): ProfilePage
    {
        return $page->loaded();
    }
}
```

Or with Fortify

```php
Fortify::loginView(static fn() => app(ProfilePage::class));
```

<a name="before-render"></a>
## Before Rendering

The `prepareBeforeRender()` method allows you to execute actions before displaying the page.

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if (auth()->user()->moonshine_user_role_id !== MoonshineUserRole::DEFAULT_ROLE_ID) {
            abort(403);
        }
    }
}
```

<a name="modify-response"></a>
## Response Modifier

By default, the page is rendered through the `PageController`, invoking the `render()` method. However, there are times when it's necessary to change the standard response, such as redirecting under certain conditions. In such cases, the `modifyResponse()` method can be used.

The `modifyResponse()` method allows you to modify the page response before it is sent. Here’s an example of its usage:

```php
use Symfony\Component\HttpFoundation\Response;

protected function modifyResponse(): ?Response
{
    if (request()->has('id')) {
        return redirect()->to('/admin/article-resource/index-page');
    }

    return null;
}
```

Using `modifyResponse()` provides a flexible way to manage the page response, allowing for complex request and response handling logic in the admin panel.

<a name="lifecycle"></a>
## Lifecycle

`Page` has several different methods to hook into various parts of its lifecycle. Let’s go through them:

<a name="on-load"></a>
### Active Page

The `onLoad` method allows integration at the moment when the page is loaded and is currently active.

```php
namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;

class PostPage extends Page
{
    // ...
    protected function onLoad(): void
    {
        parent::onLoad();

        //
    }
    // ...
}
```

<a name="on-boot"></a>
### Booting Instance

The `booted` method allows integration at the moment when `MoonShine` creates an instance of the page in the system.

```php
namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;

class PostPage extends Page
{
    // ...
    protected function booted(): void
    {
        parent::booted();

        //
    }
    // ...
}
```

<a name="link-from-resource"></a>
## Creating a Link to a Page from a Resource

In this example, to create a link to a new page, we'll use the [ActionButton](/docs/{{version}}/components/action-button) and the [getPageUrl](/docs/{{version}}/model-resource/routes) method.

```php
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

/**
 * @throws Throwable
 */
public function indexButtons(): ListOf
{
    return parent::indexButtons()->add(
        ActionButton::make('To custom page',
            url: fn($model) => $this->getPageUrl(
                PostPage::class, params: ['resourceItem' => $model->getKey()]
            ),
        ),
    );
}
```

<a name="assets"></a>
## Assets

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

protected function onLoad(): void
{
    parent::onLoad();
    
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```