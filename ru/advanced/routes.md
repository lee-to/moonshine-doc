# Routes

`MoonShine` под капотом использует стандартный `Laravel Routing`.
Все отображаемые страницы рендерятся через `PageController`, который имеет очень простой вид.

```php
public function __invoke(MoonShineRequest $request): PageContract
{
    $request->getResource()?->loaded();

    $page = $request
        ->getPage()
        ->checkUrl()
        ->loaded();

    return $page;
}
```

Тем самым вы вольны использовать самостоятельно объявленные роуты и контроллеры (если требуется) и рендерить страницы или то что требуется.

Для правильной работы CRUD страниц, необходимо передавать роут параметры `resourceUri` и `pageUri`, `resourceUri` опционален, так как не все страницы имеют ресурс.

Пример стандартного роута:

```php
Route::get('/admin/resource/{resourceUri}/{pageUri}', CustomController::class)
	->middleware(['moonshine', \MoonShine\Laravel\Http\Middleware\Authenticate::class])
	->name('moonshine.name');
```

> [!NOTE]
> Префикс `resource` можно изменить или удалить через [настройки конфигурации](/docs/{{version}}/configuration).

Данный пример включает в себя роут с параметрами ресурса и страницы, а также группу middleware `moonshine`, список которой располагается в конфиге `moonshine.php`, и middleware `Authenticate` для доступа к ендпоинту только для авторизованного пользователя

Для быстрой реализации примера выше, можно воспользоваться `Route` директивой `moonshine`:

```php
Route::moonshine(static function (Router $router) {
    $router->post(
        'permissions/{resourceItem}',
        PermissionController::class
    )->name('permissions');
}, withResource: true, withPage: true, withAuthenticate: true);

// result
// POST /admin/resource/{resourceUri}/{pageUri}/permissions/{resourceItem}
// middleware: moonshine, Authenticate::class
```

Пример получения роута из контекста ресурса:

```php
$this->getRoute('permissions')
```

Пример получения роута вне ресурса:

```php
route('moonshine.permissions', ['resourceUri' => 'user-resource', 'pageUri' => 'custom-page'])
```

```php
Route::moonshine(static function (Router $router) {
    //
},
// add prefix {resourceUri}
withResource: false,
// add prefix {pageUri}
withPage: false,
// add middleware Authenticate::class
withAuthenticate: false
);
```

Наилучший путь создать `routes/moonshine.php` и внутри объявлять собственные роуты.

> [!NOTE]
> При создании файла `routes/moonshine.php` не забудьте объявить его в системе.

> [!WARNING]
> Нельзя одновременно использовать группы middleware `web` и `moonshine`, так как они делают одно и тоже и одновременно запускают сессии.
