# Quick Filters (Tags)

- [Basics](#basics)
- [Icon](#icon)
- [Active Item](#active-item)
- [Display Condition](#display-condition)
- [Alias](#alias)
- [Dropdown](#dropdown)

---

<a name="basics"></a>
## Basics

Sometimes there is a need to create filters (result selection) and display them in the listing. Tags were created for such situations.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function queryTags(): array
    {
        return [
            QueryTag::make(
                'Post with author', // Tag title
                fn(Builder $query) => $query->whereNotNull('author_id') // Query builder
            )
        ];
    }

    //...
}

```

![query_tags](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/query_tags.png)
![query_tags_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/query_tags_dark.png)

<a name="icon"></a>
## Icon

You can add an icon to the tag using the `icon()` method.

```php
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
//...

QueryTag::make(
    'Post without an author',
    fn(Builder $query) => $query->whereNull('author_id')
)
    ->icon('users')
```

> [!NOTE]
> For more detailed information, please refer to the section [Icons](/docs/{{version}}/appearance/icons)

<a name="active-item"></a>
## Active Item

You can make a *QueryTag* active by default. To do this, use the `default()` method.

```php
default(Closure|bool|null $condition = null)
```

```php
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
//...

QueryTag::make(
    'All posts',
    fn(Builder $query) => $query
)
    ->default()
```

<a name="display-condition"></a>
## Display Condition

You might want to display tags only under certain conditions. You can use the `canSee()` method, which requires you to pass a callback function that returns `TRUE` or `FALSE`.

```php
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
//...

QueryTag::make(
    'Post with author', // Tag title
    fn(Builder $query) => $query->whereNotNull('author_id')
)
    ->canSee(fn() => auth()->user()->moonshine_user_role_id === 1)
```

<a name="alias"></a>
## Alias

By default, the URL value is automatically generated from the `label` parameter. All non-Latin alphabet characters are replaced with their corresponding transliteration, e.g., `'Заголовок' => 'zagolovok'`.

The `alias()` method allows you to set a custom value for the URL.

```php
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
//...

QueryTag::make(
    'Archived Posts',
    fn(Builder $query) => $query->where('is_archived', true)
)
    ->alias('archive')
```

<a name="dropdown"></a>
## Dropdown

By default, all tag buttons are displayed in a line, but you can display them through a drop-down list.
To do this, change the `$queryTagsInDropdown` property in the resource:

```php
class PostResource extends ModelResource 
{
    // ...
    
    protected bool $queryTagsInDropdown = true;
    
    // ...
}
```