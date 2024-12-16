# Localization

- [Basics](#basics)
- [Configuration](#configuration)
- [Language Switching](#middleware)
- [Russian Language](#ru)

---

<a name="basics"></a>
## Basics

After installing `MoonShine`, a directory `lang/vendor/moonshine` will also appear in the translations directory, where you can add support for a new language or modify the current translations.

> [!NOTE]
> By default, `MoonShine` only includes the English language.
> Look for additional languages in the [Plugins](/plugins) section.

<a name="configuration"></a>
## Configuration

Before you begin, be sure to review the [Configuration](/docs/{{version}}/configuration) section.

### Default Language

~~~tabs
tab: config/moonshine.php
```php
'locale' => 'ru',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->locale('ru');
```
~~~

### Available Languages

~~~tabs
tab: config/moonshine.php
```php
'locales' => ['en', 'ru'],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->locales(['en', 'ru']);
```
~~~

> [!WARNING]
> If you have changed the language in the panel interface, the selection is saved in sessions and will take precedence over the configuration.

<a name="middleware"></a>
## Language Switching

The logic for switching languages in the panel interface is handled by the `middleware` `MoonShine\Laravel\Http\Middleware\ChangeLocale`.
`ChangeLocale` saves the selection in the session and retrieves the value from the session to set the language, or uses the data from the config if a request to change the language is present.

If you want to change the language switching logic to your own, simply replace the `middleware` with your own.

~~~tabs
tab: config/moonshine.php
```php
'middleware' => [
    // ..
    ChangeLocale::class,
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->exceptMiddleware(ChangeLocale::class)->addMiddleware(MyChangeLocale::class);
```
~~~

<a name="ru"></a>
## Russian Language

The `MoonShine` team has also implemented a package with support for the Russian language.

### Installation

```shell
composer require moonshine/ru
```

```shell
php artisan vendor:publish --provider="MoonShine\Ru\Providers\RuServiceProvider"
```

### Configuration

~~~tabs
tab: config/moonshine.php
```php
'locale' => 'ru',
'locales' => ['en', 'ru'],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->locale('ru')->locales(['en', 'ru']);
```
~~~