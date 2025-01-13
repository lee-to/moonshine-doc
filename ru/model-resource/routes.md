# Routes

- [Основы](#basics)
- [Хелпер](#helper)
- [Текущая страница](#current-page)

---

<a name="basics"></a>
## Основы

```php
$resource->getUrl(); // First page of the resource

$resource->getRoute($name, $key, $params); // Advanced method for obtaining routes

$resource->getPageUrl($page, $params, $fragment); // Advanced method for obtaining page`s route

$resource->getIndexPageUrl(); // index page
$resource->getIndexPageUrl(['query-tag' => $tag->uri()]); // query tag

$resource->getFormPageUrl(); // create page
$resource->getFormPageUrl(1); // edit page by int
$resource->getFormPageUrl($item); // edit page by Model

$resource->getDetailPageUrl(1); // detail page by int
$resource->getDetailPageUrl($item); // detail page by Model

$resource->getAsyncMethodUrl('updateSomething'); // ANY
$resource->getFragmentLoadUrl('table-index', $resource->formPage());

// CRUD
$resource->getRoute('crud.update', $data->getKey()); // PUT
$resource->getRoute('crud.store'); // POST
$resource->getRoute('crud.destroy', $data->getKey()); // DELETE
$resource->getRoute('crud.massDelete'); // DELETE

// Handlers
$resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()]);
```

<a name="helper"></a>
## Helper
Также можно воспользоваться хелпером `toPage`:

```php
toPage(
    string|PageContract|null $page = null,
    string|ResourceContract|null $resource = null,
    array $params = [],
    bool $redirect = false,
    ?string $fragment = null
): RedirectResponse|string
```

- `$page` - страница или class-string страницы (опционально),
- `$resource` - ресурс или class-string ресурса (опционально),
- `$params` - дополнительный query (опционально),
- `$redirect` - при необходимости сразу выполнить редирект (опционально),
- `$fragment` - url будет использован для Fragment загрузки (опционально).

```php
toPage(page: IndexPage::class);
toPage(page: IndexPage::class, resource: PostResource::class);
toPage(page: IndexPage::class, redirect: true);
toPage(page: IndexPage::class, fragment: true);
```

<a name="current-page"></a>
## Текущая страница

Ресурс модели имеет метод `getActivePage()` позволяющий получить текущую активную страницу, если активной страницы нет, то результат будет `NULL`.

```php
$resource->getActivePage() // ?PageContract

if($resource->getActivePage() instanceof IndexPage)

if($resource->getActivePage() instanceof FormPage)

if($resource->getActivePage() instanceof DetailPage)
```
