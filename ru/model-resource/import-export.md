# Импорт / Экспорт
 
- [Основы](#basics)
- [Импорт](#import)
  - [Поля](#import-fields)
  - [Настройка](#import-settings)
  - [События](#import-events)
- [Экспорт](#export)
  - [Поля](#export-fields)
  - [Настройка](#export-settings)
- [Общие методы](#methods)
- [Кастомная реализация](#custom)

---

<a name="basics"></a>
## Основы

Чтобы пользоваться импортом и экспортом в ресурсах MoonShine, необходимо установить зависимость через `composer`:

```shell
composer require moonshine/import-export
```

Далее для использования в ресурсе добавьте трейт `ImportExportConcern` и реализуйте интерфейс `HasImportExportContract`

```php
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\ImportExport\Contracts\HasImportExportContract;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    
    // ...
}
```

Все готово! Далее просто объявляйте поля

<a name="import"></a>
## Импорт

<a name="import-fields"></a>
### Поля

Для импорта необходимо в ресурсе объявить поля, которые будут участвовать в импорте

```php
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    
    // ...
    
    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name'),
        ];
    }
}
```

> [!WARNING]
> Обязательно добавляйте в импорт `ID`, иначе записи будут только добавляться, без обновления существующих.

Если вам необходимо модифицировать значение при импорте, необходимо воспользоваться методом поля `fromRaw`

```php
use App\Enums\StatusEnum;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Enum;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    
    // ...
    
    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Enum::make('Status')
                ->attach(StatusEnum::class)
                ->fromRaw(static fn(string $raw, Enum $ctx) => StatusEnum::tryFrom($raw)), 
        ];
    }
}
```

<a name="import-settings"></a>
### Настройка

Для настройки импорта доступны опциональные методы, чтобы ими воспользоваться необходимо добавить метод `import`, который возвращает `ImportHandler`:

```php
use MoonShine\ImportExport\ImportHandler;
use MoonShine\UI\Components\ActionButton;

protected function import(): ?Handler
{
    return ImportHandler::make(__('moonshine::ui.import'))
        // Указать id пользователей, которые получат уведомление об окончании операции
        ->notifyUsers(fn(ImportHandler $ctx) => [auth()->id()]) 
        // Выбор диска
        ->disk('public') 
        // Выбор директории для сохранения файла импорта
        ->dir('/imports') 
        // Удалять файл после импорта
        ->deleteAfter() 
        // Разделитель для csv
        ->delimiter(',')
        // Модификация кнопки
        ->modifyButton(fn(ActionButton $btn) => $btn->class('my-class'))
  ; 
}
```

> [!NOTE]
> Если метод `import()` вернет `NULL`, то кнопка импорта не будет отображаться на индексной странице.

<a name="import-events"></a>
### События

Для изменения логики работы импорта можно воспользоваться событиями ресурса модели.

```php
public function beforeImportFilling(array $data): array
{
    return $data;
}

public function beforeImported(mixed $item): mixed
{
    return $item;
}

public function afterImported(mixed $item): mixed
{
    return $item;
}
```

<a name="export"></a>
## Экспорт

В админ-панели MoonShine можно реализовать экспорт всех данных с учетом текущей фильтрации и сортировки.

> [!NOTE]
> По умолчанию данные экспортируются в формате `xlsx`, но существует возможность изменить формат на `csv` через метод `csv()` класса `ExportHandler`.

<a name="export-fields"></a>
### Поля

Для экспорта необходимо в ресурсе объявить поля, которые будут участвовать в экспорте

```php
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Position;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    
    // ...
    
    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Position::make(),
            Text::make('Name'),
        ];
    }
}
```

Если вам необходимо модифицировать значение при экспорте, необходимо воспользоваться методом поля `modifyRawValue`

```php
use App\Enums\StatusEnum;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Enum;

/**
 * @extends ModelResource<Category>
 */
class CategoryResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;
    
    // ...
    
    protected function importFields(): iterable
    {
        return [
            ID::make(),
            Enum::make('Status')
                ->attach(StatusEnum::class)
                ->modifyRawValue(static fn(StatusEnum $raw, Enum $ctx) => $raw->value), 
        ];
    }
}
```

<a name="export-settings"></a>
### Настройка

Для настройки экмпорта доступны опциональные методы, чтобы ими воспользоваться необходимо добавить метод `export`, который возвращает `ExportHandler`:

```php
use MoonShine\ImportExport\ExportHandler;
use MoonShine\UI\Components\ActionButton;

protected function export(): ?Handler
{
    return ExportHandler::make(__('moonshine::ui.export'))
        // Указать id пользователей, которые получат уведомление об окончании операции
        ->notifyUsers(fn() => [auth()->id()]) 
        // Выбор диска
        ->disk('public') 
        // Наименование файла
        ->filename(sprintf('export_%s', date('Ymd-His'))) 
        // Выбор директории сохранения файла экспорта
        ->dir('/exports') 
        // Если необходимо экспортировать в формате csv
        ->csv() 
        // Разделитель для csv
        ->delimiter(',') 
        // Экспорт с подтверждением
        ->withConfirm()
        // Модификация кнопки
        ->modifyButton(fn(ActionButton $btn) => $btn->class('my-class'))
  ; 
}
```

> [!NOTE]
> Если метод `export()` вернет `NULL`, то кнопка экспорта не будет отображаться на индексной странице.

<a name="methods"></a>
## Общие методы

`ImportHandler` и `ExportHandler` расширяют базовый класс `Handler` который реализует дополнительные методы.

#### icon

```php
// иконка для кнопки
icon(string $icon)
```

#### queue

```php
// запускать процессы в фоне
queue()
```

#### modifyButton

```php
use MoonShine\UI\Components\ActionButton;
// модификация кнопки
modifyButton(fn(ActionButton $btn) => $btn->class('my-class'))
```

#### notifyUsers

```php
// выбор пользователей для уведомления
notifyUsers(fn() => [auth()->id()]) 
```

#### when

```
// методы по условию
when($value = null, callable $callback = null, callable $default = null)
```

`$value` - условие,  
`$callback` - `callback` функция, которая будет выполнена, если условие имеет значение `TRUE`, 
`$default` - `callback` функция, которая будет выполнена, если условие имеет значение `FALSE`.

```php
use MoonShine\ImportExport\ImportHandler;
//...

protected function import(): ?Handler
{
    return ImportHandler::make('Import')
        ->when(
            true,
            fn($handler) => $handler->delimiter(','),
            fn($handler) => $handler->delimiter(';')
        );
}

//...
```

#### unless

```php
// методы по условию
unless($value = null, callable $callback = null, callable $default = null)
```

`$value` - условие,  
`$callback` - `callback` функция, которая будет выполнена, если условие имеет значение `FALSE`,
`$default` - `callback` функция, которая будет выполнена, если условие имеет значение `TRUE`.

> [!NOTE]
> Метод `unless()` является обратным методу `when()`.

<a name="custom"></a>
## Кастомная реализация

Может возникнуть ситуация, когда вы захотите изменить реализацию импорта или экспорта. Для этого необходимо реализовать свой класс расширяющий `ImportHandler` или `ExportHandler`.

Класс можно сгенерировать, воспользовавшись консольной командой:

```shell
php artisan moonshine:handler
```

После выполнения команды будет создан базовый класс `Handler` в директории `app/MoonShine/Handlers`.

> [!NOTE]
> Не забудьте заменить наследование с `Handler` на `ImportHandler` или `ExportHandler`
