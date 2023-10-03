<x-page title="Диапазон слайдер">

<x-extendby :href="route('moonshine.page', 'fields-range')">
    Range
</x-extendby>

<x-p>
    <x-p>
        Поле <em>RangeSlider</em> является расширением <em>Range</em> и
        дополнительно имеет возможность изменять значения с помощью ползунка.
    </x-p>
</x-p>

<x-code language="php">
use MoonShine\Fields\RangeSlider; // [tl! focus]

//...

public function fields(): array
{
    return [
        RangeSlider::make('Age') // [tl! focus]
            ->fromTo('age_from', 'age_to') // [tl! focus]
    ];
}

//...
</x-code>

<x-image theme="light" src="{{ asset('screenshots/slide.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/slide_dark.png') }}"></x-image>

</x-page>
