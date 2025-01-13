# Локализация

- [Основы](#basics)
- [Конфигурация](#configuration)
- [Смена языка](#middleware)
- [Русский язык](#ru)

---

<a name="basics"></a>
## Основы

После установки `MoonShine` в директории с переводами также появится директория `lang/vendor/moonshine`, где вы можете добавить поддержку нового языка или изменить текущие переводы.

> [!NOTE]
> По умолчанию в `MoonShine` присутствует только английский язык.
> Дополнительные языки ищите в разделе [Plugins](/plugins).

<a name="configuration"></a>
## Конфигурация

Прежде чем начать, обязательно ознакомьтесь с разделом [Конфигурация](/docs/{{version}}/configuration).

### Язык по умолчанию

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

### Доступные языки

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
> Если вы изменили язык в интерфейсе панели, то выбор сохранился в сессиях и будет в приоритете над конфигурацией.

<a name="middleware"></a>
## Смена языка

За логику смены языка в интерфейсе панели отвечает `middleware` `MoonShine\Laravel\Http\Middleware\ChangeLocale`.
`ChangeLocale` в свою очередь при наличии запроса на смену языка сохраняет выбор в сессии и берет значение из сессии, чтобы установить язык, либо использует данные из конфига.

Если вы хотите изменить логику смены языка на собственную, просто замените `middleware` на свой.

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
## Русский язык

Команда `MoonShine` также реализовала пакет с поддержкой русского языка.

### Установка

```shell
composer require moonshine/ru
```

```shell
php artisan vendor:publish --provider="MoonShine\Ru\Providers\RuServiceProvider"
```

### Конфигурация

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
