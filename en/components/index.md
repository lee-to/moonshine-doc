# Basics

- [Description](#description)
- [Conditional Methods](#conditional-methods)
- [Custom View](#custom-view)
- [On Before Render Hook](#on-before-render)
- [Assets](#assets)
- [Macroable Trait](#macroable)
- [Custom Component](#custom)

---

<a name="description"></a>
## Description

Almost everything in *MoonShine* consists of components.
The *MoonShineComponent* itself is a *blade* component and contains additional convenient methods for interaction in the admin panel.

<a name="conditional-methods"></a>
## Conditional Methods

You can conditionally display a component using the `canSee()` method.

```php
Box::make()
    ->canSee(function (Box $ctx) {
        return true;
    })
```

The `when()` method implements a *fluent interface* and will execute the callback when the first argument passed to the method is true.

```php
when($value = null, ?callable $callback = null, ?callable $default = null)
```

```php
Box::make()
    ->when(fn() => true, fn(Box $ctx) => $ctx)
```

The `unless()` method is the opposite of the `when()` method.

```php
unless($value = null, ?callable $callback = null, ?callable $default = null)
```

<a name="custom-view"></a>
## Custom View

When it's necessary to change the view using a fluent interface, you can use the `customView()` method.

```php
customView(string $view, array $data = [])
```

```php
Box::make('Title', [])->customView('component.my-custom-block')
```

<a name="on-before-render"></a>
## On Before Render Hook

If you need to access a component immediately before rendering, you can use the `onBeforeRender()` method.

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
## Assets

To add assets on the fly, you can use the `addAssets()` method.

```php
Box::make()
    ->addAssets([
        new Css(Vite::asset('resources/css/block.css'))
    ]),
```

If you are implementing your own component, you can declare a set of assets in two ways.

1. Through the `assets()` method:

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

2. Through the `booted()` method:

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
## Macroable Trait

All components have access to the `Illuminate\Support\Traits\Macroable` trait, which includes the `mixin` and `macro` methods.
With this trait, you can extend the capabilities of components by adding new functionality without using inheritance.

```php
MoonShineComponent::macro('myMethod', fn() => /*implementation*/)

Box::make()->myMethod()
```

or

```php
// for all
MoonShineComponent::mixin(new MyNewMethods())

// for a specific one
Box::mixin(new MyNewMethods())
```

<a name="custom"></a>
## Custom Component

You can create your custom component with its own view and logic and use it in the MoonShine admin panel. 
To do this, use the command:

```shell
php artisan moonshine:component
```

> [!NOTE]
> You can learn about all supported options in the section [Commands](/docs/{{version}}/advanced/commands#component).
