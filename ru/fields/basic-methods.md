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
  - [Модифицирование атрибута "name"](#name-attribute)
- [Модифицирование значения поля](#field-value)
  - [Значение по умолчанию](#default)
  - [Nullable](#nullable)
  - [Изменение отображения](#custom-view)
  - [Хук до рендера](#on-before-render)
  - [Получение значения из запроса](#request-value-resolver)
  - [До и после рендеринга](#before-and-after-render)
  - [Условные методы](#conditional-methods)
  - [Apply](#apply)
  - [Заполнение](#fill)
  - [Методы onChange](#on-change)
  - [Изменение render поля](#change-render)
  - [Методы для значений](#for-value)
- [Редактирование в режиме preview](#preview-edit)
- [Ассеты](#assets)
- [Трейт Macroable](#macroable)
- [Реактивность](#reactive)
- [Динамическое отображение](#show-when)
  - [showWhen](#show-when)
  - [showWhenDate](#show-when-date)
  - [Вложенные поля](#nested-fields)
  - [Множественные условия](#multiple-conditions)
  - [Поддерживаемые операторы](#supported-operators)
- [Кастомное поле](#custom)

---

<a name="description"></a>
## Описание

Все поля наследуют базовый класс `Field`, который предоставляет базовые методы работы с полями.

<a name="create"></a>
## Создание поля

Для создания экземпляра поля используется статический метод `make()`.

```php
use Closure;
use MoonShine\UI\Fields\Text;

Text::make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
```

- `$label` - лейбл, заголовок поля.
- `$column` - связь столбца в базе и атрибута `name` у поля ввода (например: `description` > `<input name="description">`). Если это поле отношения, то используется название отношения (например: countries).
- `$formatted` - замыкание для форматирования значения поля в режиме preview (для BelongsTo и BelongsToMany форматирует значения для выбора).

> В `$label` можно добавлять html теги, они не будут экранироваться.

> Если не указать `$column`, то поле в базе данных будет определено автоматически на основе `$label` (только для английского языка).

Пример замыкания `$formatted` для форматирования значения.
```php
use MoonShine\UI\Fields\Text;

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
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Slug;

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

#### insideLabel()

Для оборачивания поля в тег `<label>` можно использовать метод `insideLabel()`.

```php
Text::make('Name')
    ->insideLabel(),
```

#### beforeLabel()

Для отображения Label после поля ввода можно использовать метод `beforeLabel()`.

```php
Text::make('Name')
    ->beforeLabel(),
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
use MoonShine\Support\Enums\Color;

Text::make('Title')
    ->badge(Color::PRIMARY)
```

или

```php
use MoonShine\UI\Fields\Field;

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

Поля при отображении в формах используют специальную обертку `wrapper` для заголовков, подсказок, ссылок и тд.
Иногда может возникнуть ситуация, когда требуется отобразить поле без дополнительных элементов.
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

Для возможности сортировки поля в таблицах (на главной странице) необходимо добавить метод `sortable()`.

```php
sortable(Closure|string|null $callback = null)
```

```php
Text::make('Title')->sortable()
```

Метод `sortable()` в качестве параметра может принимать название поля в базе данных или замыкание.

```php
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('Author')->sortable('author_id'),

Text::make('Title')->sortable(function (Builder $query, string $column, string $direction) {
    $query->orderBy($column, $direction);
})
```

<a name="view-modes"></a>
### Режимы отображения

Подробнее о режимах отображения поля можно прочитать в разделе [Основы > Смена режима отображения](/docs/{{version}}/fields/index#change-mode).

#### Режим "Default"
Чтобы поле в не зависимости от контекста всегда работало в режиме "Default" (отображение "input" поля), необходимо использовать метод `defaultMode()`.

```php
Text::make('Title')->defaultMode()
```

#### Режим "Preview"

Чтобы поле в не зависимости от контекста всегда работало в режиме "Preview", необходимо использовать метод `previewMode()`.

```php
Text::make('Title')->previewMode()
```

#### Режим "RawMode"

Чтобы поле в не зависимости от контекста всегда работало в режиме "RawMode" (отображение исходного состояния), необходимо использовать метод `rawMode()`.

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

> [!NOTE]
> Поля являются компонентами, подробнее об атрибутах читайте в разделе [Атрибуты компонентов](/docs/{{version}}/components/attributes)

```php
customAttributes(array $attributes, bool $override = false)
```

- `$attributes` - массив атрибутов
- `$override` - для добавления атрибутов к полю, используется `merge`. Если атрибут, который вы хотите добавить к полю, уже присутствует, то он добавлен не будет. `$override = true` позволяет изменить данное поведение и перезаписать уже добавленный атрибут.

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

<a name="name-attribute"></a>
### Модифицирование атрибута "name"

#### wrapName
Для того чтобы добавить wrapper для значения атрибута `name`, используется метод `wrapName`.

```php
Text::make('Name')->wrapName('options')
```

В результате атрибут name примет вид `<input name="options[name]>`. Это особенно полезно для настройки фильтров.

#### virtualName

Иногда необходимо хранить по одному полю ввода два значения Field.
Например, по условию отображение одно из полей может становиться невидимым, но присутствовать в DOM и отправляться вместе с запросом.

```php
File::make('image') // это поле отображается в DOM на одном условии

File::make('image') // это поле отображается в DOM на другом условии
```

Для того чтобы изменить атрибут name у этих полей, используется метод `virtualName`.

```php
File::make('image')->virtualColumn('image_1')
//...
File::make('image')->virtualColumn('image_2')
```

Далее, например в onApply методе, мы обрабатываем эти поля по своему усмотрению.

<a name="field-value"></a>
## Модифицирование значения поля

<a name="default"></a>
### Значение по умолчанию

Для указания значения по умолчанию используется метод `default()`
```php
default(mixed $default)
```

```php
Text::make('Name')
    ->default('Default value')
```

или

```php
Enum::make('Status')
    ->attach(ColorEnum::class)
    ->default(ColorEnum::from('B')->value)
```

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
use MoonShine\UI\Components\Thumbnails;
use MoonShine\UI\Fields\Text;

Text::make('Thumbnail')
  ->changePreview(function (?string $value, Text $field) {
      return Thumbnails::make($value);
  })
```

<a name="on-before-render"></a>
### Хук до рендера

Если вам необходимо получить доступ к полю непосредственно перед рендером, для этого можно воспользоваться методом `onBeforeRender()`.

```php
/**
 * @param  Closure(static $ctx): void  $onBeforeRender
 */
public function onBeforeRender(Closure $onBeforeRender): static
```

```php
Text::make('Thumbnail')->onBeforeRender(function(Text $ctx) {
    // 
})
```

<a name="request-value-resolver"></a>
### Получение значения из запроса

Метод `requestValueResolver()` позволяет переопределить логику получения значения из Request.

```php
/**
* @param  Closure(string|int|null $index, mixed $default, static $ctx): mixed  $resolver
*/
requestValueResolver(Closure $resolver)
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
use MoonShine\UI\Fields\Field;

Text::make('Title')
    ->beforeRender(function(Field $field) {
        return $field->preview();
    })
```

<a name="conditional-methods"></a>
### Условные методы

Отображать компонент можно по условию, воспользовавшись методом `canSee()`.

```php
Text::make('Name')
    ->canSee(function (Text $field) {
        return $field->toValue() !== 'hide';
    })
```
или для полей отношений:

```php
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('Item', 'item', resource: ItemResource::class)
    ->canSee(function (Comment $comment, BelongsTo $field) {
        //ваше условие
    })
,
```

Метод `when()` реализует *fluent interface* и выполнит callback, когда первый аргумент, переданный методу, имеет значение true.

```php
when($value = null, ?callable $callback = null, ?callable $default = null)
```

```php
use MoonShine\UI\Fields\Field;

Text::make('Slug')
    ->when(fn() => true, fn(Field $field) => $field->locked())
```

Метод `unless()` обратный методу `when()`.

```php
unless($value = null, ?callable $callback = null, ?callable $default = null)
```

<a name="apply"></a>
### Apply

У каждого поля реализован метод `apply()`, который трансформирует данные.
Чтобы переопределить стандартный `apply` у поля, можно воспользоваться методом `onApply()`.
Подробнее *о цикле жизни применения поля* можно прочитать в разделе [Основы > Процесс применения полей](/docs/{{version}}/fields/index#apply).

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): mixed  $onApply
 */
onApply(Closure $onApply)
```

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\UI\Fields\Text;

Text::make('Thumbnail by link', 'thumbnail')
    ->onApply(function(Model $item, $value, Field $field) {
        $path = 'thumbnail.jpg';

        if ($value) {
            $item->thumbnail = Storage::put($path, file_get_contents($value));
        }

        return $item;
    })
```

Для того чтобы выполнить какие-либо действия до "apply", можно воспользоваться методом `onBeforeApply()`.

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
 */
function onBeforeApply(Closure $onBeforeApply)
```

Для того чтобы выполнить какие-либо действия после "apply", можно воспользоваться методом `onAfterApply()`.

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
 */
function onAfterApply(Closure $onBeforeApply)
```

#### Глобальное определение apply логики

Если вы хотите глобально для определенного поля изменить логику `apply`, то вы можете создать `apply` класс и привязать его к необходимому полю.

Для начала создайте `apply` класс:

```shell
php artisan moonshine:apply FileModelApply
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#apply).

```php
use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;
/**
 * @implements ApplyContract<File>
 */
final class FileModelApply implements ApplyContract
{
    /**
     * @param  File  $field
     */
    public function apply(FieldContract $field): Closure
    {
        return function (mixed $item) use ($field): mixed {
            $requestValue = $field->getRequestValue();

            $newValue = // ..

            return data_set($item, $field->getColumn(), $newValue);
        };
    }
}
```

Далее зарегистрируйте его для поля:

```php
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\AppliesRegisterContract;
use MoonShine\UI\Applies\AppliesRegister;
use App\MoonShine\Applies\FileModelApply;
use MoonShine\UI\Fields\File;
use MoonShine\Laravel\Resources\ModelResource;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     * @param  AppliesRegister  $applies
     *
     */
    public function boot(
        CoreContract $core,
        ConfiguratorContract $config,
        AppliesRegisterContract $applies,
    ): void
    {
        $applies
            // resource group, default ModelResource
            ->for(ModelResource::class)
            // type fields or filters
            ->fields()
            ->add(File::class, FileModelApply::class);
    }
}
```

<a name="fill"></a>
### Заполнение

Поля можно заполнить значениями, используя метод `fill()`. Более подробно о процессе заполнения поля можно прочитать в разделе [Основы > Изменить наполнение](/docs/{{version}}/fields/index#change-fill).

```php
fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0)
```

```php
Text::make('Title')
    ->fill('Some title')
```

#### Изменение логики наполнения поля

Для того чтобы изменить логику заполнения поля, можно использовать метод `changeFill()`.

```php
Select::make('Images')->options([
    '/images/1.png' => 'Picture 1',
    '/images/2.png' => 'Picture 2',
])
    ->multiple()
    ->changeFill(
        fn(Article $article, Select $ctx) => $article->images
            ->map(fn($value) => "https://cutcode.dev$value")
            ->toArray()
    );
```

В данном примере к путям изображений будет добавлено `https://cutcode.dev`.

#### Действия после наполнения поля

Для применения логики к уже наполненному полю можно использовать метод `afterFill()`.

> [!NOTE]
> Похожий по логике метод [when](#conditional-methods) срабатывает в момент создания экземпляра поля, когда оно еще не наполнено.
> Метод `afterFill` работает уже с наполненным полем.

```php
Select::make('Links')->options([
    '/images/1.png' => 'Picture 1',
    '/images/2.png' => 'Picture 2',
])
    ->multiple()
    ->afterFill(
        function(Select $ctx) {
            if(collect($ctx->toValue())->every(fn($value) => str_contains($value, 'cutcode.dev'))) {
                return $ctx->customWrapperAttributes(['class' => 'full-url']);
            }

            return $ctx;
        }
    )
```

<a name="on-change"></a>
### Методы onChange

С помощью методов `onChangeMethod()` и `onChangeUrl()` можно добавить логику при изменении значений полей.

> Методы onChangeUrl() или onChangeMethod() присутствуют у всех полей, кроме полей отношений HasOne и HasMany.

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
- `$events` - вызываемые [AlpineJS события](/docs/{{version}}/frontend/js#events) после успешного запроса,
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
- `$events` - вызываемые [AlpineJS события](/docs/{{version}}/frontend/js#events) после успешного запроса,
- `$callback` - js callback функция после получения ответа,
- `$page` - страница содержащая метод,
- `$resource` - ресурс содержащий метод.

```php
Switcher::make('Active')
    ->onChangeMethod('someMethod')
```

```php
use MoonShine\Laravel\MoonShineRequest;

public function someMethod(MoonShineRequest $request): void
{
    // Logic
}
```

<a name="change-render"></a>
### Изменение render поля

Чтобы полностью изменить render поля, можно воспользоваться методом `changeRender()`.

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
use MoonShine\UI\Fields\Enum;

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
use MoonShine\UI\Fields\Enum;

Enum::make('Status')
    ->attach(StatusEnum::class)
    ->modifyRawValue(fn(StatusEnum $raw, Enum $ctx) => $raw->value))
```

<a name="preview-edit"></a>
## Редактирование в режиме preview

> [!NOTE]
> Редактирование в режиме preview доступно для полей `Text`, `Number`, `Checkbox`, `Select`, `Date`

Для редактирования полей в режиме preview, например в таблице или в любом другом `IterableComponent`, существуют следующие методы.

<a name="update-on-preview"></a>
### updateOnPreview

Метод `updateOnPreview()` позволяет редактировать поле в режиме предварительного просмотра. После внесения изменений (по событию onChange), значение поля будет сохранено для конкретного элемента.

```php
public function updateOnPreview(
    ?Closure $url = null,
    ?ResourceContract $resource = null,
    mixed $condition = null,
    array $events = [],
)
```

 - `$url` - (опционально) url запроса,
 - `$resource` - (опционально) ресурс содержащий updateOnPreview,
 - `$condition` - (опционально) условие установки поля в режим updateOnPreview,
 - `$events` - (опционально) вызываемые [AlpineJS события](/docs/{{version}}/frontend/js#events) после успешного запроса.

> [!NOTE]
> Параметры не являются обязательными, но должны быть заданы, если поле находится вне ресурса или же вы хотите указать полностью свой endpoint (тогда и ресурс не нужен)

```php
Text::make('Name')->updateOnPreview()
```

<a name="with-update-row"></a>
### withUpdateRow

`withUpdateRow()` работает по аналогии с `updateOnPreview()`, но при этом может полностью обновить строку в таблице без перезагрузки страницы.

```php
public function withUpdateRow(string $component)
```

- `$component` - имя компонента, в котором присутствует данная строка.

```php
Text::make('Name')->withUpdateRow('index-table-post-resource')
```

`withUpdateRow()` может использовать все параметры `updateOnPreview()`, например для изменения url запроса, для этого их необходимо вызвать вместе.

```php
Text::make('Name')->updateOnPreview(url: '/my/url')->withUpdateRow()
```

### updateInPopover

Метод `updateInPopover()` работает аналогично методу `withUpdateRow()`, но теперь все значения для редактирования появляются в отдельном окне.

```php
public function updateInPopover(string $component)
```

- `$component` - имя компонента, в котором присутствует данная строка.

```php
Text::make('Name')->updateInPopover('index-table-post-resource')
```

> [!NOTE]
> Методы `updateOnPreview`, `withUpdateRow`, `updateInPopover` формируют нужные endpoints и передают методу `setUpdateOnPreviewUrl()`, который работает с [onChangeUrl()](#onchangeurl).

<a name="assets"></a>
## Ассеты

Для добавления ассетов к полю можно использовать метод `addAssets()`.

```php
use Illuminate\Support\Facades\Vite;
use MoonShine\AssetManager\Css;

Text::make('Name')
    ->addAssets([
        new Css(Vite::asset('resources/css/text-field.css'))
    ]),
```

Если вы реализуете собственное поле, то объявить набор ассетов в нем можно двумя способами.

1. Через метод `assets()`:

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

/**
 * @return list<AssetElementContract>
 */
protected function assets(): array
{
    return [
        Js::make('/js/custom.js'),
        Css::make('/css/styles.css')
    ];
}
```

2. Через метод `booted()`:

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

protected function booted(): void
{
    parent::booted();
    
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```

<a name="macroable"></a>
## Трейт Macroable

Всем полям доступен трейт `Illuminate\Support\Traits\Macroable` с методами `mixin` и `macro`.
С помощью этого трейта вы можете расширять возможности полей, добавляя в них новый функционал без использования наследования.

```php
use MoonShine\UI\Fields\Field;

Field::macro('myMethod', fn() => /*реализация*/)

Text::make()->myMethod()
```

или

```php
use MoonShine\UI\Fields\Field;

Field::mixin(new MyNewMethods())
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

> Поля поддерживающие реактивность: Text, Number, Checkbox, Select, Date и их наследующие.

```php
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\FormBuilder;

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
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Collections\Fields;

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

Когда на поле присутствует условие `showWhen`, то при скрытии этого поля, на его DOM элемент устанавливается значение `display:none` и удаляется атрибут name, чтобы значение этого скрытого поля не попадало в итоговый запрос.
Если вы не хотите, чтобы атрибут `name` удалялся, необходимо в вашем ресурсе установить флаг `$submitShowWhen` в значение `true`.

```php
class ArticleResource extends ModelResource
{
    protected bool $submitShowWhen = true;
    
    //...
}
```

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

Методы `showWhen` и `showWhenDate` поддерживают работу с вложенными полями, например для работы с полем `Json`.
Для обращения к вложенным полям используется точечная нотация.

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
В данном примере весь столбец `Parts` внутри `attributes` и весь столбец `Width` внутри `attributes.[n].settings` будет отображаться только если значение поля `category_id` равно 3.

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
> При использовании нескольких условий они объединяются логическим "И" (AND).
> Поле будет отображаться только если выполняются все заданные условия.

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

<a name="custom"></a>
## Кастомное поле

Вы можете создать собственное поле, со своим view и логикой и использовать его в административной панели MoonShine.
Для этого воспользуйтесь командой:

```shell
php artisan moonshine:field
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#field).
