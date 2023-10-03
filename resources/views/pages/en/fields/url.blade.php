<x-page title="Url">

<x-extendby :href="route('moonshine.page', 'fields-text')">
    Text
</x-extendby>

<x-p>
    Everything is the same as the "Text field", the only difference is input type = url
</x-p>

<x-code language="php">
use MoonShine\Fields\Url;

Url::make('Url', 'url')
</x-code>

</x-page>
