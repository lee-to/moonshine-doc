# Авторизация

- [Policy](#policy)
- [Собственная логика](#is-can)

---

<a name="policy"></a>
## Policy

Мы не отходим от концепции `Laravel` и с помощью `Laravel policy` можем работать с правами доступа в рамках админ-панели MoonShine.

В ресурс-контроллерах `MoonShine` каждый метод будет проверяться на наличие прав.
Если возникают трудности, то ознакомьтесь с официально документацией `Laravel`.

По умолчанию, для ресурсов проверка прав отключена.
Чтобы включить, необходимо добавить свойство `$withPolicy`.

```php
namespace MoonShine\Resources;
 
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    // ...
   
    protected bool $withPolicy = true; 
   
    // ...
}
```

Доступные методы `Policy`:

- viewAny
- view
- create
- update
- delete
- massDelete
- restore
- forceDelete

```php
namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MoonShine\Laravel\Models\MoonshineUser;
use App\Models\Post;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user)
    {
        //
    }

    public function view(MoonshineUser $user, Post $model)
    {
        //
    }

    public function create(MoonshineUser $user)
    {
        //
    }

    public function update(MoonshineUser $user, Post $model)
    {
        //
    }

    public function delete(MoonshineUser $user, Post $model)
    {
        //
    }

    public function massDelete(MoonshineUser $user)
    {
        //
    }

    public function restore(MoonshineUser $user, Post $model)
    {
        //
    }

    public function forceDelete(MoonshineUser $user, Post $model)
    {
        //
    }
}
```

Создать `Policy` с готовым набором методов под `MoonShine` можно с помощью команды `moonshine:policy`:

```shell
php artisan moonshine:policy
```

После выполнения команды будет создан класс в директории `app/Policies`.

<a name="is-can"></a>
## Собственная логика

Также вы можете переопределить метод `isCan` в ресурсе и реализовать собственную логику или дополнить текущую:

```php
use MoonShine\Laravel\Enums\Ability;

protected function isCan(Ability $ability): bool
{
    return parent::isCan($ability);
}
```

> [!TIP]
> Также рекомендуем ознакомится с разделом [Авторизация](/docs/{{version}}/advanced/authorization).
