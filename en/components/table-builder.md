# TableBuilder

- [Basics](#basics)
- [Basic Usage](#basic-usage)
- [Basic Methods](#basic-methods)
  - [Fields](#fields)
  - [Items](#items)
  - [Paginator](#paginator)
  - [Simple Paginator](#simple-paginate)
  - [Buttons](#buttons)
- [View Methods](#view-methods)
  - [Vertical Display](#vertical-display)
  - [Editable Table](#editable-table)
  - [Preview Mode](#preview-table)
  - [With "Not Found" Notification](#not-found)
  - [Row Customization](#rows)
- [Additional Features](#additional-features)
  - [Adding New Rows](#adding-new-rows)
  - [Reindexing](#reindexing)
  - [Drag and Drop Sorting](#drag-and-drop-sorting)
  - [Sticky Header](#sticky-header)
  - [Column Selection](#column-selection)
  - [Search](#search)
  - [Click Action](#click-action)
  - [Save State in URL](#save-state-in-url)
  - [Modify Row Checkbox](#modify-row-checkbox)
- [Attribute Configuration](#attribute-configuration)
- [Async Loading](#async-loading)
  - [Lazy and whenAsync Methods](#lazy)
- [Type Cast](#type-cast)
- [Using in Blade](#blade)
  - [Basics](#blade-basics)
  - [Simple View](#blade-simple)
  - [Sticky Header](#blade-sticky)
  - [With "Not Found" Notification](#blade-notfound)
  - [Slots](#blade-slots)
  - [Styling](#blade-styles)

---

<a name="basics"></a>
## Basics

`TableBuilder` is a tool in MoonShine for creating customizable tables for displaying data. It is used on index and detail CRUD pages, as well as for relationship fields such as `HasMany`, `BelongsToMany`, `RelationRepeater`, and `Json` fields.

~~~tabs
tab: Class
```php
use MoonShine\UI\Components\Table\TableBuilder;

TableBuilder::make(iterable $fields = [], iterable $items = [])
```
tab: Blade
```blade
<x-moonshine::table
    :columns="[ 
        '#', 'First', 'Last', 'Email' 
    ]"
    :values="[ 
        [1, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [2, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [3, fake()->firstName(), fake()->lastName(), fake()->safeEmail()]
    ]"
/>
```
~~~

<a name="basic-usage"></a>
## Basic Usage

Example of using `TableBuilder`:

```php
TableBuilder::make()
    ->items([ 
      ['id' => 1, 'title' => 'Hello world'] 
    ])
    ->fields([ 
        ID::make()->sortable(), 
        Text::make('Title', 'title'),
    ])
```

<a name="basic-methods"></a>
## Basic Methods

<a name="fields"></a>
### Fields

Fields for `TableBuilder` simplify the filling of data and displaying table cells. By default, fields are displayed in `preview` mode. The `fields` method defines the table fields, each field is a table cell (`td`):

```php
->fields([
    ID::make()->sortable(),
    Text::make('Title', 'title'),
])
```

If you need to specify attributes for `td`, use the `customWrapperAttributes` method:

```php
->fields([
    ID::make()->sortable(),
    Text::make('Title', 'title')->customWrapperAttributes(['class' => 'my-class']),
])
```

<a name="items"></a>
### Items

The `items` method sets the data for the table:

```php
->items($this->getCollection())
```

<a name="paginator"></a>
### Paginator

The `paginator` method sets the paginator for the table. You need to pass an object that implements the `MoonShine\Contracts\Core\Paginator\PaginatorContract` interface:

> [!NOTE]
> If you need to specify a paginator for QueryBuilder, you can use the built-in `ModelCaster`, as in the example below:

```php
->paginator(
  (new ModelCaster(Article::class))
    ->paginatorCast(
        Article::query()->paginate()
    )
)
```

> [!NOTE]
> The paginator can also be specified through the `items` method.

<a name="simple-paginate"></a>
### Simple Paginator

The `simple()` method applies a simplified pagination style to the table:

```php
->simple()
```

<a name="buttons"></a>
### Buttons

The `buttons` method adds action buttons:

```php
->buttons([
    ActionButton::make('Delete', fn() => route('name.delete')),
    ActionButton::make('Edit', fn() => route('name.edit'))->showInDropdown(),
    ActionButton::make('Go to home', fn() => route('home'))->blank()->canSee(fn($data) => $data->active),
    ActionButton::make('Mass Delete', fn() => route('name.mass_delete'))->bulk(),
])
```

To specify bulk actions on table items, the `bulk` method should be set on `ActionButton`:

```php
->buttons([
    ActionButton::make('Mass Delete', fn() => route('name.mass_delete'))->bulk(),
])
```

If you need to stick buttons, then use the `stickyButtons()` method:

```php
->stickyButtons()
```

<a name="view-methods"></a>
## View Methods

<a name="vertical-display"></a>
### Vertical Display

The `vertical()` method displays the table in vertical format (used on `DetailPage`):

```php
->vertical()
```

If you want to change the attributes of the columns in vertical mode, use the `title` or `value` parameters:

```php
/** @param TableBuilder $component */
public function modifyDetailComponent(ComponentContract $component): ComponentContract
{
    return $component->vertical(
        title: fn(FieldContract $field, Column $default, TableBuilder $ctx): ComponentContract => $default->columnSpan(2),
        value: fn(FieldContract $field, Column $default, TableBuilder $ctx): ComponentContract => $default->columnSpan(10),
    );
}
```

- `title` - Column with the header
- `value` - Column with the value

You can also pass an integer value to specify the columns:

```php
$component->vertical(
    title: 2,
    value: 10,
)
```

<a name="editable-table"></a>
### Editable Table

The `editable()` method makes the table editable, switching all fields to `defaultMode` (form mode):

```php
->editable()
```

<a name="preview-table"></a>
### Preview Mode

The `preview()` method disables the display of buttons and sorting for the table:

```php
->preview()
```

<a name="not-found"></a>
### With "Not Found" Notification

By default, if the table has no data, it will be empty, but you can display a message saying "No records found yet." To do this, use the `withNotFound` method:

```php
TableBuilder::make()
    ->withNotFound()
```

<a name="rows"></a>
### Row Customization

Fields accelerate the process and fill the table independently, constructing the table header with field headers and sorts, the body of the table with data output through fields, and the footer of the table with bulk actions. However, sometimes there may be a need to specify rows manually or add additional ones. For this task, methods are provided for the corresponding sections of the table: `headRows` (`thead`), `rows` (`tbody`), `footRows` (`tfoot`).

```php
// tbody
TableBuilder::make()
  ->rows(
    static fn(TableRowsContract $default) => $default->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    )
  )


// thead
TableBuilder::make()
  ->headRows(
    static fn(TableRowContract $default) => TableRows::make([$default])->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    )
  )

// tfoot
TableBuilder::make()
  ->footRows(
    static fn(?TableRowContract $default) => TableRows::make([$default])->pushRow(
        TableCells::make()->pushCell(
            'td content'
        )
    )
  )
```
> [!NOTE]
> Note that for `footRows`, a `?TableRowContract` is passed, and the value of `$default` will be passed as `null` if there are no bulk action buttons. The `null` value can be specified in the `$items` list in `TableRows::make`, and it will be ignored.

`TableRows` and `TableCells` are collections of components with additional functionality for quickly adding a row or cell to the table.

```php
TableRows::make()->pushRow(
  TableCellsContract $cells,
  int|string|null $key = null,
  ?Closure $builder = null
)
```

- `$cells` - a collection of cells,
- `$key` - a unique key for `tr` for bulk actions and row update events,
- `$builder` - access to TableBuilder.

```php
TableCells::make()->pushCell(
  Closure|string $content,
  ?int $index = null,
  ?Closure $builder = null,
  array $attributes = []
)
```

- `$content` - content of the cell,
- `$index` - ordinal number of the cell,
- `$builder` - access to TableBuilder,
- `$attributes` - HTML attributes of the cell.

`TableCells` also has additional helper methods.

`pushFields` for quick generation of cells based on fields:

```php
TableCells::make()->pushFields(
  FieldsContract $fields,
  ?Closure $builder = null,
  int $startIndex = 0
)
```

- `$fields` - collection of fields,
- `$builder` - access to TableBuilder,
- `$startIndex` - starting index (since there may have already been cells added to the table previously)

Conditional methods `pushWhen` and `pushCellWhen` are also available.

<a name="additional-features"></a>
## Additional Features

<a name="adding-new-rows"></a>
### Adding New Rows

The `creatable()` method allows adding new rows, making the table dynamic:

```php
->creatable(reindex: true, limit: 5, label: 'Add', icon: 'plus', attributes: ['class' => 'my-class'])
```

```php
creatable(
    bool $reindex = true,
    ?int $limit = null,
    ?string $label = null,
    ?string $icon = null,
    array $attributes = [],
    ?ActionButtonContract $button = null
)
```

- `$reindex` - editing mode with dynamic name,
- `$limit` - the number of records that can be added,
- `$label` - button name,
- `$icon` - button icon,
- `$attributes` - additional attributes,
- `$button` - custom add button.

> [!NOTE]
> In add mode, it is necessary for the last element to be empty (skeleton for a new record)!

If there are fields in the table in editing mode with dynamic name, you need to add the method or parameter `reindex`:

```php
TableBuilder::make()
    ->creatable(reindex: true)

TableBuilder::make()
    ->creatable()
    ->reindex()
```

Example with specifying a custom add button:

```php
TableBuilder::make()
    ->creatable(
        button: ActionButton::make('Foo', '#')
    )
```

<a name="reindexing"></a>
### Reindexing

The `reindex()` method allows reindexing the table elements, adding an index to all `name` attributes of form elements. Example: The field `Text::make('Title', 'title')` in the first row of the table will look like `<input name="title[1]">`. In `creatable` or `removable` mode, when adding/removing a new row, all `name` attributes will be reindexed considering the ordinal number.

```php
->reindex()
```

<a name="drag-and-drop-sorting"></a>
### Drag and Drop Sorting

The `reorderable()` method adds the ability to sort rows by dragging:

```php
->reorderable(url: '/reorder-url', key: 'id', group: 'group-name')
```

- `$url` - handler URL,
- `$key` - item key,
- `$group` - grouping (if required).

<a name="sticky-header"></a>
### Sticky Header

The `sticky()` method makes the table header fixed:

```php
->sticky()
```

<a name="column-selection"></a>
### Column Selection

The `columnSelection()` method adds the ability to select displayed columns:

```php
->columnSelection()
```

If you need to disable the display selection for certain fields, use the `columnSelection` method on the field with the parameter set to `false`:

```php
TableBuilder::make()
    ->fields([
        Text::make('Title')
            ->columnSelection(false),
        Text::make('Text')
    ])
    ->columnSelection()
```

> [!WARNING]
> When using `columnSelection`, the `name` parameter of the `TableBuilder` component must be unique across all pages. This is because data is stored in `localStorage` based on the value of the component's `name`.

<a name="search"></a>
### Search

The `searchable()` method adds the search function for the table:

```php
->searchable()
```

<a name="click-action"></a>
### Click Action

The `clickAction()` method sets an action to be performed on clicking the row: In the example below, clicking the table row will trigger a click on the edit button.

```php
->clickAction(ClickAction::EDIT)
```

If you use custom buttons or have overridden the default buttons, you may also need to specify a button selector:

```php
->clickAction(ClickAction::EDIT, '.edit-button')
```

Types of ClickAction:

- `ClickAction::SELECT` - select a row for bulk actions,
- `ClickAction::EDIT` - go to edit,
- `ClickAction::DETAIL` - go to detailed view.

<a name="save-state-in-url"></a>
### Save State in URL

The `pushState()` method saves the state of the table in the URL:

```php
->pushState()
```

<a name="modify-row-checkbox"></a>
### Modify Row Checkbox

The `modifyRowCheckbox()` method allows modifying the bulk action checkbox. The example below demonstrates selecting the active checkbox by default:

```php
->modifyRowCheckbox(
    fn(Checkbox $checkbox, DataWrapperContract $data, TableBuilder $ctx) => $data->getKey() === 2 ? $checkbox->customAttributes(['checked' => true]) : $checkbox
)
```

<a name="attribute-configuration"></a>
## Attribute Configuration

TableBuilder provides methods for configuring HTML attributes:

```php
->trAttributes(fn(?DataWrapperContract $data, int $row): array => ['class' => $row % 2 ? 'bg-gray-100' : ''])
->tdAttributes(fn(?DataWrapperContract $data, int $row, int $cell): array => ['class' => $cell === 0 ? 'font-bold' : ''])
->headAttributes(['class' => 'bg-blue-500 text-white'])
->bodyAttributes(['class' => 'text-sm'])
->footAttributes(['class' => 'bg-gray-200'])
->customAttributes(['class' => 'custom-table'])
```

<a name="async-loading"></a>
## Async Loading

The `async()` method configures asynchronous loading of the table:

> [!NOTE]
> The `async` method must be after the `name` method

```php
->async(
  Closure|string|null $url = null,
  string|array|null $events = null,
  ?AsyncCallback $callback = null,
)
```

- `$url` - URL of the asynchronous request (the response must return TableBuilder),
- `$events` - events that will be triggered after a successful response,
- `$callback` - JS callback that can be added as a wrapper for the response.

After a successful request, you can trigger events by adding the `events` parameter.

```php
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;

TableBuilder::make()
        ->name('crud')
        ->async(events: [
          AlpineJs::event(JsEvent::FORM_RESET, 'main-form'),
          AlpineJs::event(JsEvent::TOAST, params: ['text' => 'Success', 'type' => 'success']),
        ])
```

Event list for TableBuilder:

- `JsEvent::TABLE_UPDATED` - table update,
- `JsEvent::TABLE_REINDEX` - table reindexing (see `reindex()`)
- `JsEvent::TABLE_ROW_UPDATED` - table row update (`AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, "{component-name}-{row-id}")`)

> [!NOTE]
> For more information on js events, refer to the [Events](/docs/{{version}}/frontend/events) section.

All parameters of the `async` method are optional, and by default, `TableBuilder` will automatically set URL based on the current page.

In the process of using *TableBuilder* in `async` mode, there may arise a task where you use it outside the admin panel on pages that are not declared in the MoonShine system. Then you will need to specify your own URL and implement a response with the HTML table. Let's consider an implementation example:

```php
TableBuilder::make()->name('my-table')->async(route('undefined-page.component', [
    '_namespace' => self::class,
    '_component_name' => 'my-table'
]))
```

`Controller`

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use Illuminate\Contracts\View\View;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Http\Controllers\MoonShineController;

final class UndefinedPageController extends MoonShineController
{
    public function component(MoonShineRequest $request): View
    {
        $page = app($request->input('_namespace'));

        $component = $page->getComponents()->findByName(
            $request->getComponentName()
        );

        return $component->render();
    }
}
```

<a name="lazy"></a>
### Lazy and whenAsync Methods

If you need to send a request to update the `TableBuilder` component immediately upon page load, you must add the `lazy()` method. Additionally, the `lazy()` and `whenAsync()` methods in combination can solve the problem of lazy loading data or loading data from an external source.

```php
TableBuilder::make()
    ->name('dashboard-table')
    ->fields([
        ID::make(),
        Slug::make('Slug'),
        Text::make('Title'),
        Preview::make('Image')->image()
    ])
    ->async()
    ->lazy()
    ->whenAsync(
        fn(TableBuilder $table) => $table->items(
            Http::get('https://jsonplaceholder.org/posts')->json()
        )
    ),
```

The `whenAsync()` method checks if the current request is asynchronous to get the current `TableBuilder` component. An example interaction with the methods where the loading of the table occurs by clicking a button:

```php
ActionButton::make('Reload')
    ->async(events: [AlpineJs::event(JsEvent::TABLE_UPDATED, 'my-table')]),

TableBuilder::make()
    ->name('my-table')
    ->fields([
        ID::make(),
        Slug::make('Slug'),
        Text::make('Title'),
        Preview::make('Image')->image()
    ])
    ->async()
    ->lazy()
    ->whenAsync(
        fn(TableBuilder $table) => $table->items(
            Http::get('https://jsonplaceholder.org/posts')->json()
        )
    ),
    ->withNotFound()
```

<a name="type-cast"></a>
## Type Cast

> [!WARNING]
> If you use data in the table without `cast`, you must specify what your data has as a key. Otherwise, some features, such as bulk operations, will not work.

Example:

```php
TableBuilder::make()
  ->castKeyName('id')
  ->name('my-table')
  ->fields([
      ID::make(),
      Text::make('Title')
  ])
  ->items([
      ['id' => 3,'title' => 'Hello world']
  ])
  ->buttons([
      ActionButton::make('Mass Delete')
          ->bulk()
  ]),
```

The `cast` method is used to cast values in the table to a certain type. Since by default, fields work with primitive types:

```php
use MoonShine\Laravel\TypeCasts\ModelCaster;

TableBuilder::make()
    ->cast(new ModelCaster(User::class))
```

In this example, we cast the data to the model format of `User` using `ModelCaster`.

> [!NOTE]
> For more detailed information, refer to the [TypeCasts](/docs/{{version}}/advanced/type-casts) section.

<a name="blade"></a>
## Using in Blade

<a name="blade-basics"></a>
### Basics

Styled tables can be created using the `moonshine::table` component.

```php
<x-moonshine::table
    :columns="[ 
        '#', 'First', 'Last', 'Email' 
    ]"
    :values="[ 
        [1, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [2, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [3, fake()->firstName(), fake()->lastName(), fake()->safeEmail()]
    ]"
/>
```

<a name="blade-simple"></a>
### Simple View

The `simple` parameter allows creating a simplified view of the table.

```php
<x-moonshine::table
    :simple="true"
    :columns="[ 
        '#', 'First', 'Last', 'Email' 
    ]"
    :values="[ 
        [1, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [2, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [3, fake()->firstName(), fake()->lastName(), fake()->safeEmail()]
    ]"
/>
```

<a name="blade-sticky"></a>
### Sticky Header

If the table contains a large number of items, you can sticky the head while scrolling the table.

```php
<x-moonshine::table
    :sticky="true"
    :columns="[ 
        '#', 'First', 'Last', 'Email' 
    ]"
    :values="[ 
        [1, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [2, fake()->firstName(), fake()->lastName(), fake()->safeEmail()],
        [3, fake()->firstName(), fake()->lastName(), fake()->safeEmail()]
    ]"
/>
```

<a name="blade-notfound"></a>
### With "Not Found" Notification

The `notfound` parameter allows displaying a message when there are no items in the table.

```php
<x-moonshine::table
    :columns="[ 
        '#', 'First', 'Last', 'Email' 
    ]"
    :notfound="true"
/>
```

<a name="blade-slots"></a>
### Slots

The table can be formed using slots.

```php
<x-moonshine::table>
    <x-slot:thead class="text-center">
        <th colspan="4">Header</th>
    </x-slot:thead>
    <x-slot:tbody>
        <tr>
            <th>1</th>
            <th>{{ fake()->firstName() }}</th>
            <th>{{ fake()->lastName() }}</th>
            <th>{{ fake()->safeEmail() }}</th>
        </tr>
        <tr>
            <th>2</th>
            <th>{{ fake()->firstName() }}</th>
            <th>{{ fake()->lastName() }}</th>
            <th>{{ fake()->safeEmail() }}</th>
        </tr>
        <tr>
            <th>3</th>
            <th>{{ fake()->firstName() }}</th>
            <th>{{ fake()->lastName() }}</th>
            <th>{{ fake()->safeEmail() }}</th>
        </tr>
    </x-slot:tbody>
    <x-slot:tfoot class="text-center">
        <td colspan="4">Footer</td>
    </x-slot:tfoot>
</x-moonshine::table>
```

<a name="blade-styles"></a>
### Styling

For styling the table, there are pre-defined classes that can be used for `tr` / `td`.

Available classes:

- bgc-purple
- bgc-pink
- bgc-blue
- bgc-green
- bgc-yellow
- bgc-red
- bgc-gray
- bgc-primary
- bgc-secondary
- bgc-success
- bgc-warning
- bgc-error
- bgc-info


```php
<x-moonshine::table>
    <x-slot:thead class="bgc-secondary text-center">
        <th colspan="3">Header</th>
    </x-slot:thead>
    <x-slot:tbody>
        <tr>
            <th class="bgc-pink">{{ fake()->firstName() }}</th>
            <th class="bgc-gray">{{ fake()->lastName() }}</th>
            <th class="bgc-purple">{{ fake()->safeEmail() }}</th>
        </tr>
        <tr>
            <th class="bgc-green">{{ fake()->firstName() }}</th>
            <th class="bgc-red">{{ fake()->lastName() }}</th>
            <th class="bgc-yellow">{{ fake()->safeEmail() }}</th>
        </tr>
    </x-slot:tbody>
</x-moonshine::table>
```

`TableBuilder` in `MoonShine` offers a wide range of capabilities for creating flexible and functional tables in the admin panel.