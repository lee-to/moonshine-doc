# Routes

`MoonShine` under the hood uses standard `Laravel Routing`.
All displayed pages are rendered through `PageController`, which has a very simple appearance.

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

Thus, you are free to use self-declared routes and controllers (if needed) and render pages or whatever is required.

For CRUD pages to work correctly, it is necessary to pass route parameters `resourceUri` and `pageUri`. `resourceUri` is optional, as not all pages have a resource.

Example of a standard route:

```php
Route::get('/admin/resource/{resourceUri}/{pageUri}', CustomController::class)
	->middleware(['moonshine', \MoonShine\Laravel\Http\Middleware\Authenticate::class])
	->name('moonshine.name');
```

> [!NOTE]
> The prefix `resource` can be changed or removed through [configuration settings](/docs/{{version}}/configuration).

This example includes a route with parameters for the resource and page, as well as a group of middleware `moonshine`, the list of which is located in the `moonshine.php` config file, and the middleware `Authenticate` for access to the endpoint only for authorized users.

For a quick implementation of the example above, you can use the `Route` directive `moonshine`:

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

Example of retrieving a route from the resource context: 

```php
$this->getRoute('permissions')
```

Example of retrieving a route outside the resource:

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

The best way is to create `routes/moonshine.php` and declare your own routes inside.

> [!NOTE]
> When creating the file `routes/moonshine.php`, remember to declare it in the system.

> [!WARNING]
> You cannot use middleware groups `web` and `moonshine` simultaneously, as they do the same thing and start sessions at the same time.
