# Authentication

- [Basics](#basics)
- [Configuration](#configuration)
- [Customization](#customization)
- [Disabling authentication](#disabling-authentication)
- [Custom user model](#custom-user-model)
- [Custom user fields and profile](#custom-user-fields)
- [Role-based access](#role-based-access)
- [Authentication pipelines](#authentication-pipelines)
- [Socialite](#socialite)
- [Two-factor authentication](#2fa)
- [JWT](#jwt)

---

<a name="basics"></a>
## Basics

`MoonShine` provides a built-in authentication system that by default uses its own user model and `guard`.
This allows you to quickly get started with the admin panel without worrying about setting up authentication.

<a name="configuration"></a>
## Configuration

The main authentication settings are located in the configuration file `config/moonshine.php` in the `auth` section:

```php
'auth' => [
    'enabled' => true,
    'guard' => 'moonshine',
    'model' => MoonshineUser::class,
    'middleware' => Authenticate::class,
    'pipelines' => [],
],
```

Here you can configure:

- `enabled`: enable/disable built-in authentication
- `guard`: name of the guard for authentication
- `model`: user model class
- `middleware`: middleware for authentication
- `pipelines`: additional pipelines for the authentication process

<a name="customization"></a>
## Customization

You can customize authentication in `MoonShineServiceProvider`:

```php
$config
    ->guard('admin')
    ->authMiddleware(CustomAuthMiddleware::class)
    ->authPipelines([
        TwoFactorAuthentication::class,
        PhoneVerification::class,
    ]);
```

<a name="disabling-authentication"></a>
## Disabling authentication

If you want to disable the built-in authentication of `MoonShine`, you can do this in `MoonShineServiceProvider`:

```php
$config->authDisable();
```

<a name="custom-user-model"></a>
## Custom user model

If you want to use your own user model instead of `MoonshineUser`, you can specify it in the configuration:

```php
'auth' => [
    'model' => App\Models\Admin::class,
],
```

<a name="custom-user-fields"></a>
## Custom user fields and profile

MoonShine allows you to customize the user fields used for authentication and profile:

```php
$config
    ->userField('username', 'login')
    ->userField('password', 'pass')
    ->userField('name', 'full_name')
    ->userField('avatar', 'profile_image');
```
If you want to completely replace the profile page with your own, you can do this through the `moonshine.php` configuration:

```php
'pages' => [
    'profile' => App\MoonShine\Pages\CustomProfile::class,
],
```

Or through `MoonShineServiceProvider`:

```php
$config->changePage(\MoonShine\Laravel\Pages\ProfilePage::class, \App\MoonShine\Pages\CustomProfile::class);
```

<a name="role-based-access"></a>
## Role-based access

If you need to restrict access to the MoonShine admin panel based on user roles or other conditions, you can easily add your own `middleware`.

### Creating middleware

Create a new `middleware`, for example, `CheckAdminRole`:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->hasRole('admin')) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
```

### Adding middleware to configuration

Add the `middleware` to the `config/moonshine.php` file:

```php
'middleware' => [
    // ... other middleware
    \App\Http\Middleware\CheckAdminRole::class,
],
```

Or in `MoonShineServiceProvider`:

```php
$config->addMiddleware([
    \App\Http\Middleware\CheckAdminRole::class,
]);
```

<a name="authentication-pipelines"></a>
## Authentication pipelines

Authentication `pipelines` in MoonShine allow you to add additional checks and actions to the authentication process after a successful login and password check.

### Configuring pipelines

Configure `pipelines` in `MoonShineServiceProvider`:

```php
$config->authPipelines([
    \App\MoonShine\AuthPipelines\TwoFactorAuthentication::class,
    \App\MoonShine\AuthPipelines\PhoneVerification::class,
]);
```

### Creating a pipeline

Example: Phone number login confirmation:

```php
namespace App\MoonShine\AuthPipelines;

use Closure;
use MoonShine\Laravel\Models\MoonshineUser;

class PhoneVerification
{
    public function handle(Request $request, Closure $next)
    {
        $user = MoonshineUser::query()
            ->where('email', $request->get('username'))
            ->first();

         if (! is_null($user) && ! is_null($user->getAttribute('phone'))) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('sms-challenge');
        }

        return $next($user);
    }
}
```

### Advantages of using pipelines

- Flexibility: Easy to add, remove, or change the order of checks.
- Modularity: Each aspect of extended authentication is isolated in a separate class.
- Extensibility: Easy to add new authentication methods or checks.

Using authentication pipelines allows for complex authentication scenarios while keeping the code clean and modular, and gives full control over the process of users logging into the MoonShine admin panel.

<a name="socialite"></a>
# Socialite 

For convenience, you can link your account with social networks to simplify the authentication process.

This functionality is based on the [Laravel Socialite](https://laravel.com/docs/socialite) package.

Make sure you have it installed and configured.

Next, install the package for integrating `Socialite` into `MoonShine`:

```shell
composer require moonshine/socialite
```

Run migrations:

```shell
php artisan migrate
```

Publish the configuration file:

```shell
php artisan vendor:publish --provider="MoonShine\Socialite\Providers\SocialiteServiceProvider"
```

Then in the `config/moonshine-socialite.php` config, set the available drivers and the image for the button:

```php
return [
    'drivers' => [
        'github' => '/images/github.png',
        'facebook' => '/images/facebook.svg',
    ],
];
```

> [!NOTE]
> Drivers must be pre-configured in the `Socialite` package.

Add the trait `MoonShine\Socialite\Traits\HasMoonShineSocialite` to the model that is responsible for the admin panel users (by default, this is `MoonshineUser`).

Don't forget to publish the model if you're using the default configuration:

```php
namespace App\Models;

use MoonShine\Socialite\Traits\HasMoonShineSocialite;

final class MoonshineUser extends \MoonShine\Laravel\Models\MoonshineUser
{
    use HasMoonShineSocialite;
}
```

And replace in the configuration file:

```php
// ..
'auth' => [
    // ..
    'model' => \App\Models\MoonshineUser::class,
    // ..
],
// ..
```

We will automatically add the `SocialAuth` component to the profile page and `LoginLayout`, but if you have overridden them and are using your own, then add the component yourself:

```php
use MoonShine\Socialite\Components\SocialAuth;

protected function components(): iterable
{
    return [
        // ...

        SocialAuth::make(profileMode: true),
    ];
}
```

<a name="2fa"></a>
# Two-factor authentication

For added security, you can set up two-factor authentication:

```shell
composer require moonshine/two-factor
```

Next, run migrations:

```shell
php artisan migrate
```

Then add `authPipeline`:

~~~tabs
tab: config
```php
use MoonShine\TwoFactor\TwoFactorAuthPipe;

return [
    // ...
    'auth' => [
        // ...
        'pipelines' => [
            TwoFactorAuthPipe::class
        ],
        // ...
    ]
    // ...
];
```
tab: MoonShineServiceProvider
```php
use MoonShine\TwoFactor\TwoFactorAuthPipe;

$config->authPipelines([
    TwoFactorAuthPipe::class
]);
```
~~~

Add the trait `MoonShine\TwoFactor\Traits\TwoFactorAuthenticatable` to the model that is responsible for the admin panel users (by default, this is `MoonshineUser`).

Don't forget to publish the model if you're using the default configuration:

```php
use MoonShine\TwoFactor\Traits\TwoFactorAuthenticatable;

final class MoonshineUser extends \MoonShine\Laravel\Models\MoonshineUser
{
    use TwoFactorAuthenticatable;
}
```

And replace in the configuration file:

```php
// ..
'auth' => [
    // ...
    'model' => \App\Models\MoonshineUser::class,
    // ...
],
// ...
```

We will automatically add the `TwoFactor` component to the profile page, but if you have overridden it and are using your own, then add the component yourself:

```php
use MoonShine\TwoFactor\ComponentSets\TwoFactor;

protected function components(): iterable
{
    return [
        // ...

        TwoFactor::make(),
    ];
}
```

<a name="jwt"></a>
# JWT

`MoonShine` also provides a simple way to switch the admin panel to `API` mode and interact through tokens.

For more details, read the [API](/docs/{{version}}/frontend/api) section.
