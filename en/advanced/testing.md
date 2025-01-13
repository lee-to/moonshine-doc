# Testing

## Creating a resource with a test file

By adding the `--test` flag to the `moonshine:resource` command, you can generate a test file along with a basic set of tests:

```shell
php artisan moonshine:resource PostResource --test
```

In addition to creating the resource, the above command will generate the following test file `tests/Feature/PostResourceTest.php`.
If you prefer `Pest`, you can specify the `--pest` option:

```shell
php artisan moonshine:resource PostResource --pest
```

Example of a test for a successful response from the resource's main page:

```php
public function test_index_page_successful(): void
{
    $response = $this->get(
        $this->getResource()->getIndexPageUrl()
    )->assertSuccessful();
}
```

## Setting up an authenticated user

Although testing `MoonShine` resources is no different from standard testing of your application, and setting up an authenticated user for a request should not be difficult, we will provide an example anyway:

```php
protected function setUp(): void
{
    parent::setUp();

    $user = MoonshineUser::factory()->create();

    $this->be($user, 'moonshine');
}
```
