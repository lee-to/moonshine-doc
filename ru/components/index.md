# Основы

- [Описание](#description)
- [Условные методы](#conditional-methods)
- [Изменение отображения](#custom-view)
- [Хук до рендера](#on-before-render)
- [Ассеты](#assets)
- [Трейт Macroable](#macroable)
- [Кастомный компонент](#custom)

---

<a name="description"></a>
## Описание

Практически всё в *MoonShine* это компоненты.
Сами *MoonShineComponent* являются *blade* компонентами и содержат дополнительные удобные методы для взаимодействия в административной панели.

<a name="conditional-methods"></a>
## Условные методы

Отображать компонент можно по условию, воспользовавшись методом `canSee()`.

```php
Box::make()
    ->canSee(function (Box $ctx) {
        return true;
    })
```

Метод `when()` реализует *fluent interface* и выполнит callback, когда первый аргумент, переданный методу, имеет значение true.

```php
when($value = null, ?callable $callback = null, ?callable $default = null)
```

```php
Box::make()
    ->when(fn() => true, fn(Box $ctx) => $ctx)
```

Метод `unless()` обратный методу `when()`.

```php
unless($value = null, ?callable $callback = null, ?callable $default = null)
```

<a name="custom-view"></a>
## Изменение отображения

Когда необходимо изменить view с помощью fluent interface можно воспользоваться методом `customView()`.

```php
customView(string $view, array $data = [])
```

```php
Box::make('Title', [])->customView('component.my-custom-block')
```

<a name="on-before-render"></a>
## Хук до рендера

Если вам необходимо получить доступ к компоненту непосредственно перед рендером, для этого можно воспользоваться методом `onBeforeRender()`.

```php
/**
 * @param  Closure(static $ctx): void  $onBeforeRender
 */
public function onBeforeRender(Closure $onBeforeRender): static
```

```php
Box::make('Title', [])->onBeforeRender(function(Box $ctx) {
    // 
})
```

<a name="assets"></a>
## Ассеты

Для добавления ассетов на лету можно использовать метод `addAssets()`.

```php
Box::make()
    ->addAssets([
        new Css(Vite::asset('resources/css/block.css'))
    ]),
```

Если вы реализуете собственный компонент, то объявить набор ассетов в нем можно двумя способами.

1. Через метод `assets()`:

```php
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
```

2. Через метод `booted()`:

```php
protected function booted(): void
{
    parent::booted();
    
    $this->getAssetManager()
        ->add(Css::make('/css/app.css'))
        ->append(Js::make('/js/app.js'));
}
```

<a name="macroable"></a>
## Трейт Macroable

Всем компонентам доступен трейт `Illuminate\Support\Traits\Macroable` с методами `mixin` и `macro`.
С помощью этого трейта вы можете расширять возможности компонентов, добавляя в них новый функционал без использования наследования.

```php
MoonShineComponent::macro('myMethod', fn() => /*реализация*/)

Box::make()->myMethod()
```

или

```php
// для всех
MoonShineComponent::mixin(new MyNewMethods())

// для конкретного
Box::mixin(new MyNewMethods())
```

<a name="custom"></a>
## Кастомный компонент

Вы можете создать собственный компонент, со своим view и логикой и использовать его в административной панели MoonShine. 
Для этого воспользуйтесь командой:

```shell
php artisan moonshine:component
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#component).
