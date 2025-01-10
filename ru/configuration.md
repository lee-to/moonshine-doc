# Конфигурация

- [Введение](#introduction)
- [Способы конфигурации](#configuration-methods)
  - [Конфигурация через файл moonshine.php](#config-file)
  - [Конфигурация через MoonShineServiceProvider](#service-provider)
- [Основные настройки](#basic-settings)
  - [Опции](#options)
  - [Заголовок](#title)
  - [Логотип](#logo)
  - [Middleware](#middleware)
  - [Маршрутизация](#routing)
  - [Аутентификация](#authentication)
  - [Локализация](#localization)
  - [Хранилище](#storage)
  - [Layout](#layout)
  - [Формы](#forms)
  - [Страницы](#pages)
  - [Главная страница](#home-url)
- [Получение страниц и форм](#pages-forms)
- [Полный список параметров конфигурации](#configuration-options)
- [Выбор метода конфигурации](#choosing-configuration-method)

---

<a name="introduction"></a>
## Введение

`MoonShine` предоставляет гибкие возможности для конфигурации вашего приложения.
В этом разделе мы рассмотрим два основных способа конфигурации и основные настройки.

<a name="configuration-methods"></a>
## Способы конфигурации

`MoonShine` можно настроить двумя способами:

1. Через файл конфигурации `config/moonshine.php`
2. Через `MoonShineServiceProvider` с использованием класса `MoonShineConfigurator`

<a name="config-file"></a>
### Конфигурация через файл moonshine.php

Файл `config/moonshine.php` содержит все доступные настройки MoonShine. Вы можете изменять эти настройки напрямую в файле.

Пример содержимого файла `moonshine.php`:

```php
return [
    'title' => env('MOONSHINE_TITLE', 'MoonShine'),
    'logo' => '/assets/logo.png',
    'domain' => env('MOONSHINE_DOMAIN'),
    'prefix' => 'admin',
    'auth' => [
        'enabled' => true,
        'guard' => 'moonshine',
    ],
    'use_migrations' => true,
    'use_notifications' => true,
    'use_database_notifications' => true,
    'middleware' => [
        // ...
    ],
    'layout' => \MoonShine\Laravel\Layouts\AppLayout::class,

    // ...
];
```

#### Частичная конфигурация

Альтернативно, вы можете оставить в файле `moonshine.php` только те параметры, которые отличаются от значений по умолчанию.
Это делает конфигурацию более чистой и легкой для понимания.
Пример оптимизированного содержимого файла `moonshine.php`:

```php
return [
    'title' => 'Мое приложение MoonShine',
    'use_migrations' => true,
    'use_notifications' => true,
    'use_database_notifications' => true,
];
```

> [!NOTE]
> `use_migrations`, `use_notifications`, `use_database_notifications` должны присутствовать всегда либо в `moonshine.php`, либо в `MoonShineServiceProvider`

> [!NOTE]
> Все остальные параметры, не указанные в файле, будут использовать значения по умолчанию.

<a name="service-provider"></a>
### Конфигурация через MoonShineServiceProvider

Альтернативный способ настройки - `MoonShineServiceProvider`. Этот метод предоставляет более программный подход к конфигурации.

Пример конфигурации в `MoonShineServiceProvider`:

```php
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(
        CoreContract $core,
        ConfiguratorContract $config,
    ): void
    {
        $config
            ->title('Мое приложение')
            ->logo('/assets/logo.png')
            ->prefixes('admin', 'page', 'resource')
            ->guard('moonshine')
            ->authEnable()
            ->useMigrations()
            ->useNotifications()
            ->useDatabaseNotifications()
            ->middleware([
                // ...
            ])
            ->layout(\MoonShine\Laravel\Layouts\AppLayout::class)
            // ...
        ;

        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
            ])
            ->pages([
                ...$config->getPages(),
            ])
        ;
    }
}
```

> [!NOTE]
> Конфигурация через `MoonShineServiceProvider` имеет приоритет над настройками в файле `moonshine.php`.
> При использовании этого метода вы можете полностью удалить файл moonshine.php из вашего проекта.

<a name="basic-settings"></a>
## Основные настройки

Независимо от выбранного способа конфигурации, вы можете настроить следующие основные параметры:

<a name="options"></a>
### Опции

- `use_migrations` - Использовать публикацию миграций системы по умолчанию (`moonshine_users`, `moonshine_user_roles`),
- `use_notifications` - Использовать систему уведомлений,
- `use_database_notifications` - Использовать систему уведомлений Laravel на основе драйвера базы данных,
- `dir` - Директория для `MoonShine` (по умолчанию `app/MoonShine`). Директория используется для генерации файлов через `artisan` команды, в целом `MoonShine` не привязан к структуре,
- `namespace` - Namespace для классов созданных через `artisan` команды (по умолчанию `App\MoonShine`).

~~~tabs
tab: config/moonshine.php
```php
'dir' => 'app/MoonShine',
'namespace' => 'App\MoonShine',

'use_migrations' => true,
'use_notifications' => true,
'use_database_notifications' => true,
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config
    ->dir(dir: 'app/MoonShine', namespace: 'App\MoonShine')
    ->useMigrations()
    ->useNotifications()
    ->useDatabaseNotifications()
;
```
~~~

<a name="title"></a>
### Заголовок

Мета заголовок на страницах (`<title>Мое приложение</title>`)

~~~tabs
tab: config/moonshine.php
```php
'title' => 'Мое приложение',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->title('Мое приложение');
```
~~~

<a name="logo"></a>
### Логотип

~~~tabs
tab: config/moonshine.php
```php
'logo' => '/assets/logo.png',
'logo_small' => '/assets/logo-small.png',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config
    ->logo('/assets/logo.png')
    ->logo('/assets/logo-small.png', small: true);
```
~~~

<a name="middleware"></a>
### Middleware

Вы можете переопределить или дополнить список `middleware` в системе

~~~tabs
tab: config/moonshine.php
```php
'middleware' => [
    'web',
    'auth',
    // ...
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->middleware(['web', 'auth'])
       ->addMiddleware('custom-middleware')
       ->exceptMiddleware(['auth']);
```
~~~

<a name="routing"></a>
### Маршрутизация

#### Установка префиксов

~~~tabs
tab: config/moonshine.php
```php
'prefix' => 'admin',
'page_prefix' => 'page',
'resource_prefix' => 'resource',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->prefixes('admin', 'page', 'resource');
```
~~~

> [!WARNING]
> Вы можете оставить `resource_prefix` пустым и `URL` ресурсов будет иметь вид `/admin/{resourceUri}/{pageUri}`,
> но вы можете создать конфликт с роутами пакетов

#### Установка домена

~~~tabs
tab: config/moonshine.php
```php
'domain' => 'admin.example.com',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->domain('admin.example.com');
```
~~~

#### 404

Вы можете заменить `Exception` на собственный

~~~tabs
tab: config/moonshine.php
```php
'not_found_exception' => MoonShineNotFoundException::class,
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->notFoundException(MoonShineNotFoundException::class);
```
~~~

<a name="authentication"></a>
### Аутентификация

#### Установка guard

~~~tabs
tab: config/moonshine.php
```php
'auth' => [
    'guard' => 'admin',
    // ...
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->guard('admin');
```
~~~

#### Отключение встроенной аутентификации

~~~tabs
tab: config/moonshine.php
```php
'auth' => [
    'enabled' => false,
    // ...
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->authDisable();
```
~~~

#### Изменение модели

```php
'auth' => [
    // ...
    'model' => User::class,
    // ...
],
```

> [!NOTE]
> Указывается при инициализации приложения, поэтому указывается исключительно через файл конфигурации

#### Middleware для проверки наличия сессии

~~~tabs
tab: config/moonshine.php
```php
'auth' => [
    // ...
    'middleware' => Authenticate::class,
    // ...
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->authMiddleware(Authenticate::class);
```
~~~

#### Pipelines

~~~tabs
tab: config/moonshine.php
```php
'auth' => [
    // ...
    'pipelines' => [
        TwoFactor::class
    ],
    // ...
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->authPipelines([TwoFactor::class]);
```
~~~

#### Поля пользователя

Если вы просто заменили модель на свою `auth.model`, то скорее всего вы столкнетесь с проблемой несоответствия наименования полей.
Чтобы настроить соответствие, воспользуйтесь настройкой `userField`:

~~~tabs
tab: config/moonshine.php
```php
'user_fields' => [
    'username' => 'email',
    'password' => 'password',
    'name' => 'name',
    'avatar' => 'avatar',
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->userField('username', 'username');
```
~~~

<a name="localization"></a>
### Локализация

#### Язык по умолчанию

~~~tabs
tab: config/moonshine.php
```php
'locale' => 'en',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->locale('en');
```
~~~

### Установка доступных языков

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

<a name="storage"></a>
### Хранилище

#### Storage

~~~tabs
tab: config/moonshine.php
```php
'disk' => 'public',
'disk_options' => [],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->disk('public', options: []);
```
~~~

#### Cache

~~~tabs
tab: config/moonshine.php
```php
'cache' => 'file',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->cacheDriver('redis');
```
~~~

<a name="layout"></a>
### Layout

Шаблон используемый по умолчанию

~~~tabs
tab: config/moonshine.php
```php
'layout' => \App\MoonShine\Layouts\CustomLayout::class,
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->layout(\App\MoonShine\Layouts\CustomLayout::class);
```
~~~

<a name="forms"></a>
### Формы

Для удобства мы вынесли формы аутентификации и фильтров в конфигурацию и даем быстрый способ их заменить на собственные

~~~tabs
tab: config/moonshine.php
```php
'forms' => [
    'login' => LoginForm::class,
    'filters' => FiltersForm::class,
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->set('forms.login', MyLoginForm::class);
```
~~~

<a name="pages"></a>
### Страницы

Для удобства мы вынесли базовые страницы в конфигурацию и даем быстрый способ их заменить на собственные

~~~tabs
tab: config/moonshine.php
```php
'pages' => [
    'dashboard' => Dashboard::class,
    'profile' => ProfilePage::class,
    'login' => LoginPage::class,
    'error' => ErrorPage::class,
],
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->changePage(LoginPage::class, MyLoginPage::class);
```
~~~

<a name="home-url"></a>
### Главная страница

Вы можете указать какой роут или урл является главной страницей панели. 
Используется при редиректе после успешной аутентификации, ссылке на логотипе и 404 странице.

~~~tabs
tab: config/moonshine.php
```php
'home_route' => 'moonshine.index',
// or url string
'home_url' => '/admin/page/some-page',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->homeRoute('moonshine.index');
// or url string
$config->homeUrl('/admin/page/some-page');
```
~~~

<a name="pages-forms"></a>
## Получение страниц и форм

MoonShine предоставляет удобные методы для получения страниц и форм в вашем приложении.

### Получение страниц

Метод `getPage` позволяет получить экземпляр страницы по её имени или использовать страницу по умолчанию.

```php
public function getPage(string $name, string $default, mixed ...$parameters): PageContract
```

Параметры:
- `$name`: Имя страницы в конфиге
- `$default`: Класс страницы по умолчанию, если не найдена в конфиге
- `$parameters`: Дополнительные параметры для конструктора страницы

Пример использования:

```php
// Helper

$customPage = moonshineConfig()->getPage('custom');
```

```php
// DI

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

/**
 * @param  MoonShineConfigurator  $configurator
 */
public function index(ConfiguratorContract $config)
{
  $customPage = $config->getPage('custom');
}
```

### Получение форм

Метод `getForm` позволяет получить экземпляр формы по её имени или использовать форму по умолчанию.

```php
public function getForm(string $name, string $default, mixed ...$parameters): FormBuilderContract
```

Параметры:
- `$name`: Имя формы в конфиге
- `$default`: Класс формы по умолчанию
- `$parameters`: Дополнительные параметры для конструктора формы

Пример использования:

```php
// Helper

$form = moonshineConfig()->getForm('login');
```

```php
// DI

use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

/**
 * @param  MoonShineConfigurator  $configurator
 */
public function index(ConfiguratorContract $config)
{
  $form = $config->getForm('login');
}
```

### Объявление страниц и форм в конфигурации

Вы можете настроить соответствие между именами и классами страниц и форм в файле `moonshine.php`:

```php
return [
    // Другие настройки...

    'pages' => [
        'dashboard' => \App\MoonShine\Pages\DashboardPage::class,
        'custom' => \App\MoonShine\Pages\CustomPage::class,
    ],

    'forms' => [
        'login' => \App\MoonShine\Forms\LoginForm::class,
        'custom' => \App\MoonShine\Forms\CustomForm::class,
    ],
];
```

Это позволит вам легко получать нужные страницы и формы по их именам, используя методы `getPage` и `getForm`.

> [!NOTE]
> Некоторые методы `MoonShineConfigurator` не имеют прямых аналогов в файле `moonshine.php` и наоборот. Это связано с различиями в подходах к конфигурации через файл и через код.

### Пример использования в MoonShineServiceProvider

```php
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  MoonShine  $core
     * @param  MoonShineConfigurator  $config
     *
     */
    public function boot(
        CoreContract $core,
        ConfiguratorContract $config,
    ): void
    {
        $config
            ->title('Мое приложение')
            ->dir('app/MoonShine', 'App\MoonShine')
            ->prefix('admin')
            ->guard('moonshine')
            ->middleware(['web', 'auth'])
            ->layout(\App\MoonShine\Layouts\CustomLayout::class)
            ->locale('ru')
            ->locales(['en', 'ru'])
            ->useMigrations()
            ->useNotifications()
            ->useDatabaseNotifications()
            ->cacheDriver('redis')
            ->authorizationRules(function(ResourceContract $ctx, mixed $user, Ability $ability, mixed $data): bool {
                 return true;
            })
        ;

        // ..
    }
}
```

Этот полный список параметров и методов позволяет настроить практически все аспекты работы `MoonShine`.
Выбирайте те опции, которые наилучшим образом соответствуют требованиям вашего проекта.

<a name="choosing-configuration-method"></a>
## Выбор метода конфигурации

При выборе метода конфигурации важно учитывать следующее:

1. **Приоритет**: Конфигурация через `MoonShineServiceProvider` имеет приоритет над настройками в файле `moonshine.php`.

2. **Гибкость**:
   - Полная конфигурация через `moonshine.php` дает четкий обзор всех настроек.
   - Частичная конфигурация через `moonshine.php` позволяет легко видеть, какие параметры были изменены.
   - Конфигурация через `MoonShineServiceProvider` предоставляет максимальную гибкость и возможность использовать логику при настройке.

3. **Простота поддержки**:
   - Использование файла `moonshine.php` может быть проще для быстрых изменений и понимания общей структуры настроек.
   - `MoonShineServiceProvider` позволяет централизованно управлять настройками в одном месте в коде.

4. **Интеграция с кодом**:
   - Конфигурация через `MoonShineServiceProvider` лучше интегрируется с остальным кодом приложения и позволяет использовать зависимости и сервисы Laravel.

Выберите метод, который лучше всего соответствует вашему стилю разработки и требованиям проекта. Вы также можете комбинировать эти подходы, например, используя файл `moonshine.php` для базовых настроек и `MoonShineServiceProvider` для более сложной конфигурации.
