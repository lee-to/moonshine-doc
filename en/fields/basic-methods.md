# Basic Methods

- [Description](#description)
- [Create](#create)
- [Field Display](#view)
  - [Label](#label)
  - [Hint](#hint)
  - [Link](#link)
  - [Badge](#badge)
  - [Horizontal Display](#horizontal)
  - [Wrapper](#wrapper)
  - [Sorting](#sortable)
  - [View Modes](#view-modes)
- [Attributes](#attributes)
  - [Required](#required-link)
  - [Disabled](#disabled-link)
  - [Readonly](#readonly-link)
  - [Other Attributes](#custom-attributes)
  - [Attributes for Wrapper Field](#custom-wrapper-attributes)
  - [Modifying the "name" Attribute](#name-attribute)
- [Modifying Field Value](#field-value)
  - [Default Value](#default)
  - [Nullable](#nullable)
  - [Changing Display](#custom-view)
  - [Hook Before Render](#on-before-render)
  - [Getting Value from Request](#request-value-resolver)
  - [Before and After Rendering](#before-and-after-render)
  - [Conditional Methods](#conditional-methods)
  - [Apply](#apply)
  - [Fill](#fill)
  - [onChange Methods](#on-change)
  - [Changing Field Render](#change-render)
  - [Methods for Values](#for-value)
- [Editing in Preview Mode](#preview-edit)
- [Assets](#assets)
- [Macroable Trait](#macroable)
- [Reactivity](#reactive)
- [Dynamic Display](#show-when)
  - [showWhen](#show-when)
  - [showWhenDate](#show-when-date)
  - [Nested Fields](#nested-fields)
  - [Multiple Conditions](#multiple-conditions)
  - [Supported Operators](#supported-operators)
- [Custom Field](#custom)

---

<a name="description"></a>
## Description

All fields inherit the base class `Field`, which provides basic methods for working with fields.

<a name="create"></a>
## Create Field

To create an instance of a field, the static method `make()` is used.

```php
use Closure;
use MoonShine\UI\Fields\Text;

Text::make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
```

- `$label` - the label or title of the field.
- `$column` - the relationship between the database column and the `name` attribute of the input field (e.g.: `description` > `<input name="description">`). If this field is a relationship, the name of the relationship is used (e.g.: countries).
- `$formatted` - a closure for formatting the field's value in preview mode (for BelongsTo and BelongsToMany, formats values for selection).

> HTML tags can be added in `$label`, they will not be escaped.

> If `$column` is not specified, the database field will be automatically determined based on `$label` (only for English).

Example of a closure `$formatted` for formatting a value.
```php
use MoonShine\UI\Fields\Text;

Text::make(
    'Name',
    'first_name',
    fn($item) => $item->first_name . ' ' . $item->last_name
)
```

> Fields that do not support `formatted`: `Json`, `File`, `Range`, `RangeSlider`, `DateRange`, `Select`, `Enum`, `HasOne`, `HasMany`.

<a name="view"></a>
## Field Display

<a name="label"></a>
### Label

If you need to change the `Label` after creating an instance of the field, you can use the `setLabel()` method.

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

To translate `Label`, you must pass a translation key as the name and add the `translatable()` method.

```php
translatable(string $key = '')
```

```php
Text::make('ui.Title')->translatable()
```

or

```php
Text::make('Title')->translatable('ui')
```

or

```php
Text::make(fn() => __('Title'))
```

#### insideLabel()

To wrap a field in a `<label>` tag, you can use the `insideLabel()` method.

```php
Text::make('Name')
    ->insideLabel(),
```

#### beforeLabel()

To display the Label after the input field, you can use the `beforeLabel()` method.

```php
Text::make('Name')
    ->beforeLabel(),
```

<a name="hint"></a>
### Hint

A hint with a description can be created using the `hint()` method.

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
### Link

You can add a link to the field (e.g., with instructions) using `link()`.

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

To display the field in preview mode as a badge, you need to use the `badge()` method.

```php
badge(string|Color|Closure|null $color = null)
```

Available colors:

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

or

```php
use MoonShine\UI\Fields\Field;

Text::make('Title')
    ->badge(fn($status, Field $field) => 'green')
```

<a name="horizontal"></a>
### Horizontal Display

The `horizontal()` method allows displaying the label and the field horizontally.

```php
horizontal()
```

```php
Text::make('Title')
    ->horizontal(),
```

<a name="wrapper"></a>
### Wrapper

Fields when displayed in forms use a special wrapper for headers, hints, links, etc. Sometimes there may arise a situation when it is necessary to display a field without additional elements. The `withoutWrapper()` method allows disabling the creation of a *wrapper*.

```php
withoutWrapper(mixed $condition = null)
```

```php
Text::make('Title')
    ->withoutWrapper()
```

<a name="sortable"></a>
### Sorting

To allow sorting the field in tables (on the main page), you must add the `sortable()` method.

```php
sortable(Closure|string|null $callback = null)
```

```php
Text::make('Title')->sortable()
```

The `sortable()` method can accept a database field name or a closure as a parameter.

```php
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('Author')->sortable('author_id'),

Text::make('Title')->sortable(function (Builder $query, string $column, string $direction) {
    $query->orderBy($column, $direction);
})
```

<a name="view-modes"></a>
### View Modes

You can read more about view modes in the section [Basics > Change View Mode](/docs/{{version}}/fields/index#change-mode).

#### Default Mode
To ensure that the field always works in "Default" mode (render as "input" field) regardless of context, use the `defaultMode()` method.

```php
Text::make('Title')->defaultMode()
```

#### Preview Mode

To ensure that the field always works in "Preview" mode regardless of context, use the `previewMode()` method.

```php
Text::make('Title')->previewMode()
```

#### RawMode

To ensure that the field always works in "RawMode" (render as the original state), use the `rawMode()` method.

```php
Text::make('Title')->rawMode()
```

<a name="attributes"></a>
## Attributes

Basic HTML attributes such as `required`, `disabled`, and `readonly` should be specified for the field through their respective methods.

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
### Other Attributes
To specify any other attributes, the `customAttributes()` method is used.

> [!NOTE]
> Fields are components, read more about attributes in the section [Component Attributes](/docs/{{version}}/components/attributes)

```php
customAttributes(array $attributes, bool $override = false)
```

- `$attributes` - an array of attributes
- `$override` - to add attributes to the field, use `merge`. If the attribute you want to add to the field already exists, it will not be added. Setting `$override = true` allows changing this behavior and overwriting the already added attribute.

```php
Password::make('Title')
    ->customAttributes(['autocomplete' => 'off'])
```

<a name="custom-wrapper-attributes"></a>
### Attributes for Wrapper Field

The `customWrapperAttributes()` method allows adding attributes for the wrapper field.

```php
customWrapperAttributes(array $attributes)
```

```php
Password::make('Title')
    ->customWrapperAttributes(['class' => 'mt-8'])
```

<a name="name-attribute"></a>
### Modifying the "name" Attribute

#### wrapName
To add a wrapper for the value of the `name` attribute, the `wrapName` method is used.

```php
Text::make('Name')->wrapName('options')
```

As a result, the name attribute will look like `<input name="options[name]>`. This is especially useful for setting up filters.

#### virtualName

Sometimes it is necessary to store two values in one input field. For example, under a display condition one of the fields may become invisible but still exist in the DOM and be sent with the request.

```php
File::make('image') // this is displayed in DOM under one condition
//...
File::make('image') // this is displayed in DOM under another condition
```

To change the name attribute of these fields, the `virtualName` method is used.

```php
File::make('image')->virtualColumn('image_1')
//...
File::make('image')->virtualColumn('image_2')
```

Then, for example in the onApply method, we can handle these fields as we see fit.

<a name="field-value"></a>
## Modifying Field Value

<a name="default"></a>
### Default Value

To specify a default value, the `default()` method is used.
```php
default(mixed $default)
```

```php
Text::make('Name')
    ->default('Default value')
```

or

```php
Enum::make('Status')
    ->attach(ColorEnum::class)
    ->default(ColorEnum::from('B')->value)
```

<a name="nullable"></a>
### Nullable

If you need to keep NULL as the default value for the field, then the `nullable()` method should be used.

```php
nullable(Closure|bool|null $condition = null)
```

```php
Password::make('Title')
    ->nullable()
```

<a name="custom-view"></a>
### Changing Display

When you need to change the view using the fluent interface, you can use the `customView()` method.

```php
customView(string $view, array $data = [])
```

```php
Text::make('Title')->customView('fields.my-custom-input')
```

The `changePreview()` method allows overriding the view for preview (everywhere except the form).

```php
use MoonShine\UI\Components\Thumbnails;
use MoonShine\UI\Fields\Text;

Text::make('Thumbnail')
  ->changePreview(function (?string $value, Text $field) {
      return Thumbnails::make($value);
  })
```

<a name="on-before-render"></a>
### Hook Before Render

If you need to access a field just before rendering, you can use the `onBeforeRender()` method.

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
### Getting Value from Request

The `requestValueResolver()` method allows overriding the logic for obtaining the value from the Request.

```php
/**
* @param  Closure(string|int|null $index, mixed $default, static $ctx): mixed  $resolver
*/
requestValueResolver(Closure $resolver)
```

> Relationship fields do not support the `requestValueResolver` method.

<a name="before-and-after-render"></a>
### Before and After Rendering

The `beforeRender()` and `afterRender()` methods allow displaying some information before and after the field, respectively.

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
### Conditional Methods

Components can be displayed conditionally using the `canSee()` method.

```php
Text::make('Name')
    ->canSee(function (Text $field) {
        return $field->toValue() !== 'hide';
    })
```
or for relationship fields:

```php
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('Item', 'item', resource: ItemResource::class)
    ->canSee(function (Comment $comment, BelongsTo $field) {
        // your condition
    })
,
```

The `when()` method implements a *fluent interface* and will execute the callback when the first argument passed to the method is true.

```php
when($value = null, ?callable $callback = null, ?callable $default = null)
```

```php
use MoonShine\UI\Fields\Field;

Text::make('Slug')
    ->when(fn() => true, fn(Field $field) => $field->locked())
```

The `unless()` method is the opposite of the `when()` method.

```php
unless($value = null, ?callable $callback = null, ?callable $default = null)
```

<a name="apply"></a>
### Apply

Each field has an `apply()` method that transforms the data. To override the default `apply` of a field, you can use the `onApply()` method. Read more about the *lifecycle of field application* in the section [Basics > Field Application Process](/docs/{{version}}/fields/index#apply).

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

To perform actions before "apply", you can use the `onBeforeApply()` method.

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
 */
function onBeforeApply(Closure $onBeforeApply)
```

To perform actions after "apply", you can use the `onAfterApply()` method.

```php
/**
 * @param  Closure(mixed, mixed, FieldContract): static  $onBeforeApply
 */
function onAfterApply(Closure $onBeforeApply)
```

#### Global Definition of Apply Logic

If you want to globally change the `apply` logic for a certain field, you can create an `apply` class and bind it to the necessary field.

First, create the `apply` class:

```shell
php artisan moonshine:apply FileModelApply
```

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

Then register it for the field:

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
### Fill

Fields can be filled with values using the `fill()` method. You can read more details about the filling process in the section [Basics > Change Fill](/docs/{{version}}/fields/index#change-fill).

```php
fill(mixed $value = null, ?DataWrapperContract $casted = null, int $index = 0)
```

```php
Text::make('Title')
    ->fill('Some title')
```

#### Changing the Field Filling Logic

To change the filling logic of a field, you can use the `changeFill()` method.

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

In this example, `https://cutcode.dev` will be added to the image paths.

#### Actions After Filling the Field

To apply logic to an already filled field, you can use the `afterFill()` method.

> [!NOTE]
> A similar logic method [when](#conditional-methods) triggers when creating a field instance, when it is not yet filled.
> The `afterFill` method works with an already filled field.

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
### onChange Methods

Using the `onChangeMethod()` and `onChangeUrl()` methods, you can add logic when field values change.

> The methods `onChangeUrl()` or `onChangeMethod()` are present on all fields except for HasOne and HasMany relationship fields.

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

- `$url` - the request url,
- `$method` - the method for the asynchronous request,
- `$events` - triggers [AlpineJS events](/docs/{{version}}/frontend/js#events) after a successful request,
- `$selector` - the selector of the element whose content will change,
- `$callback` - a js callback function after receiving a response.

```php
Switcher::make('Active')
    ->onChangeUrl(fn() => '/endpoint')
```

If you need to replace an area with HTML after a successful request, you can return HTML content or JSON with the key html in the response.

```php
Switcher::make('Active')
    ->onChangeUrl(fn() => '/endpoint', selector: '#my-selector')
```

#### onChangeMethod()

The `onChangeMethod()` method allows asynchronously calling a resource or page method when the field changes without the need to create additional controllers.

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

- `$method` - the name of the method,
- `$params` - parameters for the request,
- `$message` - messages,
- `$selector` - the selector of the element whose content will change,
- `$events` - triggers [AlpineJS events](/docs/{{version}}/frontend/js#events) after a successful request,
- `$callback` - a js callback function after receiving a response,
- `$page` - the page containing the method,
- `$resource` - the resource containing the method.

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
### Changing Field Render

To completely change the render of a field, you can use the `changeRender()` method.

```php
changeRender(Closure $callback)
```
In this example, the Select field transforms into text:
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
### Methods for Values

#### Obtaining Value from Raw

The `fromRaw()` method allows adding a closure to obtain the final value from the raw one.

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

#### Obtaining Raw Value

The `modifyRawValue()` method allows adding a closure to obtain a raw value.

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
## Editing in Preview Mode

> [!NOTE]
> Editing in preview mode is available for `Text`, `Number`, `Checkbox`, `Select`, `Date` fields.

For editing fields in preview mode, such as in a table or any other `IterableComponent`, there are the following methods.

<a name="update-on-preview"></a>
### updateOnPreview

The `updateOnPreview()` method allows editing a field in preview mode. After making changes (onChange event), the value of the field will be saved for the specific item.

```php
public function updateOnPreview(
    ?Closure $url = null,
    ?ResourceContract $resource = null,
    mixed $condition = null,
    array $events = [],
)
```

 - `$url` - (optional) request url,
 - `$resource` - (optional) resource containing updateOnPreview,
 - `$condition` - (optional) condition for setting the field to updateOnPreview mode,
 - `$events` - (optional) triggers [AlpineJS events](/docs/{{version}}/frontend/js#events) after a successful request.

> [!NOTE]
> Parameters are not mandatory but should be provided if the field is outside a resource or if you want to specify a completely custom endpoint (then the resource is not needed)

```php
Text::make('Name')->updateOnPreview()
```

<a name="with-update-row"></a>
### withUpdateRow

`withUpdateRow()` works similarly to `updateOnPreview()`, but can completely update the row in the table without reloading the page.

```php
public function withUpdateRow(string $component)
```

- `$component` - the name of the component that contains this row.

```php
Text::make('Name')->withUpdateRow('index-table-post-resource')
```

`withUpdateRow()` can use all parameters from `updateOnPreview()`, for example, to change the request url; they need to be called together.

```php
Text::make('Name')->updateOnPreview(url: '/my/url')->withUpdateRow()
```

### updateInPopover

The `updateInPopover()` method works similarly to the `withUpdateRow()` method, but now all values for editing appear in a separate window.

```php
public function updateInPopover(string $component)
```

- `$component` - the name of the component that contains this row.

```php
Text::make('Name')->updateInPopover('index-table-post-resource')
```

> [!NOTE]
> The methods `updateOnPreview`, `withUpdateRow`, and `updateInPopover` create the necessary endpoints and pass them to the `setUpdateOnPreviewUrl()` method, which works with [onChangeUrl()](#onchangeurl)

<a name="assets"></a>
## Assets

To add assets to the field, you can use the `addAssets()` method.

```php
use Illuminate\Support\Facades\Vite;
use MoonShine\AssetManager\Css;

Text::make('Name')
    ->addAssets([
        new Css(Vite::asset('resources/css/text-field.css'))
    ]),
```

If you are implementing your custom field, you can declare the asset set in it in two ways.

1. Through the `assets()` method:

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

2. Through the `booted()` method:

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
## Macroable Trait

All fields have access to the `Illuminate\Support\Traits\Macroable` trait with the `mixin` and `macro` methods. You can use this trait to extend the functionality of fields by adding new features without the need for inheritance.

```php
use MoonShine\UI\Fields\Field;

Field::macro('myMethod', fn() => /*implementation*/)

Text::make()->myMethod()
```

or

```php
use MoonShine\UI\Fields\Field;

Field::mixin(new MyNewMethods())
```

<a name="reactive"></a>
## Reactivity

The `reactive()` method allows reactively changing fields.

```php
reactive(
    ?Closure $callback = null,
    bool $lazy = false,
    int $debounce = 0,
    int $throttle = 0,
)
```

- `$callback` - callback function,
- `$lazy` - delayed function call,
- `$debounce` - time between function calls (ms.),
- `$throttle` - function call interval (ms.).

> Fields that support reactivity: Text, Number, Checkbox, Select, Date, and their descendants.

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

In this example, the slug field is created based on the title. The slug will be generated during the input process.

> [!WARNING]
> A reactive field can change the state of other fields but does not change its own state!

To change the state of the field initiating reactivity, it is convenient to use the parameters of the `callback` function.

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
## Dynamic Display

To change the display of fields depending on the values of other fields in real-time, without reloading the page and making server requests, the `showWhen` and `showWhenDate` methods are used.

<a name="show-when"></a>
### showWhen Method

The `showWhen` method allows setting a display condition for a field based on the value of another field.

```php
public function showWhen(
    string $column,
    mixed $operator = null,
    mixed $value = null
): static
```

- `$column` - the name of the field on which the display depends,
- `$operator` - comparison operator (optional),
- `$value` - value for comparison.

```php
Text::make('Name')
    ->showWhen('category_id', 1)
```

In this example, the field "Name" will only be displayed if the value of the field "category_id" is equal to 1.

> [!NOTE]
> If only two parameters are passed to the `showWhen` function, the `'='` operator is used by default.

```php
Text::make('Name')
    ->showWhen('category_id', 'in', [1, 2, 3])
```

In this example, the field "Name" will only be displayed if the value of the field "category_id" is equal to 1, 2, or 3.

When the showWhen condition is present on a field, hiding this field sets its DOM element to display:none and removes the name attribute so that the value of this hidden field does not end up in the final request. If you do not want the name attribute to be removed, you need to set the $submitShowWhen flag to true in your resource.

```php
class ArticleResource extends ModelResource
{
    protected bool $submitShowWhen = true;
    
    //...
}
```

<a name="show-when-date"></a>
### showWhenDate Method

The `showWhenDate` method allows setting a display condition for a field based on the value of a date-type field. The logic for working with dates has been separated into a specific method due to the specifics of converting and comparing date and datetime types on both the backend and frontend.

```php
public function showWhenDate(
    string $column,
    mixed $operator = null,
    mixed $value = null
): static
```

- `$column` - the name of the date field on which the display depends,
- `$operator` - comparison operator (optional),
- `$value` - date value for comparison.

```php
Text::make('Content')
    ->showWhenDate('created_at', '>', '2024-09-15 10:00')
```

In this example, the field "Content" will only be displayed if the value of the field "created_at" is greater than '2024-09-15 10:00'.

> [!NOTE]
> If only two parameters are passed to the `showWhenDate` function, the `'='` operator is used by default.

> [!NOTE]
> You can use any date format that can be recognized by the `strtotime()` function.

<a name="nested-fields"></a>
### Nested Fields

The `showWhen` and `showWhenDate` methods support working with nested fields, such as working with the `Json` field. Point notation is used to access nested fields.

```php
Text::make('Parts')
    ->showWhen('attributes.1.size', '!=', 2)
```

In this example, the field "Parts" will only be displayed if the value of the nested field "size" in the second element of the array "attributes" is not equal to 2.

showWhen also works with nested `Json` fields:

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
In this example, the entire column `Parts` inside `attributes` and the entire column `Width` inside `attributes.[n].settings` will only be displayed if the value of the field `category_id` is equal to 3.

<a name="multiple-conditions"></a>
### Multiple Conditions

The `showWhen` and `showWhenDate` methods can be called multiple times for the same field, allowing the specification of several display conditions.

```php
BelongsTo::make('Category', 'category', resource: CategoryResource::class)
    ->showWhenDate('created_at', '>', '2024-08-05 10:00')
    ->showWhenDate('created_at', '<', '2024-08-05 19:00')
```

In this example, the field "Category" will only be displayed if the value of the field "created_at" is within the range between '2024-08-05 10:00' and '2024-08-05 19:00'.

> [!NOTE]
> When using multiple conditions, they are combined logically with "AND". The field will be displayed only if all specified conditions are fulfilled.

<a name="supported-operators"></a>
### Supported Operators

- `=`
- `!=`
- `>`
- `<`
- `>=`
- `<=`
- `in`
- `not in`

> [!NOTE]
> The `in` operator checks if the value is in the array.
> The `not in` operator checks if the value is not in the array.

<a name="custom"></a>
## Custom Field

You can create a custom field with your view and logic and use it in the MoonShine administration panel.
To do this, use the command:

```shell
php artisan moonshine:field
```