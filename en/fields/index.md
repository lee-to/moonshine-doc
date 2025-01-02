# Basics

- [Concept](#concept)
- [Default Mode](#default-mode)
- [Preview Mode](#preview-mode)
- [Raw Mode](#raw-mode)
- [Change Preview](#change-preview)
- [Change Fill](#change-fill)
- [Change Display Mode](#change-mode)
- [Field Application Process](#apply)

---

<a name="concept"></a>
## Concept
Fields play a crucial role in the **MoonShine** admin panel.
They are used in the `FormBuilder` for building forms, in the `TableBuilder` for creating tables, as well as in forming filters for `ModelResource` (CrudResource). They can also be used in your custom pages and even outside the admin panel, both as objects and directly in blade.

Fields are elements of a form, so their default rendering state is simply an HTML form element.

Creating an instance of a field is very simple; there is a convenient `make` method, and for basic usage, it is sufficient to specify the label and name of the field.

```php
use MoonShine\UI\Fields\Text;

Text::make('Title')
Text::make('Title', 'title')
```

Most often, fields are used within the FormBuilder, where they can change the original object based on the request due to the FormBuilder itself.

The complexity of understanding MoonShine fields is due to several visual states.

<a name="default-mode"></a>
## Default Mode

Simply a form element; let's say if we are talking about a Text field, its visual state will be `input type="text"`.

<a name="preview-mode"></a>
## Preview Mode

The second mode is for displaying the value of the field. When outputting a field through TableBuilder, we do not need to edit it; we just want to show its contents. Let's consider the Image field; its `preview` view will have an `img` thumbnail or a carousel of images if it is in multiple mode.

Thus, each field looks different, just like the variety of form elements, but they also appear differently in preview mode as they have different purposes.

This has the advantage that we, as developers, do not need to worry about how to display, for example, a Date field. Under the hood, MoonShine will perform the formatting, escape text fields for security, or simply make the output more aesthetically pleasing.

<a name="raw-mode"></a>
## Raw Mode

You may not encounter this mode while using the panel, but its essence is that it simply outputs the value of the field that was initially assigned to it, without any additional modifications.

This mode is ideal for exporting, to ultimately display the original content for further importing.

In the process of declaring fields, you can change the visual states of each of them, but before we look at the examples, let's briefly review the basic life cycle of a field.

**Cycle through FormBuilder**
- The field is declared in the resource
- The field enters the FormBuilder
- The FormBuilder fills the field
- The FormBuilder renders the field
- Upon request, the FormBuilder calls the fields and saves the original object using them

**Cycle through TableBuilder**
- The field is declared in the resource
- The field enters the TableBuilder
- The TableBuilder includes the field in preview mode
- The TableBuilder iterates the original data and transforms it into TableRow, pre-filling each field with data
- The TableBuilder renders itself and each of its rows along with the fields

**Cycle through export**
- The field is declared in the resource in the export method
- The field enters the Handler
- The Handler includes the field in raw mode
- The Handler iterates the original data, fills the fields with it, and generates a table for export based on the raw field values

Fields in **MoonShine** are not tied to a model (except for the Slug field and relationship fields), so their application spectrum is limited only by your imagination.

In the process of interacting with the fields, you may encounter a number of tasks regarding their modification; all of them will be related to the cycles and states described above. Let's explore them.

<a name="change-preview"></a>
## Change Preview

You use a Select field with options that are links to images and want to output not links in preview mode, but to render the images directly; your code would look as follows, and the result will be achieved thanks to the changePreview method:

```php
use MoonShine\UI\Components\Carousel;
use MoonShine\UI\Fields\Select;

Select::make('Links')->options([
    '/images/1.png' => 'Picture 1',
    '/images/2.png' => 'Picture 2',
])
    ->multiple() // The field can have multiple values
    ->fill(['/images/1.png', '/images/2.png']) // We filled the field, indicating which values are selected
    ->changePreview(
        fn(?array $values, Select $ctx) => Carousel::make($values)
    ) // changed the preview state
```

As a result, you will get a carousel of images based on the Select values; you can return a component or any string.

<a name="change-fill"></a>
## Change Fill

In the process, we also encountered the fill method and filled the field, but if we use it in a ready ModelResource or FormBuilder, the field will be filled for us, and our called fill will be overwritten. Therefore, in your tasks, a situation may arise when you need to change the filling logic, integrate into this process. For this, the methods `changeFill` and `afterFill` will help you.

Let's look at the same example with Select and images, but transform the relative path into a full URL.

In this case, the filling happens automatically; these actions will be done for us by FormBuilder and ModelResource; we will just change the process:

```php
use MoonShine\UI\Components\Carousel;
use MoonShine\UI\Fields\Select;

Select::make('Images')->options([
    '/images/1.png' => 'Picture 1',
    '/images/2.png' => 'Picture 2',
])
    ->multiple()
    ->changeFill(
        fn(Article $data, Select $ctx) => $article->images
            ->map(fn($value) => "https://cutcode.dev$value")
            ->toArray()
    )
    ->changePreview(
        fn(?array $values, Select $ctx) => Carousel::make($values)
    ),
```

This method accepts the full object that was passed to the fields by FormBuilder, and since we considered the context with ModelResource, our original data was `Model` - `Article`.

In the process, we returned the values necessary for the field but changed the content; we used changePreview from the previous step to demonstrate the result.

Let's consider another example of filling. Suppose we need to check its value against a certain condition when outputting the Select in the table and add a class to the cell if it is met. Therefore, we need to obtain the final value with which the Select is filled, and it is important for us that the filling has already occurred (since the conditional `when` method is called before filling, and we do not want that).

```php
use MoonShine\UI\Components\Carousel;
use MoonShine\UI\Fields\Select;

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
    ->changePreview(
        fn(?array $values, Select $ctx) => Carousel::make($values)
    ),
```

The field builder has wide capabilities, and you can change any states on the fly. Let's consider a rare case of changing the default visual state, although we do not recommend doing this and it would be better to create a separate field class for these tasks to extract the logic and reuse the field later.

But let's assume for some reason we want to turn a Select field into a Text field:

```php
use MoonShine\UI\Fields\Select;

Select::make('Links')->options([
    '/images/1.png' => 'Picture 1',
    '/images/2.png' => 'Picture 2',
])
    ->multiple()
    ->changeRender(
        fn(?array $values, Select $ctx) => Text::make($ctx->getLabel())->fill(implode(',', $values))
    )
```

<a name="change-mode"></a>
## Change Display Mode

As we have already understood, fields have different visual states, and in FormBuilder, by default, this will be a form element, in TableBuilder there will be varied values, and for example, in export, it will simply be the original value.

But let's imagine a situation where we need to output the field in TableBuilder not in preview mode but in default mode, or conversely, output it in preview mode inside FormBuilder or even in its original:

```php
Text::make('Title')->defaultMode()
```

Regardless of where we display this field, it will always be in default mode as a form element:

```php
Text::make('Title')->previewMode()
```

The same goes for always being in preview mode.

And finally, the mode with the original state:

```php
Text::make('Title')->rawMode()
```

Since we have touched on the topic of `rawMode` and have already discussed the process of changing the filling, let's also take a look at the method that allows us to modify the original value. For example, we use the field for export and we do not need to perform subsequent imports; it is necessary to display the value for the manager in a clear format:

```php
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('User')->modifyRawValue(fn(int $rawUserId, Article $model, BelongsTo $ctx) => $model->user->name)
```

Let's also imagine a situation where you need to export in a manager-friendly format but also import this file later, and no matter how smart MoonShine is, it will not understand that the value "Ivan Ivanov" should be found in the users table by the name field and take only the id, but we can solve this task:

```php
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make('User')->fromRaw(fn(string $name) => User::where('name', $name)->value('id'))
```

<a name="apply"></a>
## Field Application Process

We already know that fields work with any data, and this is not necessarily a `Model`, but fields can also modify the original incoming data; simply put, this can be called `saving`, but we do not initially use this term since fields do not always save. For instance, the original data may be a QueryBuilder, and the fields will act as filters, thus modifying the QueryBuilder request, or any other case, so it is more accurate to say "they are applied" (apply).

**Life Cycle of Field Application (using model saving as an example)**

- FormBuilder takes the original object; let this be a User model
- It iterates through the fields passing the User object to them and calling the apply method of the field
- Fields within apply take the value from the request based on their column property
- Fields modify the User model's attribute based on their column property and return it back
- Afterwards, the FormBuilder will call the save method of the model
- Additionally, before the apply method of the fields, the beforeApply method will be called if something needs to be done with the object before the main application
- After the save method of the model, the fields' afterApply method will be called (which in this case is well suited for relationship fields to ensure they have the original object which is already saved in the database)

**Life Cycle of Field Application (using filtering as an example)**

- FormBuilder takes the original QueryBuilder object
- It iterates through the fields passing the QueryBuilder object to them and calling the apply method of the field
- Fields modify the QueryBuilder based on their column property and return it back
- The QueryBuilder object will then be used for data output

As a result of the knowledge gained and the use of MoonShine in real conditions, you may encounter a situation where you need to change the application logic or add logic before or after the main application of the field.

The field builder allows you to easily achieve these goals on the fly:

```php
use Illuminate\Support\Facades\Storage;
use MoonShine\UI\Fields\Text;

Text::make('Thumbnail by link', 'thumbnail')
	->onApply(function(Model $item, $value, Text $field) {
		$path = 'thumbnail.jpg';

		if ($value) {
			$item->thumbnail = Storage::put($path, file_get_contents($value));
		}

		return $item;
	}
)
```

Thus, we simply added a link to the text field, but did not save it as is; rather, we uploaded it and placed it in storage and returned the final path.

We also have the methods `onBeforeApply` and `onAfterApply`.

Next, let's take a closer look at the field interface as well as each field separately.