# Аутентификация

- [Основы](#basics)
- [Конфигурация](#configuration)
- [Кастомизация](#customization)
- [Отключение аутентификации](#disabling-authentication)
- [Кастомная модель пользователя](#custom-user-model)
- [Кастомные поля пользователя и профиль](#custom-user-fields)
- [Ограничение доступа по ролям](#role-based-access)
- [Аутентификационные pipelines](#authentication-pipelines)
- [Socialite](#socialite)
- [Двухфакторная аутентификация](#2fa)
- [JWT](#jwt)

---

<a name="basics"></a>
## Основы

`MoonShine` предоставляет встроенную систему аутентификации, которая по умолчанию использует собственную модель пользователя и `guard`.
Это позволяет быстро начать работу с административной панелью, не беспокоясь о настройке аутентификации.

<a name="configuration"></a>
## Конфигурация

Основные настройки аутентификации находятся в файле конфигурации `config/moonshine.php` в секции `auth`:

```php
'auth' => [
    'enabled' => true,
    'guard' => 'moonshine',
    'model' => MoonshineUser::class,
    'middleware' => Authenticate::class,
    'pipelines' => [],
],
```

Здесь вы можете настроить:

- `enabled`: включение/отключение встроенной аутентификации
- `guard`: имя guard'а для аутентификации
- `model`: класс модели пользователя
- `middleware`: middleware для аутентификации
- `pipelines`: дополнительные pipeline'ы для процесса аутентификации

<a name="customization"></a>
## Кастомизация

Вы можете настроить аутентификацию в `MoonShineServiceProvider`:

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
## Отключение аутентификации

Если вы хотите отключить встроенную аутентификацию `MoonShine`, вы можете сделать это в `MoonShineServiceProvider`:

```php
$config->authDisable();
```

<a name="custom-user-model"></a>
## Кастомная модель пользователя

Если вы хотите использовать собственную модель пользователя вместо `MoonshineUser`, вы можете указать её в конфигурации:

```php
'auth' => [
    'model' => App\Models\Admin::class,
],
```

<a name="custom-user-fields"></a>
## Кастомные поля пользователя и профиль

MoonShine позволяет настроить поля пользователя, используемые для аутентификации и профиля:

```php
$config
    ->userField('username', 'login')
    ->userField('password', 'pass')
    ->userField('name', 'full_name')
    ->userField('avatar', 'profile_image');
```
При этом если вы хотите полностью заменить страницу профиля на свою, то можете это сделать через конфигурацию `moonshine.php`:

```php
'pages' => [
    'profile' => App\MoonShine\Pages\CustomProfile::class,
],
```

Или через `MoonShineServiceProvider`:

```php
$config->changePage(\MoonShine\Laravel\Pages\ProfilePage::class, \App\MoonShine\Pages\CustomProfile::class);
```

<a name="role-based-access"></a>
## Ограничение доступа по ролям

Если вам нужно ограничить доступ к административной панели MoonShine на основе ролей пользователей или других условий, вы можете легко добавить собственный `middleware`.

### Создание middleware

Создайте новый `middleware`, например, `CheckAdminRole`:

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
            abort(403, 'Доступ запрещен.');
        }

        return $next($request);
    }
}
```

### Добавление middleware в конфигурацию

Добавьте `middleware` в файл `config/moonshine.php`:

```php
'middleware' => [
    // ... другие middleware
    \App\Http\Middleware\CheckAdminRole::class,
],
```

Или в `MoonShineServiceProvider`:

```php
$config->addMiddleware([
    \App\Http\Middleware\CheckAdminRole::class,
]);
```

<a name="authentication-pipelines"></a>
## Аутентификационные pipelines

Аутентификационные `pipelines` в MoonShine позволяют добавлять дополнительные проверки и действия в процесс аутентификации после успешной проверки логина и пароля.

### Настройка pipelines

Настройте `pipelines` в `MoonShineServiceProvider`:

```php
$config->authPipelines([
    \App\MoonShine\AuthPipelines\TwoFactorAuthentication::class,
    \App\MoonShine\AuthPipelines\PhoneVerification::class,
]);
```

### Создание pipeline

Пример: Подтверждение входа по номеру телефона:

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

### Преимущества использования pipelines

- Гибкость: Легко добавлять, удалять или изменять порядок проверок.
- Модульность: Каждый аспект расширенной аутентификации изолирован в отдельном классе.
- Расширяемость: Простое добавление новых методов аутентификации или проверок.

Использование аутентификационных pipelines позволяет реализовать сложные сценарии аутентификации, сохраняя чистоту и модульность кода, и дает полный контроль над процессом входа пользователей в административную панель MoonShine.

<a name="socialite></a>
# Socialite

Для удобства можно связать аккаунт с социальными сетями и упростить процесс аутентификации.

В основе этого функционала - пакет [Laravel Socialite](https://laravel.com/docs/socialite).

Убедитесь, что он у вас установлен и настроен.

Далее установите пакет для интеграции `Socialite` в `MoonShine`:

```shell
composer require moonshine/socialite
```

Выполните миграции:

```shell
php artisan migrate
```

Опубликуйте файл конфигурации:

```shell
php artisan vendor:publish --provider="MoonShine\Socialite\Providers\SocialiteServiceProvider"
```

Далее в конфиге `config/moonshine-socialite.php` установите доступные драйверы и изображение для кнопки

```shell
return [
    'drivers' => [
        'github' => '/images/github.png',
        'facebook' => '/images/facebook.svg',
    ],
];
```

> [!NOTE]
> Драйверы должны быть заранее настроены в пакете `Socialite`

Добавьте трейт `MoonShine\Socialite\Traits\HasMoonShineSocialite` к модели, которая отвечает за пользователей админ. панели (по умолчанию это `MoonshineUser`).

Не забудьте опубликовать модель, если используете конфигурацию по умолчанию:

```php
namespace App\Models;

use MoonShine\Socialite\Traits\HasMoonShineSocialite;

final class MoonshineUser extends \MoonShine\Laravel\Models\MoonshineUser
{
    use HasMoonShineSocialite;
}
```

И заменить в файле конфигурации:

```php
// ..
'auth' => [
    // ..
    'model' => \App\Models\MoonshineUser::class,
    // ..
],
// ..
```

Мы автоматически добавим компонент `SocialAuth` на страницу профиля и `LoginLayout`, но если вы их переопределили и используете собственные, то добавьте компонент самостоятельно:

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

<a name="2fa></a>
# Двухфакторная аутентификация

Для дополнительной безопасности вы можете установить двухфакторную проверку аутентификации:

```shell
composer require moonshine/two-factor
```

Далее выполните миграции:

```shell
php artisan migrate
```

Далее добавьте `authPipeline`:

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

Добавьте трейт `MoonShine\TwoFactor\Traits\TwoFactorAuthenticatable` к модели, которая отвечает за пользователей админ-панели (по умолчанию это `MoonshineUser`).

Не забудьте опубликовать модель, если используете конфигурацию по умолчанию:

```php
use MoonShine\TwoFactor\Traits\TwoFactorAuthenticatable;

final class MoonshineUser extends \MoonShine\Laravel\Models\MoonshineUser
{
    use TwoFactorAuthenticatable;
}
```

И заменить в файле конфигурации:

```php
// ..
'auth' => [
    // ...
    'model' => \App\Models\MoonshineUser::class,
    // ...
],
// ...
```

Мы автоматически добавим компонент `TwoFactor` на страницу профиля, но если вы её переопределили и используете собственную, то добавьте компонент самостоятельно:

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

<a name="jwt></a>
# JWT

`MoonShine` также предоставляет простой способ переключить панель администратора в режим `API` и взаимодействовать через токены.

Подробнее читайте в разделе [API](/docs/{{version}}/frontend/api).
