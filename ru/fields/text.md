# Text

- [Основы](#basics)
- [Основные методы](#basic-methods)
  - [Подсказка](#placeholder)
  - [Маска](#mask)
  - [Теги](#tags)
  - [Отключение экранирования](#unescape)
- [Расширения](#extensions)
  - [Копирование](#copy)
  - [Скрытие значения](#eye)
  - [Замок](#locked)
  - [Суффикс](#suffix)
- [Редактирование в режиме preview](#preview-edit)
- [Реактивность](#reactive)

---

<a name="basics"></a>
## Основы

Содержит все [Базовые методы](/docs/{{version}}/fields/basic-methods).

Поле `Text` - это базовое текстовое поле ввода в MoonShine. Это поле эквивалент `<input type="text">`

~~~tabs
tab: Class
```php
use MoonShine\UI\Fields\Text;

Text::make('Title')
```
tab: Blade
```blade
<x-moonshine::field-container label="Title">
    <x-moonshine::form.input
        type="text"
        name="title"
    />
</x-moonshine::field-container>
```
~~~

![mask](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/mask.png)

![mask_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/mask_dark.png)

<a name="basic-methods"></a>
## Основные методы

<a name="placeholder"></a>
### Подсказка

Метод `placeholder()` позволяет задать текст-подсказку для поля.

```php
placeholder(string $value)
```

```php
Text::make('Имя пользователя', 'username')
    ->placeholder('Введите имя пользователя')
```

<a name="mask"></a>
### Маска
Метод `mask()` позволяет применить маску к вводимому тексту.

```php
mask(string $mask)
```

Пример использования:

```php
Text::make('Телефон', 'phone')
    ->mask('+7 (999) 999-99-99')
```

![mask](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/mask.png)

![mask_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/mask_dark.png)

<a name="tags"></a>
### Теги

Метод `tags()` преобразует текстовое поле в поле для ввода тегов.

```php
tags(?int $limit = null)
```

```php
Text::make('Теги', 'tags')
    ->tags(5)
```

<a name="unescape"></a>
### Отключение экранирования

Метод `unescape()` отключает экранирование HTML-тегов в значении поля.

```php
unescape()
```

Пример использования:

```php
Text::make('HTML-контент', 'content')
    ->unescape()
```

<a name="extensions"></a>
## Расширения

Поле поддерживает различные расширения для помощи и контроля ввода

![expansion](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/expansion.png)

![expansion_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/expansion_dark.png)

<a name="copy"></a>
### Копирование

Метод `copy()` добавляет кнопку для копирования значения поля.

```php
copy(string $value = '{{value}}')
```

Пример использования:

```php
Text::make('Токен', 'token')
    ->copy()
```

<a name="eye"></a>
### Скрытие значения

Метод `eye()` добавляет кнопку для показа/скрытия значения поля (например, для пароля).

```php
eye()
```

Пример использования:

```php
Text::make('Пароль', 'password')
    ->eye()
```

<a name="locked"></a>
### Замок

Метод `locked()` добавляет иконку замка к полю.

```php
locked()
```

Пример использования:

```php
Text::make('Защищенное поле', 'protected_field')
    ->locked()
```

### Суффикс

Метод `suffix()` добавляет суффикс к полю ввода.

```php
suffix(string $ext)
```

Пример использования:

```php
Text::make('Домен', 'domain')
    ->suffix('.com')
```

<a name="preview-edit"></a>
### Редактирование в режиме preview

Данному полю доступно [редактирование в режиме preview](/docs/{{version}}/fields/basic-methods#preview-edit).

> [!NOTE]
> Если вы хотите избежать ошибок ввода, можете использовать расширение [Замок](#locked).

```php
Text::make('Name')->updateOnPreview()->locked(),
```

<a name="reactive"></a>
## Реактивность

Данному полю доступна [реактивность](/docs/{{version}}/fields/basic-methods#reactive).