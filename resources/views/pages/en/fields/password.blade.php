<x-page title="Password">

<x-extendby :href="route('moonshine.page', 'fields-text')">
    Text
</x-extendby>

<x-p>
    Everything is the same as the "Text field", the only difference is input type = password
</x-p>

<x-p>
    And usually it could be accompanied with the password confirmation field
</x-p>

<x-code language="php">
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;

//...

public function fields(): array
{
    return [
        Password::make('Password', 'password')->hideOnIndex(),  // [tl! focus]
        PasswordRepeat::make('Repeat password', 'password_repeat')->hideOnIndex(),  // [tl! focus]
    ];
}

//...

</x-code>

</x-page>
