# Страницы

- [Основы](#basics)
- [Создание страницы](#create)
- [Заголовок](#title)
- [Компоненты](#components)
- [Хлебные крошки](#breadcrumbs)
- [Шаблон](#layout)
    - [Модификация шаблона](#modify-layout)
- [Псевдоним](#alias)
- [Рендеринг](#render)
- [Перед рендерингом](#before-render)
- [Модификатор ответа](#modify-response)
- [Жизненный цикл](#lifecycle)
    - [Активная страница](#on-load)
    - [Создание экземпляра](#on-boot)
- [Создание ссылки на страницу в ресурсе](#link-from-resource)
- [Assets](#assets)

---

<a name="basics"></a>
## Основы

*Page* является основой админ-панели `MoonShine`. Основное назначение `Page` - отображение компонентов.

Страницы с одинаковой логикой могут быть объединены в `Resource`.

<a name="create"></a>
## Создание страницы

Для создания класса страницы можно использовать консольную команду:

```php
php artisan moonshine:page
```

После ввода имени класса будет создан файл, который является основой для страницы в админ-панели.
По умолчанию он располагается в директории `app/MoonShine/Pages`.

> [!NOTE]
> Страницы при выполнении команды автоматически регистрируются в системе, но если вы создаете страницу вручную, то её необходимо самостоятельно зарегистрировать в `MoonShineServiceProvider` в методе `$core->pages()`

Также можно указать имя класса и директорию его расположения в команде.

```php
php artisan moonshine:page OrderStatistics --dir=Pages/Statistics
```

Файл `OrderStatistics` будет создан в директории `app/MoonShine/Pages/Statistics`.

<a name="title"></a>
## Заголовок

Заголовок страницы можно задать через свойство `title`, а подзаголовок — через `subtitle`:

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected string $title = 'CustomPage';

    protected string $subtitle = 'Подзаголовок';

    //...
}
```

Если для заголовка и подзаголовка требуется какая-то логика, то методы `title()` и `subtitle()` позволяют её реализовать:

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
        return $this->subtitle ?: 'Подзаголовок';
    }

    //...
}
```

<a name="components"></a>
## Компоненты

Для регистрации компонентов страницы используется метод `components()`.

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
> Для более подробной информации обратитесь к разделу [Компоненты](/docs/{{version}}/components/index).

<a name="breadcrumbs"></a>
## Хлебные крошки

За генерацию хлебных крошек отвечает метод `getBreadcrumbs()`.

```php
use MoonShine\Pages\Page;

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
## Шаблон

По умолчанию страницы используют шаблон отображения `AppLayout` или `CompactLayout`.
Подробнее про шаблоны читайте в разделе [Layout](/docs/{{version}}/appearance/layout)

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
### Модификация шаблона

При разработке административной панели с использованием `MoonShine` часто возникает потребность в гибком управлении шаблонами. Вместо создания множества отдельных шаблонов для различных ситуаций, `MoonShine` предоставляет возможность динамически модифицировать существующий шаблон. Это достигается с помощью метода `modifyLayout`.

Метод `modifyLayout` позволяет получить доступ к шаблону после создания его экземпляра и внести в него необходимые изменения. Это особенно полезно, когда вам нужно адаптировать шаблон под конкретные условия или добавить динамический контент.

Пример использования
Рассмотрим пример из пакета `moonshine-software/two-factor`, который демонстрирует, как можно использовать modifyLayout для настройки шаблона аутентификации:

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
## Псевдоним

Если необходимо изменить псевдоним страницы, это можно сделать через свойство `alias`.

```php
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected ?string $alias = null;

    //...
}
```

Также можно переопределить метод `getAlias()`.

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
## Рендеринг

Вы можете отображать страницу вне `MoonShine`, просто вернув её в контроллере:

```php
class ProfileController extends Controller
{
    public function __invoke(ProfilePage $page): ProfilePage
    {
        return $page->loaded();
    }
}
```

Или с Fortify

```php
Fortify::loginView(static fn() => app(ProfilePage::class));
```

<a name="before-render"></a>
## Перед рендерингом

Метод `prepareBeforeRender()` позволяет выполнить какие-либо действия перед отображением страницы.

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
## Модификатор ответа

По умолчанию страница рендерится через `PageController`, вызывая метод `render()`. Однако иногда возникает необходимость изменить стандартный ответ, например, выполнить редирект при определенных условиях. В таких случаях можно использовать метод `modifyResponse()`.

Метод `modifyResponse()` позволяет модифицировать ответ страницы перед его отправкой. Вот пример его использования:

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

Использование `modifyResponse()` предоставляет гибкий способ управления ответом страницы, позволяя реализовать сложную логику обработки запросов и ответов в административной панели.

<a name="lifecycle"></a>
## Жизненный цикл

`Page` имеет несколько различных методов подключения к различным частям своего жизненного цикла. Давайте пройдемся по ним:

<a name="on-load"></a>
### Активная страница

Метод `onLoad` дает возможность интегрироваться в момент когда страница загружена и в данный момент является активной

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
### Создание экземпляра

Метод `booted` дает возможность интегрироваться в момент, когда MoonShine создает экземпляр страницы в системе

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
## Создание ссылки на страницу в ресурсе

В данном примере для создания ссылки на новую страницу будем использовать [ActionButton](/docs/{{version}}/components/action-button) и метод [getPageUrl](/docs/{{version}}/model-resource/routes)

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
