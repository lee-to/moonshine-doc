# Handlers

- [Основы](#basics)
- [Создание Handler](#create)
- [Регистрация](#registration)
- [Взаимодействие](#interaction)

---

<a name="basics"></a>
## Основы

`Handlers` в `MoonShine` - это переиспользуемые обработчики, которые позволяют легко добавлять пользовательские действия в ресурсы.

Основные преимущества:
- Не требуют создания контроллеров,
- Автоматическая обработка ошибок внутри `MoonShine`,
- Множество готовых методов для взаимодействия с системой,
- Простая интеграция с `UI` через автоматическую генерацию кнопок,
- После подключения автоматически отображаются в интерфейсе.

<a name="create"></a>
## Создание Handler

Для создания нового `Handler` используйте команду:

```bash
php artisan moonshine:handler MyCustomHandler
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#handler).

После выполнения команды будет создан класс `Handler` в директории `app\MoonShine\Handlers` со следующей структурой:

```php
namespace App\MoonShine\Handlers;

use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Handlers\Handler;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\UI\Components\ActionButton;
use Symfony\Component\HttpFoundation\Response;

class MyCustomHandler extends Handler
{
    /**
     * @throws ActionButtonException
     */
    public function handle(): Response
    {
        if (! $this->hasResource()) {
            throw new ActionButtonException('Resource is required for action');
        }

        if ($this->isQueue()) {
            // Job here

            MoonShineUI::toast(
                __('moonshine::ui.resource.queued')
            );

            return back();
        }

        self::process();

        return back();
    }

    public static function process()
    {
        // Logic here
    }

    public function getButton(): ActionButtonContract
    {
        return ActionButton::make($this->getLabel(), $this->getUrl());
    }
}
```

<a name="registration"></a>
## Регистрация

Для регистрации `Handler` в ресурсе необходимо переопределить метод `handlers()`:

```php
class PostResource extends ModelResource 
{
    protected function handlers(): ListOf
    {
        return parent::handlers()->add(new MyCustomHandler());
    }
}
```

После регистрации на индексной странице ресурса справа автоматически появится кнопка для запуска `Handler`.

<a name="interaction"></a>
## Взаимодействие

`Handler` тесно интегрирован с ресурсом и имеет доступ к:

- Текущему ресурсу через `$this->getResource()`,
- Возможностям запуска через очереди (`queue`),
- Системе уведомлений и настройку пользователей, которые получат уведомления через `notifyUsers()`,
- Модификация кнопки через `modifyButton()`.
