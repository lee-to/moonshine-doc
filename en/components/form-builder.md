# FormBuilder

- [Basics](#basics)
- [Basic Usage](#basic-usage)
- [Basic Methods](#basic-methods)
  - [Form Name](#form-name)
  - [Fields](#fields)
  - [Fill Fields](#fill-fields)
  - [Type Cast](#type-cast)
  - [Buttons](#buttons)
- [Attribute Configuration](#attributes)
- [Asynchronous Mode](#asynchronous-mode)
  - [Calling Methods](#calling-methods)
  - [Reactivity](#reactive)
  - [Field values](#fields-values)
  - [Selectors](#selectors)
- [Validation](#validation)
  - [Displaying Validation Errors](#displaying-validation-errors)
  - [Pre-Cognitive Validation](#precognitive)
  - [Multiple Forms Simultaneously](#multiple-forms)
- [Apply](#apply)
- [Dispatch Events](#dispatch-events)
- [Using in Blade](#blade)
  - [Basics](#blade-basics)

---

<a name="basics"></a>
## Basics

Fields and components in `FormBuilder` are used within forms that are processed by `FormBuilder`. Thanks to `FormBuilder`, fields are displayed and filled with data. `FormBuilder` is used on the edit page, as well as for relationship fields such as `HasOne`. You can also use `FormBuilder` on your own pages, in modal windows, or even outside of `MoonShine`.

~~~tabs
tab: Class
```php
use MoonShine\UI\Components\FormBuilder;

FormBuilder::make(
  string $action = '',
  FormMethod $method = FormMethod::POST,
  FieldsContract|iterable $fields = [],
  mixed $values = []
)
```
tab: Blade
```blade
<x-moonshine::form name="crud-edit">
    <x-moonshine::form.input
        name="title"
        placeholder="Title"
        value=""
    />

    <x-slot:buttons>
        <x-moonshine::form.button type="reset">Cancel</x-moonshine::form.button>
        <x-moonshine::form.button class="btn-primary">Submit</x-moonshine::form.button>
    </x-slot:buttons>
</x-moonshine::form>
```
~~~

<a name="basic-usage"></a>
## Basic Usage

Example usage of `FormBuilder`:

```php
FormBuilder::make(
    action:'/crud/update',
    method: FormMethod::POST,
    fields: [
        Hidden::make('_method')->setValue('put')
        Text::make('Text')
    ],
    values: ['text' => 'Value']
)

// or

FormBuilder::make()
    ->action('/crud/update')
    ->method(FormMethod::POST)
    ->fields([
        Hidden::make('_method')->setValue('put')
        Text::make('Text')
    ])
    ->fill(['text' => 'Value'])
```

- `action` - handler
- `method` - request type,
- `fields` - fields and components.
- `values` - field values.

<a name="basic-methods"></a>
## Basic Methods

<a name="fields"></a>
### Fields

The method `fields()` is used to declare form fields and components:

```php
fields(FieldsContract|Closure|iterable $fields)
```

```php
FormBuilder::make('/crud/update')
    ->fields([
        Heading::make('Title'),
        Text::make('Text'),
    ])
```

<a name="form-name"></a>
### Form Name

The method `name()` allows you to set a unique name for the form, through which events can be triggered.

```php
FormBuilder::make('/crud/update')
    ->name('main-form')
```

<a name="fill-fields"></a>
### Fill Fields

The method `fill()` is used to fill fields with values:

```php
fill(mixed $values = [])
```

```php
FormBuilder::make('/crud/update')
    ->fields([
        Heading::make('Title'),
        Text::make('Text'),
    ])
    ->fill(['text' => 'value'])
```

<a name="casting"></a>
### Type Cast

The method `cast()` is used to cast form values to a specific type. Since fields work with primitive types by default:

```php
cast(DataCasterContract $cast)
```

```php
use MoonShine\Laravel\TypeCasts\ModelCaster;

FormBuilder::make('/crud/update')
    ->fields([
        Heading::make('Title'),
        Text::make('Text'),
    ])
    ->values(
        ['text' => 'value'],
    )
    ->cast(new ModelCaster(User::class))
```

In this example, we cast the data to the `User` model format using `ModelCaster`.

> [!NOTE]
> For more detailed information, refer to the [TypeCasts](/docs/{{version}}/advanced/type-casts) section.

<a name="fill-cast"></a>
#### Fill and Type Cast

The method `fillCast()` allows you to cast data to a specific type and fill them with values at the same time:

```php
fillCast(mixed $values, DataCasterContract $cast)
```

```php
use MoonShine\TypeCasts\ModelCaster;

FormBuilder::make('/crud/update')
    ->fields([
        Heading::make('Title'),
        Text::make('Text'),
    ])
    ->fillCast(
        ['text' => 'value'],
        new ModelCaster(User::class)
    )
```

or

```php
use MoonShine\TypeCasts\ModelCaster;

FormBuilder::make('/crud/update')
    ->fields([
        Heading::make('Title'),
        Text::make('Text'),
    ])
    ->fillCast(
        User::query()->first(),
        new ModelCaster(User::class)
    )
```

<a name="buttons"></a>
### Buttons

Form buttons can be modified and added.

To configure the "submit" button, use the method `submit()`.

```php
submit(string $label, array $attributes = [])
```

- `label` - button name,
- `attributes` - additional attributes

```php
FormBuilder::make('/crud/update')
    ->submit(label: 'Click me', attributes: ['class' => 'btn-primary'])
```

The method `hideSubmit()` allows you to hide the `submit` button.

```php
FormBuilder::make('/crud/update')
    ->hideSubmit()
```

To add new buttons based on `ActionButton`, use the method `buttons()`

```php
buttons(iterable $buttons = [])
```

```php
FormBuilder::make('/crud/update')
    ->buttons([
        ActionButton::make('Delete', route('name.delete'))
    ])
```

<a name="attributes"></a>
## Attribute Configuration

You can set any HTML attributes for the form using the method `customAttributes()`.

```php
FormBuilder::make()
    ->customAttributes(['class' => 'custom-form'])
```

<a name="asynchronous-mode"></a>
## Asynchronous Mode

If you need to submit the form asynchronously, use the method `async()`.

```php
async(
    Closure|string|null $url = null,
    string|array|null $events = null,
    ?AsyncCallback $callback = null,
)
```

- `url` - request URL (by default, the request is sent to the action URL),
- `events` - events triggered after a successful request,
- `callback` - JS callback function after receiving a response.

```php
FormBuilder::make('/crud/update')
    ->async()
```

After a successful request, you can trigger events by adding the `events` parameter.

```php
FormBuilder::make('/crud/update')
        ->name('main-form')
        ->async(events: [
          AlpineJs::event(JsEvent::TABLE_UPDATED, 'crud-table'),
          AlpineJs::event(JsEvent::FORM_RESET, 'main-form'),
        ])
```

Event list for `FormBuilder`:

- `JsEvent::FORM_SUBMIT` - submit the form,
- `JsEvent::FORM_RESET` - reset the form values by its name,

> [!NOTE]
> Recipe [On successful request, the form updates the table and resets values](/docs/{{version}}/recipes/form-with-events).

> [!WARNING]
> The `async()` method must come after the `name()` method!

<a name="calling-methods"></a>
### Calling Methods

`asyncMethod()` allows you to specify the method name in the resource and call it asynchronously when submitting the `FormBuilder` without the need to create additional controllers.

```php
FormBuilder::make()->asyncMethod('updateSomething'),
```

```php
// With notification
public function updateSomething(MoonShineRequest $request): MoonShineJsonResponse
{
    // $request->getResource();
    // $request->getResource()->getItem();
    // $request->getPage();

    return MoonShineJsonResponse::make()->toast('My message', ToastType::SUCCESS);
}

// Redirect
public function updateSomething(MoonShineRequest $request): MoonShineJsonResponse
{
    return MoonShineJsonResponse::make()->redirect('/');
}

// Redirect
public function updateSomething(MoonShineRequest $request): RedirectResponse
{
    return back();
}

// Exception
public function updateSomething(MoonShineRequest $request): void
{
    throw new \Exception('My message');
}
```

<a name="reactive"></a>
### Reactivity

By default, fields inside the form are reactive, but if the form is outside the resource, then reactivity will not be available, as the form does not know where to send requests. In the case of using the form outside of resources, you can specify the reactive URL yourself:

```php
FormBuilder::make()->reactiveUrl(fn(FormBuilder $form) => $form->getCore()->getRouter()->getEndpoints()->reactive($page, $resource, $extra))
```

<a name="fields-values"></a>
### Field values

If you are using your own controller handler, `asyncMethod` or response handler,
then using `MoonShineJsonResponse` you have the opportunity to replace the values of form fields using the selector:

```php
public function formAction(): MoonShineJsonResponse
{
  return MoonShineJsonResponse::make()->fieldsValues([
    '.title' => 'Hello',
  ]);    
}

protected function components(): iterable
{
    return [
        FormBuilder::make()
            ->asyncMethod('formAction')
            ->fields([
                Text::make('Title')->class('title'),
            ]),
    ];
}
```

<a name="selectors"></a>
### Selectors

You can also replace *HTML* areas by selectors using the `asyncSelector` method:

```php
public function formAction(): MoonShineJsonResponse
{
  return MoonShineJsonResponse::make()->html([
    '.some-class1' => time(),
    '.some-class2' => time(),
  ]);    
}

protected function components(): iterable
{
    return [
        FormBuilder::make()
            ->asyncMethod('formAction')
            ->asyncSelector(['.some-class1','.some-class2'])
            ->fields([
                Div::make([])->class('some-class1'),
                Div::make([])->class('some-class2'),
            ]),
    ];
}
```

<a name="validation"></a>
## Validation

<a name="displaying-validation-errors"></a>
### Displaying Validation Errors

By default, validation errors are displayed at the top of the form.

The method `errorsAbove(bool $enable = true)` is used to control the display of validation errors at the top of the form. It allows you to enable or disable this feature.

```php
FormBuilder::make('/crud/update')
    ->errorsAbove(false)
```

<a name="precognitive"></a>
### Pre-Cognitive Validation

If you need to perform pre-cognitive validation first, you need the method `precognitive()`.

```php
FormBuilder::make('/crud/update')
    ->precognitive()
```

<a name="multiple-forms"></a>
### Multiple Forms Simultaneously

If you have multiple forms on one page and they are not in `async` mode, you also need to specify a name for the `errorBag` in `FormRequest` or in `Controller`:

[Learn more about errorBag naming](https://laravel.com/docs/validation#named-error-bags)

```php
FormBuilder::make(route('multiple-forms.one'))
    ->name('formOne'),

FormBuilder::make(route('multiple-forms.two'))
    ->name('formTwo'),

FormBuilder::make(route('multiple-forms.three'))
    ->name('formThree')

class FormOneFormRequest extends FormRequest
{
    protected $errorBag = 'formOne';

    // ..
}

class FormTwoFormRequest extends FormRequest
{
    protected $errorBag = 'formTwo';

    // ..
}

class FormThreeFormRequest extends FormRequest
{
    protected $errorBag = 'formThree';

    // ..
}
```

<a name="apply"></a>
## Apply

The method `apply()` in `FormBuilder` iterates over all form fields and calls their `apply` methods.

```php
apply(
    Closure $apply,
    ?Closure $default = null,
    ?Closure $before = null,
    ?Closure $after = null,
    bool $throw = false,
)
```
- `$apply` - callback function;
- `$default` - apply for the default field;
- `$before` - callback function before applying;
- `$after` - callback function after applying;
- `$throw` - throw exceptions.

#### Examples

You need to save data from all fields of the *FormBuilder* in the controller:

```php
$form->apply(fn(Model $item) => $item->save());
```

A more complex option, specifying events before and after saving:

```php
$form->apply(
    static fn(Model $item) => $item->save(),
    before: function (Model $item) {
        if (! $item->exists) {
            $item = $this->beforeCreating($item);
        }

        if ($item->exists) {
            $item = $this->beforeUpdating($item);
        }

        return $item;
    },
    after: function (Model $item) {
        $wasRecentlyCreated = $item->wasRecentlyCreated;

        $item->save();

        if ($wasRecentlyCreated) {
            $item = $this->afterCreated($item);
        }

        if (! $wasRecentlyCreated) {
            $item = $this->afterUpdated($item);
        }

        return $item;
    },
    throw: true
);
```

<a name="dispatch-events"></a>
## Dispatch Events

To dispatch JavaScript events, you can use the method `dispatchEvent()`.

```php
dispatchEvent(array|string $events)
```

```php
FormBuilder::make()
    ->dispatchEvent(AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default')),
```

By default, when calling an event with a request, all form data will be sent. If the form is large, you may need to exclude a set of fields. Exclude can be done through the `exclude` parameter:

```php
->dispatchEvent(
    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default'),
    exclude: ['text', 'description']
)
```

You can also completely exclude data from being sent through the `withoutPayload` parameter:

```php
->dispatchEvent(
    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default'),
    withoutPayload: true
)
```

<a name="submit-event"></a>
### Submit Event

To submit the form, you can call the *Submit* event.

```php
AlpineJs::event(JsEvent::FORM_SUBMIT, 'componentName')
```

#### Example of calling the event on the form page

```php
protected function formButtons(): ListOf
{
    return parent::formButtons()->add(ActionButton::make('Save')->dispatchEvent(AlpineJs::event(JsEvent::FORM_SUBMIT, $this->uriKey())));
}
```

> [!NOTE]
> For additional information on JS events, refer to the [Events](/docs/{{version}}/frontend/events) section.

<a name="blade"></a>
## Using in Blade

<a name="blade-basics"></a>
### Basics

Forms can be created using the `moonshine::form` component.

```php
<x-moonshine::form
name="crud-form"
:errors="$errors"
precognitive
>
    <x-moonshine::form.input
        name="title"
        placeholder="Title"
        value=""
    />
    <x-slot:buttons>
        <x-moonshine::form.button type="reset">Cancel</x-moonshine::form.button>
        <x-moonshine::form.button class="btn-primary">Submit</x-moonshine::form.button>
    </x-slot:buttons>
</x-moonshine::form>
```