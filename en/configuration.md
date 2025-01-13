# Configuration

- [Introduction](#introduction)
- [Configuration Methods](#configuration-methods)
  - [Configuration via moonshine.php File](#config-file)
  - [Configuration via MoonShineServiceProvider](#service-provider)
- [Basic Settings](#basic-settings)
  - [Options](#options)
  - [Title](#title)
  - [Logo](#logo)
  - [Middleware](#middleware)
  - [Routing](#routing)
  - [Authentication](#authentication)
  - [Localization](#localization)
  - [Storage](#storage)
  - [Layout](#layout)
  - [Forms](#forms)
  - [Pages](#pages)
  - [Home page](#home-url)
- [Getting Pages and Forms](#pages-forms)
- [Full List of Configuration Parameters](#configuration-options)
- [Choosing Configuration Method](#choosing-configuration-method)

---

<a name="introduction"></a>
## Introduction

`MoonShine` provides flexible options for configuring your application.
In this section, we will examine two main ways of configuration and the primary settings.

<a name="configuration-methods"></a>
## Configuration Methods

`MoonShine` can be configured in two ways:

1. Through the configuration file `config/moonshine.php`
2. Through `MoonShineServiceProvider` using the `MoonShineConfigurator` class

<a name="config-file"></a>
### Configuration via moonshine.php File

The `config/moonshine.php` file contains all available MoonShine settings. You can modify these settings directly in the file.

Example content of the `moonshine.php` file:

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

#### Partial Configuration

Alternatively, you can leave only those parameters in the `moonshine.php` file that differ from the default values.
This makes the configuration cleaner and easier to understand. 
Example of optimized content for the `moonshine.php` file:

```php
return [
    'title' => 'My MoonShine Application',
    'use_migrations' => true,
    'use_notifications' => true,
    'use_database_notifications' => true,
];
```

> [!NOTE]
> `use_migrations`, `use_notifications`, `use_database_notifications` must always be present either in `moonshine.php` or in `MoonShineServiceProvider`.

> [!NOTE]
> All other parameters not specified in the file will use the default values.

<a name="service-provider"></a>
### Configuration via MoonShineServiceProvider

An alternative way to configure is via `MoonShineServiceProvider`. This method provides a more programmatic approach to configuration.

Example configuration in `MoonShineServiceProvider`:

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
            ->title('My Application')
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
> Configuration via `MoonShineServiceProvider` takes precedence over settings in the `moonshine.php` file. 
> When using this method, you can completely remove the `moonshine.php` file from your project.

<a name="basic-settings"></a>
## Basic Settings

Regardless of the chosen configuration method, you can set up the following basic parameters:

<a name="options"></a>
### Options

- `use_migrations` - Use default system migrations (`moonshine_users`, `moonshine_user_roles`),
- `use_notifications` - Use notification system,
- `use_database_notifications` - Use Laravel notification system based on database driver,
- `dir` - Directory for `MoonShine` (by default `app/MoonShine`). The directory is used for generating files via `artisan` commands, in general `MoonShine` is not tied to structure,
- `namespace` - Namespace for classes created via `artisan` commands (by default `App\MoonShine`).

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
### Title

Meta title on pages (`<title>My Application</title>`)

~~~tabs
tab: config/moonshine.php
```php
'title' => 'My Application',
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config->title('My Application');
```
~~~

<a name="logo"></a>
### Logo

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

You can override or append to the list of `middleware` in the system.

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
### Routing

#### Setting Prefixes

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
> You can leave `resource_prefix` empty and the URL of resources will look like `/admin/{resourceUri}/{pageUri}`, 
> but you may create a conflict with the routes of packages.

#### Setting Domain

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

You can replace the `Exception` with your own

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
### Authentication

#### Setting Guard

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

#### Disabling Built-in Authentication

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

#### Changing Model

```php
'auth' => [
    // ...
    'model' => User::class,
    // ...
],
```

> [!NOTE]
> This is specified during application initialization, therefore it is exclusively specified through the configuration file.

#### Middleware for Checking Session Presence

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

#### User Fields

If you have simply replaced the model with your own `auth.model`, you will likely encounter a naming mismatch of fields.
To configure the correspondence, use the `userField` setting:

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
### Localization

#### Default Language

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

### Setting Available Languages

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
### Storage

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

The default template used

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
### Forms

For convenience, we have moved the authentication and filter forms to configuration and provide a quick way to replace them with your own.

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
### Pages

For convenience, we have moved the basic pages to configuration and provide a quick way to replace them with your own.

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
### Home page

You can specify which route or URL is the main page of the panel.
Used when redirecting after successful authentication, link in logo and 404 page.

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
## Getting Pages and Forms

MoonShine provides convenient methods for retrieving pages and forms in your application.

### Getting Pages

The `getPage` method allows you to retrieve an instance of a page by its name or use the default page.

```php
public function getPage(string $name, string $default, mixed ...$parameters): PageContract
```

Parameters:
- `$name`: Page name in the config
- `$default`: Default page class if not found in the config
- `$parameters`: Additional parameters for the page constructor

Example usage:

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

### Getting Forms

The `getForm` method allows you to retrieve an instance of a form by its name or use the default form.

```php
public function getForm(string $name, string $default, mixed ...$parameters): FormBuilderContract
```

Parameters:
- `$name`: Form name in the config
- `$default`: Default form class
- `$parameters`: Additional parameters for the form constructor

Example usage:

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

### Declaring Pages and Forms in Configuration

You can set up the correspondence between the names and classes of pages and forms in the `moonshine.php` file:

```php
return [
    // Other settings...

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

This will allow you to easily retrieve the desired pages and forms by their names using the `getPage` and `getForm` methods.

> [!NOTE]
> Some methods of `MoonShineConfigurator` do not have direct analogs in the `moonshine.php` file and vice versa. This is due to differences in approaches to configuration through the file and through code.

### Example Usage in MoonShineServiceProvider

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
            ->title('My Application')
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

        // ...
    }
}
```

This complete list of parameters and methods allows you to configure almost every aspect of `MoonShine` operations.
Choose the options that best meet your project requirements.

<a name="choosing-configuration-method"></a>
## Choosing Configuration Method

When choosing a configuration method, it's important to consider the following:

1. **Priority**: Configuration via `MoonShineServiceProvider` takes precedence over settings in the `moonshine.php` file.

2. **Flexibility**:
   - Full configuration via `moonshine.php` provides a clear overview of all settings,
   - Partial configuration via `moonshine.php` makes it easy to see what parameters have been changed,
   - Configuration via `MoonShineServiceProvider` offers maximum flexibility and the ability to use logic during setup.

3. **Ease of Maintenance**:
   - Using the `moonshine.php` file may be easier for quick changes and understanding the overall structure of settings,
   - `MoonShineServiceProvider` allows centralized management of settings in one place in the code.

4. **Integration with Code**:
   - Configuration via `MoonShineServiceProvider` integrates better with the rest of the application code and allows the use of Laravel dependencies and services.

Choose the method that best fits your development style and project requirements.
You can also combine these approaches, for example, using the `moonshine.php` file for basic settings and the `MoonShineServiceProvider` for more complex configurations.
