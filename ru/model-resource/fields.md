# Поля

- [Основы](#basics)

---

<a name="basics"></a>
## Основы

Поля в большинстве случаев относятся к полям таблицы из базы данных.
В рамках `CRUD` будут выводиться на главной странице раздела (ресурса) со списком и на странице создания и редактирования записей.
В административной панели `MoonShine` существует множество видов полей, которые покрывают все возможные требования!
Также охватывают и все возможные связи в `Laravel` и для удобства называются так же, как и методы отношений `BelongsTo`, `BelongsToMany`, `HasOne`, `HasMany`, `HasOneThrough`, `HasManyThrough`, `MorphOne`, `MorphMany`.

Добавлять поля в `ModelResource` очень просто!

Для этого можно воспользоваться методами которые позволяют объявить поля для соответствующих страниц: `indexFields()`, `formFields()` или `detailFields()`.

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
