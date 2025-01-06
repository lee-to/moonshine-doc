# Display menu items based on a condition

1. Using Gate facade:

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"} 
// [tl! collapse:start] 
use Illuminate\Support\Facades\Gate;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\MenuManager\MenuItem; 
// [tl! collapse:end]

protected function menu(): array
{
  return [
    MenuItem::make('Roles', MoonShineUserRoleResource::class)
      ->canSee(fn() => Gate::check(Ability::VIEW_ANY, MoonshineUserRole::class)),
  ];
}
```

2. Using resource:

```php
use MoonShine\Laravel\Enums\Ability;

protected function menu(): array
{
  return [
    MenuItem::make('Roles', MoonShineUserRoleResource::class)
      ->canSee(fn(MenuItem $item) => $item->getFiller()->can(Ability::VIEW_ANY)),
  ];
}
```

3. Without Policy:

```php
protected function menu(): array
{
    $menu = [
        MenuItem::make('Articles', ArticleResource::class),
    ];

    if (request()->user()->isSuperUser()) {
        $menu[] = MenuItem::make(
            'Admins',
            MoonShineUserResource::class
        );
    }

    return $menu;
}
```
