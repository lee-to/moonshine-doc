# Иконки

- [Основы](#basics)
- [Outline](#outline)
- [Solid](#solid)
- [Mini](#mini)
- [Compact](#compact)

---

<a name="basics"></a>
## Основы

Для всех сущностей, которые имеют метод `icon()`, вы можете использовать один из предустановленных наборов из коллекции [Heroicons](https://heroicons.com) (по умолчанию набор **Outline**)
или создать свой собственный набор.

```php
icon(string $icon, bool $custom = false, ?string $path = null)
```

`$icon` - название иконки или html (если используется кастомный режим),
`$custom` - кастомный режим,
`$path` - путь до директории где лежат blade шаблоны иконок.

Простой пример:

```php
->icon('cog')
```

Пример с передачей HTML иконки:

```php
->icon(svg('path-to-icon-pack')->toHtml(), custom: true),
```

> [!NOTE]
> Функция `svg` в примере из пакета `Blade Icons`

Пример с указанием директории где располагаются ваши иконки:

```php
->icon('cog', path: 'icons')
```

> [!NOTE]
> В примере иконки должны располагаться в директории `resources/views/icons`, наименование иконки эквивалентно `blade` файлу, в котором располагается `svg`.

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
