# Icons

- [Basics](#basics)
- [Outline](#outline)
- [Solid](#solid)
- [Mini](#mini)
- [Compact](#compact)

---

<a name="basics"></a>
## Basics

For all entities that have the method `icon()`, you can use one of the preset sets from the [Heroicons](https://heroicons.com) collection (by default, the **Outline** set)
or create your own set.

```php
icon(string $icon, bool $custom = false, ?string $path = null)
```

`$icon` - the name of the icon or HTML (if using custom mode),  
`$custom` - custom mode,  
`$path` - the path to the directory where the blade templates for icons are located.

Simple example:

```php
->icon('cog')
```

Example with passing an HTML icon:

```php
->icon(svg('path-to-icon-pack')->toHtml(), custom: true),
```

> [!NOTE]
> The `svg` function in the example is from the `Blade Icons` package.

Example specifying the directory where your icons are located:

```php
->icon('cog', path: 'icons')
```

> [!NOTE]
> In this example, the icons should be located in the `resources/views/icons` directory, and the icon name is equivalent to the `blade` file in which the `svg` is located.

<a name="outline"></a>
## Outline

```php
->icon('academic-cap')
```

<x-docs.icon-list prefix=""></x-docs.icon-list>

<a name="solid"></a>
## Solid

```php
->icon('s.academic-cap')
```

<x-docs.icon-list prefix="s"></x-docs.icon-list>

<a name="mini"></a>
## Mini

```php
->icon('m.academic-cap')
```

<x-docs.icon-list prefix="m"></x-docs.icon-list>

<a name="compact"></a>
## Compact

```php
->icon('c.academic-cap')
```

<x-docs.icon-list prefix="c"></x-docs.icon-list>
