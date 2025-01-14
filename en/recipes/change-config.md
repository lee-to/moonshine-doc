# Saving to file

An example of the capabilities of the Template field, in this case we save the fields to a config file. The saving process itself may vary.

```php
Template::make('Config', 'config')->fields([
    Text::make('Var'),
    Text::make('Bar'),
])
    ->changeFill(fn(mixed $data) => config('test'))
    ->changeRender(fn(mixed $value, Template $ctx) => FieldsGroup::make($ctx->getPreparedFields())->fill($value))
    ->onApply(function(mixed $item, mixed $value) {
        $content = str_replace(['array (', ')'], ['[', ']'], var_export($value, true));

        file_put_contents(config_path('test.php'), "<\?php \n\nreturn $content;");

        return $item;
    })
```

### config/test.php

```php
<?php 

return [
  'var' => 'foo',
  'bar' => 'test',
];
```