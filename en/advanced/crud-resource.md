# CrudResource

- [Basics](#basics)
- [Creating a Custom Resource](#custom-resource)
- [REST Resource Example](#rest-example)
- [Full Customization](#full-customization)

---

<a name="basics"></a>
## Basics

`CrudResource` is a fundamental part of `MoonShine for Laravel`. 
It is important to understand that the core of `MoonShine` does not depend on `Laravel` and even more so on `Eloquent` models. 
However, in the implementation for `Laravel`, we provide a ready-made `ModelResource` for working with models and corresponding `type-casts`.
`MoonShine` is very flexible, and you can create your own resource to work with any data sources.

`CrudResource` provides a basic structure for working with data without being tied to a specific implementation.
This allows:

- Working with any data sources (databases, `API`, files, etc.),
- Creating your own implementations for specific tasks,
- Using a single interface regardless of the data source.

<a name="custom-resource"></a>
## Creating a Custom Resource

To create a custom resource, it is enough to extend `CrudResource` and implement the abstract methods:

```php
namespace App\MoonShine\Resources;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Laravel\Resources\CrudResource;

final class RestCrudResource extends CrudResource
{
    public function findItem(bool $orFail = false): mixed
    {
        // ...
    }

    public function getItems(): mixed
    {
        // ...
    }

    public function massDelete(array $ids): void
    {
        // ...
    }

    public function delete(mixed $item, ?FieldsContract $fields = null): bool
    {
        // ...
    }

    public function save(mixed $item, ?FieldsContract $fields = null): mixed
    {
        // ...
    }
}
```

<a name="rest-example"></a>
## REST Resource Example

Here is an example of implementing a resource for working with a `REST API`:

```php
namespace App\MoonShine\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Laravel\Resources\CrudResource;

final class RestCrudResource extends CrudResource
{
    public function getItems(): iterable
    {
        yield from Http::get('https://jsonplaceholder.typicode.com/todos')->json();
    }

    public function findItem(bool $orFail = false): array
    {
        yield from Http::get('https://jsonplaceholder.typicode.com/todos/' . $this->getItemID())->json();
    }

    public function massDelete(array $ids): void
    {
        $this->beforeMassDeleting($ids);

        foreach ($ids as $id) {
            $this->delete(['id' => $id]);
        }

        $this->afterMassDeleted($ids);
    }

    public function delete(mixed $item, ?FieldsContract $fields = null): bool
    {
        return Http::delete('https://jsonplaceholder.typicode.com/todos/' . $item['id'])->successful();
    }

    public function save(mixed $item, ?FieldsContract $fields = null): mixed
    {
        $data = request()->all();

        if ($item['id'] ?? false) {
            return Http::put('https://jsonplaceholder.typicode.com/todos/' . $item['id'], $data)->json();
        }

        $this->isRecentlyCreated = true;

        return Http::post('https://jsonplaceholder.typicode.com/todos', $data)->json();
    }
}
```

<a name="full-customization"></a>
# Full Customization

If you require complete control over the resource, instead of inheriting from `CrudResource`, you can implement the interface `MoonShine\Contracts\Core\CrudResourceContract`. 
This will give you maximum flexibility in implementing all necessary methods.
