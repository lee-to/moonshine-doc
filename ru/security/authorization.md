# Авторизация

- [Основы](#basics)
- [Дополнительная логика](#additional-logic)

---

<a name="basics"></a>
## Основы

**MoonShine** не отходит от концепций _Laravel_ и также использует _Laravel policy_ для работы с правами доступа.

В контроллерах ресурсов **MoonShine** каждый метод будет проверяться на наличие разрешений.
Если у вас возникнут трудности, обратитесь к официальной документации [Laravel](https://laravel.com/docs/authorization#creating-policies).

По умолчанию, проверка разрешений для ресурсов отключена.
Чтобы включить её, необходимо добавить свойство `withPolicy`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:4]
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected bool $withPolicy = true;

    // ...
}
```

Для создания *Policy* с привязкой к пользователю админ-панели вы можете использовать консольную команду:

```php
php artisan moonshine:policy PostPolicy
```

Доступные методы Policy:
- `viewAny` - страница индекса;
- `view` - детальная страница;
- `create` - создание записи;
- `update` - редактирование записи;
- `delete` - удаление записи;
- `massDelete` - массовое удаление записей;
- `restore` - восстановление записи после мягкого удаления;
- `forceDelete` - окончательное удаление записи из базы данных.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:5]
namespace App\Policies;

use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use MoonShine\Models\MoonshineUser;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user)
    {
        return true;
    }

    public function view(MoonshineUser $user, Post $item)
    {
        return true;
    }

    public function create(MoonshineUser $user)
    {
        return true;
    }

    public function update(MoonshineUser $user, Post $item)
    {
        return true;
    }

    public function delete(MoonshineUser $user, Post $item)
    {
        return true;
    }

    public function restore(MoonshineUser $user, Post $item)
    {
        return true;
    }

    public function forceDelete(MoonshineUser $user, Post $item)
    {
        return true;
    }

    public function massDelete(MoonshineUser $user)
    {
        return true;
    }
}
```

<a name="additional_logic"></a>
## Дополнительная логика

Если вам нужно добавить дополнительную логику авторизации в ваше приложение или внешний пакет,
используйте метод `authorizationRules()` в `AuthServiceProvider` или в `MoonShineServiceProvider`.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Laravel\DependencyInjection\ConfiguratorContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\Enums\Ability; // [tl! collapse:end]

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
        $config->authorizationRules(
            static function (ResourceContract $resource, Model $user, Ability $ability, Model $item): bool {
                return true;
            }
        );

        // ...
    }
}
```
