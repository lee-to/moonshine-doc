# TableBuilder

- [Основы](#basics)
- [Основное использование](#basic-usage)
- [Основные методы](#basic-methods)
  - [Поля](#fields)
  - [Данные](#items)
  - [Пагинация](#paginator)
  - [Упрощенный вид пагинатора](#simple-paginate)
  - [Кнопки](#buttons)
- [Отображение](#view-methods)
  - [Вертикальное отображение](#vertical-display)
  - [Редактируемая таблица](#editable-table)
  - [Упрощенный режим](#preview-table)
  - [С уведомлением "Ничего не найдено"](#not-found)
  - [Кастомизация строк](#rows)
- [Дополнительные возможности](#additional-features)
  - [Добавление новых строк](#adding-new-rows)
  - [Переиндексация](#reindexing)
  - [Сортировка перетаскиванием](#drag-and-drop-sorting)
  - [Фиксированный заголовок](#sticky-header)
  - [Выбор колонок](#column-selection)
  - [Поиск](#search)
  - [Действие по клику](#click-action)
  - [Сохранение состояния в URL](#save-state-in-url)
  - [Модификация чекбокса массовых действий](#modify-row-checkbox)
- [Настройка атрибутов](#attribute-configuration)
- [Асинхронная загрузка](#async-loading)
  - [Lazy и whenAsync методы](#lazy)
- [Приведение к типу](#type-cast)
- [Использование в blade](#blade)
  - [Основы](#blade-basics)
  - [Упрощенный вид](#blade-simple)
  - [Фиксированный заголовок](#blade-sticky)
  - [С уведомлением "Ничего не найдено"](#blade-notfound)
  - [Слоты](#blade-slots)
  - [Стилизация](#blade-styles)

---

<a name="basics"></a>
## Основы

`TableBuilder` - инструмент в MoonShine для создания настраиваемых таблиц для отображения данных. Он используется на индексной и детальной CRUD-страницах, а также для полей отношений, таких как `HasMany`, `BelongsToMany`, `RelationRepeater` и поля `Json`.

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
## Основное использование

Пример использования `TableBuilder`:

```php
TableBuilder::make()
    ->items([
      ['id' => 1, 'title' => 'Hello world']
    ])
    ->fields([
        ID::make()->sortable(),
        Text::make('Название', 'title'),
    ])
```

<a name="basic-methods"></a>
## Основные методы

<a name="fields"></a>
### Поля

Поля для `TableBuilder` упрощают наполнение данными и отображение ячеек таблицы.
По умолчанию поля выводятся в режиме `preview`.
Метод `fields` определяет поля таблицы, каждое поле является ячейкой таблицы (`td`):

```php
->fields([
    ID::make()->sortable(),
    Text::make('Название', 'title'),
])
```

Если необходимо указать атрибуты для `td`, воспользуйтесь методом `customWrapperAttributes`:

```php
->fields([
    ID::make()->sortable(),
    Text::make('Название', 'title')->customWrapperAttributes(['class' => 'my-class']),
])
```

<a name="items"></a>
### Данные

Метод `items` устанавливает данные для таблицы:

```php
->items($this->getCollection())
```

<a name="paginator"></a>
### Пагинация

Метод `paginator` устанавливает пагинатор для таблицы. Необходимо передать объект, реализующий интерфейс `MoonShine\Contracts\Core\Paginator\PaginatorContract`:

> [!NOTE]
> Если необходимо указать пагинатор для QueryBuilder, можно воспользоваться встроенным `ModelCaster`, как в примере ниже:

```php
->paginator(
  (new ModelCaster(Article::class))
    ->paginatorCast(
        Article::query()->paginate()
    )
)
```

> [!NOTE]
> Пагинатор также можно указать через метод `items`.

<a name="simple-paginate"></a>
### Упрощенный вид пагинатора

Метод `simple()` применяет упрощенный стиль пагинации в таблице:

```php
->simple()
```

<a name="buttons"></a>
### Кнопки

Метод `buttons` добавляет кнопки действий:

```php
->buttons([
    ActionButton::make('Delete', fn() => route('name.delete')),
    ActionButton::make('Edit', fn() => route('name.edit'))->showInDropdown(),
    ActionButton::make('Go to home', fn() => route('home'))->blank()->canSee(fn($data) => $data->active),
    ActionButton::make('Mass Delete', fn() => route('name.mass_delete'))->bulk(),
])
```

Для указания массовых действий над элементами таблицы необходимо у `ActionButton` указать метод `bulk`:

```php
->buttons([
    ActionButton::make('Mass Delete', fn() => route('name.mass_delete'))->bulk(),
])
```

Если вам необходимо зафиксировать кнопки (sticky), тогда воспользуйтесь методом `stickyButtons()`:

```php
->stickyButtons()
```

<a name="view-methods"></a>
## Отображение

<a name="vertical-display"></a>
### Вертикальное отображение

Метод `vertical()` отображает таблицу в вертикальном формате (используется на `DetailPage`):

```php
->vertical()
```

Если вы хотите изменить атрибуты колонок при вертикальном режиме, то воспользуйтесь параметрами `title` или `value`:

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

- `title` - Колонка с заголовком
- `value` - Колонка со значением

Также можно передать целочисленное значение для указания колонок:

```php
$component->vertical(
    title: 2,
    value: 10,
)
```

<a name="editable-table"></a>
### Редактируемая таблица

Метод `editable()` делает таблицу редактируемой, все поля переводятся в режим `defaultMode` (режим формы):

```php
->editable()
```

<a name="preview-table"></a>
### Упрощенный режим

Метод `preview()` отключает отображение кнопок и сортировок для таблицы:

```php
->preview()
```

<a name="not-found"></a>
### С уведомлением "Ничего не найдено"

По умолчанию, если у таблицы нет данных, то она будет пустой, но можно вывести сообщение "Пока записей нет".
Для этого воспользуйтесь методом `withNotFound`:

```php
TableBuilder::make()
    ->withNotFound()
```

<a name="rows"></a>
### Кастомизация строк

Поля ускоряют процесс и наполняют таблицу самостоятельно, выстраивая шапку таблицы с заголовками полей и сортировок, тело таблицы с выводом данных через поля и футер таблицы с массовыми действиями. Однако иногда может возникнуть потребность указать строки самостоятельно либо добавить дополнительные.
Для этой задачи существуют методы для соответствующих секций таблицы: `headRows` (`thead`), `rows` (`tbody`), `footRows` (`tfoot`).

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
> Обратите внимание, для `footRows` передается `?TableRowContract` и в значении `$default` будет передано `null`, если кнопки массовых действий отсутствуют. Значение `null` можно указывать в списке `$items` в `TableRows::make`, оно будет проигнорировано.

`TableRows` и `TableCells` - это коллекции компонентов с дополнительным функционалом для быстрого добавления строки или ячейки таблицы.

```php
TableRows::make()->pushRow(
  TableCellsContract $cells,
  int|string|null $key = null,
  ?Closure $builder = null
)
```

- `$cells` - коллекция ячеек,
- `$key` - уникальный ключ tr для массовых действий и событий обновления строк таблицы,
- `$builder` - доступ к TableBuilder.

```php
TableCells::make()->pushCell(
  Closure|string $content,
  ?int $index = null,
  ?Closure $builder = null,
  array $attributes = []
)
```

- `$content` - содержимое ячейки,
- `$index` - порядковый номер ячейки,
- `$builder` - доступ к TableBuilder,
- `$attributes` - HTML атрибуты ячейки.

У TableCells также есть дополнительные вспомогательные методы.

`pushFields` для быстрой генерации ячеек на основе полей:

```php
TableCells::make()->pushFields(
  FieldsContract $fields,
  ?Closure $builder = null,
  int $startIndex = 0
)
```

- `$fields` - коллекция полей,
- `$builder` - доступ к TableBuilder,
- `$startIndex` - начальный индекс (так как до этого, возможно, уже были добавлены ячейки таблицы)

Также доступны условные методы `pushWhen` и `pushCellWhen`.

<a name="additional-features"></a>
## Дополнительные возможности

<a name="adding-new-rows"></a>
### Добавление новых строк

Метод `creatable()` позволяет добавлять новые строки, делает таблицу динамической:

```php
->creatable(reindex: true, limit: 5, label: 'Добавить', icon: 'plus', attributes: ['class' => 'my-class'])
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

- `$reindex` - режим редактирования с динамическим name,
- `$limit` - количество записей, которые можно добавить,
- `$label` - название кнопки,
- `$icon` - иконка у кнопки,
- `$attributes` - дополнительные атрибуты,
- `$button` - кастомная кнопка добавления.

> [!NOTE]
> В режиме добавления необходимо, чтобы последний элемент был пустым (скелет новой записи)!

Если в таблице находятся поля в режиме редактирования с динамическим name, то нужно добавить метод или параметр `reindex`:

```php
TableBuilder::make()
    ->creatable(reindex: true)

TableBuilder::make()
    ->creatable()
    ->reindex()
```

Пример с указанием кастомной кнопки добавления:

```php
TableBuilder::make()
    ->creatable(
        button: ActionButton::make('Foo', '#')
    )
```

<a name="reindexing"></a>
### Переиндексация

Метод `reindex()` позволяет переиндексировать элементы таблицы, всем `name` атрибутам элементов формы будет добавлен индекс.
Пример: Поле `Text::make('Title', 'title')` на первой строке `tr` таблицы будет иметь вид `<input name="title[1]">`.
В режиме `creatable` или `removable` при добавлении/удалении новой строки все атрибуты `name` будут переиндексированы с учетом порядкового номера.

```php
->reindex()
```

<a name="drag-and-drop-sorting"></a>
### Сортировка перетаскиванием

Метод `reorderable()` добавляет возможность сортировки строк перетаскиванием:

```php
->reorderable(url: '/reorder-url', key: 'id', group: 'group-name')
```

- `$url` - URL-обработчика,
- `$key` - ключ элемента,
- `$group` - группировка (если требуется).

<a name="sticky-header"></a>
### Фиксированный заголовок

Метод `sticky()` делает заголовок таблицы фиксированным:

```php
->sticky()
```

<a name="column-selection"></a>
### Выбор колонок

Метод `columnSelection()` добавляет возможность выбора отображаемых колонок:

```php
->columnSelection()
```

Если необходимо у определенных полей отключить выбор отображения, то воспользуйтесь методом `columnSelection` у поля с параметром, равным `false`:

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
> При использовании `columnSelection` параметр `name` компонента `TableBuilder` должен быть уникальным для всех страниц.
> Это связано с тем, что данные сохраняются в `localStorage` на основе значения `name` компонента.

<a name="search"></a>
### Поиск

Метод `searchable()` добавляет функцию поиска по таблице:

```php
->searchable()
```

<a name="click-action"></a>
### Действие по клику

Метод `clickAction()` задает действие при клике на строку:
В примере ниже при клике на строку таблицы произойдет клик на кнопку редактирования.

```php
->clickAction(ClickAction::EDIT)
```

Если вы используете кастомные кнопки или переопределили кнопки по умолчанию, в таком случае также может потребоваться указать селектор кнопки:

```php
->clickAction(ClickAction::EDIT, '.edit-button')
```

Типы ClickAction:

- `ClickAction::SELECT` - выбор строки для массовых действий,
- `ClickAction::EDIT` - переход в редактирование,
- `ClickAction::DETAIL` - переход в детальный просмотр.

<a name="save-state-in-url"></a>
### Сохранение состояния в URL

Метод `pushState()` сохраняет состояние таблицы в URL:

```php
->pushState()
```
<a name="modify-row-checkbox"></a>
### Модификация чекбокса массовых действий

Метод `modifyRowCheckbox()` позволяет модифицировать чекбокс массовых действий.
Пример ниже демонстрирует выбор активного чекбокса по умолчанию:

```php
->modifyRowCheckbox(
    fn(Checkbox $checkbox, DataWrapperContract $data, TableBuilder $ctx) => $data->getKey() === 2 ? $checkbox->customAttributes(['checked' => true]) : $checkbox
)
```

<a name="attribute-configuration"></a>
## Настройка атрибутов

TableBuilder предоставляет методы для настройки HTML-атрибутов:

```php
->trAttributes(fn(?DataWrapperContract $data, int $row): array => ['class' => $row % 2 ? 'bg-gray-100' : ''])
->tdAttributes(fn(?DataWrapperContract $data, int $row, int $cell): array => ['class' => $cell === 0 ? 'font-bold' : ''])
->headAttributes(['class' => 'bg-blue-500 text-white'])
->bodyAttributes(['class' => 'text-sm'])
->footAttributes(['class' => 'bg-gray-200'])
->customAttributes(['class' => 'custom-table'])
```

<a name="async-loading"></a>
## Асинхронная загрузка

Метод `async()` настраивает асинхронную загрузку таблицы:

> [!NOTE]
> Метод `async` должен быть после метода `name`

```php
->async(
  Closure|string|null $url = null,
  string|array|null $events = null,
  ?AsyncCallback $callback = null,
)
```

- `$url` - URL асинхронного запроса (в ответе необходимо вернуть TableBuilder),
- `$events` - события, которые будут вызваны после успешного ответа,
- `$callback` - JS callback, который можно добавить как обертку ответа.

После успешного запроса можно вызвать события, добавив параметр `events`.

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

Список событий для TableBuilder:

- `JsEvent::TABLE_UPDATED` - обновление таблицы,
- `JsEvent::TABLE_REINDEX` - реиндексация таблицы (см. `reindex()`)
- `JsEvent::TABLE_ROW_UPDATED` - обновление строки таблицы (`AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, "{component-name}-{row-id}")`)

> [!NOTE]
> Для получения дополнительной информации о js событиях обратитесь к разделу [Events](/docs/{{version}}/frontend/events).

Все параметры метода `async` являются опциональными, и по умолчанию `TableBuilder` автоматически укажет URL на основе текущей страницы.

В процессе использования *TableBuilder* в режиме `async` может возникнуть задача, когда вы используете его вне админ-панели на страницах, не объявленных в системе *MoonShine*.
Тогда вам потребуется указать собственный URL и реализовать ответ с HTML таблицей. Давайте рассмотрим пример реализации:

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
### Lazy и whenAsync методы

Если вам необходимо отправить запрос на обновление компонента `TableBuilder` сразу при загрузке страницы, то нужно добавить метод `lazy()`.
Также методы `lazy()` и `whenAsync()` в сочетании могут решить задачу ленивой загрузки данных или загрузки данных из внешнего источника.

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

Метод `whenAsync()` проверяет, является ли текущий запрос асинхронным для получения текущего компонента `TableBuilder`.
Пример взаимодействия с методами, где загрузка таблицы происходит по нажатию на кнопку:

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
## Приведение к типу

> [!WARNING]
> Если вы используете данные в таблице без `cast`, необходимо указать, что в ваших данных является ключом.
> В противном случае некоторые возможности, такие как bulk-операции, работать не будут.

Пример:

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

Метод `cast` служит для приведения значений таблицы к определенному типу.
Так как по умолчанию поля работают с примитивными типами:

```php
use MoonShine\Laravel\TypeCasts\ModelCaster;

TableBuilder::make()
    ->cast(new ModelCaster(User::class))
```

В этом примере мы привели данные к формату модели `User` с использованием `ModelCaster`.

> [!NOTE]
> За более подробной информацией обратитесь к разделу [TypeCasts](/docs/{{version}}/advanced/type-casts)

<a name="blade"></a>
## Использование в blade

<a name="blade-basics"></a>
### Основы

Стилизованные таблицы можно создавать с помощью компонента `moonshine::table`.

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
### Упрощенный вид

Параметр `simple` позволяет создавать упрощенного вида таблицы.

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
### Фиксированный заголовок

Если в таблице содержится большое количество элементов, то можно зафиксировать шапку при прокрутке таблицы.

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
### С уведомлением "Ничего не найдено"

Параметр `notfound` позволяет выводить сообщение при отсутствии элементов таблицы.

```php
<x-moonshine::table
    :columns="[
        '#', 'First', 'Last', 'Email'
    ]"
    :notfound="true"
/>
```

<a name="blade-slots"></a>
### Слоты

Таблицу можно сформировать с использованием слотов.

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
### Стилизация

Для стилизации таблицы есть предустановленные классы, которые можно использовать для `tr` / `td`.

Доступные классы:

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

`TableBuilder` в `MoonShine` предоставляет широкий спектр возможностей для создания гибких и функциональных таблиц в административной панели.
