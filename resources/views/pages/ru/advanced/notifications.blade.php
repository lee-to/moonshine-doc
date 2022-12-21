<x-page title="Уведомления">

<x-p>
    Если есть необходимость добавить уведомления в центр уведомлений MoonShine, тогда
    воспользуйтесь классом <code>Leeto\MoonShine\Notifications\MoonShineNotification</code>
</x-p>

<x-code>
use Leeto\MoonShine\Notifications\MoonShineNotification;

MoonShineNotification::send(
    message: 'Notification message',
    // Опционально button
    button: ['link' => 'https://moonshine.cutcode.ru', 'label' => 'Click me'],
    // Опционально id администраторов (по умолчанию всем)
    ids: [1,2,3]
);
</x-code>

<x-image src="{{ asset('screenshots/notifications.png') }}"></x-image>

</x-page>
