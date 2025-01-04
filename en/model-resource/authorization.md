# Authorization

- [Policy](#policy)
- [Custom Logic](#is-can)

---

<a name="policy"></a>
## Policy

We stick to the concept of `Laravel` and with the help of `Laravel policy`, we can manage access rights within the MoonShine admin panel.

In the resource controllers of `MoonShine`, each method will be checked for permissions. If you encounter difficulties, please refer to the official `Laravel` documentation.

By default, permission checks for resources are disabled. To enable it, you need to add the property `$withPolicy`.

```php
namespace MoonShine\Resources;
 
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
  //...
   
  protected bool $withPolicy = true; 
   
  //...
}
```

Available `Policy` methods:

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

You can create a `Policy` with a ready-made set of methods for `MoonShine` using the command `moonshine:policy`:

```shell
php artisan moonshine:policy
```

After executing the command, a class will be created in the `app/Policies` directory.

<a name="is-can"></a>
## Custom Logic

You can also override the `isCan` method in the resource and implement your own logic or supplement the current one:

```php
use MoonShine\Laravel\Enums\Ability;

protected function isCan(Ability $ability): bool
{
    return parent::isCan($ability);
}
```

> [!TIP]
> We also recommend reviewing the [Authorization](/docs/{{version}}/advanced/authorization) section.