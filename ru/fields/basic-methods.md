# Базовые методы
- [Описание](#description)
- [Создание](#create)
- [Отображение поля](#view)
  - [Label](#label)
  - [Подсказка](#hint)
  - [Ссылка](#link)
  - [Badge](#badge)
  - [Горизонтально отображение](#horizontal)
  - [Обертка](#wrapper)
  - [Сортировка](#sortable)
  - [Режимы отображения](#view-modes)
- [Атрибуты](#attributes)
  - [Required](#required-link)
  - [Disabled](#disabled-link)
  - [Readonly](#readonly-link)
  - [Другие атрибуты](#custom-attributes)
  - [Атрибуты для wrapper поля](#custom-wrapper-attributes)
- [Модифицирование значения поля](#field-value)
  - [Nullable](#nullable)
  - [Изменение отображения](#custom-view)
  - [Получение значения из запроса](#request-value-resolver)
  - [До и после рендеринга](#before-and-after-render)
  - [Условные методы](#conditional-methods)
  - [Apply](#apply)
  - [Заполнение](#fill)
  - [Методы onChange](#on-change)
  - [Изменение render поля](#change-render)
  - [Методы для значений](#for-value)
- [Реактивность](#reactive)
- [Динамическое отображение](#show-when)
  - [showWhen](#show-when)
  - [showWhenDate](#show-when-date)
  - [Вложенные поля](#nested-fields)
  - [Множественные условия](#multiple-conditions)
  - [Поддерживаемые операторы](#supported-operators)
---

<a name="description"></a>
## Описание

Все поля наследуют базовый класс `Field`, который предоставляет базовые методы работы с полями.

<a name="create"></a>
## Создание поля

Для создания экземпляра поля используется статический метод `make()`.

```php
Text::make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
```

- `$label` - лейбл, заголовок поля,
- `$column` - поле в базе (например name) или отношение (например countries),
- `$formatted` - замыкание для форматирования значения поля в режиме preview (для BelongsTo и BelongsToMany форматирует значения для выбора).

> Если не указать `$column`, то поле в базе данных будет определено автоматически на основе `$label` (только для английского языка).

Пример замыкания `$formatted` для форматирования значения
```php
Text::make(
    'Name',
    'first_name',
    fn($item) => $item->first_name . ' ' . $item->last_name
)
```

> Поля не поддерживающие `formatted`: `Json`, `File`, `Range`, `RangeSlider`, `DateRange`, `Select`, `Enum`, `HasOne`, `HasMany`.

<a name="view"></a>
## Отображение поля

<a name="label"></a>
### Label

Если необходимо изменить `Label` после создания экземпляра поля, можно воспользоваться методом `setLabel()`.

```php
setLabel(Closure|string $label)
```

```php
Slug::make('Slug')
    ->setLabel(
        fn(Field $field) => $field->getData()?->exists
            ? 'Slug (do not change)'
            : 'Slug'
    )
```

Для перевода `Label` необходимо в качестве названия передать ключ перевода и добавить метод `translatable()`.

```php
translatable(string $key = '')
```

```php
Text::make('ui.Title')->translatable()
```

или

```php
Text::make('Title')->translatable('ui')
```

или

```php
Text::make(fn() => __('Title'))
```

<a name="hint"></a>
### Подсказка

Подсказка с описанием можно создать с помощью метода `hint()`.

```php
hint(string $hint)
```

```php
Number::make('Rating')
    ->hint('From 0 to 5')
    ->min(0)
    ->max(5)
    ->stars()
```

<a name="link"></a>
### Ссылка

Полю можно добавить ссылку (например с инструкциями) `link()`.

```php
link(
    string|Closure $link,
    string|Closure $name = '',
    ?string $icon = null,
    bool $withoutIcon = false,
    bool $blank = false
)
```

```php
Text::make('Link')
    ->link('https://cutcode.dev', 'CutCode', blank: true)
```

<a name="badge"></a>
### Badge

Для отображения поля в режиме `preview` в виде `badge`, необходимо воспользоваться методом `badge()`.

```php
badge(string|Color|Closure|null $color = null)
```

Доступные цвета:

<span style="background-color: #7843e9; padding: 5px; border-radius: 0.375rem">primary</span> <span style="background-color: #ec4176; padding: 5px; border-radius: 0.375rem">secondary</span> <span style="background-color: #00aa00; padding: 5px; border-radius: 0.375rem">success</span> <span style="background-color: #ffdc2a; padding: 5px; border-radius: 0.375rem; color: rgb(139 116 0 / 1);">warning</span> <span style="background-color: #e02d2d; padding: 5px; border-radius: 0.375rem">error</span> <span style="background-color: #0079ff; padding: 5px; border-radius: 0.375rem">info</span>

<span style="background-color: rgb(243 232 255 / 1); color: rgb(107 33 168 / 1); padding: 5px; border-radius: 0.375rem">purple</span>
<span style="background-color: rgb(252 231 243 / 1); color: rgb(157 23 77 / 1); padding: 5px; border-radius: 0.375rem">pink</span>
<span style="background-color: rgb(219 234 254 / 1); color: rgb(30 64 175 / 1); padding: 5px; border-radius: 0.375rem">blue</span>
<span style="background-color: rgb(220 252 231 / 1); color: rgb(22 101 52 / 1); padding: 5px; border-radius: 0.375rem">green</span>
<span style="background-color: rgb(254 249 195 / 1); color: rgb(133 77 14 / 1); padding: 5px; border-radius: 0.375rem">yellow</span>
<span style="background-color: rgb(243 232 255 / 1); color: rgb(153 27 27 / 1); padding: 5px; border-radius: 0.375rem">red</span>
<span style="background-color: rgb(243 244 246 / 1); color: rgb(31 41 55 / 1); padding: 5px; border-radius: 0.375rem">gray</span>


```php
Text::make('Title')
    ->badge(Color::PRIMARY)
```

или

```php
Text::make('Title')
    ->badge(fn($status, Field $field) => 'green')
```

<a name="horizontal"></a>
### Горизонтально отображение

Метод `horizontal()` позволяет отображать название и поле горизонтально.

```php
horizontal()
```

```php
Text::make('Title')
    ->horizontal(),
```

<a name="wrapper"></a>
### Обертка

Поля при отображении в формах используют специальную обертку `wrapper` для заголовков, подсказок, ссылок и тд. Иногда может возникнуть ситуация, когда требуется отобразить поле без дополнительных элементов.
Метод `withoutWrapper()` позволяет отключить создание *wrapper*.

```php
withoutWrapper(mixed $condition = null)
```

```php
Text::make('Title')
    ->withoutWrapper()
```

<a name="sortable"></a>
### Сортировка

Для возможности сортировки поля на главной странице ресурса необходимо добавить метод `sortable()`.

```php
sortable(Closure|string|null $callback = null)
```

```php
Text::make('Title')->sortable()
```

Метод `sortable()` в качестве параметра может принимать название поля в базе данных или замыкание.

```php
BelongsTo::make('Author')->sortable('author_id'),

Text::make('Title')->sortable(function (Builder $query, string $column, string $direction) {
    $query->orderBy($column, $direction);
})
```

<a name="view-modes"></a>
### Режимы отображения

Подробнее о режимах отображения поля можно прочитать в разделе [Основы | Смена режима отображения](https://github.com/moonshine-software/doc/blob/3.x/ru/fields/index.md#смена-режима-отображения).

#### Режим "Default"
Чтобы поле в не зависимости от контекста всегда работало в режиме "Default" (отображение "input" поля), необходимо использовать метод `defaultMode()`.

```php
Text::make('Title')->defaultMode()
```

#### Режим "Preview"

Чтобы поле в не зависимости от контекста всегда работало в режиме "Preview", необходимо использовать метод `previewMode()`

```php
Text::make('Title')->previewMode()
```

#### Режим "RawMode"

Чтобы поле в не зависимости от контекста всегда работало в режиме "RawMode" (отображение исходного состояния), необходимо использовать метод `rawMode()`

```php
Text::make('Title')->rawMode()
```

<a name="attributes"></a>
## Атрибуты

Основные html атрибуты, такие как `required`, `disabled` и `readonly`, у поля необходимо задавать через соответствующие методы.

<a name="required-link"></a>
### Required

```php
required(Closure|bool|null $condition = null)
```
```php
Text::make('Title')->required()
```

<a name="disabled-link"></a>
### Disabled

```php
disabled(Closure|bool|null $condition = null)
```
```php
Text::make('Title')->disabled()
```

<a name="readonly-link"></a>
### Readonly

```php
readonly(Closure|bool|null $condition = null)
```
```php
Text::make('Title')->readonly()
```

<a name="custom-attributes"></a>
### Другие атрибуты
Чтобы указать любые другие атрибуты, используется метод `customAttributes()`.

```php
customAttributes(array $attributes, bool $override = false)
```

- `$attributes` - массив атрибутов
- `$override` - иногда у полей есть базовые атрибуты, которые заданы для них по умолчанию. Чтобы их переопределить, необходимо использовать `$override = true`

```php
Password::make('Title')
    ->customAttributes(['autocomplete' => 'off'])
```

<a name="custom-wrapper-attributes"></a>
### Атрибуты для wrapper поля

Метод `customWrapperAttributes()` позволяет добавить атрибуты для `wrapper` поля.

```php
customWrapperAttributes(array $attributes)
```

```php
Password::make('Title')
    ->customWrapperAttributes(['class' => 'mt-8'])
```

<a name="field-value"></a>
## Модифицирование значения поля

<a name="nullable"></a>
### Nullable

Если необходимо у поля по умолчанию сохранять NULL, то необходимо использовать метод `nullable()`.

```php
nullable(Closure|bool|null $condition = null)
```

```php
Password::make('Title')
    ->nullable()
```

<a name="custom-view"></a>
### Изменение отображения

Когда необходимо изменить view с помощью fluent interface можно воспользоваться методом `customView()`.

```php
customView(string $view, array $data = [])
```

```php
Text::make('Title')->customView('fields.my-custom-input')
```

Метод `changePreview()` позволяет переопределить view для превью (везде кроме формы).

```php
Text::make('Thumbnail')
  ->changePreview(function ($value, Field $field) {
      return view('moonshine::ui.image', [
          'value' => Storage::url($value)
      ]);
  }) 
```

Методы `beforeRender()` и `afterRender()` позволяют вывести какую-то информацию перед и после поля соответственно.

```php
beforeRender(Closure $closure)
```

```php
afterRender(Closure $closure)
```

```php
Image::make('Thumbnail')
  ->beforeRender(function (Field $field) {
      return $field->preview();
  }) 
```

<a name="request-value-resolver"></a>
### Получение значения из запроса (TODO описать раздел)

Метод `requestValueResolver()` позволяет переопределить логику получения значения из Request.

```php
/**
* @param  Closure(string|int|null $index, mixed $default, static $ctx): mixed  $resolver
*/
requestValueResolver(Closure $resolver)
```

```php
Text::make('Thumbnail')
  ->requestValueResolver(function (string $nameDot, mixed $default, Field $field) {
      return request($nameDot, $default);
  })
```

> Поля отношений не поддерживают метод `requestValueResolver`.

<a name="before-and-after-render"></a>
### До и после рендеринга

Методы `beforeRender()` и `afterRender()` позволяют вывести какую-то информацию перед и после поля соответственно.

```php
beforeRender(Closure $closure)
afterRender(Closure $closure)
```

```php
Text::make('Title')
    ->beforeRender(function(Field $field) {
        return $field->preview();
    })
```

<a name="conditional-methods"></a>
### Условные методы

Метод `when()` реализует *fluent interface* и выполнит callback, когда первый аргумент, переданный методу, имеет значение true.

```php
when($value = null, ?callable $callback = null, ?callable $default = null)
```

```php
Text::make('Slug')
    ->when(fn() => true, fn(Field $field) => $field->locked())
```

Метод `unless()` обратный методу `when()`.

```php
unless($value = null, ?callable $callback = null, ?callable $default = null)
```

<a name="apply"></a>
### Apply

У каждого поля реализован метод `apply()`, который трансформирует данные с учетом *request* и *resolve* методов. Чтобы переопределить стандартный `apply` у поля, можно воспользоваться методом `onApply()`. Подробнее *о цикле жизни применения поля* можно прочитать в разделе [Основы | Процесс применения полей](https://github.com/moonshine-software/doc/blob/3.x/ru/fields/index.md#процесс-применения-полей).

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): mixed  $onApply
 */
onApply(Closure $onApply)
```

```php
Text::make('Thumbnail by link', 'thumbnail')
    ->onApply(function(Model $item, $value, Field $field) {
        $path = 'thumbnail.jpg';
        
        if ($value) {
            $item->thumbnail = Storage::put($path, file_get_contents($value));
        }
        
        return $item;
    })
```

<a name="fill"></a>
### Заполнение

Поля можно заполнить значениями используя метод `fill()`. Более подробно о процессе заполнения поля можно прочитать в разделе [Основы | Изменить наполнение](https://github.com/moonshine-software/doc/blob/3.x/ru/fields/index.md#изменить-наполнение).

```php
fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0)
```

```php
Text::make('Title')
    ->fill('Some title')
```

<a name="on-change"></a>
### Методы onChange

C помощью методов `onChangeMethod()` и `onChangeUrl()` можно добавить логику при изменении значений полей.

>Методы onChangeUrl() или onChangeMethod() присутствуют у всех полей, кроме полей отношений HasOne и HasMany

#### onChangeUrl()

```php
onChangeUrl(
    Closure $url,
    HttpMethod $method = HttpMethod::GET,
    array $events = [],
    ?string $selector = null,
    ?AsyncCallback $callback = null,
)
```

- `$url` - url запроса,
- `$method` - метод асинхронного запроса,
- `$events` - вызываемые [AlpineJS события](TODO ссылка на доку) после успешного запроса,
- `$selector` - selector элемента у которого будет изменяться контент,
- `$callback` - js callback функция после получения ответа.

```php
Switcher::make('Active')
    ->onChangeUrl(fn() => '/endpoint') 
```

Eсли требуется заменить область с html после успешного запроса, вы можете в ответе вернуть HTML контент или json с ключом html.

```php
Switcher::make('Active')
    ->onChangeUrl(fn() => '/endpoint', selector: '#my-selector')
```

#### onChangeMethod()

Метод `onChangeMethod()` позволяет асинхронно вызывать метод ресурса или страницы при изменении поля без необходимости создавать дополнительные контроллеры.

```php
onChangeMethod(
  string $method,
  array|Closure $params = [],
  ?string $message = null,
  ?string $selector = null,
  array $events = [],
  ?AsyncCallback $callback = null,
  ?PageContract $page = null,
  ?ResourceContract $resource = null,
)
```

- `$method` - наименование метода,
- `$params` - параметры для запроса,
- `$message` - сообщения,
- `$selector` - selector элемента у которого будет изменяться контент,
- `$events` - вызываемые [AlpineJS события](TODO ссылка на доку) после успешного запроса,
- `$callback` - js callback функция после получения ответа,
- `$page` - страница содержащая метод,
- `$resource` - ресурс содержащий метод.

```php
Switcher::make('Active')
    ->onChangeMethod('someMethod')
```

```php
public function someMethod(MoonShineRequest $request): void
{
    // Logic
}
```

<a name="change-render"></a>
### Изменение render поля

Чтобы полностью изменить render поля, можно воспользоваться методом `changeRender()`

```php
changeRender(Closure $callback)
```
В данном поле Select трансформируется в текст:
```php
Select::make('Links')->options([  
    '/images/1.png' => 'Picture 1',  
    '/images/2.png' => 'Picture 2',  
])  
    ->multiple()  
    ->changeRender(  
        fn(?array $values, Select $ctx) => Text::make($ctx->getLabel())->fill(implode(',', $values))  
    )
```

<a name="for-value"></a>
### Методы для значений

#### Получение значение из исходного

Метод `fromRaw()` позволяет добавить замыкание для получения итоговое значение из исходного.

```php
/**
 * @param  Closure(mixed $raw, static): mixed  $callback
 * @return $this
 */
    fromRaw(Closure $callback)
```

```php
use App\Enums\StatusEnum;
use MoonShine\Fields\Enum;
 
Enum::make('Status')
    ->attach(StatusEnum::class)
    ->fromRaw(fn(string $raw, Enum $ctx) => StatusEnum::tryFrom($raw))
```

#### Получения необработанного значения

Метод `modifyRawValue()` позволяет добавить замыкание для получения необработанного значения. 

```php
/**
 * @param  Closure(mixed $raw, static): mixed  $callback
 * @return $this
 */
modifyRawValue(Closure $callback)
```

```php
use App\Enums\StatusEnum;
use MoonShine\Fields\Enum;
 
Enum::make('Status')
    ->attach(StatusEnum::class)
    ->modifyRawValue(fn(StatusEnum $raw, Enum $ctx) => $raw->value))
```

<a name="reactive"></a>
## Реактивность

Метод `reactive()` позволяет реактивно изменять поля.

```php
reactive(
    ?Closure $callback = null,
    bool $lazy = false,
    int $debounce = 0,
    int $throttle = 0,
)
```

- `$callback` - callback функция,
- `$lazy` - отложенный вызов функции,
- `$debounce` - время между вызовами функций (ms.),
- `$throttle` - интервал вызова функций (ms.).

> Поля поддерживающие реактивность: Text, Number, Checkbox, Select и их наследующие.

```php
FormBuilder::make()
    ->name('my-form')
    ->fields([
        Text::make('Title')
            ->reactive(function(Fields $fields, ?string $value): Fields {
                return tap($fields, static fn ($fields) => $fields
                    ->findByColumn('slug')
                    ?->setValue(str($value ?? '')->slug()->value())
                );
            }), 
 
        Text::make('Slug')
            ->reactive() 
    ])
```

В данном пример реализовано формирование slug-поля на основе заголовка.
Slug будет генерироваться в процессе ввода текста.

> [!WARNING]
> Реактивное поле может менять состояние других полей, но не изменяет свое состояние!

Для изменения состояния поля инициирующего реактивность удобно воспользоваться параметрами `callback` функции.

```php
Select::make('Category', 'category_id')
    ->reactive(function(Fields $fields, ?string $value, Field $field, array $values): Fields {
        $field->setValue($value);  
 
        return tap($fields, static fn ($fields) =>
            $fields
                ->findByColumn('article_id')
                ?->options(
                    Article::where('category_id', $value)
                        ->get()
                        ->pluck('title', 'id')
                        ->toArray()
                );
        );
    })
```

<a name="show-when"></a>
## Динамическое отображение

Для изменения отображения полей в зависимости от значений других полей в реальном времени, без перезагрузки страницы и запросов к серверу, используются методы `showWhen` и `showWhenDate`.

<a name="show-when"></a>
### Метод showWhen

Метод `showWhen` позволяет задать условие отображения поля в зависимости от значения другого поля.

```php
public function showWhen(
    string $column,
    mixed $operator = null,
    mixed $value = null
): static
```

- `$column` - имя поля, от которого зависит отображение,
- `$operator` - оператор сравнения (необязательный),
- `$value` - значение для сравнения.

```php
Text::make('Name')
    ->showWhen('category_id', 1)
```

В этом примере поле "Name" будет отображаться только если значение поля "category_id" равно 1.

> [!NOTE]
> Если в функции `showWhen` передается только два параметра, то по умолчанию используется оператор `'='`.

```php
Text::make('Name')
    ->showWhen('category_id', 'in', [1, 2, 3])
```

В этом примере поле "Name" будет отображаться только если значение поля "category_id" равно 1, 2 или 3.

<a name="show-when-date"></a>
### Метод showWhenDate

Метод `showWhenDate` позволяет задать условие отображения поля в зависимости от значения поля типа `date`. Логика для работы с датами была вынесена в отдельный метод из за специфики конвертации и сравнения типа `date` и `datetime` на backend и frontent

```php
public function showWhenDate(
    string $column,
    mixed $operator = null,
    mixed $value = null
): static
```

- `$column` - имя поля с датой, от которого зависит отображение,
- `$operator` - оператор сравнения (необязательный),
- `$value` - значение даты для сравнения.

```php
Text::make('Content')
    ->showWhenDate('created_at', '>', '2024-09-15 10:00')
```

В этом примере поле "Content" будет отображаться только если значение поля "created_at" больше '2024-09-15 10:00'.

> [!NOTE]
> Если в функции `showWhenDate` передается только два параметра, то по умолчанию используется оператор `'='`.

> [!NOTE]
> Вы можете использовать любой формат даты, который может быть распознан функцией `strtotime()`.


<a name="nested-fields"></a>
### Вложенные поля

Методы `showWhen` и `showWhenDate` поддерживают работу с вложенными полями, например для работы с полем `Json`. Для обращения к вложенным полям используется точечная нотация.

```php
Text::make('Parts')
    ->showWhen('attributes.1.size', '!=', 2)
```

В этом примере поле "Parts" будет отображаться только если значение вложенного поля "size" во втором элементе массива "attributes" не равно 2.

showWhen работает и для вложенных полей типа `Json`:

```php
Json::make('Attributes', 'attributes')->fields([
    Text::make('Size'),
    Text::make('Parts')
        ->showWhen('category_id', 3)
    ,
    Json::make('Settings', 'settings')->fields([
        Text::make('Width')
            ->showWhen('category_id', 3)
        ,
        Text::make('Height'),
    ])
]),
```
В данном примере весь столбец `Parts` внтури `attributes` и весь столбец `Width` внтури `attributes.[n].settings` будет отображаться только если значение поля `category_id` равно 3.

<a name="multiple-conditions"></a>
### Множественные условия

Методы `showWhen` и `showWhenDate` могут быть вызваны несколько раз для одного поля, что позволяет задать несколько условий отображения.

```php
BelongsTo::make('Category', 'category', , resource: CategoryResource::class)
    ->showWhenDate('created_at', '>', '2024-08-05 10:00')
    ->showWhenDate('created_at', '<', '2024-08-05 19:00')
```

В этом примере поле "Category" будет отображаться только если значение поля "created_at" находится в диапазоне между '2024-08-05 10:00' и '2024-08-05 19:00'.

> [!NOTE]
> При использовании нескольких условий они объединяются логическим "И" (AND). Поле будет отображаться только если выполняются все заданные условия.

<a name="supported-operators"></a>
### Поддерживаемые операторы

- `=`
- `!=`
- `>`
- `<`
- `>=`
- `<=`
- `in`
- `not in`

> [!NOTE]
> Оператор `in` проверяет, содержится ли значение в массиве.
> Оператор `not in` проверяет, не содержится ли значение в массиве.