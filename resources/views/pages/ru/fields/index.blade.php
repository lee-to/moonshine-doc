<x-page
    title="Основы"
    :sectionMenu="[
        'Разделы' => [
            ['url' => '#make', 'label' => 'Make'],
            ['url' => '#formatted', 'label' => 'Форматирование значения'],
            ['url' => '#label', 'label' => 'Label'],
            ['url' => '#attributes', 'label' => 'Аттрибуты'],
            ['url' => '#hint', 'label' => 'Подсказка'],
            ['url' => '#link', 'label' => 'Ссылка'],
            ['url' => '#nullable', 'label' => 'Nullable'],
            ['url' => '#sortable', 'label' => 'Сортировка'],
            ['url' => '#badge', 'label' => 'Badge'],
            ['url' => '#hide-show', 'label' => 'Отображение'],
            ['url' => '#show-when', 'label' => 'Динамическое отображение'],
            ['url' => '#custom-view', 'label' => 'Изменение отображения'],
            ['url' => '#when-unless', 'label' => 'Методы по условию'],
            ['url' => '#fill', 'label' => 'Заполнение'],
            ['url' => '#apply', 'label' => 'Apply'],
            ['url' => '#events', 'label' => 'События'],
            ['url' => '#assets', 'label' => 'Assets'],
        ]
    ]"
>

<x-p>
    Полям отводится важнейшая роль в админ-панели <strong>MoonShine</strong>.<br />
    Они используются в <code>FormBuilder</code> для построения форм, в <code>TableBuilder</code> для создания таблиц,
    а также в формировании фильтра для <code>ModelResource</code>.
    Их можно использовать в ваших кастомных страницах и даже вне админ-панели.<br />
    Поля в <strong>MoonShine</strong> не привязаны к модели,
    поэтому спектр их применения ограничивается только вашей фантазией.<br />
</x-p>
<x-p>
    Для удобства у полей реализован <em>fluent интерфейс</em>.
</x-p>

<x-sub-title id="make">Make</x-sub-title>

<x-p>
    Для создании экземпляра поля используется статический метод <code>make()</code>.
</x-p>

<x-code language="php">
    Text::make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
</x-code>

<x-p>
    $label - лейбл, заголовок поля<br>
    $field - поле в базе (например name) или отношение (например countries)<br>
    $formatted - замыкание для форматирования значения поля при превью (везде кроме формы).
</x-p>

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    Если не указать <code>$field</code>,
    то поле в базе данных будет определено автоматически на основе <code>$label</code>.
</x-moonshine::alert>

<x-sub-title id="formatted">Форматирование значения</x-sub-title>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make(
            'Name',
            'first_name',
            fn($item) => $item->first_name . ' ' . $item->last_name // [tl! focus]
        )
    ];
}

//...
</x-code>

<x-sub-title id="label">Label</x-sub-title>

<x-p>
    Если необходимо изменить <em>Label</em>, можно воспользоваться методом <code>setLabel()</code>
</x-p>

<x-code language="php">
setLabel(Closure|string $label)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Slug::make('Slug')
            ->setLabel(
                fn() => $this->getItem()?->exists
                    ? 'Slug (do not change)'
                    : 'Slug'
            ) // [tl! focus:-4]
    ];
}

//...
</x-code>

<x-p>
    Для перевода <em>Label</em> необходимо в качестве названия передать ключ перевода и
    добавить метод <code>translatable()</code>
</x-p>

<x-code language="php">
translatable(string $key = '')
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')->translatable('ui') // [tl! focus]
    ];
}

//...
</x-code>

<x-p>или</x-p>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('ui.Title')->translatable() // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="attributes">Аттрибуты</x-sub-title>

<x-p>
    Основные html аттрибуты, такие как <code>required</code>,
    <code>disabled</code> и <code>readonly</code>, у поля необходимо задавать через соответствующие методы.
</x-p>

<x-code language="php">
disabled(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
hidden(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
required(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->disabled() // [tl! focus]
            ->hidden() // [tl! focus]
            ->readonly() // [tl! focus]
    ];
}

//...
</x-code>

<x-p>
    Возможность указать и любые другие аттрибуты используя метод <code>customAttributes()</code>.
</x-p>

<x-code language="php">
customAttributes(array $attributes)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Password::make('Title')
            ->customAttributes(['autocomplete' => 'off']) // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="hint">Подсказка</x-sub-title>

<x-p>
    Полю можно добавить подсказку с описанием вызвав метод <code>hint()</code>
</x-p>

<x-code language="php">
hint(string $hint)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Number::make('Rating')
            ->hint('From 0 to 5') // [tl! focus]
            ->min(0)
            ->max(5)
            ->stars()
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/hint.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/hint_dark.png') }}"></x-image>

<x-sub-title id="link">Ссылка</x-sub-title>

<x-p>
    Полю можно добавить ссылку (например с инструкциями)
    <code>link()</code>.
</x-p>

<x-code language="php">
link(
    string|Closure $link,
    string|Closure $name = '',
    ?string $icon = null,
    bool $withoutIcon = false,
    bool $blank = false
)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Link')
            ->link('https://cutcode.dev', 'CutCode', blank: true) // [tl! focus]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/link.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/link_dark.png') }}"></x-image>

<x-sub-title id="nullable">Nullable</x-sub-title>

<x-p>
    Если необходимо у поля по умолчанию сохранять NULL, то необходимо использовать метод <code>nullable()</code>.
</x-p>

<x-code language="php">
nullable(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Password::make('Title')
            ->nullable() // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="sortable">Сортировка</x-sub-title>

<x-p>
    Для возможности сортировки поля на главной странице ресурса необходимо добавить метод <code>sortable()</code>.
</x-p>

<x-code language="php">
sortable()
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->sortable() // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="badge">Badge</x-sub-title>

<x-p>
    Для отображения поля в режиме preview в виде <em>badge</em>,
    необходимо воспользоваться методом <code>badge()</code>.
</x-p>

<x-code language="php">
badge(string|Closure|null $color = null)
</x-code>

@include('pages.ru.components.shared.colors')

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->badge('green') // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="hide-show">Отображение</x-sub-title>

<x-p>
    В ресурсе модели поля отображаются на странице со списком (главная страница)
    и на страницах создания/редактирования/просмотра.<br />
    Чтобы исключить вывод поля на какой-либо странице, можно воспользоваться соответствующими методами
    <code>hideOnIndex()</code>, <code>hideOnForm()</code>, <code>hideOnDetail()</code> или
    обратные методы <code>showOnIndex()</code>, <code>showOnForm()</code>, <code>showOnDetail()</code>.<br />
    Чтобы исключить только со страницы редактирования или добавления -
    <code>hideOnCreate()</code>, <code>hideOnUpdate()</code>,
    а также обратные <code>showOnCreate()</code>, <code>showOnUpdate</code>
</x-p>

<x-code language="php">
hideOnIndex(Closure|bool|null $condition = null)
showOnIndex(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
hideOnForm(Closure|bool|null $condition = null)
showOnForm(Closure|bool|null $condition = null)

hideOnCreate(Closure|bool|null $condition = null)
showOnCreate(Closure|bool|null $condition = null)

hideOnUpdate(Closure|bool|null $condition = null)
showOnUpdate(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
hideOnDetail(Closure|bool|null $condition = null)
showOnDetail(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title') // [tl! focus:start]
            ->hideOnIndex()
            ->hideOnForm(), // [tl! focus:end]
    ];
}

//...
</x-code>

<x-moonshine::alert type="default" icon="heroicons.book-open">
    Если вам необходимо просто указать какие поля отображать на страницах или изменить очередность вывода,
    то можно воспользоваться удобным способом
    <x-link :link="route('moonshine.page', 'resources-fields') . '#override'" >
        переопределения полей
    </x-link>.
</x-moonshine::alert>

<x-sub-title id="show-when">Динамическое отображение</x-sub-title>

<x-p>
    Может возникнуть потребность отображать поле только в том случае, если значение у другого поля
    в форме имеет определенное значение (Например: отображать телефон, только если стоит галочка, что телефон есть).<br />
    Для этих целей используется метод <code>showWhen()</code>.
</x-p>

<x-code language="php">
showWhen(
    string $column,
    mixed $operator = null,
    mixed $value = null
)
</x-code>

<x-p>
    Доступные операторы:
</x-p>

<x-p>
    <x-moonshine::badge color="gray">=</x-moonshine::badge>
    <x-moonshine::badge color="gray"><</x-moonshine::badge>
    <x-moonshine::badge color="gray">></x-moonshine::badge>
    <x-moonshine::badge color="gray"><=</x-moonshine::badge>
    <x-moonshine::badge color="gray">>=</x-moonshine::badge>
    <x-moonshine::badge color="gray">!=</x-moonshine::badge>
    <x-moonshine::badge color="gray">in</x-moonshine::badge>
    <x-moonshine::badge color="gray">not in</x-moonshine::badge>
</x-p>

<x-moonshine::alert type="default" icon="heroicons.book-open">
    Если оператор не указан, то будет использоваться <code>=</code>
</x-moonshine::alert>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Checkbox::make('Has phone', 'has_phone'),
        Phone::make('Phone')
            ->showWhen('has_phone','=', 1) // [tl! focus]
    ];
}

//...
</x-code>

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    Если оператор имеет значение <code>in</code> или <code>not in</code>,
    то в <code>$value</code> необходимо передать массив
</x-moonshine::alert>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Select::make('List', 'list')->multiple()->options([
            'value 1' => 'Option Label 1',
            'value 2' => 'Option Label 2',
            'value 3' => 'Option Label 3',
        ]),

        Text::make('Name')
            ->showWhen('list', 'not in', ['value 1', 'value 3']), // [tl! focus]

        Textarea::make('Content')
            ->showWhen('list', 'in', ['value 2', 'value 3']) // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="custom-view">Изменение отображения</x-sub-title>

<x-p>
    Когда необходимо изменить view с помощью <em>fluent interface</em>
    можно воспользоваться методом <code>customView()</code>.
</x-p>

<x-code language="php">
customView(string $customView)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->customView('fields.my-custom-input') // [tl! focus]
    ];
}

//...
</x-code>

<x-p>
    Метод <code>changePreview()</code> позволяет переопределить view для превью (везде кроме формы).
</x-p>

<x-code language="php">
changePreview(Closure $closure)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Thumbnail')
            ->changePreview(function ($value) {
                return view('moonshine::ui.image', [
                    'value' => Storage::url($value)
                ]);
            }) // [tl! focus:-4]
    ];
}

//...
</x-code>

<x-p>
    Методы <code>beforeRender()</code> и <code>afterRender()</code>
    позволяю вывести какую-то информацию перед и после поля соответственно.
</x-p>

<x-code language="php">
beforeRender(Closure $closure)
</x-code>

<x-code language="php">
afterRender(Closure $closure)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Image::make('Thumbnail')
            ->beforeRender(function (Image $field) {
                return $field->preview());
            }) // [tl! focus:-2]
    ];
}

//...
</x-code>

<x-sub-title id="when-unless">Методы по условию</x-sub-title>

<x-p>
    Метод <code>when()</code> реализует <em>fluent interface</em>
    и выполнит callback, когда первый аргумент, переданный методу, имеет значение true.
</x-p>

<x-code language="php">
when($value = null, callable $callback = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Slug')
            ->when($this->getItem()?->exist, fn(Text $field) => $field->locked()) // [tl! focus]
    ];
}

//...
</x-code>

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    Экземпляр поля, будет передан в функции callback.
</x-moonshine::alert>

<x-p>
    Методу <code>when()</code> может быть передан второй callback, он будет выполнен,
    когда первый аргумент, переданный методу, имеет значение false.
</x-p>

<x-code language="php">
when($value = null, callable $callback = null, callable $default = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Slug')
            ->when(
                $this->getItem()?->exist,
                fn(Text $field) => $field->locked(),
                fn(Text $field) => $field->hidden()
            ) // [tl! focus:-4]
    ];
}

//...
</x-code>

<x-p>
    Метод <code>unless()</code> обратный методу <code>when()</code> и выполнит первый callback,
    когда первый аргумент имеет значение false, иначе будет выполнен второй callback, если он передан методу.
</x-p>

<x-code language="php">
unless($value = null, callable $callback = null, callable $default = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Slug')
            ->unless(
                auth('moonshine')->user()->moonshine_user_role_id === 1,
                fn(Text $field) => $field->readonly()->hideOnCreate(),
                fn(Text $field) => $field->locked()
            ) // [tl! focus:-4]
    ];
}

//...
</x-code>

<x-sub-title id="fill">Заполнение</x-sub-title>

<x-p>
    Поля можно заполнить значениями использую метод <code>fill()</code>.
</x-p>

<x-code language="php">
fill(mixed $value, mixed $casted = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->fill('Some title') // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="apply">Apply</x-sub-title>

<x-p>
    У каждого поля реализован метод <code>apply()</code>,
    который трансформирует данные с учетом <em>request</em> и <em>resolve</em> методов.
    Например, трансформирует данные модели для сохранения в базе данных или формирует запрос для фильтрации.
</x-p>

<x-p>
    Существует возможность переопределить действия при выполнении метода <code>apply()</code>,
    для этого необходимо воспользоваться методом <code>onApply()</code> который принимает замыкание.
</x-p>

<x-code language="php">
onApply(Closure $onApply)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Thumbnail by link', 'thumbnail')
            ->onApply(function(Post $item, $value) {
                $path = 'thumbnail.jpg';

                if ($value) {
                    $item->thumbnail = Storage::put($path, file_get_content($value));
                }

                return $item;
            }) // [tl! focus:-8]
    ];
}

//...
</x-code>

<x-p>
    Если вы не хотите чтобы поле выполняло какие-то действия,
    то можно воспользоваться методом <code>canApply()</code>.
</x-p>

<x-code language="php">
canApply(Closure|bool|null $condition = null)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->canApply() // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="events">События</x-sub-title>

<x-p>
    Иногда может возникнуть потребность переопределить <em>resolve</em> методы, которые выполняются до и после <code>apply()</code>,
    для этого необходимо воспользоваться соответствующими методами.
</x-p>

<x-code language="php">
onBeforeApply(Closure $onBeforeApply)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->onBeforeApply(function(Post $item, $value) {
                //
                return $item;
            }) // [tl! focus:-3]
    ];
}
</x-code>

<x-code language="php">
onAfterApply(Closure $onAfterApply)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->onAfterApply(function(Post $item, $value) {
                //
                return $item;
            }) // [tl! focus:-3]
    ];
}
</x-code>

<x-code language="php">
onAfterDestroy(Closure $onAfterDestroy)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->onAfterDestroy(function(Post $item, $value) {
                //
                return $item;
            }) // [tl! focus:-3]
    ];
}
</x-code>

<x-sub-title id="assets">Assets</x-sub-title>

<x-p>
    Для поля есть возможность загрузить дополнительные css стили и js скрипты, используя метод <code>addAssets()</code>.
</x-p>

<x-code language="php">
addAssets(array $assets)
</x-code>

<x-code language="php">
//...

public function fields(): array
{
    return [
        Text::make('Title')
            ->addAssets(['custom.css', 'custom.js']) // [tl! focus]
    ];
}
</x-code>

</x-page>
