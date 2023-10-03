<x-page title="Action" :sectionMenu="[
    'Sections' => [
        ['url' => '#basics', 'label' => 'Basics'],
        ['url' => '#view', 'label' => 'Display method'],
    ]
]">

<x-sub-title id="basics">Basics</x-sub-title>

<x-p>
    Often it is necessary to do something with the list of the section and "Actions" serve for this purpose.
    At the moment, there is only one Action class in MoonShine, which is responsible for exporting data,
    but actions could be extended and you can easily write your own.
</x-p>

<x-p>
    You can simply return all the necessary actions in the <code>actions</code> method which returns an array.
    And we will talk about actions structure in the <x-link link="{{ route('moonshine.page', 'actions-index') }}">"Actions"</x-link> section.
</x-p>

<x-moonshine::alert type="default" icon="heroicons.information-circle">
    If the method is absent or returns an empty array, then the actions will not be displayed
</x-moonshine::alert>

<x-code language="php">
namespace MoonShine\Resources;

use MoonShine\Models\MoonshineUser;
use MoonShine\Actions\ExportAction; // [tl! focus]

class PostResource extends Resource
{
    public static string $model = App\Models\Post::class;

    public static string $title = 'Articles';
    //...

    public function actions(): array // [tl! focus:start]
    {
        return [
            ExportAction::make('Export')
        ];
    } // [tl! focus:end]

    //...
}
</x-code>

<x-image theme="light" src="{{ asset('screenshots/export.png') }}"></x-image>
<x-image theme="dark" src="{{ asset('screenshots/export_dark.png') }}"></x-image>

@include('pages.en.resources.shared.actions_view', ['action' => 'ExportAction'])

</x-page>
