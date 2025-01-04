# Форма

- [Основы](#basics)
- [Валидация](#validation)
  - [Сообщения](#messages)
  - [Подготовка входных данных для проверки](#prepare)
  - [Отображение ошибок](#display-errors)
  - [Precognition](#precognitive)
- [Кнопки](#buttons)
- [Асинхронный режим](#async)
- [Модификаторы](#modifiers)
  - [Компоненты](#components)

---

<a name="basics"></a>
## Основы

В `CrudResource`(`ModelResource`) на страницe `formPage` используется `FormBuilder`, поэтому мы рекомендуем вам также изучить раздел документации [FormBuilder](/docs/{{version}}/components/form-builder).

<a name="validation"></a>
## Валидация

Валидация так же проста, как и в классах `FormRequests` из `Laravel`.

Достаточно добавить правила в метод `rules()` ресурса модели обычным способом.

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
## Сообщения

Используя метод `validationMessages()`, вы можете создать свои собственные сообщения об ошибках валидации.

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
            'email.required' => 'Требуется email'
        ];
    }

    //...
}
```

<a name="prepare"></a>
### Подготовка входных данных для проверки

Если вам нужно подготовить или очистить какие-либо данные из запроса перед применением правил валидации, вы можете использовать метод `prepareForValidation()`.

```php
use MoonShine\Laravel\Resources\ModelResource;

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
### Отображение ошибок

По умолчанию ошибки валидации отображаются в верхней части формы.

Свойство `$errorsAbove` используется для управления отображением ошибок валидации в верхней части формы.

> [!NOTE]
> Актуально только если "Асинхронный режим" выключен

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

Если необходимо предварительно выполнить `precognition` валидацию, необходим метод `precognitive()`.

[Подробности в документации Laravel](https://laravel.com/docs/precognition)

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
## Кнопки

Для добавления кнопок используйте `ActionButton` и метод `formButtons` в ресурсе.

> [!NOTE]
> Подробнее о [ActionButton](/docs/{{version}}/components/action-button)

```php
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

protected function formButtons(): ListOf
{
    return parent::formButtons()->add(ActionButton::make('Ссылка', '/endpoint'));
}
```

<a name="async"></a>
## Асинхронный режим

По умолчанию в `ModelResource` включен "Асинхронный режим", но если вам требуется его выключить, то установить свойство `$isAsync` = false

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
## Модификация

<a name="components"></a>
### Компоненты

Вы можете полностью заменить или модифицировать `FormBuilder` ресурса для страницы редактирования. Для этого воспользуйтесь методом `modifyFormComponent`

```php
use MoonShine\Contracts\UI\ComponentContract;

public function modifyFormComponent(ComponentContract $component): ComponentContract
{
    return parent::modifyFormComponent($component)->customAttributes([
        'data-my-attr' => 'value'
    ]);
}
```
