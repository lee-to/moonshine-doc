# Уведомления

- [Основы](#basics)
- [Настройка](#settings)
- [Компонент](#component)
- [Кастомные уведомления](#custom)
- [WebSocket](#web-socket)

---

<a name="basics"></a>
## Основы

> [!NOTE]
> По умолчанию MoonShine использует [Laravel Database Notification](https://laravel.com/docs/notifications#database-notifications), но мы используем абстракции, которые легко заменить.

Если есть необходимость добавить уведомления в центр уведомлений `MoonShine`, используйте класс `MoonShine\Laravel\Notifications\MoonShineNotification`.

Напрямую через статически метод `send()`:

```php
use MoonShine\Laravel\Notifications\MoonShineNotification;
use MoonShine\Laravel\Notifications\NotificationButton;
use MoonShine\Support\Enums\Color;

MoonShineNotification::send(
    message: 'Текст уведомления',
    // Необязательная кнопка
    button: new NotificationButton('Нажми меня', 'https://moonshine.cutcode.dev'),
    // Необязательные ID администраторов (по умолчанию для всех)
    ids: [1,2,3],
    // Необязательный цвет иконки
    color: Color::green
);
```

Или через `DI`:

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
## Настройка

Во время установки `MoonShine` у вас есть возможность выбрать, хотите ли вы использовать уведомления и `Database Notification`.
Кроме того, вы можете изменить эти настройки позже через конфигурацию:

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
## Компонент

Для вывода уведомлений используется компонент `MoonShine\Laravel\Components\Layout\Notifications`, который вы можете заменить на свой через [Layout](/docs/{{version}}/appearance/layout).

<a name="custom"></a>
## Кастомные уведомления

`MoonShine` гибкий и всё в нем можно заменить на собственные реализации, для уведомлений нужно реализовать интерфейсы:

- `MoonShine\Laravel\Contracts\Notifications\MoonShineNotificationContract`
- `MoonShine\Laravel\Contracts\Notifications\NotificationItemContract`
- `MoonShine\Laravel\Contracts\Notifications\NotificationButtonContract`(опционально)

После в ServiceProvider заменить реализацию на собственную:

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
> Готовая реализация уведомлений через WebSocket реализована в пакете [Rush](/plugins/rush).
