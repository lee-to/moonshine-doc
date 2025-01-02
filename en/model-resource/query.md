# Queries

- [Queries](#query)
- [Receiving a Record](#receiving-a-record)
- [Eager Load](#eager-load)
- [Search](#search)
- [Sorting](#sorting)

---

<a name="query"></a>
## Queries

It is often necessary to initially modify all resource queries to the database. You can easily override the `QueryBuilder` in the resource.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function modifyQueryBuilder(Builder $builder): Builder
    {
        return $builder->where('active', true);
    }

    //...
}
```

> [!NOTE]
> If you need to completely override the `Builder`, you can override the resource method `newQuery`

<a name="receiving-a-record"></a>
## Receiving a Record

The `modifyItemQueryBuilder()` method is used if you need to modify the query for retrieving a record from the database.

```php
use App\Models\Post;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function modifyItemQueryBuilder(Builder $builder): Builder
    {
        return $builder->withTrashed();
    }

    //...
}
```

<a name="eager-load"></a>
## Eager Load

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    //...

    protected array $with = ['user', 'categories'];
}
```

> [!NOTE]
> If you need to completely override the `Builder`, you can override the resource method `findItem`

<a name="search"></a>
## Search

The `searchQuery()` method allows you to modify the query when searching for records.

```php
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected function searchQuery(string $terms): void
    {
        return parent::searchQuery($terms)->withTrashed();
    }

    //...
}
```

You can also completely override the search logic

```php
protected function resolveSearch(string $terms, ?iterable $fullTextColumns = null): static
{
  // Your logic

  return $this;
}
```

<a name="sorting"></a>
## Sorting

By overriding the `resolveOrder()` method, you can customize the sorting of records.

```php
use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function resolveOrder(string $column, string $direction, ?Closure $callback): static
    {
        if ($callback instanceof Closure) {
            $callback($this->newQuery(), $column, $direction);
        } else {
            $this->newQuery()->orderBy($column, $direction);
        }

        return $this;
    }

    //...
}
```