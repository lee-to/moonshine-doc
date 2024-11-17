# Руководство по обновлению MoonShine 2.x → 3.0

- [Обновление пакета](#update)
- [Первоначальная настройка](#install)
- [Список изменений](#changes)
  - [Пространства имен](#namespace)
  - [Методы](#methods)
  - [Переменные](#vars)

---

<a name="update"></a>
## Обновление пакета

### 1. Обновление composer.json
Измените версию пакета в вашем composer.json:

```json
{
    "require": {
        "moonshine/moonshine": "^3.0"
    }
}
```

### 2. Создание резервных копий
Перед обновлением необходимо сделать backup следующих файлов:

```bash
mv config/moonshine.php config/moonshine_old.php
mv app/Providers/MoonShineServiceProvider.php app/Providers/MoonShineServiceProvider_old.php
mv app/MoonShine/Pages/Dashboard.php app/MoonShine/Pages/Dashboard_old.php
```

Эти файлы понадобятся для переноса конфигурации и настроек, смотрите раздел [Список изменений](#changes).

### 3. Обновление конфигурации приложения
Если у вас Laravel < 11, то в конфигурации `config/app.php` необходимо найти и удалить `App\Providers\MoonShineServiceProvider::class`

> [!WARNING]
> После выполнения команды `moonshine:install` сервис-провайдер будет добавлен автоматически.

### 4. Запуск обновления

```bash
composer update
```

<a name="install"></a>
## Первоначальная настройка

### 1. Установка новой версии
Выполните команду:

```bash
php artisan moonshine:install
```

Эта команда создаст:
- Новый сервис-провайдер
- Обновленную конфигурацию
- Новый Layout
- Новый Dashboard

### 2. Миграция настроек
1. Перенесите параметры из старого конфига (`moonshine_old.php`) в новый, смотрите документацию по [конфигурации](/docs/{{version}}/configuration)
2. В новой версии изменилась структура меню:
  - Откройте `app/MoonShine/Layouts/MoonShineLayout.php`
  - Скопируйте старое меню из `MoonShineServiceProvider_old.php` в метод `menu`
  - У иконок удалите префикс `heroicons.outline.`
  - Обновите все экземпляры ресурсов на строковые классы:

   ```php
   // Было
   MenuItem::make('Settings', new SettingResource(), 'heroicons.outline.adjustments-vertical')
   
   // Стало
   MenuItem::make('Settings', SettingResource::class, 'adjustments-vertical')
   ```

### 3. Регистрация ресурсов и страниц
В новом `MoonShineServiceProvider.php` необходимо зарегистрировать все ресурсы и страницы:

```php
$core->resources([
    MoonShineUserResource::class,
    MoonShineUserRoleResource::class,
    // Добавьте все ваши ресурсы
]);

$core->pages([
    ...$config->getPages(),
    SettingPage::class,
]);
```

Команды для генерации списков:

Для импорта пространств имен:
```bash
find app/MoonShine/Resources -type f | sed "s/app/use App/" | sed "s|/|\\\|g" | sed "s/.php/;/" | sort
```

Для списка ресурсов:
```bash
find app/MoonShine/Resources -type f -exec basename {} \; | sed "s/.php/::class,/" | sort
```

### 4. Обновление Dashboard
- Перенесите нужные компоненты из `Dashboard_old.php` в новый `Dashboard.php`
- Учтите изменения из раздела [Список изменений](#changes)

### 5. Удаление старых файлов
После успешной миграции удалите:

```bash
# Старый Layout (если существует)
rm app/MoonShine/MoonShineLayout.php

# Бэкапы файлов от 2.x
rm config/moonshine_old.php
rm app/Providers/MoonShineServiceProvider_old.php
rm app/MoonShine/Pages/Dashboard_old.php
```

<a name="changes"></a>
## Список изменений

<a name="namespace"></a>
### Пространства имен

#### Основные изменения
```
MoonShine\Resources\ → MoonShine\Laravel\Resources\
MoonShine\Fields\Relationships\ → MoonShine\Laravel\Fields\Relationships\
MoonShine\Fields\Slug → MoonShine\Laravel\Fields\Slug
MoonShine\Fields\ → MoonShine\UI\Fields\
MoonShine\Decorations\Block → MoonShine\UI\Components\Layout\Div
MoonShine\Decorations\ → MoonShine\UI\Components\Layout\* 
    (некоторые на MoonShine\UI\Components\, проверьте вручную)
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

#### Дополнительные пакеты
При необходимости установите и обновите пространства имен для:

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

5. [EasyMDE](https://github.com/moonshine-software/tinymce):
  - `MoonShine\Fields\TinyMce`

<a name="methods"></a>
### Методы

#### Основные изменения
1. Создание экземпляров ресурсов и страниц:

```php
// Было
new NameResource()

// Стало
// Рекомендуется через DI
// или:
app(NameResource::class)
```

2. Сигнатуры методов:
```php
// Было
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

// Стало
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

3. Изменения в методах полей:
```php
// Было
public function fields(): array

// Стало
protected function indexFields(): iterable // допускает только поля
protected function detailFields(): iterable
protected function formFields(): iterable
```

4. Атрибуты таблиц:
```php
// Новый формат
TableBuilder::make()
    ->tdAttributes(fn(mixed $data, int $row, TableBuilder $table): array => 
        $row === 3 ? ['class' => 'bgc-yellow'] : []
    ) 
    ->tdAttributes(fn(mixed $data, int $row, int $cell, TableBuilder $table): array => 
        $cell === 3 ? ['align' => 'right'] : []
    ) 
```

5. Изменения в других методах:
- Хелпер `to_page` → `toPage`
- Вместо метода `columnSpan` у компонентов использовать метод компонента `Column`: `Column::make([...])->columnSpan(..)`

#### Удаленные методы
1. Методы отображения полей:
  - hideOnIndex, showOnIndex
  - hideOnForm, showOnForm
  - hideOnCreate, showOnCreate
  - hideOnUpdate, showOnUpdate
  - hideOnDetail, showOnDetail
  - hideOnAll
  - hideOnExport, showOnExport
  - useOnImport (используйте пакет [import-export](https://github.com/moonshine-software/import-export))

2. Хелперы:
  - form
  - table
  - actionBtn

<a name="vars"></a>
### Переменные

#### Основные изменения
1. Иконки:
  - Удалите префиксы `heroicons.outline` и `heroicons.solid`
  - Теперь эти иконки доступны по умолчанию

2. Меню:

```php
// Было
MenuItem::make('Settings', new SettingResource(), 'heroicons.outline.adjustments-vertical')

// Стало
MenuItem::make('Settings', SettingResource::class, 'adjustments-vertical')
```

3. Асинхронные события:
```php
// Было
->async(asyncUrl: ..., asyncEvents: ...)
'table-updated-{name}'

// Стало
->async(url: ..., events: ...)
AlpineJs::event(JsEvent::TABLE_UPDATED, {name})
```

4. Направление сортировки:
```php
// Было
protected string $sortDirection = 'ASC';

// Стало
protected SortDirection $sortDirection = SortDirection::ASC;
```

5. Assets:
```php
// Было
$assets // строки

// Стало
$assets // принимает AssetElementContract, такие как Css, InlineCss, Js, InlineJs
```

#### Удаленные переменные
- `protected bool $isAsync = true;` (теперь включено по умолчанию)
