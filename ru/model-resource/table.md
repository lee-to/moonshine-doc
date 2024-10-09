# Таблицы
  - [Основы](#basics)
  - [Сортировка](#order-by)
  - [Кнопки](#buttons)
  - [Атрибуты](#attributes)
  - [Действия по клику](#click)
  - [Фиксированная шапка](#sticky-table)
  - [Отображение колонок](#column-display)
  - [Пагинация](#pagination)
    - [Курсорная](#simple-pagination)
    - [Упрощенная](#simple-pagination)
    - [Отключение пагинации](#disable-pagination)
  - [Асинхронный режим](#async)
    - [Обновление ряда](#update-row)
  - [Модификаторы](#modifiers)
    - [Компоненты](#components)
    - [Элементы thead, tbody, tfoot](#thead-tbody-tfoot) 

---

<a name="basics"></a>
## Основы

В `CrudResource`(`ModelResource`) на страницах `indexPage`, а также `DetailPage` для отображенния основных данных используется `TableBuilder`, поэтому мы рекомендуем вам также изучить раздел документации [TableBuilder](/docs/{{version}}/components/table-builder).

<a name="order-by"></a>
## Сортировка


```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\SortDirection;

class PostResource extends ModelResource
{
    protected string $sortColumn = 'created_at'; // Поле сортировки по умолчанию 

    protected SortDirection $sortDirection = SortDirection::DESC; // Тип сортировки по умолчанию

    //...
}
```


<a name="buttons"></a>
## Кнопки

Для добавления кнопок в таблицу используются `ActionButton` и методы `indexButtons` или `customIndexButtons`, а также `detailButtons` и `customDetailButtons` для детальной страницы

> [!TIP]
> [More details ActionButton](/docs/{{version}}/components/action-button)

```php
protected function customIndexButtons(): ListOf
{
   return parent::customIndexButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

При использовании метода `customIndexButtons` все ваши кнопки будут добавлятся перед основными `CRUD` кнопками, но если вам необходимо заменить основные кнопки или добавить новые после основных, то воспользуйтесь методом `indexButtons`

После основных:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

До основных:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->prepend(ActionButton::make('Link', '/endpoint'));
}
```

Убрать кнопку удаления:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->except(fn(ActionButton $btn) => $btn->getName() === 'resource-delete-button');
}
```

Очистить набор кнопок и добавить свою:

```php
protected function indexButtons(): ListOf
{
   parent::indexButtons()->empty()->add(ActionButton::make('Link', '/endpoint'));
}
```

> [!NOTE]
> Такой же подход используется и для таблицы на детальной страницы, только через методы `detailButtons` и `customDetailButtons`

> [!TIP]
> Пример создания кастомных кнопок у индексной таблицы в разделе [Рецепты](/docs/{{version}}/recipes/index#custom-buttons)

Для массовых действий необходимо добавить метод `bulk`

```php
protected function customIndexButtons(): ListOf
{
   return parent::customIndexButtons()->add(ActionButton::make('Link', '/endpoint')->bulk());
}
```

<a name="attributes"></a>
## Атрибуты

Чтобы добавить атрибуты для `td` элемента таблицы, можно воспользоваться методом `customWrapperAttributes` у поля которое представляет нужную вам ячейку

```php
protected function indexFields(): iterable
{
  return [
    // ..
    Text::make('Title')->customWrapperAttributes(['width' => '20%])m
    // ..
  ];
}
```

Также есть возможность возможность кастомизировать `tr` и `td` у таблицы с данными через ресурс.
Для это необходимо использовать соответствующие методы `trAttributes()` и `tdAttributes()`, которым нужно передать замыкание, возвращающее массив атрибутов для компонента таблица.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use Closure;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

class PostResource extends ModelResource
{
    //...

    protected function tdAttributes(): Closure
    {
        return fn(?DataWrapperContract $data, int $row, int $cell) => [
            'width' => '20%'
        ];
    }

    protected function trAttributes(): Closure
    {
        return fn(?DataWrapperContract $data, int $row) => [
            'data-tr' => $row
        ];
    }

    //...
}
```

![img](https://moonshine-laravel.com/screenshots/table_class_dark.png)

<a name="click"></a>
## Действия по клику

По умолчанию на клик по `tr` ничего не произойдет, но можно изменить поведение на переход в редактирование, выбор или переход к детальному просмотру

```php
    // ClickAction::SELECT, ClickAction::DETAIL, ClickAction::EDIT

    protected ?ClickAction $clickAction = ClickAction::SELECT;
```

<a name="sticky-table"></a>
## Фиксированная шапка таблицы

Свойство ресурса модели `stickyTable` позволяет зафиксировать шапку при прокрутке таблицы с большим числом элементов.

```php
namespace App\MoonShine\Resources;

use MoonShine\Laravle\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    protected bool $stickyTable = true;

    // ...
}
```

<a name="column-display"></a>
## Отображение колонок

Можно предоставить пользователям самостоятельно определять какие колонки отображать в таблице, с сохранением выбора. Для этого необходимо у ресурса задать параметр `$columnSelection`.

```
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    protected bool $columnSelection = true;

    //...
}
```

Если необходимо исключить поля из выбора, то воспользуйтесь методом `columnSelection()`.

```php
public function columnSelection(bool $active = true)
```

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    protected bool $columnSelection = true;

    //...

    protected function indexFields(): iterable
    {
        return [
            ID::make()
                ->columnSelection(false),
            Text::make('Title'),
            Textarea::make('Body'),
        ];
    }

    //...
}
```

<a name="pagination"></a>
## Пагинация

Для изменения количества элементов на странице воспользуйтесь свойством `$itemsPerPage`

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ..

    protected int $itemsPerPage = 25;

    //...
}
```

<a name="cursor-pagination"></a>
### Курсорная

При большом объеме данных наилучшим решение будет использовать курсорную пагинацию

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ..

    protected bool $cursorPaginate = true;

    //...
}
```

<a name="simple-pagination"></a>
### Упрощенная

Если вы не планируете отображать общее количество страниц, воспользуйтесь `Simple Pagination`. Это позволит избежать дополнительных запросов на общее количество записей в базе данных.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected bool $simplePaginate = true;

    // ...
}
```

![img] (https://moonshine-laravel.com/screenshots/resource_simple_paginate_dark.png)

<a name="disable-pagination"></a>
### Отключение пагинации

Если вы не планируете использовать разбиение на страницы, то его можно отключить.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected bool $usePagination = false;

    // ...
}
```

<a name="async"></a>
## Асинхронный режим

В ресурсе асинхронный режим используется по умолчанию. Такой режим дает возможность использовать пагинацию, фильтрацию и сортировку без перезагрузки страницы, но если вы хотите отключить асинхронный режим, то воспользуйтесь свойством `$isAsync`

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected bool $isAsync = false;

    // ...
}
```

<a name="update-row"></a>
### Updating a row

У таблицы можно асинхронно обновить ряд, для этого необходимо вызвать событие:

```php
table-row-updated-{{componentName}}-{{row-id}}
```

- `{{componentName}}` - название компонента;
- `{{row-id}}` - ключ элемента ряда

Для добавления события можно воспользоваться классом-помощником:

```php
AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, 'main-table-{row-id}')
```

- `{row-id}` - shortcode для id текущей записи модели.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;

class PostResource extends ModelResource
{
    //...

    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Textarea::make('Body'),
            Switcher::make('Active')
                ->updateOnPreview(
                    events: [AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, 'index-table-{row-id}')]
                )
        ];
    }

    //...
}
```

Также доступен метод `withUpdateRow()`, который помогает упростить назначение событий:

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    //...

    protected function fields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Textarea::make('Body'),
            Switcher::make('Active')
                ->withUpdateRow($this->getListComponentName())
        ];
    }

    //...
}
```

<a name="modify"></a>
## Модификация

<a name="components"></a>
### Компоненты

Вы можете полностью заменить или модифицировать `TableBuilder` ресурса для индексной и детальной страницы. Для этого воспользуйтесь методами `modifyListComponent` или `modifyDetailComponent`

```php
public function modifyListComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyListComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

```php
public function modifyDetailComponent(MoonShineRenderable $component): MoonShineRenderable
{
    return parent::modifyDetailComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```

<a name="thead-tbody-tfoot"></a>
### Элементы thead, tbody, tfoot

Если вам недостаточно просто автоматически выводить поля в `thead`, `tbody` и `tfoot`, то вы можете переопределить или дополнить эту логику на основе методов ресурса `thead()`, `tbody()`, `tfoot()`

```php
use MoonShine\Contracts\UI\Collection\TableRowsContract;
use MoonShine\Contracts\UI\TableRowContract;
use MoonShine\UI\Collections\TableCells;
use MoonShine\UI\Collections\TableRows;

protected function thead(): null|TableRowsContract|Closure
{
    return static fn(TableRowContract $default) => TableRows::make([$default])->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    );
}

protected function tbody(): null|TableRowsContract|Closure
{
    return static fn(TableRowsContract $default) => $default->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    );
}

protected function tfoot(): null|TableRowsContract|Closure
{
    return static fn(?TableRowContract $default) => TableRows::make([$default])->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    );
}
```

#### Пример добавления дополнительной строки в tfoot  
```php
    protected function tfoot(): null|TableRowsContract|Closure
    {
        return static function(?TableRowContract $default, TableBuilder $table) {
            $cells = TableCells::make();

            $cells->pushCell('Баланс:');
            $cells->pushCell('1000 р.');

            return TableRows::make([TableRow::make($cells), $default]);
        };
    }
```


