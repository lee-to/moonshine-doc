---
title: Upgrade guide
---

# MoonShine Update Guide 2.x → 3.0

- [Package Update](#update)
- [Initial Setup](#install)
- [Changelog](#changes)
  - [Namespaces](#namespace)
  - [Methods](#methods)
  - [Variables](#vars)

---

<a name="update"></a>
## Package Update

### 1. Update composer.json
Change the package version in your composer.json:

```json
{
    "require": {
        "moonshine/moonshine": "^3.0"
    }
}
```

### 2. Create Backups
Before updating, you need to back up the following files:

```bash
mv config/moonshine.php config/moonshine_old.php
mv app/Providers/MoonShineServiceProvider.php app/Providers/MoonShineServiceProvider_old.php
mv app/MoonShine/Pages/Dashboard.php app/MoonShine/Pages/Dashboard_old.php
```

These files will be needed for transferring configurations and settings. See the [Changelog](#changes) section.

### 3. Update Application Configuration
If you have Laravel < 11, you need to find and remove `App\Providers\MoonShineServiceProvider::class` from the `config/app.php` configuration.

> [!WARNING]
> After running the `moonshine:install` command, the service provider will be added automatically.

### 4. Run the Update

```bash
composer update
```

<a name="install"></a>
## Initial Setup

### 1. Install the New Version
Run the command:

```bash
php artisan moonshine:install
```

This command will create:
- A new service provider
- Updated configuration
- A new Layout
- A new Dashboard

### 2. Migrate Settings
1. Transfer the parameters from the old config (`moonshine_old.php`) to the new one. See the documentation on [configuration](/docs/{{version}}/configuration).
2. The menu structure has changed in the new version:
  - Open `app/MoonShine/Layouts/MoonShineLayout.php`.
  - Copy the old menu from `MoonShineServiceProvider_old.php` to the `menu` method.
  - Remove the `heroicons.outline.` prefix from icons.
  - Update all resource instances to string classes:

   ```php
   // Was
   MenuItem::make('Settings', new SettingResource(), 'heroicons.outline.adjustments-vertical')

   // Now
   MenuItem::make('Settings', SettingResource::class, 'adjustments-vertical')
   ```

### 3. Register Resources and Pages
In the new `MoonShineServiceProvider.php`, you need to register all resources and pages:

```php
$core->resources([
    MoonShineUserResource::class,
    MoonShineUserRoleResource::class,
    // Add all your resources
]);

$core->pages([
    ...$config->getPages(),
    SettingPage::class,
]);
```

Commands to generate lists:

For importing namespaces:
```bash
find app/MoonShine/Resources -type f | sed "s/app/use App/" | sed "s|/|\\\|g" | sed "s/.php/;/" | sort
```

For the list of resources:
```bash
find app/MoonShine/Resources -type f -exec basename {} \; | sed "s/.php/::class,/" | sort
```

### 4. Update Dashboard
- Move required components from `Dashboard_old.php` to the new `Dashboard.php`.
- Take note of the changes in the [Changelog](#changes) section.

### 5. Remove Old Files
After successful migration, remove:

```bash
# Old Layout (if it exists)
rm app/MoonShine/MoonShineLayout.php

# Backups of files from 2.x
rm config/moonshine_old.php
rm app/Providers/MoonShineServiceProvider_old.php
rm app/MoonShine/Pages/Dashboard_old.php
```

<a name="changes"></a>
## Changelog

<a name="namespace"></a>
### Namespaces

#### Main Changes
```
MoonShine\Resources\ → MoonShine\Laravel\Resources\
MoonShine\Fields\Relationships\ → MoonShine\Laravel\Fields\Relationships\
MoonShine\Fields\Slug → MoonShine\Laravel\Fields\Slug
MoonShine\Fields\ → MoonShine\UI\Fields\
MoonShine\Decorations\Block → MoonShine\UI\Components\Layout\Box
MoonShine\Decorations\ → MoonShine\UI\Components\Layout\*
    (some to MoonShine\UI\Components\, check manually)
MoonShine\Enums\ → MoonShine\Support\Enums\
MoonShine\Pages\ → MoonShine\Laravel\Pages\
MoonShine\Models\ → MoonShine\Laravel\Models\
MoonShine\QueryTags\ → MoonShine\Laravel\QueryTags\
MoonShine\Attributes\ → MoonShine\Support\Attributes\
MoonShine\Components\ → MoonShine\UI\Components\
MoonShine\Metrics\ → MoonShine\UI\Components\Metrics\Wrapped\
MoonShine\ActionButtons\ → MoonShine\UI\Components\
MoonShine\Http\Responses\ → MoonShine\Laravel\Http\Responses\
MoonShine\Http\Controllers\ → MoonShine\Laravel\Http\Controllers\
MoonShine\MoonShineAuth → MoonShine\Laravel\MoonShineAuth
```

#### Additional Packages
If needed, install and update namespaces for:

1. [Import/Export](https://github.com/moonshine-software/import-export):
  - `MoonShine\Laravel\Handlers\ExportHandler`
  - `MoonShine\Laravel\Handlers\ImportHandler`

2. [Apexcharts](https://github.com/moonshine-software/apexcharts):
  - `MoonShine\UI\Components\Metrics\Wrapped\DonutChartMetric`
  - `MoonShine\UI\Components\Metrics\Wrapped\LineChartMetric`

3. [Ace Editor](https://github.com/moonshine-software/ace):
  - `MoonShine\Fields\Code`

4. [EasyMDE](https://github.com/moonshine-software/easymde):
  - `MoonShine\Fields\Markdown`

5. [TinyMce](https://github.com/moonshine-software/tinymce):
  - `MoonShine\Fields\TinyMce`

<a name="methods"></a>
### Methods

#### Main Changes
1. Creating instances of resources and pages:

```php
// Was
new NameResource()

// Now
// Recommended via DI
// or:
app(NameResource::class)
```

2. Method signatures:
```php
// Was
public function components(): array
public function title(): string
public function breadcrumbs(): string
public function rules(Model $item): array
protected function afterUpdated(Model $user): Model
public function detailButtons(): array
public function modifyListComponent(MoonShineRenderable|TableBuilder $table): MoonShineRenderable
$field->getData()
detailPageUrl
MoonShineAuth::guard()
getActiveActions()

// Now
protected function components(): iterable
public function getTitle(): string
public function getBreadcrumbs(): string
protected function rules($item): array
protected function afterUpdated($user): Model
public function detailButtons(): ListOf
public function modifyListComponent(ComponentContract $table): ComponentContract
$field->getData()->getOriginal()
getDetailPageUrl
MoonShineAuth::getGuard()
activeActions()
```

3. Changes in field methods:
```php
// Was
public function fields(): array

// Now
protected function indexFields(): iterable // only accepts fields
protected function detailFields(): iterable
protected function formFields(): iterable
```

4. Table attributes:
```php
// New format
TableBuilder::make()
    ->tdAttributes(fn(mixed $data, int $row, TableBuilder $table): array =>
        $row === 3 ? ['class' => 'bgc-yellow'] : []
    )
    ->tdAttributes(fn(mixed $data, int $row, int $cell, TableBuilder $table): array =>
        $cell === 3 ? ['align' => 'right'] : []
    )
```

5. Changes in other methods:
- Helper `to_page` → `toPage`
- Instead of the `columnSpan` method in components, use the component method `Column`: `Column::make([...])->columnSpan(..)`
- Instead of `expansion('url')`, use the `suffix('url')` method

#### Removed Methods
1. Field display methods:
  - hideOnIndex, showOnIndex
  - hideOnForm, showOnForm
  - hideOnCreate, showOnCreate
  - hideOnUpdate, showOnUpdate
  - hideOnDetail, showOnDetail
  - hideOnAll
  - hideOnExport, showOnExport
  - useOnImport (use the [import-export](https://github.com/moonshine-software/import-export) package)

2. Helpers:
  - form
  - table
  - actionBtn

<a name="vars"></a>
### Variables

#### Main Changes
1. Icons:
  - Remove the `heroicons.outline` and `heroicons.solid` prefixes.
  - These icons are now available by default.

2. Menu:

```php
// Was
MenuItem::make('Settings', new SettingResource(), 'heroicons.outline.adjustments-vertical')

// Now
MenuItem::make('Settings', SettingResource::class, 'adjustments-vertical')
```

3. Asynchronous events:
```php
// Was
->async(asyncUrl: ..., asyncEvents: ...)
'table-updated-{name}'

// Now
->async(url: ..., events: ...)
AlpineJs::event(JsEvent::TABLE_UPDATED, {name})
```

4. Sort direction:
```php
// Was
protected string $sortDirection = 'ASC';

// Now
protected SortDirection $sortDirection = SortDirection::ASC;
```

5. Assets:
```php
// Was
$assets // strings

// Now
$assets // accepts AssetElementContract, such as Css, InlineCss, Js, InlineJs
For management, use [AssetManager](/docs/{{version}}/appearance/assets).
```

#### Removed Variables
- `protected bool $isAsync = true;` (now enabled by default)
