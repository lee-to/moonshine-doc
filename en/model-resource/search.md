# Search

- [Basics](#basics)
- [Full-Text Search](#fulltext)
- [JSON Key Search](#json)
- [Relation Search](#relation)
- [Global Search](#global)

---

<a name="basics"></a>
## Basics

To perform a search, you need to specify which fields of the model will participate in the search. For this, you need to list them in the returned array in the `search()` method.

> [!TIP] 
> If the method returns an empty array, the search string will not be displayed.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function search(): array
    {
        return ['id', 'title', 'text'];
    }

    //...
}
```

![MoonShine Search Dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/search_dark.png)

<a name="fulltext"></a>
## Full-Text Search

If full-text search is required, you need to use the attribute `MoonShine\Support\Attributes\SearchUsingFullText`.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\Support\Attributes\SearchUsingFullText;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    #[SearchUsingFullText(['title', 'text'])]
    protected function search(): array
    {
        return ['id'];
    }

    //...
}
```

> [!TIP] 
> Don't forget to add a full-text index

<a name="json"></a>
## JSON Key Search

For `Json` fields that are used as key-value `keyValue()`, you can specify which field key participates in the search.

```php
use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function search(): array
    {
        return ['data->title'];
    }

    //...
}
```

For multidimensional `Json`, which are formed through `fields()`, the search key should be specified as follows:

```php
use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function search(): array
    {
        return ['data->[*]->title'];
    }

    //...
}
```

<a name="relation"></a>
## Relation Search

You can perform a search on relations; for this, you need to specify which relation field to search by.

```php
use App\Models\Post;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function search(): array
    {
        return ['category.title'];
    }

    //...
}
```

<a name="global"></a>
## Global Search

In the MoonShine admin panel, you can implement global search based on integration with
[Laravel Scout](https://laravel.com/docs/scout).

To implement global search, you need to:

1. Install the `moonshine/scout` package:

```shell
composer require moonshine/scout
```

```shell
php artisan vendor:publish --provider="MoonShine\Scout\Providers\ScoutServiceProvider"
```

2. Specify the list of models for searching in the configuration file `config/moonshine-scout.php`.

```php
'models' => [
    Article::class,
    User::class
],
```

3. Implement the interface in the models.

```php
use MoonShine\Scout\HasGlobalSearch;
use MoonShine\Scout\SearchableResponse;
use Laravel\Scout\Searchable;
use Laravel\Scout\Builder;

class Article extends Model implements HasGlobalSearch
{
    use Searchable;

    public function searchableQuery(Builder $builder): Builder
    {
        return $builder->take(4);
    }

    public function toSearchableResponse(): SearchableResponse
    {
        return new SearchableResponse(
            group: 'Articles',
            title: $this->title,
            url: '/',
            preview: $this->text,
            image: $this->thumbnail
        );
    }
}
```

4. Replace the component in `Layout`

```php
use MoonShine\Laravel\Components\Layout\Locales;
use MoonShine\Laravel\Components\Layout\Notifications;
use MoonShine\UI\Components\Layout\Header;
use MoonShine\UI\Components\Breadcrumbs;

protected function getHeaderComponent(): Header
{
    return Header::make([
        Breadcrumbs::make($this->getPage()->getBreadcrumbs())->prepend($this->getHomeUrl(), icon: 'home'),
        \MoonShine\Scout\Components\Search::make(),
        When::make(
            fn (): bool => $this->isUseNotifications(),
            static fn (): array => [Notifications::make()]
        ),
        Locales::make(),
    ]);
}
```