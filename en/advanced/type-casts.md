# TypeCasts

By default, in `MoonShine`, fields work with primitive types and know nothing about models. 
This was done to ensure that the system is not bound only to models, allowing fields to have access to both raw data and typed data depending on the situation.

`TypeCast` for models is already enabled in `MoonShine`, but if you need to work with a different type of data, you will need an object that implements the `MoonShine\Contracts\Core\TypeCasts\DataCasterContract` interface.

```php
interface DataCasterContract
{
    public function cast(mixed $data): DataWrapperContract;

    public function paginatorCast(mixed $data): ?PaginatorContract;
}
```

You also need to implement the `DataWrapperContract` interface.
This abstraction helps to determine what exactly is the key of the object and how to convert it to an array.

```php
interface DataWrapperContract
{
    public function getOriginal(): mixed;

    public function getKey(): int|string|null;

    public function toArray(): array;
}
```

Let's look at an example of a `TypeCast` for models.

```php
final readonly class ModelCaster implements DataCasterContract
{
    public function __construct(
        /** @var class-string<T> $class */
        private string $class
    ) {
    }

    /** @return class-string<T> $class */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return ModelDataWrapper<T>
     */
    public function cast(mixed $data): ModelDataWrapper
    {
        $model = new ($this->getClass());
        return new ModelDataWrapper($model->fill($data));
    }

    public function paginatorCast(mixed $data): ?PaginatorContract
    {
        if (! $data instanceof Paginator && ! $data instanceof CursorPaginator) {
            return null;
        }

        $paginator = new PaginatorCaster(
            $data->appends(
                moonshine()->getRequest()->getExcept('page')
            )->toArray(),
            $data->items()
        );

        return $paginator->cast();
    }
}
```

`ModelDataWrapper` implements the `DataWrapperContract` and, thanks to the model methods `getKey` and `toArray`, helps to determine the key and transform the object into an array.

```php
final readonly class ModelDataWrapper implements DataWrapperContract
{
    public function __construct(private Model $model)
    {
    }

    public function getOriginal(): Model
    {
        return $this->model;
    }

    public function getKey(): int|string|null
    {
        return $this->model->getKey();
    }

    public function toArray(): array
    {
        return $this->model->toArray();
    }
}
```

Now let's consider its application in `FormBuilder`/`TableBuilder`.

```php
TableBuilder::make(items: User::paginate())
    ->fields([
        Text::make('Email'),
    ])
    ->cast(new ModelCaster(User::class))
```

```php
FormBuilder::make()
    ->fields([
        Text::make('Email'),
    ])
    ->fillCast(User::query()->first(), new ModelCaster(User::class))
```
