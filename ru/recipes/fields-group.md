# Группировка полей

Вы можете столкнуться с задачей когда вам потребуется сохранить группу полей объединенных общей логикой. 
Они могут отправить сторонний запрос, изменить файл или же просто наполнить *json* поле. Для таких задач идеально подойдет поле *Template*:

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