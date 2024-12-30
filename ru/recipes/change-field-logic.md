# Изменение логики полей на лету

## Сохранение изображений в связанной таблице

Для решения этой задачи необходимо заблокировать метод `onApply()` и перенести логику в `onAfterApply()`. 
Это позволит получить родительскую модель на странице создания. У нас будет доступ к модели, и мы сможем работать с ее отношениями.
Метод `onAfterApply()` сохраняет и получает старые и текущие значения, а также очищает удаленные файлы.
После удаления родительской записи метод `onAfterDestroy()` удаляет загруженные файлы.

```php
use MoonShine\UI\Fields\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

//...

Image::make('Images', 'images')
    ->multiple()
    ->removable()
    ->changeFill(function (Model $data, Image $field) {
        // return $data->images->pluck('file');
        // или raw
        return DB::table('images')->pluck('file');
    })
    ->onApply(function (Model $data): Model {
        // блокируем onApply
        return $data;
    })
    ->onAfterApply(function (Model $data, false|array $values, Image $field) {
        // $field->getRemainingValues(); значения, которые остались в форме с учетом удалений
        // $field->toValue(); текущие изображения
        // $field->toValue()->diff($field->getRemainingValues()) удаленные изображения

        if($values !== false) {
            foreach ($values as $value) {
                DB::table('images')->insert([
                    'file' => $field->getApplyClass()->store($field, $value),
                    // or 'file' => $value->store(),
                ]);
            }
        }

        foreach ($field->toValue()->diff($field->getRemainingValues()) as $removed) {
            DB::table('images')->where('file', $removed)->delete();
            Storage::disk('public')->delete($removed);
        }

        // или $field->removeExcludedFiles();

        return $data;
    })
    ->onAfterDestroy(function (Model $data, mixed $values, Image $field) {
        foreach ($values as $value) {
            Storage::disk('public')->delete($value);
        }

        return $data;
    })

//...
```

> [!WARNING]
> В коде закомментирован вариант с отношением и приведен пример нативного получения путей к файлам из другой таблицы.

