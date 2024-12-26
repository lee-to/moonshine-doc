# Grouping fields

You may encounter a task when you need to save a group of fields united by common logic. 
They can send a third-party request, change a file, or simply populate a *json* field. The *Template* field is ideal for such tasks:
```php
Template::make('Config', 'config')->fields([
    Text::make('Language'),
    Text::make('Cache'),
])
    ->changeFill(fn(mixed $data) => data_get($data, 'config'))
    ->changeRender(fn(mixed $value, Template $ctx) => FieldsGroup::make($ctx->getPreparedFields())->fill($value))
    ->onApply(function(mixed $item, mixed $value) {
        $item->config = $value;
        
        return $item;
    })
```