# Основы

- [Основы](#basics)
- [Создание](#creating-a-section)
- [Базовые свойства](#basic-section-properties)
- [Объявление в системе](#declaring-a-section-in-the-system)
- [Добавление в меню](#declaring-a-section-in-the-menu)
    - [Alias](#alias)
- [Текущий элемент/модель](#current-element-model)
- [Модальные окна](#modal-windows)
- [Редиректы](#redirects)
- [Активные действия](#active-actions)
- [Кнопки](#buttons)
    - [Отображение](#display)
- [Модификаторы](#modifiers)
- [Компоненты](#components)
- [Жизненный цикл](#lifecycle)
    - [Активный ресурс](#on-load)
    - [Создание экземпляра](#on-boot)
- [Assets](#assets)

---

<a name="basics"></a>
## Основы

`ModelResource` - расширяет `CrudResource` и предоставляет функциональность для работы с моделями Eloquent. Он обеспечивает основу для создания ресурсов, связанных с моделями базы данных. `ModelResource` предоставляет методы для выполнения CRUD-операций, управления отношениями, применения фильтров и многое другое.

> [!TIP]
> Вы также можете ознакомится с разделом [CrudResource](/docs/{{version}}/advanced/crud-resource).
> `CrudResource` это абстрактный класс предоставляющий базовый интерфейс для `CRUD` операций без привязки к хранилищу и типу данных

Под капотом `ModelResource` расширяет `CrudResource` и сразу включает возможность работы с `Eloquent`, если углубляться в детали MoonShine, то вы увидите все те же стандартные `Controller`, `Model` и `blade views`

Если бы вы разрабатывали самостоятельно, то создать ресурс контроллеры и ресурс маршруты можно следующим образом:

```php
php artisan make:controller Controller --resource
```

```php
use Illuminate\Support\Facades\Route;

Route::resource('resources', Controller::class);
```

Но эту работу можно поручить админ-панели `MoonShine`, которая будет их генерировать и объявлять самостоятельно.

`ModelResource` является основным компонентом для создания раздела в админ-панели при работе с базой данных.

<a name="creating-a-section"></a>
## Создание

```php
php artisan moonshine:resource Post
```

- измените название вашего ресурса, если требуется
- выберите тип ресурса

При создания `ModelResource` доступно несколько вариантов:

- [Default model resource](/docs/{{version}}/model-resource/fields) - с объявлением полей внутри методов ресурса (`indexFields`, `formFields`, `detailFields`)
- [Model resource with pages](/docs/{{version}}/model-resource/pages) - c публикацией страниц (`IndexPage`, `FormPage`, `DetailPage`)

В результате создастся класс `PostResource`, который будет основой нового раздела в панели.
Располагается он, по умолчанию, в директории `app/MoonShine/Resources`.
MoonShine автоматически, исходя из названия, привяжет ресурс к модели `app/Models/Post`.
Заголовок раздела так же сгенерируется автоматически и будет "Posts".

Можно сразу указать команде привязку к модели и заголовок раздела:

```php
php artisan moonshine:resource Post --model=CustomPost --title="Articles"
```

```php
php artisan moonshine:resource Post --model="App\Models\CustomPost" --title="Articles"
```

<a name="basic-section-properties"></a>
## Базовые свойства

Базовые параметры, которые можно менять у ресурса, чтобы кастомизировать его работу

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

/**
 * @extends ModelResource<Post>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class; // Модель

    protected string $title = 'Posts'; // Заголовок раздела

    protected array $with = ['category']; // Eager load

    protected string $column = 'id'; // Поле для отображения значений в связях и хлебных крошках

    //...
}
```

![resource_paginate](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_paginate.png)
![resource_paginate_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_paginate_dark.png)

<a name="declaring-a-section-in-the-system"></a>
## Объявление в системе

Ресурс автоматически регистрируется в `MoonShineServiceProvider` при вызове команды `php artisan moonshine:resource`.
Но если вы создаете раздел вручную, то вам необходимо самостоятельно его объявить в системе в `MoonShineServiceProvider`

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;

use App\MoonShine\Resources\ArticleResource;
use App\MoonShine\Resources\CategoryResource;
use App\MoonShine\Resources\CommentResource;

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
                CategoryResource::class,
                CommentResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
```

<a name="declaring-a-section-in-the-menu"></a>
## Добавление в меню

Все страницы в `MoonShine` имеют `Layout` и у каждой страницы он может быть свой, но по умолчанию при установке `MoonShine` добавляет базовый `MoonShineLayout` в директорию `app/MoonShine/Layouts`. В `Layout` кастомизируется все что отвечает за внешний вид ваших страниц и это касается также и навигации.

Чтобы добавить раздел в меню, необходимо объявить его через метод `menu()` по средствам `MenuManager`:

```php
namespace App\MoonShine\Layouts;

use MoonShine\Laravel\Layouts\CompactLayout;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;
use App\MoonShine\Resources\PostResource;

final class MoonShineLayout extends CompactLayout
{
    // ...

    protected function menu(): array
    {
        return [
            MenuGroup::make(static fn () => __('moonshine::ui.resource.system'), [
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.admins_title'),
                    MoonShineUserResource::class
                ),
                MenuItem::make(
                    static fn () => __('moonshine::ui.resource.role_title'),
                    MoonShineUserRoleResource::class
                ),
                MenuItem::make('Posts', PostResource::class),
            ]),
        ];
    }
```

> [!TIP]
> О расширенных настройках `Layout` можно узнать в разделе [Layout](/docs/{{version}}/appearance/layout).

> [!TIP]
> О расширенных настройках `MenuManager` можно узнать в разделе [Menu](/docs/{{version}}/appearance/menu).

<a name="alias"></a>
### Alias

По умолчанию alias ресурса который используется в `url` генерируется на основе наименования класс в `kebab-case`.
Пример:
`MoonShineUserResource` - `moon-shine-user-resource`

Для того чтобы изменить `alias` можно воспользоваться свойство ресурса `$alias` или метод `getAlias`

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    //...
    protected ?string $alias = 'custom-alias';
    //...
}
```

или

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
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
## Текущий элемент/модель

Если в `url` детальной страницы или страницы редактирования присутствует параметр `resourceItem`, то в ресурсе вы можете получить доступ к текущему элементу через метод `getItem()`.

```php
$this->getItem();
```

Через метод `getModel()` можно получить доступ к модели.

```php
$this->getModel();
```

<a name="modal-windows"></a>
## Модальные окна

Возможность добавлять, редактировать и просматривать записи прямо на странице со списком в модальном окне.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $detailInModal = false;

    //...
}
```

<a name="redirects"></a>
## Редиректы

По умолчанию при создании и редактировании записи осуществляется редирект на страницу с формой, но это поведение можно контролировать.

```php
use MoonShine\Support\Enums\PageType;

// Через свойство в ресурсе
protected ?PageType $redirectAfterSave = PageType::FORM;

// или через методы (также доступен редирект после удаления)

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
## Активные действия

Часто бывает, что необходимо создать ресурс, в котором будет исключена возможность удалять, или добавлять, или редактировать. И здесь речь не об авторизации, а о глобальном исключении этих разделов. Делается это крайне просто за счет метода `activeActions` в ресурсе

```php
namespace App\MoonShine\Resources;

use MoonShine\Support\ListOf;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    //...

    protected function activeActions(): ListOf
    {
        return parent::activeActions()
            ->except(Action::VIEW, Action::MASS_DELETE)
            // ->only(Action::VIEW)
        ;
    }

    //...
}
```

Также можно просто создать новый список:
```php
use MoonShine\Laravel\Enums\Action;

protected function activeActions(): ListOf
{
    return new ListOf(Action::class, [Action::VIEW, Action::UPDATE]);
}
```

<a name="buttons"></a>
## Кнопки

По умолчанию на индексной странице ресурса модели присутствует только кнопка для создания.
Метод `actions()` позволяет добавить дополнительные [кнопки](/docs/{{version}}/action-button/index).

```php
namespace App\MoonShine\Resources;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;

class PostResource extends ModelResource
{
    //...

    protected function topButtons(): ListOf
    {
        return parent::topButtons()->add(
            ActionButton::make('Refresh', '#')
                ->dispatchEvent(AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName()))
        );
    }

    //...
}
```

<a name="display"></a>
#### Отображение

Вы также можете изменить отображение кнопок, отображать их в линию или же в выпадающем меню для экономии места.

```php
namespace App\MoonShine\Resources;

use MoonShine\UI\Components\ActionButton;

class PostResource extends ModelResource
{
    //...

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()->prepend(
            ActionButton::make('Button 1', '/')
                ->showInLine(),
            ActionButton::make('Button 2', '/')
                ->showInDropdown()
        );
    }

    //...
}
```


<a name="modifiers"></a>
## Модификаторы

Для модификации основного компонента `IndexPage`, `FormPage` или `DetailPage` страницы из ресурса можно переопределить соответствующие методы `modifyListComponent()`, `modifyFormComponent()` и `modifyDetailComponent()`.

```php
use MoonShine\Contracts\UI\ComponentContract;

public function modifyListComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyListComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

```php
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
use MoonShine\Contracts\UI\ComponentContract;

public function modifyDetailComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyDetailComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

<a name="components"></a>
## Компоненты

Лучший способ изменять компоненты страниц это опубликовать страницы и взаимодействовать через них, но если вы хотите быстро добавить компоненты на страницы, то можете воспользоваться методами ресурса `pageComponents`, `indexPageComponents`, `formPageComponents`, `detailPageComponents`

```php
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Text;

// or indexPageComponents/formPageComponents/detailPageComponents
protected function pageComponents(): array
{
    return [
        Modal::make(
            'My Modal'
            components: PageComponents::make([
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
> Компоненты будут добавлены в `bottomLayer`

<a name="lifecycle"></a>
## Жизненный цикл

`Resource` имеет несколько различных методов подключения к различным частям своего жизненного цикла. Давайте пройдемся по ним:

<a name="on-load"></a>
### Активный ресурс

Метод `onLoad` дает возможность интегрироваться в момент когда ресурс загружен и в данный момент является активным

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...
    protected function onLoad(): void
    {
        //
    }
    // ...
}
```

> [!TIP]
> Рецепт: [Изменение breadcrumbs из ресурса](/docs/{{version}}/recipes/custom-breadcrumbs).

Вы также можете подключить `trait` к ресурсу и внутри `trait` добавить метод согласно конвенции наименований - `load{TraitName}` и через трейт обратится к `onLoad` ресурса

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;
use App\Traits\WithPermissions;

class PostResource extends ModelResource
{
    use WithPermissions;
}
```

```php
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
### Создание экземпляра

Метод `onBoot` дает возможность интегрироваться в момент когда MoonShine создает экземпляр ресурса в системе

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...
    protected function onBoot(): void
    {
        //
    }
    // ...
}
```

Вы также можете подключить `trait` к ресурсу и внутри `trait` добавить метод согласно конвенции наименований - `boot{TraitName}` и через трейт обратится к `onBoot` ресурса

<a name="assets"></a>
## Assets

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

protected function onLoad(): void
{
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```