# Разработка пакетов

- [Основы](#basics)
- [ServiceProvider](#serviceprovider)
- [Traits](#traits)
- [Пример пользовательского поля](#custom-field-example)

---

<a name="basics"></a>
## Основы

Основой `MoonShine` являются пакеты `Laravel`.
Если вы новичок в разработке пакетов `Laravel`, вот несколько ресурсов, которые помогут вам понять основные концепции:

- Глава [Разработка пакетов](https://laravel.com/docs/packages) в документации `Laravel` служит отличным справочным руководством,
- [Курс по разработке пакетов от CutCode](https://learn.cutcode.dev/moonshine),
- [Бесплатное руководство по разработке пакетов от CutCode](https://youtu.be/a_udqxegrRI?si=F8F_v8uGLGLkEbpQ).

<a name="serviceprovider"></a>
## ServiceProvider

Через `ServiceProvider` вашего пакета вы можете автоматически добавлять ресурсы, страницы, создавать меню и правила авторизации, и многое другое.

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

Также вы можете взаимодействовать с `MenuManager`

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

Также вы можете взаимодействовать с `AssetManager` или `ColorManager`

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

Если вам нужно добавить дополнительную логику авторизации в приложение или во внешний пакет, используйте метод `defineAuthorization`.

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

Вы также можете прямо из `ServiceProvider` добавлять компоненты на страницы

```php
public function boot(): void
{
    ProfilePage::pushComponent(fn() => MyPackageComponent::make());
}
```

Не забудьте автоматически подключить ваш `ServiceProvider` в `composer.json`.

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

Вы также можете включать в свой пакет трейты для ресурсов или страниц и изменять логику с помощью `load{TraitName}`/`boot{TraitName}` магических методов.

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
## Пример пользовательского поля

Давайте быстро рассмотрим создание собственного поля! Это будет визуальный редактор на основе плагина `Quill.js`.

Создадим поле с помощью команды `moonshine:field` и выберем, что оно расширяет `Textarea`.

```shell
php artisan moonshine:field Quill
```

Удалим ненужные методы и добавим css/js.

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
            Css::make('/css/moonshine/quill/quill.snow.css'), // тема
            Js::make('/js/moonshine/quill/quill.js'), // библиотека
            Js::make('/js/moonshine/quill/quill-init.js'), // инициализация
        ];
    }
}
```

Также изменим представление поля:

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

Мы взяли `quill.snow.css` и `quill.js` из библиотеки, а инициализация `js` с использованием `Alpine.js` представлена ниже.

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

Пример кода этого поля можно найти [в репозитории](https://github.com/moonshine-software/quill).
