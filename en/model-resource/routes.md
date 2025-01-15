# Routes

- [Basics](#basics)
- [Helper](#helper)
- [Current Page](#current-page)

---

<a name="basics"></a>
## Basics

```php
// First page of the resource
$resource->getUrl();

// Advanced method for obtaining routes
$resource->getRoute($name, $key, $params);

// Advanced method for obtaining page's route
$resource->getPageUrl($page, $params, $fragment);

// Index page
$resource->getIndexPageUrl();
// Query tag
$resource->getIndexPageUrl(['query-tag' => $tag->uri()]);

// Create page
$resource->getFormPageUrl();
// Edit page by int
$resource->getFormPageUrl(1);
// Edit page by Model
$resource->getFormPageUrl($item);

// Detail page by int
$resource->getDetailPageUrl(1);
// Detail page by Model
$resource->getDetailPageUrl($item);

// ANY
$resource->getAsyncMethodUrl('updateSomething');
$resource->getFragmentLoadUrl('table-index', $resource->formPage());

// CRUD

// PUT
$resource->getRoute('crud.update', $data->getKey());
// POST
$resource->getRoute('crud.store');
// DELETE
$resource->getRoute('crud.destroy', $data->getKey());
// DELETE
$resource->getRoute('crud.massDelete');

// Handlers
$resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()]);
```

<a name="helper"></a>
## Helper
You can also use the helper `toPage()`.

```php
toPage(
    string|PageContract|null $page = null,
    string|ResourceContract|null $resource = null,
    array $params = [],
    bool $redirect = false,
    ?string $fragment = null
): RedirectResponse|string
```

- `$page` - page or class-string of the page (optional),
- `$resource` - resource or class-string of the resource (optional),
- `$params` - additional query (optional),
- `$redirect` - if necessary, redirect immediately (optional),
- `$fragment` - URL will be used for Fragment loading (optional).

```php
toPage(page: IndexPage::class);
toPage(page: IndexPage::class, resource: PostResource::class);
toPage(page: IndexPage::class, redirect: true);
toPage(page: IndexPage::class, fragment: true);
```

<a name="current-page"></a>
## Current Page

The model resource has a method `getActivePage()` that allows you to get the current active page; if there is no active page, the result will be `NULL`.

```php
$resource->getActivePage() // ?PageContract

if($resource->getActivePage() instanceof IndexPage)

if($resource->getActivePage() instanceof FormPage)

if($resource->getActivePage() instanceof DetailPage)
```
