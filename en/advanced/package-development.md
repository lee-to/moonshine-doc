# Package Development

- [Basics](#basics)
- [ServiceProvider](#serviceprovider)
- [Traits](#traits)
- [Custom Field Example](#custom-field-example)

---

<a name="basics"></a>
## Basics

The foundation of `MoonShine` is the `Laravel` packages.
If you are new to `Laravel` package development, here are some resources to help you understand the core concepts:

- The chapter on [Package Development](https://laravel.com/docs/packages) in the `Laravel` documentation serves as an excellent reference guide,
- [Package Development Course by CutCode](https://learn.cutcode.dev/moonshine),
- [Free Guide to Package Development by CutCode](https://youtu.be/a_udqxegrRI?si=F8F_v8uGLGLkEbpQ).

<a name="serviceprovider"></a>
## ServiceProvider

Through your package's `ServiceProvider`, you can automatically add resources, pages, create menus, and authorization rules, among other things.

```php
namespace Author\MoonShineMyPackage;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;

class MyPackageServiceProvider extends ServiceProvider
{
    /** @param MoonShine $core */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                MyPackageResource::class
            ])
            ->page([
                MyPackagePage::class
            ])
        ;
    }
}
```

You can also interact with the `MenuManager`.

```php
namespace Author\MoonShineMyPackage;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Contracts\MenuManager\MenuManagerContract;

class MyPackageServiceProvider extends ServiceProvider
{
    /** @param MoonShine $core */
    public function boot(CoreContract $core, MenuManagerContract $menu): void
    {
        $menu->add([
            MenuItem::make('MyPackagePage', MyPackagePage::class)
        ]);
    }
}
```

You can also interact with the `AssetManager` or `ColorManager`.

```php
use MoonShine\Contracts\AssetManager\AssetManagerContract;

// ..

public function boot(CoreContract $core, AssetManagerContract $assets): void
{
    $assets->add([
        InlineCss::make('body {background: red;}')
    ]);
}
```

```php
use MoonShine\Contracts\ColorManager\ColorManagerContract;

// ...

public function boot(CoreContract $core, ColorManagerContract $colors): void
{
    $colors
        ->background('#A3C3D9')
        ->content('#A3C3D9')
        ->tableRow('#AE76A6')
        ->dividers('#AE76A6')
        ->borders('#AE76A6')
        ->buttons('#AE76A6')
        ->primary('#CCD6EB')
        ->secondary('#AE76A6');
}
```

If you need to add additional authorization logic to the application or an external package, use the `defineAuthorization` method.

```php
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

//..

/**
 * @param  MoonShineConfigurator  $configurator
 */
public function boot(ConfiguratorContract $configurator): void
{
    $configurator->authorizationRules(
        static function (ResourceContract $resource, Model $user, Ability $ability): bool {
            return true;
        }
    );
}
```

You can also directly add components to the pages from the `ServiceProvider`.

```php
public function boot(): void
{
    ProfilePage::pushComponent(fn() => MyPackageComponent::make());
}
```

Don’t forget to automatically include your `ServiceProvider` in `composer.json`.

```json
"extra": {
    "laravel": {
        "providers": [
            "Author\\MoonShineMyPackage\\MyPackageServiceProvider"
        ]
    }
}
```

<a name="traits"></a>
## Traits

You can also include traits for resources or pages in your package and change the logic using the `load{TraitName}`/`boot{TraitName}` magic methods.

```php
trait HasMyPackageTrait
{
    public function loadHasMyPackageTrait(): void
    {
        $this->getFormPage()->addAssets([
            Js::make('vendor/my-package/js/app.js'),
            Css::make('vendor/my-package/css/app.css'),
        ]);
    }

    public function modifyFormComponent(ComponentContract $component): ComponentContract
    {
        return parent::modifyFormComponent($component)->fields([
            Modal::make(
                'This is my package modal.',
                ''
            ),
            ...$component->getFields()->toArray(),
        ]);
    }
}
```

<a name="custom-field-example"></a>
## Custom Field Example

Let's quickly look at creating a custom field! This will be a visual editor based on the `Quill.js` plugin.

We will create a field using the `moonshine:field` command and choose to extend `Textarea`.

```shell
php artisan moonshine:field Quill
```

Remove the unnecessary methods and add css/js.

```php
namespace App\MoonShine\Fields;

use MoonShine\UI\Fields\Textarea;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

final class Quill extends Textarea
{
    protected string $view = 'moonshine-quill::fields.quill';

    public function assets(): array
    {
        return [
            Css::make('/css/moonshine/quill/quill.snow.css'), // theme
            Js::make('/js/moonshine/quill/quill.js'), // library
            Js::make('/js/moonshine/quill/quill-init.js'), // initialization
        ];
    }
}
```

We will also change the field view:

```blade
<div x-data="quill">
    <div class="ql-editor" :id="$id('quill')" style="height: auto;">{!! $value ?? '' !!}</div>

    <x-moonshine::form.textarea
        :attributes="$attributes->merge([
            'class' => 'ql-textarea',
            'style' => 'display: none;'
        ])->except('x-bind:id')"
    >{!! $value ?? '' !!}</x-moonshine::form.textarea>
</div>
```

We took `quill.snow.css` and `quill.js` from the library, and the js initialization using `Alpine.js` is provided below.

```js
document.addEventListener('alpine:init', () => {
    Alpine.data('quill', () => ({
        textarea: null,
        editor: null,

        init() {
            this.textarea = this.$root.querySelector('.ql-textarea')
            this.editor = this.$root.querySelector('.ql-editor')

            const t = this

            this.$nextTick(function() {
                let quill = new Quill(`#${t.editor.id}`, {
                    theme: 'snow'
                });

                quill.on('text-change', () => {
                    t.textarea.value = t.editor.innerHTML || '';
                    t.textarea.dispatchEvent(new Event('change'));
                });
            })
        },
    }))
})
```

You can find the code example of this field in the [repository](https://github.com/moonshine-software/quill).
