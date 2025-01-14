# Layout

- [Основы](#basics)
- [Создание шаблона](#create)
- [Изменение шаблона страницы](#page)
- [Assets](#assets)
- [Favicons](#favicons)
- [Меню](#menu)
    - [Верхнее меню](#top-menu)
- [Цвета](#colors)
- [Blade](#blade)

---

<a name="basics"></a>
## Основы

`Layout` в `MoonShine` представляет собой набор компонентов, формирующих структуру страницы административной панели.
Каждый элемент страницы, включая `HTML`-теги, является компонентом `MoonShine`.
Это обеспечивает высокую степень гибкости и возможность кастомизации.

`MoonShine` предлагает два готовых шаблона:

- `AppLayout` - базовый шаблон,
- `CompactLayout` - компактный шаблон.

При установке `MoonShine` вы выбираете один из этих шаблонов по умолчанию.
Выбранный шаблон публикуется в директорию `app/MoonShine/Layouts` и указывается в конфигурационном файле `moonshine.layout`.

Вы можете:

- Модифицировать существующий шаблон,
- Создать новый шаблон,
- Применять разные шаблоны для различных страниц.

Пример возможного шаблона вашего приложения:

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\MoonShine\Layouts;

use App\MoonShine\Resources\PackageCategoryResource;
use App\MoonShine\Resources\PackageResource;
use App\MoonShine\Resources\UserResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Components\Layout\{Locales, Notifications, Profile, Search};
use MoonShine\Laravel\Layouts\CompactLayout;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\{Breadcrumbs,
    Components,
    Layout\Assets,
    Layout\Div,
    Layout\Body,
    Layout\Burger,
    Layout\Content,
    Layout\Favicon,
    Layout\Flash,
    Layout\Footer,
    Layout\Head,
    Layout\Header,
    Layout\Html,
    Layout\Layout,
    Layout\Logo,
    Layout\Menu,
    Layout\Meta,
    Layout\Sidebar,
    Layout\ThemeSwitcher,
    Layout\Wrapper,
    When}; // [tl! collapse:end]

final class MoonShineLayout extends CompactLayout
{
    // ...

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                Head::make([
                    Meta::make()->customAttributes([
                        'name' => 'csrf-token',
                        'content' => csrf_token(),
                    ]),
                    Favicon::make()->bodyColor($this->getColorManager()->get('body')),
                    Assets::make(),
                ])
                    ->bodyColor($this->getColorManager()->get('body'))
                    ->title($this->getPage()->getTitle()),
                Body::make([
                    Wrapper::make([
                        Sidebar::make([
                            Div::make([
                                Div::make([
                                    Logo::make(
                                        $this->getHomeUrl(),
                                        $this->getLogo(),
                                        $this->getLogo(small: true),
                                    )->minimized(),
                                ])->class('menu-heading-logo'),

                                Div::make([
                                    Div::make([
                                        ThemeSwitcher::make(),
                                    ])->class('menu-heading-mode'),

                                    Div::make([
                                        Burger::make(),
                                    ])->class('menu-heading-burger'),
                                ])->class('menu-heading-actions'),
                            ])->class('menu-heading'),

                            Div::make([
                                Menu::make(),
                                When::make(
                                    fn(): bool => $this->isAuthEnabled(),
                                    static fn(): array => [Profile::make(withBorder: true)],
                                ),
                            ])->customAttributes([
                                'class' => 'menu',
                                ':class' => "asideMenuOpen && '_is-opened'",
                            ]),
                        ])->collapsed(),

                        Div::make([
                            Flash::make(),
                            Header::make([
                                Breadcrumbs::make($this->getPage()->getBreadcrumbs())->prepend(
                                    $this->getHomeUrl(),
                                    icon: 'home',
                                ),
                                Search::make(),
                                When::make(
                                    fn(): bool => $this->isUseNotifications(),
                                    static fn(): array => [Notifications::make()],
                                ),
                                Locales::make(),
                            ]),

                            Content::make([
                                Components::make(
                                    $this->getPage()->getComponents(),
                                ),
                            ]),

                            Footer::make()
                                ->copyright(static fn(): string
                                    => sprintf(
                                    <<<'HTML'
                                        &copy; 2021-%d Made with ❤️ by
                                        <a href="https://cutcode.dev"
                                            class="font-semibold text-primary hover:text-secondary"
                                            target="_blank"
                                        >
                                            CutCode
                                        </a>
                                        HTML,
                                    now()->year,
                                ))
                                ->menu([
                                    'https://moonshine-laravel.com/docs' => 'Documentation',
                                ]),
                        ])->class('layout-page'),
                    ]),
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
```

Как видите, начиная от тега `HTML` всё в `MoonShine` является компонентами, что дает огромную свободу кастомизации вашей админ-панели.

Полный список компонентов ищите в разделе [Компоненты](/docs/{{version}}/components/index).

> [!NOTE]
> Как можно заметить, компонентов огромное количество, и для удобства мы объединили их в группы, чтобы вы могли удобно переопределять только те группы, которые требуются.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Laravel\Layouts\CompactLayout;

final class MoonShineLayout extends CompactLayout
{
    // ...

    protected function getFooterMenu(): array
    {
        return [
            'https://example.com' => 'Custom link',
        ];
    }

    protected function getFooterCopyright(): string
    {
        return 'MoonShine';
    }

    public function build(): Layout
    {
        return parent::build();
    }
}
```

В примере, с помощью методов `getFooterMenu` и `getFooterCopyright`, мы переопределили вывод меню в футере и copyright.

Доступные быстрые методы:

#### Переопределить компонент Head

```php
protected function getHeadComponent(): Head
```

#### Переопределить компонент Logo

```php
protected function getLogoComponent(): Logo
```

#### Переопределить компонент Sidebar

```php
protected function getSidebarComponent(): Sidebar
```

#### Переопределить компонент Header

```php
protected function getHeaderComponent(): Header
```

#### Переопределить или интегрировать компонент TopBar

```php
protected function getTopBarComponent(): Topbar
```

#### Переопределить компонент Footer

```php
protected function getFooterComponent(): Footer
```

#### Путь до логотипа

```php
protected function getLogo(bool $small = false): string
```

#### URL главной страницы

```php
protected function getHomeUrl(): string
```

> [!TIP]
> Вы также можете создать собственный шаблон со своим набором удобных методов для дальнейшего удобного взаимодействия.

<a name="create"></a>
## Создание шаблона

Чтобы создать еще один шаблон, воспользуйтесь командой:

```
php artisan moonshine:layout
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#layout).

<a name="page"></a>
## Изменение шаблона страницы

По умолчанию страницы используют шаблон отображения `AppLayout` или `CompactLayout`.
Но вы можете изменить на собственный шаблон, просто заменив значение свойства `$layout`.

Подробнее про страницы читайте в разделе [Страница](/docs/{{version}}/page/index).

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use App\MoonShine\Layouts\MyLayout;
use MoonShine\Laravel\Pages\Page;

class CustomPage extends Page
{
    protected ?string $layout = MyLayout::class;

    // ...
}
```

<a name="assets"></a>
## Assets

Каждый шаблон может иметь свой набор стилей и скриптов, определяемых через метод `assets()`:

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\AssetManager\Css;

final class MyLayout extends AppLayout
{
    // ...

    protected function assets(): array
    {
        return [
            ...parent::assets(),

            Css::make('/vendor/moonshine/assets/minimalistic.css')->defer(),
        ];
    }

    // ...
}
```

> [!NOTE]
> За более подробной информацией обратитесь в раздел [Assets](/docs/{{version}}/appearance/assets).

<a name="favicons"></a>
## Favicons

Вы можете заменить набор favicons в шаблоне через переопределение метода `getFaviconComponent()`:

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Laravel\Layouts\AppLayout;

final class MyLayout extends AppLayout
{
    // ...
    
    protected function getFaviconComponent(): Favicon
    {
        return parent::getFaviconComponent()->customAssets([
            'apple-touch' => 'favicon_path',
            '32' => 'favicon_path',
            '16' => 'favicon_path',
            'safari-pinned-tab' => 'favicon_path',
            'web-manifest' => 'favicon_path',
        ]);
    }
}
```

<a name="menu"></a>
## Меню

Для каждого шаблона можно объявить список пунктов меню через метод `menu()`, которые автоматически будут переданы в компонент `Menu`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;

final class MyLayout extends AppLayout
{
    // ...

    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuItem::make('Articles', ArticleResource::class),
        ];
    }
}
```

> [!NOTE]
> За более подробной информацией обратитесь в раздел [Меню](/docs/{{version}}/appearance/menu).

> [!TIP]
> Вы также можете не пользоваться методом `menu`, а передать список вручную в компонент `Menu`.

<a name="top-menu"></a>
### Верхнее меню

По умолчанию, `MoonShine` имеет компонент верхнего меню, который можно использовать вместо `Sidebar` или совместно с ним.
Давайте посмотрим, как заменить `Sidebar` на `TopBar` в `Layout`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Laravel\Layouts\CompactLayout;

final class MoonShineLayout extends CompactLayout
{
    // ...

    public function build(): Layout
    {
        return Layout::make([
            Html::make([
                $this->getHeadComponent(),
                Body::make([
                    Wrapper::make([
                        $this->getTopBarComponent(),
                        //$this->getSidebarComponent(),
                        Div::make([
                            Flash::make(),
                            $this->getHeaderComponent(),

                            Content::make([
                                Components::make(
                                    $this->getPage()->getComponents()
                                ),
                            ]),

                            $this->getFooterComponent(),
                        ])->class('layout-page'),
                    ]),
                ])->class('theme-minimalistic'),
            ])
                ->customAttributes([
                    'lang' => $this->getHeadLang(),
                ])
                ->withAlpineJs()
                ->withThemes(),
        ]);
    }
}
```

> [!WARNING]
> Если вы хотите оставить и Sidebar и TopBar одновременно, то обязательно соблюдайте очередность, первым должен идти TopBar.

<a name="colors"></a>
## Цвета

Каждый шаблон может иметь собственную цветовую схему, определяемую в методе `colors`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Contracts\ColorManager\ColorManagerContract;

final class MyLayout extends AppLayout
{
    // ...

    /**
     * @param  ColorManager  $colorManager
     */
    protected function colors(ColorManagerContract $colorManager): void
    {
        $colorManager
            ->primary('#1E96FC')
            ->secondary('#1D8A99')
            ->body('249, 250, 251')
            ->dark('30, 31, 67', 'DEFAULT')
            ->dark('249, 250, 251', 50)
            ->dark('243, 244, 246', 100)
            ->dark('229, 231, 235', 200)
            ->dark('209, 213, 219', 300)
            ->dark('156, 163, 175', 400)
            ->dark('107, 114, 128', 500)
            ->dark('75, 85, 99', 600)
            ->dark('55, 65, 81', 700)
            ->dark('31, 41, 55', 800)
            ->dark('17, 24, 39', 900)
            ->successBg('209, 255, 209')
            ->successText('15, 99, 15')
            ->warningBg('255, 246, 207')
            ->warningText('92, 77, 6')
            ->errorBg('255, 224, 224')
            ->errorText('81, 20, 20')
            ->infoBg('196, 224, 255')
            ->infoText('34, 65, 124');

        $colorManager
            ->body('27, 37, 59', dark: true)
            ->dark('83, 103, 132', 50, dark: true)
            ->dark('74, 90, 121', 100, dark: true)
            ->dark('65, 81, 114', 200, dark: true)
            ->dark('53, 69, 103', 300, dark: true)
            ->dark('48, 61, 93', 400, dark: true)
            ->dark('41, 53, 82', 500, dark: true)
            ->dark('40, 51, 78', 600, dark: true)
            ->dark('39, 45, 69', 700, dark: true)
            ->dark('27, 37, 59', 800, dark: true)
            ->dark('15, 23, 42', 900, dark: true)
            ->successBg('17, 157, 17', dark: true)
            ->successText('178, 255, 178', dark: true)
            ->warningBg('225, 169, 0', dark: true)
            ->warningText('255, 255, 199', dark: true)
            ->errorBg('190, 10, 10', dark: true)
            ->errorText('255, 197, 197', dark: true)
            ->infoBg('38, 93, 205', dark: true)
            ->infoText('179, 220, 255', dark: true);
    }
}
```

> [!NOTE]
> За более подробной информацией обратитесь в раздел [Цветовая схема](/docs/{{version}}/appearance/colors).

<a name="blade"></a>
## Blade

`MoonShine` позволяет создавать шаблоны напрямую через `Blade`.

Пример базового шаблона:

```blade
<x-moonshine::layout>
    <x-moonshine::layout.html :with-alpine-js="true" :with-themes="true">
        <x-moonshine::layout.head>
            <x-moonshine::layout.meta name="csrf-token" :content="csrf_token()"/>
            <x-moonshine::layout.favicon />
            <x-moonshine::layout.assets>
                @vite([
                    'resources/css/main.css',
                    'resources/js/app.js',
                ], 'vendor/moonshine')
            </x-moonshine::layout.assets>
        </x-moonshine::layout.head>

        <x-moonshine::layout.body>
            <x-moonshine::layout.wrapper>
                <x-moonshine::layout.sidebar :collapsed="true">
                    <x-moonshine::layout.div class="menu-heading">
                        <x-moonshine::layout.div class="menu-heading-logo">
                            <x-moonshine::layout.logo href="/" logo="/logo.png" :minimized="true"/>
                        </x-moonshine::layout.div>

                        <x-moonshine::layout.div class="menu-heading-actions">
                            <x-moonshine::layout.div class="menu-heading-mode">
                                <x-moonshine::layout.theme-switcher/>
                            </x-moonshine::layout.div>
                            <x-moonshine::layout.div class="menu-heading-burger">
                                <x-moonshine::layout.burger/>
                            </x-moonshine::layout.div>
                        </x-moonshine::layout.div>

                    </x-moonshine::layout.div>

                    <x-moonshine::layout.div class="menu" ::class="asideMenuOpen && '_is-opened'">
                        <x-moonshine::layout.menu :elements="[['label' => 'Dashboard', 'url' => '/'], ['label' => 'Section', 'url' => '/section']]"/>
                    </x-moonshine::layout.div>
                </x-moonshine::layout.sidebar>

                <x-moonshine::layout.div class="layout-page">
                    <x-moonshine::layout.header>
                        <x-moonshine::breadcrumbs :items="['#' => 'Home']"/>
                        <x-moonshine::layout.search placeholder="Search" />
                        <x-moonshine::layout.locales :locales="collect()"/>
                    </x-moonshine::layout.header>

                    <x-moonshine::layout.content>
                        <article class="article">
                            Ваш контент
                        </article>
                    </x-moonshine::layout.content>
                </x-moonshine::layout.div>
            </x-moonshine::layout.wrapper>
        </x-moonshine::layout.body>
    </x-moonshine::layout.html>
</x-moonshine::layout>
```