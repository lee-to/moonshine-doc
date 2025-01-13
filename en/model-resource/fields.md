# Fields

- [Basics](#basics)

---

<a name="basics"></a>
## Basics

Fields usually refer to database table fields.
Within the `CRUD`, they will be displayed on the main page of the section (resource) with the list and on the page for creating and editing records.
In the `MoonShine` admin panel, there are many types of fields that cover all possible requirements!
They also encompass all possible relationships in `Laravel` and are conveniently named after the relationship methods `BelongsTo`, `BelongsToMany`, `HasOne`, `HasMany`, `HasOneThrough`, `HasManyThrough`, `MorphOne`, `MorphMany`.

Adding fields to `ModelResource` is very simple!

You can use methods that allow you to declare fields for the corresponding pages: `indexFields()`, `formFields()`, or `detailFields()`.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    protected function indexFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Title'),
            Text::make('Subtitle'),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            Text::make('Title', 'title'),
            Text::make('Subtitle'),
        ];
    }

    // ...
}
```
