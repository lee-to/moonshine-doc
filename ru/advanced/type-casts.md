# TypeCasts

По умолчанию в `MoonShine` поля работают с примитивными типами и ничего не знают о моделях. 
Это было сделано для того, чтобы система не была привязана только к моделям, и поля могли, в зависимости от ситуации, иметь доступ как к необработанным данным, так и к типизированным данным.

`TypeCast` для моделей уже включен в `MoonShine`, но если вам нужно работать с другим типом данных, вам понадобится объект, реализующий интерфейс `MoonShine\Contracts\Core\TypeCasts\DataCasterContract`.

```php
interface DataCasterContract
{
    public function cast(mixed $data): DataWrapperContract;

    public function paginatorCast(mixed $data): ?PaginatorContract;
}
```

Также необходимо реализовать интерфейс `DataWrapperContract`.
Данная абстракция помогает определить что именно является ключом объекта и как его привести к массиву.

```php
interface DataWrapperContract
{
    public function getOriginal(): mixed;

    public function getKey(): int|string|null;

    public function toArray(): array;
}
```

Давайте рассмотрим пример `TypeCast` для моделей.

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

`ModelDataWrapper` реализует `DataWrapperContract` и за счет методов модели `getKey` и `toArray` помогает определить ключ и трансформировать объект в массив.

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

Теперь рассмотрим его применение в `FormBuilder`/`TableBuilder`.

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
