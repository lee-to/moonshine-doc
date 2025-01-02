# Assets

- [Основы](#basics)
- [Типы ресурсов](#asset-types)
- [Коллекции ресурсов](#asset-collections)
- [Модификация ресурсов](#asset-modification)
- [Версионирование](#versioning)
- [Добавление ресурсов](#how-to-add)
    - [Глобально](#global)
    - [Шаблон](#layout)
    - [CrudResource](#resource)
    - [Page](#page)
    - [Component](#component)
    - [Field](#field)

---

<a name="basics"></a>
## Основы

*AssetManager* в *MoonShine* предоставляет удобный способ управления *CSS* и *JavaScript* ресурсами вашей административной панели.
Он поддерживает различные типы ресурсов, включая внешние файлы, встроенный код и версионирование.

<a name="asset-types"></a>
## Типы ресурсов

В *MoonShine* есть несколько типов ресурсов:

- `MoonShine\AssetManager\Js` - js через тег `<script src>`
- `MoonShine\AssetManager\Css` - css через тег `<link>`
- `MoonShine\AssetManager\InlineCss` - css через тег `<style>`
- `MoonShine\AssetManager\InlineJs` - js через тег `<script>`
- `MoonShine\AssetManager\Raw` - произвольный контент в `head`

### JavaScript файлы

```php
use MoonShine\AssetManager\Js;

// Базовое подключение
Js::make('/js/app.js')

// С отложенной загрузкой
Js::make('/js/app.js')->defer()

// С атрибутами
Js::make('/js/app.js')->customAttributes([
    'data-module' => 'main'
])
```

### CSS файлы

```php
use MoonShine\AssetManager\Css;

// Базовое подключение
Css::make('/css/styles.css')

// С отложенной загрузкой
Css::make('/css/styles.css')->defer()

// С атрибутами
Css::make('/css/styles.css')->customAttributes([
    'media' => 'print'
])
```

### Встроенный JavaScript

```php
use MoonShine\AssetManager\InlineJs;

InlineJs::make(<<<JS
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Loaded");
    });
JS);
```

### Встроенный CSS

```php
use MoonShine\AssetManager\InlineCss;

InlineCss::make(<<<CSS
    .custom-class {
        color: red;
    }
CSS)
```

### Raw-контент

```php
use MoonShine\AssetManager\Raw;

Raw::make('<link rel="preconnect" href="https://fonts.googleapis.com">')
```

<a name="asset-collections"></a>
## Коллекции ресурсов

*AssetManager* позволяет управлять порядком загрузки ресурсов. 
Мы рекомендуем использовать DI, чтобы начать взаимодействие с *AssetManager*, за сервис отвечает интерфейс *MoonShine\Contracts\AssetManager\AssetManagerContract*.
Также MoonShine предоставляет удобные методы взаимодействия с *AssetManager* в разных сущностях, таких как *CrudResource*, *Page*, *Layout*, *Component*, *Field*

```php
use MoonShine\AssetManager\Js;

// Добавить ресурсы в конец
$assetManager->append([
    Js::make('/js/last.js')
]);

// Добавить ресурсы в начало
$assetManager->prepend([
    Js::make('/js/first.js')
]);

// Добавить ресурсы в порядке добавления
$assetManager->add([
    Js::make('/js/middle.js')
]);
```

> [!TIP]
> Вы также можете воспользоваться хелпером `moonshine()->getAssetManager()`

Метод *append* всегда будет добавлять ресурсы до основного списка из *CrudResource*, *Page*, *Layout*, *Component*, *Field*, а *prepend* после.
Метод *add* будет зависеть от жизненного цикла приложения. Допустим вы добавляете ассеты в *ModelResource*,
но перед отображением страницы будет вызван *Layout*, который также в свою очередь добавит ассеты, тем самым ассеты *Layout* добавятся в конце.

<a name="asset-modification"></a>
## Модификация ресурсов

Вы можете модифицировать коллекцию ресурсов с помощью замыканий:

```php
$assetManager->modifyAssets(function($assets) {
    // Модифицируем коллекцию ресурсов
    return array_filter($assets, function($asset) {
        return !str_contains($asset->getLink(), 'remove-this');
    });
});
```

<a name="versioning"></a>
## Версионирование

*AssetManager* поддерживает версионирование ресурсов для управления кешированием, по умолчанию будет использоваться версия *MoonShine*, но вы можете переопределить у конкретного ресурса:

```php
use MoonShine\AssetManager\Js;

// Добавление версии к отдельному ресурсу
Js::make('/js/app.js')->version('1.0.0')

// Результат: /js/app.js?v=1.0.0
```

Версионирование автоматически добавляет параметр `v` к URL ресурса. Если URL уже содержит параметры запроса, версия будет добавлена через `&`.


<a name="how-to-add"></a>
## Добавление ресурсов

<a name="global"></a>
### Глобально

```php
// MoonShineServiceProvider
use MoonShine\AssetManager\Js;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;

public function boot(
    CoreContract $core,
    ConfiguratorContract $config,
    AssetManagerContract $assets,
): void
{
    $assets->add(Js::make('/js/app.js'));
}
```

<a name="layout"></a>
### Шаблон

```php
use Illuminate\Support\Facades\Vite;
use MoonShine\AssetManager\Js;

final class MoonShineLayout extends CompactLayout
{
    protected function assets(): array
    {
        return [
            Js::make(Vite::asset('resources/js/app.js'))
        ];
    }
}
```
<a name="resource"></a>
### CrudResource

```php
use MoonShine\AssetManager\InlineJs;
use MoonShine\AssetManager\Js;

protected function onLoad(): void
{
    $this->getAssetManager()
        ->prepend(InlineJs::make('alert(1)'))
        ->append(Js::make('/js/app.js'));
}
```

<a name="page"></a>
### Page

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

protected function onLoad(): void
{
    parent::onLoad();
    
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```

<a name="component"></a>
### Component

#### На лету

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\UI\Components\Layout\Box;

Box::make()->addAssets([
    Js::make('/js/custom.js'),
    Css::make('/css/styles.css')
]);
```

#### При создании компонента

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\UI\Components\MoonShineComponent;

final class MyComponent extends MoonShineComponent
{
    /**
     * @return list<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            Js::make('/js/custom.js'),
            Css::make('/css/styles.css')
        ];
    }
}
```

#### При создании компонента через AssetManager

```php
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\UI\Components\MoonShineComponent;

final class MyComponent extends MoonShineComponent
{
    protected function booted(): void
    {
        parent::booted();
        
        $this->getAssetManager()
          ->add(Css::make('/css/app.css'))
          ->append(Js::make('/js/app.js'));
    }
}
```

<a name="field"></a>
### Field

То же самое как и у *Component*, так как *Field* является компонентом