<x-page
    title="BelongsToMany"
    :sectionMenu="[
        'Разделы' => [
            ['url' => '#basics', 'label' => 'Основы'],
            ['url' => '#label-column', 'label' => 'Заголовок столбца'],
            ['url' => '#pivot', 'label' => 'Pivot'],
            ['url' => '#creatable', 'label' => 'Создание объекта отношения'],
            ['url' => '#select', 'label' => 'Select'],
		    ['url' => '#options', 'label' => 'Опции'],
            ['url' => '#placeholder', 'label' => 'Placeholder'],
            ['url' => '#tree', 'label' => 'Tree'],
            ['url' => '#preview', 'label' => 'Preview'],
            ['url' => '#only-link', 'label' => 'Только ссылка'],
            ['url' => '#values-query', 'label' => 'Запрос для значений'],
            ['url' => '#async-search', 'label' => 'Асинхронный поиск'],
		    ['url' => '#associated', 'label' => 'Связанные поля'],
            ['url' => '#with-image', 'label' => 'Значения с изображением'],
            ['url' => '#buttons', 'label' => 'Кнопки'],
        ]
    ]"
>

<x-sub-title id="basics">Основы</x-sub-title>

@include('pages.ru.fields.shared.relation_make', ['field' => 'BelongsToMany', 'label' => 'Categories'])

<x-sub-title id="label-column">Заголовок столбца</x-sub-title>

<x-p>
    По умолчанию в качестве заголовка столбца таблицы используется свойство
    <code>$title</code> ресурса модели отношения.<br />
    Метод <code>columnLabel()</code> позволяет переопределить заголовок.
</x-p>

<x-code language="php">
columnLabel(string $label)
</x-code>

<x-code language="php">
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->columnLabel('Title') // [tl! focus]
    ];
}

//...
</x-code>

<x-sub-title id="pivot">Pivot</x-sub-title>

<x-p>
    Метод <code>fields()</code> используется для реализации <em>pivot</em> полей у отношения BelongsToMany.
</x-p>

<x-code language="php">
fields(Fields|Closure|array $fields)
</x-code>

<x-code language="php">
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Text;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Contacts', resource: new ContactResource())
            ->fields([
                Text::make('Contact', 'text'),
            ]) // [tl! focus:-2]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_pivot.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_pivot_dark.png') }}"></x-image>

<x-moonshine::alert type="warning" icon="heroicons.information-circle">
    У отношения необходимо указать какие <em>pivot</em> поля используются в промежуточной таблице!<br />
    Подробнее в официальной документации
    <x-link
        link="https://laravel.com/docs/eloquent-relationships#retrieving-intermediate-table-columns"
        target="_blank"
    >Laravel</x-link>.
</x-moonshine::alert>

<x-sub-title id="creatable">Создание объекта отношения</x-sub-title>

@include('pages.ru.fields.shared.relation_creatable', ['field' => 'BelongsToMany', 'label' => 'Categories'])

<x-sub-title id="select">Select</x-sub-title>

<x-p>
    Поле <em>BelongsToMany</em> можно отобразить в виде выпадающего списка,
    для этого необходимо воспользоваться методом <code>selectMode()</code>.
</x-p>

<x-code language="php">
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->selectMode() // [tl! focus]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_select.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_select_dark.png') }}"></x-image>

@include('pages.ru.fields.shared.choices_options', ['field' => 'BelongsToMany'])

@include('pages.ru.fields.shared.placeholder', ['field' => 'BelongsToMany'])

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    Метод <code>placeholder()</code> используется только,
    если поле отображается в виде выпадающего списка <code>selectMode()</code>!
</x-moonshine::alert>

<x-sub-title id="tree">Tree</x-sub-title>

<x-p>
    Метод <code>tree()</code> позволять отобразить значения в виде дерева с чекбоксами,
    например для категорий, которые имеют вложенность.
    Методу необходимо передать столбец в базе данных по которому будет строиться дерево.
</x-p>

<x-code language="php">
tree(string $parentColumn)
</x-code>

<x-code language="php">
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->tree('parent_id') // [tl! focus]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_tree.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_tree_dark.png') }}"></x-image>

<x-sub-title id="preview">Preview</x-sub-title>

<x-p>
    По умолчанию в <em>preview</em> поле будет отображаться в виде таблицы.
</x-p>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_preview.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_preview_dark.png') }}"></x-image>

<x-p>
    Для того чтобы изменить отображение в <em>preview</em> можно воспользоваться следующими методами.
</x-p>

<x-moonshine::divider label="onlyCount" />

<x-p>
    Метод <code>onlyCount()</code> позволяет отобразить в <em>preview</em> только количество выбранных значений.
</x-p>

<x-code language="php">
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->onlyCount() // [tl! focus]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_preview_count.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_preview_count_dark.png') }}"></x-image>

<x-moonshine::divider label="inLine" />

<x-p>
    Метод <code>inLine()</code> позволяет отобразить значения поля в виде строки.
</x-p>

<x-code language="php">
inLine(string $separator = '', Closure|bool $badge = false, ?Closure $link = null)
</x-code>

<x-p>
    Методу можно передать необязательные параметры:
    <x-ul>
        <li><code>separator</code> - разделитель между элементами;</li>
        <li><code>badge</code> - замыкание или булево значение, отвечающий за отображение элемента в виде badge;</li>
        <li><code>$link</code> - замыкание которое должно вернуть <em>url</em> ссылки или компонент.</li>
    </x-ul>
</x-p>
<x-p>
    При передаче булевого значения true в параметр <code>badge</code> будет использоваться цвет <x-moonshine::badge color="primary">Primary</x-moonshine::badge>. Для изменения цвета, отображаемого <code>badge</code>, используйте замыкание и возвращайте компонент <code>Badge::make()</code>.
</x-p>

<x-code language="php">
use MoonShine\Components\Link;
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->inLine(
                separator: ' ',
                badge: fn($model, $value) => Badge::make($value, 'color'),
                link: fn(Category $category, $value, $field) => Link::make(
                    (new CategoryResource())->detailPageUrl($category),
                    $value
                )
            ) // [tl! focus:-7]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/belongs_to_many_preview_in_line.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/belongs_to_many_preview_in_line_dark.png') }}"></x-image>

@include('pages.ru.fields.shared.only_link', ['field' => 'BelongsToMany', 'label' => 'Categories'])

@include('pages.ru.fields.shared.values_query', ['field' => 'BelongsToMany'])

@include('pages.ru.fields.shared.async_search', ['field' => 'BelongsToMany'])

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    Запросы необходимо кастомизировать через метод <code>asyncSearch()</code>.
    Не используйте <code>valuesQuery()</code>!
</x-moonshine::alert>

@include('pages.ru.fields.shared.with_associated', ['field' => 'BelongsToMany'])

@include('pages.ru.fields.shared.with_image', ['field' => 'BelongsToMany'])

<x-sub-title id="buttons">Кнопки</x-sub-title>

<x-p>
    Метод <code>buttons()</code> позволяет добавить дополнительные кнопки к полю <em>BelongsToMany</em>.
</x-p>

<x-code language="php">
buttons(array $buttons)
</x-code>

<x-code language="php">
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->buttons([
                ActionButton::make('Check all', '')
                    ->onClick(fn() => 'checkAll', 'prevent'),

                ActionButton::make('Uncheck all', '')
                    ->onClick(fn() => '', 'prevent')
            ]) // [tl! focus:-6]
    ];
}

//...
</x-code>

<x-moonshine::divider label="withCheckAll" />

<x-p>
    Метод <code>withCheckAll()</code> позволяет добавить кнопки checkAll/uncheckAll к полю <em>BelongsToMany</em>
    аналогично предыдущему примеру.
</x-p>

<x-code language="php">
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Relationships\BelongsToMany;

//...

public function fields(): array
{
    return [
        BelongsToMany::make('Categories', resource: new CategoryResource())
            ->withCheckAll() // [tl! focus]
    ];
}

//...
</x-code>

</x-page>
