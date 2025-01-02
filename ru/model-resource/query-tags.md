# Быстрые фильтры (теги)

- [Основы](#basics)
- [Иконка](#icon)
- [Активный элемент](#active-item)
- [Условие отображения](#display-condition)
- [Псевдоним](#alias)

---

<a name="basics"></a>
## Основы

Иногда возникает необходимость создать фильтры (выборку результатов) и отобразить их на листинге. Для таких ситуаций были созданы теги.

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
                'Post with author', // Заголовок тега
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
## Иконка

Вы можете добавить иконку к тегу с помощью метода `icon()`.

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
> Для более подробной информации, пожалуйста, обратитесь к разделу [Иконки](/docs/{{version}}/appearance/icons)

<a name="active-item"></a>
## Активный элемент

Вы можете сделать *QueryTag* активным по умолчанию. Для этого нужно использовать метод `default()`.

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
## Условие отображения

Вы можете захотеть отображать теги только при определенных условиях. Для этого можно использовать метод `canSee()`, которому нужно передать функцию обратного вызова, возвращающую `TRUE` или `FALSE`.

```php
use Illuminate\Database\Eloquent\Builder;
use MoonShine\Laravel\QueryTags\QueryTag;
//...

QueryTag::make(
    'Post with author', // Заголовок тега
    fn(Builder $query) => $query->whereNotNull('author_id')
)
    ->canSee(fn() => auth()->user()->moonshine_user_role_id === 1)
```

<a name="alias"></a>
## Псевдоним

По умолчанию значение для URL генерируется автоматически из параметра `label`. При этом все символы не латинского алфавита заменяются соответствующей транслитерацией `'Заголовок' => 'zagolovok'`.

Метод `alias()` позволяет задать собственное значение для URL.

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
