# Color Manager

- [Basics](#basics)
- [Default Colors](#default-colors)
- [Methods](#methods)
    - [Set Colors](#set-colors)
    - [Get Colors](#get-colors)
    - [Theme Management](#theme)
    - [Special Methods](#special)
- [HTML Output](#html)
- [Color Conversion](#conversion)
- [Global Override](#service-provider)

---

<a name="basics"></a>
## Basics

`ColorManager` is a `MoonShine` component for managing the color scheme of the admin panel. 
It allows you to configure the colors of various interface elements for both light and dark themes.
Can be used in `Layout` or globally in `ServiceProvider`.

<a name="default-colors"></a>
## Default Colors

The following colors are available by default:

```php
// Primary colors
'primary' => '120, 67, 233' // Primary color
'secondary' => '236, 65, 118' // Secondary color
'body' => '27, 37, 59' // Background color

// Dark shades
'dark' => [
    'DEFAULT' => '30, 31, 67',
    50 => '83, 103, 132', // search, toasts, progress bars
    100 => '74, 90, 121', // separators
    200 => '65, 81, 114', // separators
    300 => '53, 69, 103', // borders
    400 => '48, 61, 93',  // dropdowns, buttons, pagination
    500 => '41, 53, 82',  // default button background
    600 => '40, 51, 78',  // table rows
    700 => '39, 45, 69',  // content background
    800 => '27, 37, 59',  // sidebar background
    900 => '15, 23, 42',  // main background
]

// Status colors
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
## Methods

<a name="set-colors"></a>
### Set Colors

```php
// Set a single color
$colorManager->set('primary', '120, 67, 233');

// Set color for dark theme
$colorManager->set('primary', '120, 67, 233', dark: true);

// Bulk assign colors
$colorManager->bulkAssign([
    'primary' => '120, 67, 233',
    'secondary' => '236, 65, 118'
]);
```

<a name="get-colors"></a>
### Get Colors

```php
// Get color
$colorManager->get('primary'); // Returns HEX
$colorManager->get('primary', hex: false); // Returns RGB

// Get shade
$colorManager->get('dark', 500); // Get a specific shade

// Get all colors
$colorManager->getAll(); // For light theme
$colorManager->getAll(dark: true); // For dark theme
```

<a name="theme"></a>
### Theme Management

```php
// Set background colors
$colorManager->background('27, 37, 59');

// Set content colors
$colorManager->content('39, 45, 69');

// Configure interface components
$colorManager->tableRow('40, 51, 78'); // Table rows
$colorManager->borders('53, 69, 103'); // Borders
$colorManager->dropdowns('48, 61, 93'); // Dropdowns
$colorManager->buttons('83, 103, 132'); // Buttons
$colorManager->dividers('74, 90, 121'); // Dividers
```

<a name="special"></a>
### Special Methods

`ColorManager` supports dynamic methods for all primary colors:

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
## HTML Output

If you need to output color variables in `HTML`, use the `toHtml()` method:

```php
$colorManager->toHtml()
```

Result:

```html
<style>
    :root {
        --primary:120,67,233;
        --secondary:236,65,118;
        /* other light theme variables */
    }
    :root.dark {
        /* dark theme variables */
    }
</style>
```

<a name="conversion"></a>
## Color Conversion

`ColorManager` includes the `ColorMutator` utility for converting between HEX and RGB formats.

```php
use MoonShine\ColorManager\ColorMutator;

// Convert to HEX
ColorMutator::toHEX('120, 67, 233'); // '#7843e9'

// Convert to RGB
ColorMutator::toRGB('#7843e9'); // '120,67,233'
```

<a name="service-provider"></a>
## Global Override

You can also globally override colors for all `Layout` via `MoonShineServiceProvider`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
use Illuminate\Support\ServiceProvider;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator; // [tl! collapse:end]

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
> `Layout` loads after `ServiceProvider` and will take precedence, so when using color specifications through `ServiceProvider`, ensure they are not overridden in `Layout`.
