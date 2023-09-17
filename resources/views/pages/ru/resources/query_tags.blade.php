<x-page title="Быстрые фильтры/Теги" :sectionMenu="[
    'Разделы' => [
        ['url' => '#basics', 'label' => 'Основы'],
        ['url' => '#icon', 'label' => 'Иконка'],
        ['url' => '#can-see', 'label' => 'Условие отображения'],
    ]
]">

<x-sub-title id="basics">Основы</x-sub-title>

<x-p>
    Иногда возникает потребность создать набор фильтров (подборку результатов)
    и отобразить ее на листинге. Для таких ситуаций созданы теги.
</x-p>

<x-code language="php">
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\QueryTags\QueryTag; // [tl! focus]
use MoonShine\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    public function queryTags(): array // [tl! focus:start]
    {
        return [
            QueryTag::make(
                'Post with author', // Tag Title
                fn(Builder $query) => $query->whereNotNull('author_id') // Query builder
            )
        ];
    } // [tl! focus:end] // [tl! focus:end]

    //...
}
</x-code>

<x-image theme="light" src="{{ asset('screenshots/query_tags.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/query_tags_dark.png') }}"></x-image>

<x-sub-title id="icon">Иконка</x-sub-title>

<x-p>
    Тега можно добавить иконку воспользовавшись методом <code>icon()</code>.
</x-p>

<x-code language="php">
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Post without an author',
                fn(Builder $query) => $query->whereNull('author_id')
            )->icon('heroicons.users') // [tl! focus]
        ];
    }

    //...
}
</x-code>

<x-moonshine::alert type="default" icon="heroicons.book-open">
    За более подробной информацией обратитесь к разделу <x-link link="{{ route('moonshine.custom_page', 'icons-index') }}">Icons</x-link>
</x-moonshine::alert>

<x-sub-title id="can-see">Условие отображения</x-sub-title>

<x-p>
    Может потребоваться отобразить теги только при определенных условиях,
    для этого можно воспользоваться методом <code>canSee()</code>,
    которому необходимо передать callback функцию возвращающее <code>TRUE</code> или <code>FALSE</code>.
</x-p>

<x-code language="php">
namespace App\MoonShine\Resources;

use App\Models\Post;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    public function queryTags(): array
    {
        return [
            QueryTag::make(
                'Post with author', // Заголовок тега
                fn(Builder $query) => $query->whereNotNull('author_id')
            )->canSee(fn() => auth()->user()->moonshine_user_role_id === 1) // [tl! focus]
        ];
    }

    //...
}
</x-code>

</x-page>
