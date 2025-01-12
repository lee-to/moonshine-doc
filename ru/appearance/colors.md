# Color Manager

- [Основы](#basics)
- [Основные цвета](#default-colors)
- [Методы](#methods)
    - [Установка цветов](#set-colors)
    - [Получение цветов](#get-colors)
    - [Управление темой](#theme)
    - [Специальные методы](#special)
- [Вывод в HTML](#html)
- [Конвертация цветов](#conversion)
- [Глобальное переопределение](#service-provider)

---

<a name="basics"></a>
## Основы

`ColorManager` - это компонент `MoonShine` для управления цветовой схемой административной панели. 
Позволяет настраивать цвета различных элементов интерфейса как для светлой, так и для тёмной темы.
Можно использовать в `Layout` или глобально в `ServiceProvider`.

<a name="default-colors"></a>
## Основные цвета

По умолчанию доступны следующие цвета:

```php
// Основные
'primary' => '120, 67, 233' // Основной цвет
'secondary' => '236, 65, 118' // Вторичный цвет
'body' => '27, 37, 59' // Цвет фона

// Оттенки тёмного (dark)
'dark' => [
    'DEFAULT' => '30, 31, 67',
    50 => '83, 103, 132', // поиск, тосты, прогресс бары
    100 => '74, 90, 121', // разделители
    200 => '65, 81, 114', // разделители
    300 => '53, 69, 103', // границы
    400 => '48, 61, 93',  // выпадающие списки, кнопки, пагинация
    500 => '41, 53, 82',  // фон кнопок по умолчанию
    600 => '40, 51, 78',  // строки таблицы
    700 => '39, 45, 69',  // фон контента
    800 => '27, 37, 59',  // фон сайдбара
    900 => '15, 23, 42',  // основной фон
]

// Статусные цвета
'success-bg' => '0, 170, 0'
'success-text' => '255, 255, 255'
'warning-bg' => '255, 220, 42'
'warning-text' => '139, 116, 0'
'error-bg' => '224, 45, 45'
'error-text' => '255, 255, 255'
'info-bg' => '0, 121, 255'
'info-text' => '255, 255, 255'
```

<a name="methods"></a>
## Методы

<a name="set-colors"></a>
### Установка цветов

```php
// Установка одного цвета
$colorManager->set('primary', '120, 67, 233');

// Установка цвета для тёмной темы
$colorManager->set('primary', '120, 67, 233', dark: true);

// Массовая установка цветов
$colorManager->bulkAssign([
    'primary' => '120, 67, 233',
    'secondary' => '236, 65, 118'
]);
```

<a name="get-colors"></a>
### Получение цветов

```php
// Получение цвета
$colorManager->get('primary'); // Возвращает HEX
$colorManager->get('primary', hex: false); // Возвращает RGB

// Получение оттенка
$colorManager->get('dark', 500); // Получение конкретного оттенка

// Получение всех цветов
$colorManager->getAll(); // Для светлой темы
$colorManager->getAll(dark: true); // Для тёмной темы
```

<a name="theme"></a>
### Управление темой

```php
// Установка цветов фона
$colorManager->background('27, 37, 59');

// Установка цветов контента
$colorManager->content('39, 45, 69');

// Настройка компонентов интерфейса
$colorManager->tableRow('40, 51, 78'); // Строки таблицы
$colorManager->borders('53, 69, 103'); // Границы
$colorManager->dropdowns('48, 61, 93'); // Выпадающие списки
$colorManager->buttons('83, 103, 132'); // Кнопки
$colorManager->dividers('74, 90, 121'); // Разделители
```

<a name="special"></a>
### Специальные методы

`ColorManager` поддерживает динамические методы для всех основных цветов:

```php
$colorManager->primary('120, 67, 233');
$colorManager->secondary('236, 65, 118');
$colorManager->successBg('0, 170, 0');
$colorManager->successText('255, 255, 255');
$colorManager->warningBg('255, 220, 42');
$colorManager->warningText('139, 116, 0');
$colorManager->errorBg('224, 45, 45');
$colorManager->errorText('255, 255, 255');
$colorManager->infoBg('0, 121, 255');
$colorManager->infoText('255, 255, 255');
```

<a name="html"></a>
## Вывод в HTML

Если требуется вывести переменные цветов в `HTML` воспользуйтесь методом `toHtml()`:

```php
$colorManager->toHtml()
```

Результат:

```html
<style>
    :root {
        --primary:120,67,233;
        --secondary:236,65,118;
        /* остальные переменные светлой темы */
    }
    :root.dark {
        /* переменные темной темы */
    }
</style>
```

<a name="conversion"></a>
## Конвертация цветов

`ColorManager` включает утилиту `ColorMutator` для конвертации между HEX и RGB форматами:

```php
use MoonShine\ColorManager\ColorMutator;

// Конвертация в HEX
ColorMutator::toHEX('120, 67, 233'); // '#7843e9'

// Конвертация в RGB
ColorMutator::toRGB('#7843e9'); // '120,67,233'
```

<a name="service-provider"></a>
## Глобальное переопределение

Вы также можете переопределить цвета глобально для всех `Layout` через `MoonShineServiceProvider`:

```php
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     * @param  ColorManager  $colors
     *
     */
    public function boot(
        CoreContract $core,
        ConfiguratorContract $config,
        ColorManagerContract $colors,
    ): void
    {
        $colors->primary('#7843e9');
    }
}
```

> [!WARNING]
> `Layout` загружается после `ServiceProvider` и будет иметь приоритет, поэтому при использовании указания цветов через `ServiceProvider` убедитесь, что они не переопределяются в `Layout`.
