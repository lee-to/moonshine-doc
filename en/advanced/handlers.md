# Handlers

- [Basics](#basics)
- [Creating Handler](#create)
- [Registration](#registration)
- [Interaction](#interaction)

---

<a name="basics"></a>
## Basics

`Handlers` in `MoonShine` are reusable handlers that allow you to easily add custom actions to resources.

The main advantages:
- Do not require the creation of controllers,
- Automatic error handling within `MoonShine`,
- Many ready-made methods for interaction with the system,
- Simple integration with `UI` through automatic button generation,
- Automatically displayed in the interface after connection.

<a name="create"></a>
## Creating Handler

To create a new `Handler`, use the command:

```bash
php artisan moonshine:handler MyCustomHandler
```

> [!NOTE]
> You can learn about all supported options in the section [Commands](/docs/{{version}}/advanced/commands#handler).

After executing the command, a `Handler` class will be created in the `app\MoonShine\Handlers` directory with the following structure:

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
## Registration

To register a `Handler` in a resource, you need to override the `handlers()` method:

```php
class PostResource extends ModelResource 
{
    protected function handlers(): ListOf
    {
        return parent::handlers()->add(new MyCustomHandler());
    }
}
```

After registration, a button for launching the `Handler` will automatically appear on the right side of the resource's index page.

<a name="interaction"></a>
## Interaction

`Handler` is closely integrated with the resource and has access to:

- The current resource via `$this->getResource()`,
- Queueing capabilities,
- Notification system and settings for users who will receive notifications via `notifyUsers()`,
- Modifying the button via `modifyButton()`.
