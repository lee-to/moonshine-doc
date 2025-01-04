# Form

- [Basics](#basics)
- [Validation](#validation)
  - [Messages](#messages)
  - [Preparing Input Data for Validation](#prepare)
  - [Displaying Errors](#display-errors)
  - [Precognition](#precognitive)
- [Buttons](#buttons)
- [Asynchronous Mode](#async)
- [Modifiers](#modifiers)
  - [Components](#components)

---

<a name="basics"></a>
## Basics

In `CrudResource` (`ModelResource`) on the `formPage`, `FormBuilder` is used, so we recommend that you also check out the [FormBuilder](/docs/{{version}}/components/form-builder) section of the documentation.

<a name="validation"></a>
## Validation

Validation is as simple as in `FormRequests` classes from `Laravel`.

Just add the rules in the `rules()` method of the model resource in the usual way.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'min:5']
        ];
    }

    //...
}
```

![validation](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/validation.png)
![validation_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/validation_dark.png)

<a name="messages"></a>
## Messages

Using the `validationMessages()` method, you can create your own validation error messages.

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    public function validationMessages(): array
    {
        return [
            'email.required' => 'Email is required'
        ];
    }

    //...
}
```

<a name="prepare"></a>
### Preparing Input Data for Validation

If you need to prepare or clean any data from the request before applying validation rules, you can use the `prepareForValidation()` method.

```php
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    public function prepareForValidation(): void
    {
        request()?->merge([
            'email' => request()
                ?->string('email')
                ->lower()
                ->value()
        ]);
    }

    //...
}
```

<a name="display-errors"></a>
### Displaying Errors

By default, validation errors are displayed at the top of the form.

The `$errorsAbove` property is used to control the display of validation errors at the top of the form.

> [!NOTE]
> Relevant only if "Asynchronous Mode" is turned off

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
  // ..

  protected bool $errorsAbove = true;

  // ..
}
```

<a name="precognitive"></a>
### Precognition

If it is necessary to perform `precognition` validation in advance, you need the `precognitive()` method.

[Details in the Laravel documentation](https://laravel.com/docs/precognition)

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected bool $isPrecognitive = true;

    // ...
}
```

<a name="buttons"></a>
## Buttons

To add buttons, use `ActionButton` and the `formButtons` method in the resource.

> [!NOTE]
> More about [ActionButton](/docs/{{version}}/components/action-button)

```php
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

protected function formButtons(): ListOf
{
    return parent::formButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

<a name="async"></a>
## Asynchronous Mode

By default, "Asynchronous Mode" is enabled in `ModelResource`, but if you need to turn it off, set the `$isAsync` property to false.

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...

    protected bool $isAsync = false;

    // ...
}
```

<a name="modify"></a>
## Modifiers

<a name="components"></a>
### Components

You can fully replace or modify the resource's `FormBuilder` for the edit page. To do this, use the `modifyFormComponent` method.

```php
use MoonShine\Contracts\UI\ComponentContract;

public function modifyFormComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyFormComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```