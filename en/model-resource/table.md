# Tables

- [Basics](#basics)
- [Sorting](#order-by)
- [Buttons](#buttons)
- [Attributes](#attributes)
- [Click Actions](#click)
- [Sticky Header](#sticky-table)
- [Column Display](#column-display)
- [Sticky](#sticky)
- [Pagination](#pagination)
  - [Cursor](#simple-pagination)
  - [Simple](#simple-pagination)
  - [Disable Pagination](#disable-pagination)
- [Async Mode](#async)
  - [Updating a row](#update-row)
  - [Lazy](#lazy)
- [Modifiers](#modifiers)
  - [Components](#components)
  - [Elements thead, tbody, tfoot](#thead-tbody-tfoot)

---

<a name="basics"></a>
## Basics

In `CrudResource` (`ModelResource`) on the `indexPage` as well as on the `DetailPage`, `TableBuilder` is used to display the main data, so we recommend you also study the documentation section [TableBuilder](/docs/{{version}}/components/table-builder).

<a name="order-by"></a>
## Sorting

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\SortDirection;

class PostResource extends ModelResource
{
    protected string $sortColumn = 'created_at'; // Default sort field

    protected SortDirection $sortDirection = SortDirection::DESC; // Default sort type

    //...
}
```

<a name="buttons"></a>
## Buttons

To add buttons to the table, you can use `ActionButton` and the methods `indexButtons` or `customIndexButtons`, as well as `detailButtons` and `customDetailButtons` for the detail page.

> [!TIP]
> [More details ActionButton](/docs/{{version}}/components/action-button)

```php
protected function customIndexButtons(): ListOf
{
   return parent::customIndexButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

When using the `customIndexButtons` method, all your buttons will be added before the main `CRUD` buttons. However, if you need to replace the main buttons or add new ones after the main buttons, you can use the `indexButtons` method.

After the main buttons:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

Before the main buttons:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->prepend(ActionButton::make('Link', '/endpoint'));
}
```

Remove the delete button:

```php
protected function indexButtons(): ListOf
{
   return parent::indexButtons()->except(fn(ActionButton $btn) => $btn->getName() === 'resource-delete-button');
}
```

Clear the button set and add your own:

```php
protected function indexButtons(): ListOf
{
   parent::indexButtons()->empty()->add(ActionButton::make('Link', '/endpoint'));
}
```

> [!NOTE]
> The same approach is used for the table on the detail page, only through the methods `detailButtons` and `customDetailButtons`.

For bulk actions, you need to add the `bulk` method.

```php
protected function customIndexButtons(): ListOf
{
   return parent::customIndexButtons()->add(ActionButton::make('Link', '/endpoint')->bulk());
}
```

<a name="attributes"></a>
## Attributes

To add attributes for the `td` element of the table, you can use the `customWrapperAttributes` method on the field that represents the cell you need.

```php
protected function indexFields(): iterable
{
  return [
    // ..
    Text::make('Title')->customWrapperAttributes(['width' => '20%']);
    // ..
  ];
}
```

You can also customize `tr` and `td` for the table with data through the resource. To do this, you need to use the corresponding methods `trAttributes()` and `tdAttributes()`, to which you need to pass a closure that returns an array of attributes for the table component.

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

![img](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/table_class_dark.png)

<a name="click"></a>
## Click Actions

By default, clicking on `tr` does nothing, but you can change the behavior to navigate to editing, selection, or to the detailed view.

```php
    // ClickAction::SELECT, ClickAction::DETAIL, ClickAction::EDIT

    protected ?ClickAction $clickAction = ClickAction::SELECT;
```

<a name="sticky-table"></a>
## Sticky Header

The model resource property `stickyTable` allows you to fix the header when scrolling a table with a large number of elements.

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
## Column Display

You can allow users to independently determine which columns to display in the table while retaining their selection. To do this, you need to set the parameter `$columnSelection` for the resource.

```php
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

If you need to exclude fields from the selection, use the `columnSelection()` method.

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

<a name="sticky"></a>
## Sticky columns

You can freeze cells in large tables, suitable for ID columns and buttons

To fix buttons in the table, switch the resource to `stickyButtons` mode:

```php
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    protected bool $stickyButtons = true;
}
```

To fix a column, call the `sticky()` method on the Field:

```php
protected function indexFields(): iterable
{
    return [
        ID::make()->sticky(),
    ];
}
```

<a name="pagination"></a>
## Pagination

To change the number of items per page, use the property `$itemsPerPage`.

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
### Cursor

When dealing with a large volume of data, the best solution is to use cursor pagination.

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
### Simple

If you do not plan to display the total number of pages, use `Simple Pagination`. This avoids additional queries for the total number of records in the database.

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

![img] (https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_simple_paginate_dark.png)

<a name="disable-pagination"></a>
### Disable Pagination

If you do not plan to use pagination, it can be disabled.

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
## Async Mode

In the resource, async mode is used by default. This mode allows for pagination, filtering, and sorting without page reloads. However, if you want to disable async mode, you can use the property `$isAsync`.

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

You can asynchronously update a row in the table by triggering the event:

```php
table-row-updated-{{componentName}}-{{row-id}}
```

- `{{componentName}}` - the name of the component;
- `{{row-id}}` - the key of the row item.

To add an event, you can use the helper class:

```php
AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, 'main-table-{row-id}')
```

- `{row-id}` - shortcode for the id of the current model record.

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

The `withUpdateRow()` method is also available, which helps simplify the assignment of events:

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

<a name="lazy"></a>
## Lazy mode

If you want to display a page without waiting for data to load,
and then send a query to get the table data, use *Lazy* mode:

```php
class PostResource extends ModelResource
{
    protected bool $isLazy = true;
}
```

<a name="modifiers"></a>
## Modifiers

<a name="components"></a>
### Components

You can completely replace or modify the resource's `TableBuilder` for both the index and detail pages. Use the `modifyListComponent` or `modifyDetailComponent` methods for this.

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
### Elements thead, tbody, tfoot

If it is not enough to just automatically output fields in `thead`, `tbody`, and `tfoot`, you can override or extend this logic based on the resource methods `thead()`, `tbody()`, `tfoot()`.

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

#### Example of adding an additional row in tfoot
```php
    protected function tfoot(): null|TableRowsContract|Closure
    {
        return static function(?TableRowContract $default, TableBuilder $table) {
            $cells = TableCells::make();

            $cells->pushCell('Balancе:');
            $cells->pushCell('1000 р.');

            return TableRows::make([TableRow::make($cells), $default]);
        };
    }
```