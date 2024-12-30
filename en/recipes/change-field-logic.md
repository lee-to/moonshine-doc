# Changing Field Logic on the Fly

## Storing Images in a Related Table

To solve this task, it is necessary to block the method `onApply()` and transfer the logic to `onAfterApply()`. This will allow us to obtain the parent model on the creation page. We will have access to the model, and we will be able to work with its relationships. The method `onAfterApply()` saves and retrieves old and current values, as well as cleans up deleted files. After deleting the parent record, the method `onAfterDestroy()` removes the uploaded files.

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
        // or raw
        return DB::table('images')->pluck('file');
    })
    ->onApply(function (Model $data): Model {
        // block onApply
        return $data;
    })
    ->onAfterApply(function (Model $data, false|array $values, Image $field) {
        // $field->getRemainingValues(); the values that remained in the form considering deletions
        // $field->toValue(); current images
        // $field->toValue()->diff($field->getRemainingValues()) deleted images

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

        // or $field->removeExcludedFiles();

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
> In the code, the commented option with a relationship is provided, and an example of native retrieval of file paths from another table is shown.
