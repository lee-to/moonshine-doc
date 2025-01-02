# Метрики

На странице индекса модели ресурса вы можете отображать информационные блоки со статистикой - метрики. Для этого в методе `metrics()` верните массив из `Metric`.

```php
namespace App\MoonShine\Resources;

use App\Models\Post;
use App\Models\Comment;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use MoonShine\Laravel\Resources\ModelResource;

class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Posts';

    //...

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [
            ValueMetric::make('Articles')->value(fn() => Post::count())->columnSpan(6),
            ValueMetric::make('Comments')->value(fn() => Comment::count())->columnSpan(6),
        ];
    }

    //...
}
```
![metrics](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/metrics.png)
![metrics_dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/metrics_dark.png)

> [!NOTE]
> Для более подробной информации, пожалуйста, обратитесь к разделам [Metrics](/docs/{{version}}/components/metrics)


Если вам необходимо обвернуть метрики во *Fragment*:

```php
use Closure;
use MoonShine\Laravel\Components\Fragment;

protected function fragmentMetrics(): ?Closure
{
    return static fn(array $components): Fragment => Fragment::make($components)->name('metrics');
}
```
