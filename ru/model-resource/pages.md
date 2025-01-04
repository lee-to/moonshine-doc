# Страницы

- [Основы](#basics)
- [Типы страниц](#page-type)
- [Добавление полей](#fields)
- [Основные компоненты](#components)
- [Слои на странице](#layers)

---

<a name="basics"></a>
## Основы

`MoonShine` предоставляет возможность настройки `CRUD` страниц в `ModelResource`, для этого необходимо при создании ресурса через команду выбрать тип ресурса `Model resource with pages`.

Это создаст класс ресурса модели и дополнительные классы для страниц индекса, детального просмотра и формы. Классы страниц по умолчанию будут располагаться в директории `app/MoonShine/Pages`.

В созданном ресурсе модели страницы `CRUD` будут зарегистрированы в методе `pages()`.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use App\MoonShine\Pages\Post\PostIndexPage;
use App\MoonShine\Pages\Post\PostFormPage;
use App\MoonShine\Pages\Post\PostDetailPage;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function pages(): array
    {
        return [
            PostIndexPage::class
            PostFormPage::class,
            PostDetailPage::class,
        ];
    }

    //...
}
```

<a name="page-type"></a>
## Типы страниц

Для указания типа страницы в `ModelResource` используется `enum` класс `PageType`.

Доступны следующие типы страниц:

- `INDEX` - страница индекса,
- `FORM` - страница формы,
- `DETAIL` - страница детального просмотра.

```php
use MoonShine\Support\Enums;

//...

PageType::INDEX;
PageType::FORM;
PageType::DETAIL;
```

<a name="fields"></a>
## Добавление полей

[Поля](/docs/{{version}}/fields/index) в `MoonShine` используются не только для ввода данных, но и для их вывода.  
Метод `fields()` в классе страницы `CRUD` позволяет указать необходимые поля.

```php
namespace App\MoonShine\Pages\Post;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

class PostIndexPage extends IndexPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
        ];
    }

    //...
}
```

<a name="components"></a>
## Основные компоненты

В админ-панели `MoonShine` можно быстро изменить основной компонент на странице.

#### IndexPage

Метод `getItemsComponent()` позволяет изменить основной компонент страницы индекса.

```php
getItemsComponent(iterable $items, Fields $fields): ComponentContract
```

- `$items` - значения полей,
- `$fields` - поля.

```php
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\UI\Components\Table\TableBuilder;

class ArticleIndexPage extends IndexPage
{
    // ...

    protected function getItemsComponent(iterable $items, Fields $fields): ComponentContract
    {
        return TableBuilder::make(items: $items)
            ->name($this->getListComponentName())
            ->fields($fields)
            ->cast($this->getResource()->getCaster())
            ->withNotFound()
            ->when(
                ! is_null($head = $this->getResource()->getHeadRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->headRows($head)
            )
            ->when(
                ! is_null($body = $this->getResource()->getRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->rows($body)
            )
            ->when(
                ! is_null($foot = $this->getResource()->getFootRows()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->footRows($foot)
            )
            ->when(
                ! is_null($this->getResource()->getTrAttributes()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->trAttributes(
                    $this->getResource()->getTrAttributes()
                )
            )
            ->when(
                ! is_null($this->getResource()->getTdAttributes()),
                fn (TableBuilderContract $table): TableBuilderContract => $table->tdAttributes(
                    $this->getResource()->getTdAttributes()
                )
            )
            ->buttons($this->getResource()->getIndexButtons())
            ->clickAction($this->getResource()->getClickAction())
            ->when($this->getResource()->isAsync(), static function (TableBuilderContract $table): void {
                $table->async()->pushState();
            })
            ->when($this->getResource()->isStickyTable(), function (TableBuilderContract $table): void {
                $table->sticky();
            })
            ->when($this->getResource()->isColumnSelection(), function (TableBuilderContract $table): void {
                $table->columnSelection();
            });
    }
}
```
> [!TIP]
> Пример страницы индекса с компонентом `CardsBuilder` в разделе [Рецепты](/docs/{{version}}/recipes/index-page-cards)

#### DetailPage

Метод `getDetailComponent()` позволяет изменить основной компонент страницы детального просмотра.

```php
getDetailComponent(?DataWrapperContract $item, Fields $fields): ComponentContract
```

-`$item` - данные
-`$fields` - поля

```php
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\UI\Components\Table\TableBuilder;

class ArticleDetailPage extends DetailPage
{
    // ...

    protected function getDetailComponent(?DataWrapperContract $item, Fields $fields): ComponentContract
    {
        return TableBuilder::make($fields)
            ->cast($this->getResource()->getCaster())
            ->items([$item])
            ->vertical()
            ->simple()
            ->preview();
    }
}
```
#### FormPage

Метод `getFormComponent()` позволяет изменить основной компонент на странице с формой.

```php
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Laravel\Collections\Fields;

getFormComponent(
  string $action,
  ?DataWrapperContract $item,
  Fields $fields,
  bool $isAsync = true,
): ComponentContract
```

-`$action` - endpoint,
-`$item` - данные,
-`$fields` - поля,
-`$isAsync` - асинхронный режим.

```php
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;

class ArticleFormPage extends FormPage
{
    // ...

    protected function getFormComponent(
        string $action,
        ?DataWrapperContract $item,
        Fields $fields,
        bool $isAsync = true,
    ): ComponentContract {
        $resource = $this->getResource();

        return FormBuilder::make($action)
            ->cast($this->getResource()->getCaster())
            ->fill($item)
            ->fields([
                ...$fields
                    ->when(
                        ! is_null($item),
                        static fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_method')->setValue('PUT')
                        )
                    )
                    ->when(
                        ! $resource->isItemExists() && ! $resource->isCreateInModal(),
                        static fn (Fields $fields): Fields => $fields->push(
                            Hidden::make('_force_redirect')->setValue(true)
                        )
                    )
                    ->toArray(),
            ])
            ->when(
                ! $resource->hasErrorsAbove(),
                fn (FormBuilderContract $form): FormBuilderContract => $form->errorsAbove($resource->hasErrorsAbove())
            )
            ->when(
                $isAsync,
                static fn (FormBuilderContract $formBuilder): FormBuilderContract => $formBuilder
                    ->async(events: array_filter([
                        $resource->getListEventName(
                            request()->input('_component_name', 'default'),
                            $isAsync && $resource->isItemExists() ? array_filter([
                                'page' => request()->input('page'),
                                'sort' => request()->input('sort'),
                            ]) : []
                        ),
                        ! $resource->isItemExists() && $resource->isCreateInModal()
                            ? AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey())
                            : null,
                    ]))
            )
            ->when(
                $resource->isPrecognitive() || (moonshineRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->precognitive()
            )
            ->when(
                $resource->isSubmitShowWhen(),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->submitShowWhenAttribute()
            )
            ->name($resource->getUriKey())
            ->submit(__('moonshine::ui.save'), ['class' => 'btn-primary btn-lg'])
            ->buttons($resource->getFormBuilderButtons());
    }
}
```

<a name="layers"></a>
## Слои на странице

Для удобства все страницы *crud* разделены на три слоя, которые отвечают за отображение определенной области на странице.

- `TopLayer` - используется для отображения метрик на странице индекса и для дополнительных кнопок на странице редактирования
- `MainLayer` - этот слой используется для отображения основной информации с помощью [FormBuilder](/docs/{{version}}/components/form-builder) и [TableBuilder](/docs/{{version}}/components/table-builder)
- `BottomLayer` - используется для отображения дополнительной информации

Для настройки слоев используются соответствующие методы: `topLayer()`, `mainLayer()` и `bottomLayer()`. Методы должны возвращать массив [Компонентов](/docs/{{version}}/page/index#components).

```php
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\UI\Components\Heading;

class PostIndexPage extends IndexPage
{
    //...

    protected function topLayer(): array
    {
        return [
            Heading::make('Custom top'),
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            Heading::make('Custom main'),
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            Heading::make('Custom bottom'),
            ...parent::bottomLayer()
        ];
    }

    //...
}
```
> [!TIP]
> Если вам нужно получить доступ к компонентам определенного слоя через ресурс или страницу, то используйте метод `getLayerComponents`.

```php
use MoonShine\Support\Enums\Layer;

// ...

// Resource
$this->getFormPage()->getLayerComponents(Layer::BOTTOM);

// Page
$this->getLayerComponents(Layer::BOTTOM);
```

> [!TIP]
> Если вам нужно добавить компонент для указанной страницы в нужный слой через ресурс, то используйте метод `onLoad` ресурса и `pushToLayer` страницы.

```php
use MoonShine\Permissions\Components\Permissions;
use MoonShine\Support\Enums\Layer;

protected function onLoad(): void
{
    $this->getFormPage()->pushToLayer(
            layer: Layer::BOTTOM,
            component: Permissions::make(
                'Permissions',
                $this,
            )
        );
}
```
