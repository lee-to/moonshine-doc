# Image

Inherits [File](/docs/{{version}}/fields/file).

* has the same capabilities

The *Image* field is an extension of *File* that allows previewing uploaded images.

~~~tabs
tab: Class
```php
use MoonShine\UI\Fields\Image;

Image::make('Thumbnail')
```
tab: Blade
```blade
<x-moonshine::form.file
    :imageable="true"
    name="thumbnail"
/>
```
~~~

![image](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/image.png)

![image dark](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/image_dark.png)

If you need to customize a modal window with an image in preview mode, then you can use the `extraAttributes()` method:

```php
Image::make('avatar')
    ->extraAttributes(fn(string $filename, int $index): ?FileItemExtra => new FileItemExtra(wide: false, auto: true, styles: 'width: 250px;')),
```

- `wide` - XL size of the modal window,
- `auto` - The window size will adjust to the size of the content,
- `styles` - Additional styles for the image in the modal window.