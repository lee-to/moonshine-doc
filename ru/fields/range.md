# Range

- [Основы](#basics)
- [Основные методы](#basic-methods)
    - [Создание](#make)
    - [Атрибуты](#attributes)
- [Методы для работы с числовыми значениями](#number-type-methods)
    - [Максимальное и минимальное значение](#min-and-max)
    - [Шаг](#step)
    - [Звезды](#stars)
- [Фильтр](#filter)

---

<a name="basics"></a>
## Основы

Содержит все [Базовые методы](/docs/{{version}}/fields/basic-methods).

Поле *Range* позволяет устанавливать диапазон значений.

<a name="basic-methods"></a>
## Основные методы

<a name="make"></a>
## Создание

Поскольку диапазон имеет два значения, вам нужно указать их с помощью метода `fromTo()`.

```php
fromTo(string $fromField, string $toField)
```

```php
use MoonShine\UI\Fields\Range;

Range::make('Возраст', 'age')
    ->fromTo('age_from', 'age_to')
```

<a name="attributes"></a>
## Атрибуты

Если вам нужно добавить пользовательские атрибуты для полей, вы можете использовать соответствующие методы `fromAttributes()` и `toAttributes()`.

```php
fromAttributes(array $attributes)
```

```php
toAttributes(array $attributes)
```

В данном примере добавляется подсказка.

```php
Range::make('Возраст', 'age')
    ->fromTo('age_from', 'age_to')
    ->fromAttributes(['placeholder' => 'от'])
    ->toAttributes(['placeholder' => 'до'])
```

<a name="number-type-methods"></a>
## Методы для работы с числовыми значениями

<a name="min-and-max"></a>
### Максимальное и минимальное значение

Методы `min()` и `max()` используются для установки минимального и максимального значений поля.

```php
min(int|float $min)
```

```php
max(int|float $max)
```

<a name="step"></a>
### Шаг

Метод `step()` используется для указания шага значения для поля.

```php
step(int|float $step)
```

```php
Range::make('Price')
    ->fromTo('price_from', 'price_to')
    ->min(0)
    ->max(10000)
    ->step(5)
```

<a name="stars"></a>
### Звезды

Метод `stars()` используется для отображения числового значения в режиме preview в виде звезд (например, для рейтингов).

```php
stars()
```

```php
Range::make('Rating')
    ->fromTo('rating_from', 'rating_to')
    ->stars()
```

<a name="filter"></a>
## Фильтр

При использовании поля *Range* для построения фильтра метод `fromTo()` не используется, поскольку фильтрация происходит по одному полю в таблице базы данных.

```php
Range::make('Возраст', 'age')
```