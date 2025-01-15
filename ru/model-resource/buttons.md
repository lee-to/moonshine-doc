# Кнопки

- [Основы](#basics)
- [Кнопка создания](#create)
- [Кнопка просмотра](#detail)
- [Кнопка редактирования](#edit)
- [Кнопка удаления](#delete)
- [Кнопка массовое удаление](#mass-delete)
- [Фильтр](#filters)
- [Кнопки индексной страницы](#top-buttons)
- [Индексная таблица](#index-buttons)
- [Форма](#form-buttons)
- [Детальная страница](#detail-buttons)

---

<a name="basics"></a>
## Основы

Кнопки отображаются на страницах ресурса: индексная страница, страницы с формой (создание / редактирование) и детальная страница. 
Они отвечают за основные действия с элементами и являются компонентами [ActionButton](/docs/{{version}}/components/action-button).

В **MoonShine** есть множество методов, позволяющих переопределить у ресурса как отдельную [кнопку](/docs/{{version}}/components/action-button), так и всю [группу](/docs/{{version}}/components/action-group).

> [!NOTE]
> Более подробная информация о компоненте [ActionButton](/docs/{{version}}/components/action-button).

> [!WARNING]
> Кнопки для создания, просмотра, редактирования, удаления и массового удаления размещены в отдельных классах,
> чтобы применить к ним все необходимые методы и тем самым устранить дублирование, поскольку эти кнопки также используются в HasMany, BelongsToMany и т.д.

<a name="create"></a>
## Кнопка создания

Метод `modifyCreateButton()` позволяет модифицировать кнопку создания нового элемента.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->error();
}
```

Так же, вы можете переопределить кнопку.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;

protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
{
    return ActionButton::make('Create');
}
```

![resource_button_create](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_create.png) 
![resource_button_create_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_create_dark.png)

<a name="detail"></a>
## Кнопка детального просмотра

Метод `modifyDetailButton()` позволяет модифицировать или переопределить кнопку детального просмотра элемента.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->warning();
}
```

![resource_button_detail](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_detail.png) 
![resource_button_detail_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_detail_dark.png)

<a name="edit"></a>
## Кнопка редактирования

Метод `modifyEditButton()` позволяет модифицировать или переопределить кнопку редактирования элемента.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->icon('pencil-square');
}
```

![resource_button_edit](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_edit.png) 
![resource_button_edit_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_edit_dark.png)

<a name="delete"></a>
## Кнопка удаления

Метод `modifyDeleteButton()` позволяет модифицировать или переопределить кнопку удаления элемента.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->icon('x-mark');
}
```

![resource_button_delete](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_delete.png) 
![resource_button_delete_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_delete_dark.png)

<a name="mass-delete"></a>
## Кнопка массового удаления

Метод `modifyMassDeleteButton()` позволяет модифицировать или переопределить кнопку массового удаления.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyMassDeleteButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->icon('x-mark');
}
```

![resource_button_mass_delete](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_mass_delete.png) ![resource_button_mass_delete_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_mass_delete_dark.png)

<a name="filters"></a>
## Кнопка фильтров

#### Модификация

Метод `modifyFiltersButton()` позволяет или переопределить модифицировать кнопку фильтров.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyFiltersButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->error();
}
```

![resource_button_filters](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_filters.png) 
![resource_button_filters_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_button_filters_dark.png)

<a name="top-buttons"></a>
## Кнопки на странице индекса

По умолчанию на странице индекса ресурса модели есть только кнопка создания.  
Метод `topButtons()` позволяет добавить дополнительные [кнопки](/docs/{{version}}/components/action-button).

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\ListOf;

protected function topButtons(): ListOf
{
    return parent::topButtons()->add(
      ActionButton::make('Refresh', '#')
            ->dispatchEvent(AlpineJs::event(JsEvent::TABLE_UPDATED, $this->getListComponentName()))
  );
}
```

![resource_buttons_actions](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_actions.png) 
![resource_buttons_actions_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_actions_dark.png)

<a name="index-buttons"></a>
## Кнопки в таблице индекса

Для добавления кнопок в таблицу индекса используйте метод `indexButtons()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:3]
use Illuminate\Database\Eloquent\Model;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

protected function indexButtons(): ListOf
{
    return parent::indexButtons()->prepend(
        ActionButton::make(
            'Link',
            fn(Model $item) => '/endpoint?id=' . $item->getKey()
        )
    );
}
```

![resource_buttons_index](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_index.png) 
![resource_buttons_index_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_index_dark.png)

Для массовых действий с элементами необходимо добавить метод `bulk()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

protected function indexButtons(): ListOf
{
    return parent::indexButtons()->prepend(
        ActionButton::make('Link', '/endpoint')
            ->bulk()
    );
}
```

![resource_buttons_bulk](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_bulk.png) 
![resource_buttons_bulk_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_bulk_dark.png)


<a name="form-buttons"></a>
## Кнопки на странице формы

Чтобы добавить кнопки на страницу с формой, используйте метод `formButtons()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

protected function formButtons(): ListOf
{
    return parent::formButtons()->add(ActionButton::make('Link')->method('updateSomething'));
}
```

![resource_buttons_form](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_form.png) 
![resource_buttons_form_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_form_dark.png)

Метод `formBuilderButtons()` позволяет добавить дополнительные [кнопки](/docs/{{version}}/components/action-button) в форму создания или редактирования.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

protected function formBuilderButtons(): ListOf
{
    return parent::formBuilderButtons()->add(
      ActionButton::make('Back', fn() => $this->getIndexPageUrl())->class('btn-lg')
    );
}
```

![resource_buttons_form_builder](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_form_builder.png) ![resource_buttons_form_builder_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_form_builder_dark.png)


<a name="detail-buttons"></a>
## Кнопки на странице детального просмотра

Чтобы добавить кнопки на страницу детального просмотра, используйте метод `detailButtons()`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:2]
use MoonShine\UI\Components\ActionButton;
use MoonShine\Support\ListOf;

protected function detailButtons(): ListOf
{
    return parent::detailButtons()->add(ActionButton::make('Link', '/endpoint'));
}
```

![resource_buttons_detail](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_detail.png) 
![resource_buttons_detail_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/resource_buttons_detail_dark.png)
