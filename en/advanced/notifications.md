# Notifications

- [Basics](#basics)
- [Settings](#settings)
- [Component](#component)
- [Custom Notifications](#custom)
- [WebSocket](#web-socket)

---

<a name="basics"></a>
## Basics

> [!NOTE]
> By default, MoonShine uses [Laravel Database Notification](https://laravel.com/docs/notifications#database-notifications), but we use abstractions that can be easily replaced.

If you need to add notifications to the `MoonShine` notification center, use the `MoonShine\Laravel\Notifications\MoonShineNotification` class.

Directly via the static method `send()`:

```php
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Laravel\Notifications\NotificationButton;
use MoonShine\Support\Enums\Color;

MoonShineNotification::send(
    message: 'Notification text',
    // Optional button
    button: new NotificationButton('Click me', 'https://moonshine.cutcode.dev'),
    // Optional administrator IDs (default for all)
    ids: [1,2,3],
    // Optional icon color
    color: Color::green
);
```

Or via `DI`:

```php
use MoonShine\Laravel\Contracts\Notifications\MoonShineNotificationContract;

public function di(MoonShineNotificationContract $notification)
{
    $notification->notify(
        'Hello'
    );
}
```

![notifications](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/notifications.png)
![notifications_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/notifications_dark.png)

<a name="settings"></a>
## Settings

During the installation of `MoonShine`, you have the option to choose whether you want to use notifications and `Database Notification`.
Additionally, you can change these settings later through the configuration:

~~~tabs
tab: config/moonshine.php
```php
'use_notifications' => true,
'use_database_notifications' => true,
```
tab: app/Providers/MoonShineServiceProvider.php
```php
$config
    ->useNotifications()
    ->useDatabaseNotifications()
;
```
~~~

<a name="component"></a>
## Component

The component `MoonShine\Laravel\Components\Layout\Notifications` is used to display notifications, which you can replace with your own through [Layout](/docs/{{version}}/appearance/layout).

<a name="custom"></a>
## Custom Notifications

`MoonShine` is flexible and everything can be replaced with your own implementations; for notifications, you need to implement the interfaces:

- `MoonShine\Laravel\Contracts\Notifications\MoonShineNotificationContract`
- `MoonShine\Laravel\Contracts\Notifications\NotificationItemContract`
- `MoonShine\Laravel\Contracts\Notifications\NotificationButtonContract` (optional)

Then, in the ServiceProvider, replace the implementation with your own:

```php
public function boot(): void
{
    $this->app->singleton(
        MoonShineNotificationContract::class,
        MyNotificationSystem::class
    );
}
```

<a name="web-socket"></a>
## WebSocket

> [!TIP]
> A ready-made implementation of notifications via WebSocket is available in the [Rush](/plugins/rush) package.
