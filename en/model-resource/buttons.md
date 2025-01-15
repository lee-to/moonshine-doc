# Buttons

- [Basics](#basics)
- [Create button](#create)
- [Detail button](#detail)
- [Edit button](#edit)
- [Delete button](#delete)
- [Mass delete button](#mass-delete)
- [Filters](#filters)
- [Top buttons on index page](#top-buttons)
- [Index table buttons](#index-buttons)
- [Form buttons](#form-buttons)
- [Detail page buttons](#detail-buttons)

---

<a name="basics"></a>
## Basics

Buttons are displayed on resource pages: index page, form pages (create / edit), and detail page.  
They are responsible for basic actions with elements and are components of [ActionButton](/docs/{{version}}/components/action-button).

In **MoonShine**, there are many methods that allow you to override either a single [button](/docs/{{version}}/components/action-button) for the resource or an entire [group](/docs/{{version}}/components/action-group).

> [!NOTE]
> More detailed information about the [ActionButton](/docs/{{version}}/components/action-button) component.

> [!WARNING]
> The buttons for creating, viewing, editing, deleting, and mass deleting are placed in separate classes
> to apply all necessary methods to them and thereby eliminate duplication, as these buttons are also used in HasMany, BelongsToMany, etc.

<a name="create"></a>
## Create button

The `modifyCreateButton()` method allows you to modify the button for creating a new item.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Contracts\UI\ActionButtonContract;

protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
{
    return $button->error();
}
```

You can also override the button.

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
## Detail button

The `modifyDetailButton()` method allows you to modify or override the button for viewing the details of an item.

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
## Edit button

The `modifyEditButton()` method allows you to modify or override the button for editing an item.

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
## Delete button

The `modifyDeleteButton()` method allows you to modify or override the button for deleting an item.

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
## Mass delete button

The `modifyMassDeleteButton()` method allows you to modify or override the button for mass deleting.

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
## Filters button

#### Modification

The `modifyFiltersButton()` method allows you to overwrite or modify the filters button.

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
## Top buttons on index page

By default, the model resource index page has only a create button.  
The `topButtons()` method allows you to add additional [buttons](/docs/{{version}}/components/action-button).

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
## Index table buttons

To add buttons in the index table, use the `indexButtons()` method.

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

For bulk actions with elements, you need to add the `bulk()` method.

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
## Form buttons

To add buttons to the form page, use the `formButtons()` method.

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

The `formBuilderButtons()` method allows you to add additional [buttons](/docs/{{version}}/components/action-button) in the create or edit form.

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
## Detail page buttons

To add buttons to the detail view page, use the `detailButtons()` method.

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
