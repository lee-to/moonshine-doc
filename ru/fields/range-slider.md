# RangeSlider

- [Основы](#basics)
- [Создание](#make)
- [Фильтр](#filter)

---

<a name="basics"></a>
## Основы

Наследует [Range](/docs/{{version}}/fields/range)

\* имеет те же возможности.

Поле *RangeSlider* является расширением *Range* и дополнительно имеет возможность изменять значения с помощью слайдера.

<a name="make"></a>
## Создание

```php
use MoonShine\UI\Fields\RangeSlider;

RangeSlider::make('Возраст', 'age')
    ->fromTo('age_from', 'age_to')
```

<a name="filter"></a>
## Фильтр

При использовании поля *RangeSlider* для построения фильтра метод `fromTo()` не используется, так как фильтрация происходит по одному полю в таблице базы данных.

```php
use MoonShine\UI\Fields\RangeSlider;

RangeSlider::make('Возраст', 'age')
```
