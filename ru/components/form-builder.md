# FormBuilder

- [Основы](#basics)
- [Основное использование](#basic-usage)
- [Основные методы](#basic-methods)
  - [Имя формы](#form-name)
  - [Поля](#fields)
  - [Заполнение полей](#fill-fields)
  - [Приведение к типу](#type-cast)
  - [Кнопки](#buttons)
- [Настройка атрибутов](#attributes)
- [Асинхронный режим](#asynchronous-mode)
  - [Вызов методов](#calling-methods)
  - [Реактивность](#reactive)
- [Валидация](#validation)
  - [Отображение ошибок валидации](#displaying-validation-errors)
  - [Прекогнитивная валидация](#precognitive)
  - [Несколько форм одновременно](#multiple-forms)
- [Применение](#apply)
- [События](#events)
- [Использование в blade](#blade)
  - [Основы](#blade-basics)

---

<a name="basics"></a>
## Основы

Поля и компоненты в `FormBuilder` используются внутри форм, которые обрабатываются `FormBuilder`. Благодаря `FormBuilder` поля отображаются и заполняются данными.
`FormBuilder` используется на странице редактирования, а также для полей отношений, таких как `HasOne`.
Вы также можете использовать `FormBuilder` на своих собственных страницах, в модальных окнах или даже за пределами `MoonShine`.

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
## Основное использование

Пример использования `FormBuilder`:

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

- `action` - обработчик
- `method` - тип запроса,
- `fields` - поля и компоненты.
- `values` - значения полей.

<a name="basic-methods"></a>
## Основные методы

<a name="fields"></a>
### Поля

Метод `fields()` для объявления полей формы и компонентов:

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
### Имя формы

Метод `name()` позволяет установить уникальное имя для формы, через которое можно вызывать события.

```php
FormBuilder::make('/crud/update')
    ->name('main-form')
```

<a name="fill-fields"></a>
### Заполнение полей

Метод `fill()` для заполнения полей значениями:

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
### Приведение типов

Метод `cast()` для приведения значений формы к определенному типу. Поскольку по умолчанию поля работают с примитивными типами:

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

В этом примере мы приводим данные к формату модели `User`, используя `ModelCaster`.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [TypeCasts](/docs/{{version}}/advanced/type-casts)

<a name="fill-cast"></a>
#### Заполнение и приведение к типу

Метод `fillCast()` позволяет привести данные к определенному типу и сразу заполнить их значениями:

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

или

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
### Кнопки

Кнопки формы можно модифицировать и добавлять.

Для настройки кнопки "submit" используйте метод `submit()`.

```php
submit(string $label, array $attributes = [])
```

- `label` - название кнопки,
- `attributes` - дополнительные атрибуты

```php
FormBuilder::make('/crud/update')
    ->submit(label: 'Нажми меня', attributes: ['class' => 'btn-primary'])
```

Метод `hideSubmit()` позволяет скрыть кнопку `submit`.

```php
FormBuilder::make('/crud/update')
    ->hideSubmit()
```

Для добавления новых кнопок на основе `ActionButton` используйте метод `buttons()`

```php
buttons(iterable $buttons = [])
```

```php
FormBuilder::make('/crud/update')
    ->buttons([
        ActionButton::make('Удалить', route('name.delete'))
    ])
```

<a name="attributes"></a>
## Настройка атрибутов

Вы можете установить любые html атрибуты для формы с помощью метода `customAttributes()`.

```php
FormBuilder::make()
    ->customAttributes(['class' => 'custom-form'])
```


<a name="asynchronous-mode"></a>
## Асинхронный режим

Если необходимо отправить форму асинхронно, используйте метод `async()`.

```php
async(
    Closure|string|null $url = null,
    string|array|null $events = null,
    ?AsyncCallback $callback = null,
)
```

- `url` - url запроса (по умолчанию запрос отправляется по url action),
- `events` - события, поднимаемые после успешного запроса,
- `callback` - js callback функция после получения ответа.

```php
FormBuilder::make('/crud/update')
    ->async()
```

После успешного запроса можно вызвать события, добавив параметр `events`.

```php
FormBuilder::make('/crud/update')
        ->name('main-form')
        ->async(events: [
          AlpineJs::event(JsEvent::TABLE_UPDATED, 'crud-table'),
          AlpineJs::event(JsEvent::FORM_RESET, 'main-form'),
        ])
```

Список событий для `FormBuilder`:

- `JsEvent::FORM_SUBMIT` - submit формы,
- `JsEvent::FORM_RESET` - сброс значений формы по её имени,

> [!NOTE]
> Рецепт [При успешном запросе форма обновляет таблицу и сбрасывает значения](/docs/{{version}}/recipes/form-with-events)

> [!WARNING]
> Метод `async()` должен идти после метода `name()`!

<a name="calling-methods"></a>
### Вызов методов

`asyncMethod()` позволяет указать имя метода в ресурсе и вызвать его асинхронно при отправке `FormBuilder` без необходимости создания дополнительных контроллеров.

```php
FormBuilder::make()->asyncMethod('updateSomething'),
```

```php
// С уведомлением
public function updateSomething(MoonShineRequest $request): MoonShineJsonResponse
{
    // $request->getResource();
    // $request->getResource()->getItem();
    // $request->getPage();

    return MoonShineJsonResponse::make()->toast('Мое сообщение', ToastType::SUCCESS);
}

// Редирект
public function updateSomething(MoonShineRequest $request): MoonShineJsonResponse
{
    return MoonShineJsonResponse::make()->redirect('/');
}

// Редирект
public function updateSomething(MoonShineRequest $request): RedirectResponse
{
    return back();
}

// Исключение
public function updateSomething(MoonShineRequest $request): void
{
    throw new \Exception('Мое сообщение');
}
```

<a name="reactive"></a>
### Реактивность

По умолчанию полям внутри формы доступна реактивность, но если форма находится вне ресурса, 
тогда реактивность будет недоступна, так как форма не знает куда отправлять запросы. В случае использования формы вне ресурсов вы можете указать реактивный URL самостоятельно:

```php
FormBuilder::make()->reactiveUrl(fn(FormBuilder $form) => $form->getCore()->getRouter()->getEndpoints()->reactive($page, $resource, $extra))
```

<a name="validation"></a>
## Валидация

<a name="displaying-validation-errors"></a>
### Отображение ошибок валидации

По умолчанию ошибки валидации отображаются в верхней части формы.

Метод `errorsAbove(bool $enable = true)` используется для управления отображением ошибок валидации в верхней части формы. Он позволяет включить или отключить эту функцию.

```php
FormBuilder::make('/crud/update')
    ->errorsAbove(false)
```

<a name="precognitive"></a>
### Прекогнитивная валидация

Если необходимо сначала выполнить прекогнитивную валидацию, вам нужен метод `precognitive()`.

```php
FormBuilder::make('/crud/update')
    ->precognitive()
```

<a name="multiple-forms"></a>
### Несколько форм одновременно

Если у вас есть несколько форм на одной странице и они не в режиме `async`, то вам также необходимо указать наименование для `errorBag` в `FormRequest` или в `Controller`:

[Подробнее о наименования errorBag](https://laravel.com/docs/validation#named-error-bags)

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
## Применение

Метод `apply()` в `FormBuilder` итерирует все поля формы и вызывает их методы `apply`.

```php
apply(
    Closure $apply,
    ?Closure $default = null,
    ?Closure $before = null,
    ?Closure $after = null,
    bool $throw = false,
)
```
- `$apply` - функция обратного вызова;
- `$default` - применение для поля по умолчанию;
- `$before` - функция обратного вызова перед применением;
- `$after` - функция обратного вызова после применения;
- `$throw` - выбрасывать исключения.

#### Примеры

Необходимо сохранить данные всех полей *FormBuilder* в контроллере:

```php
$form->apply(fn(Model $item) => $item->save());
```

Более сложный вариант, с указанием событий до и после сохранения:

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
## Отправка событий

Для отправки javascript событий можно использовать метод `dispatchEvent()`.

```php
dispatchEvent(array|string $events)
```

```php
FormBuilder::make()
    ->dispatchEvent(AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default')),
```

По умолчанию при вызове события с запросом будут отправлены все данные формы.
Если форма большая, то может потребоваться исключить набор полей.
Исключить можно через параметр `exclude`:

```php
->dispatchEvent(
    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default'),
    exclude: ['text', 'description']
)
```

Также можно полностью исключить отправку данных через параметр `withoutPayload`:

```php
->dispatchEvent(
    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'default'),
    withoutPayload: true
)
```

<a name="submit-event"></a>
### Событие "Submit"

Для отправки формы можно вызвать событие *Submit*.

```php
AlpineJs::event(JsEvent::FORM_SUBMIT, 'componentName')
```

#### Пример вызова события на странице формы

```php
protected function formButtons(): ListOf
{
    return parent::formButtons()->add(ActionButton::make('Сохранить')->dispatchEvent(AlpineJs::event(JsEvent::FORM_SUBMIT, $this->uriKey())));
}
```

> [!NOTE]
> Для получения дополнительной информации о js событиях обратитесь к разделу [Events](/docs/{{version}}/frontend/events).

<a name="blade"></a>
## Использование в blade

<a name="blade-basics"></a>
### Основы

Формы можно создавать с помощью компонента `moonshine::form`.

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
