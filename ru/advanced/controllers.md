# Контроллеры

- [Генерация контроллера](#generate-controller)
- [Отображение blade-представления](#show-blade-view)
- [Отображение страницы](#display-page)
- [Показать уведомление](#show-notification)
- [Отправить уведомление](#send-notification)
- [Доступ к странице или ресурсу](#access-a-page-or-resource)
- [JSON-ответ](#json-response)

---

`MoonShine` позволяет работать привычным образом, используя контроллеры.

Мы предоставляем вам наш базовый контроллер, который помогает удобно работать с `UI` и отображать ваши представления с макетом `MoonShine`.

Это полезно для отображения ваших сложных решений или написания дополнительных обработчиков.

> [!NOTE]
> Наследовать `MoonshineController` не является обязательным, мы всего лишь предоставляем удобные готовые методы.

<a name="generate-controller"></a>
## Генерация контроллера

```shell
php artisan moonshine:controller
```

> [!NOTE]
> О всех поддерживаемых опциях можно узнать в разделе [Команды](/docs/{{version}}/advanced/commands#controller).

<a name="show-blade-view"></a>
## Отображение blade-представления

```php
namespace App\MoonShine\Controllers;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Http\Controllers\MoonShineController;

final class CustomViewController extends MoonShineController
{
    public function __invoke(): PageContract
    {
        return $this->view(
            'path_to_blade',
            ['param' => 'value']
        );
    }
}
```

<a name="display-page"></a>
## Отображение страницы

```php
namespace App\MoonShine\Controllers;

use App\MoonShine\Pages\MyPage;
use MoonShine\Laravel\Http\Controllers\MoonShineController;

final class CustomViewController extends MoonShineController
{
    public function __invoke(MyPage $page): MyPage
    {
        return $page->loaded();
    }
}
```

<a name="show-notification"></a>
## Показать уведомление

```php
namespace App\MoonShine\Controllers;

use MoonShine\Laravel\Http\Controllers\MoonShineController;
use MoonShine\Support\Enums\ToastType;
use Symfony\Component\HttpFoundation\Response;

final class CustomViewController extends MoonShineController
{
    public function __invoke(): Response
    {
        $this->toast('Hello world', ToastType::SUCCESS);

        return back();
    }
}
```

<a name="send-notification"></a>
## Отправить уведомление

```php
namespace App\MoonShine\Controllers;

use MoonShine\Laravel\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class CustomViewController extends MoonShineController
{
    public function __invoke(): Response
    {
        $this->notification('Message');

        return back();
    }
}
```

<a name="access-a-page-or-resource"></a>
## Доступ к странице или ресурсу

```php
namespace App\MoonShine\Controllers;

use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class CustomViewController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request)
    {
        // $request->getPage();
        // $request->getResource();
    }
}
```

<a name="json-response"></a>
## JSON-ответ

```php
namespace App\MoonShine\Controllers;

use MoonShine\Laravel\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class CustomViewController extends MoonShineController
{
    public function __invoke(): Response
    {
        return $this->json(message: 'Message', data: []);
    }
}
```
