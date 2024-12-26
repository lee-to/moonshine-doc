# Checkbox

- [Basics](#basics)
- [Creation](#make)
- [On/Off Values](#on-off)
- [Preview Editing](#preview-edit)
- [Reactivity](#reactive)

---

<a name="basics"></a>
## Basics

Contains all [basic methods](/docs/{{version}}/fields/basic-methods).

The *Checkbox* field is a field for selecting a yes/no value.

<a name="make"></a>
## Creation

~~~tabs
tab: Class
```php
use MoonShine\UI\Fields\Checkbox; 

Checkbox::make('Publish', 'is_publish') 
```
tab: Blade
```blade
<x-moonshine::form.wrapper label="Publish">
    <x-moonshine::form.input
        type="checkbox"
        name="is_publish"
    />
</x-moonshine::field-container>
```
~~~ 

<a name="on-off"></a>
## On/Off Values

By default, the field has values `1` and `0` for selected and unselected states respectively. The `onValue()` and `offValue()` methods allow you to override these values.

```php
onValue(int|string $onValue)
```

```php
offValue(int|string $offValue)
```

```php
Checkbox::make('Publish', 'is_publish')
    ->onValue('yes')
    ->offValue('no')
```

<a name="preview-edit"></a>
## Preview Editing

This field supports [preview editing](/docs/{{version}}/fields/basic-methods#preview-edit).

<a name="reactive"></a>
## Reactivity

This field supports [reactivity](/docs/{{version}}/fields/basic-methods#reactive).
