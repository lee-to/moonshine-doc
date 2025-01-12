# Поля отношений во вкладках

В данном рецепте мы покажем как кастомизировать страницу с формой (взяли пример статей из основного демо репозитория), где вынесем поля отношений в отдельные вкладки, 
а также этот рецепт даст понимание огромных возможностей кастомизации страниц.

## Создание страницы с формой

Для начала вам необходимо добавить страницу, в нашем примере это *ArticleFormPage*, которая наследует *FormPage*.
После необходимо заменить *FormPage* на *ArticleFormPage* в ресурсе:

```php
class ArticleResource extends ModelResource
{
    // ..
    protected function pages(): array
    {
        return [
            IndexPage::class,
            // FormPage::class -> ArticleFormPage::class
            ArticleFormPage::class,
            DetailPage::class
        ];
    }
```

## Кастомизация страницы

```php
namespace App\MoonShine\Pages\Article;

use App\Models\Comment;
use App\MoonShine\Resources\CommentResource;
use App\MoonShine\Resources\MoonShineUserResource;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\TinyMce\Fields\TinyMce;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\StackFields;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

final class ArticleFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),

            Grid::make([
                Column::make([
                    Box::make('Main information', [
                        ActionButton::make(
                            'Link to article',
                            $this->getResource()->getItem()?->getKey() ? route('articles.show', $this->getResource()->getItem()) : '/',
                        )
                            ->icon('paper-clip')
                            ->blank(),

                        LineBreak::make(),

                        BelongsTo::make('Author', resource: MoonShineUserResource::class)
                            ->asyncSearch()
                            ->canSee(fn () => auth()->user()->moonshine_user_role_id === 1)
                            ->required(),

                        Collapse::make('Title/Slug', [
                            Heading::make('Title/Slug'),

                            Flex::make([
                                Text::make('Title')
                                    ->withoutWrapper()
                                    ->required()
                                ,

                                Slug::make('Slug')
                                    ->from('title')
                                    ->unique()
                                    ->separator('-')
                                    ->withoutWrapper()
                                    ->required()
                                ,
                            ])
                                ->name('flex-titles')
                                ->justifyAlign('start')
                                ->itemsAlign('start'),
                        ]),

                        StackFields::make('Files')->fields([
                            Image::make('Thumbnail')
                                ->removable()
                                ->disk('public')
                                ->dir('articles'),
                        ]),

                        Preview::make('No input field', 'no_input', static fn () => fake()->realText()),

                        RangeSlider::make('Age')
                            ->min(0)
                            ->max(60)
                            ->step(1)
                            ->fromTo('age_from', 'age_to'),

                        Number::make('Rating')
                            ->hint('From 0 to 5')
                            ->min(0)
                            ->max(5)
                            ->link('https://cutcode.dev', 'CutCode', blank: true)
                            ->stars(),

                        Url::make('Link')
                            ->hint('Url')
                            ->link('https://cutcode.dev', 'CutCode', blank: true)
                            ->suffix('url')
                        ,

                        Color::make('Color'),

                        Json::make('Data')->fields([
                            Text::make('Title'),
                            Text::make('Value'),
                        ])->creatable()->removable(),

                        Switcher::make('Active'),
                    ]),
                ])->columnSpan(6),

                Column::make([
                    Box::make('Seo and categories', [
                        Tabs::make([
                            Tab::make('Seo', [
                                Text::make('Seo title')
                                    ->withoutWrapper(),

                                Text::make('Seo description')
                                    ->withoutWrapper(),

                                TinyMce::make('Description')
                                    ->addPlugins(['code', 'codesample'])
                                    ->toolbar(' | code codesample')
                                    ->required()
                                ,
                            ]),

                            Tab::make('Categories', [
                                BelongsToMany::make('Categories')->tree('category_id'),
                            ]),
                        ]),
                    ]),
                ])->columnSpan(6),
            ]),

            $this->getCommentsField(),
            $this->getCommentField(),
        ];
    }

    private function getCommentsField(): HasMany
    {
        return HasMany::make('Comments', resource: CommentResource::class)
            ->fillData($this->getResource()->getItem())
            ->async()
            ->creatable();
    }

    private function getCommentField(): HasOne
    {
        return HasOne::make('Comment', resource: CommentResource::class)
            ->fillData($this->getResource()->getItem())
            ->async();
    }

    protected function mainLayer(): array
    {
        return [
            Tabs::make([
                Tab::make('Basics', parent::mainLayer()),
                Tab::make('Comments', [
                    $this->getResource()->getItem() ? $this->getCommentsField() : 'To add comments, save the article',
                ]),
                Tab::make('Comment', [
                    $this->getResource()->getItem() ? $this->getCommentField() : 'To add comments, save the article',
                ]),
                Tab::make('Table', [
                    TableBuilder::make()
                        ->fields([
                            ID::make(),
                            Text::make('Text')
                        ])
                        ->cast(new ModelCaster(Comment::class))
                        ->items($this->getResource()->getItem()?->comments ?? [])
                ]),
            ]),
        ];
    }

    protected function bottomLayer(): array
    {
        return [];
    }
}
```

Стоит обратить внимание на то, что мы вынесли *HasOne* и *HasMany* поле в отдельные методы, которые также дублируем в методе `fields`. 
Это необходимо для того, чтобы *MoonShine* при взаимодействии с полями мог найти их в системе.

```php
final class ArticleFormPage extends FormPage
{
    protected function fields(): iterable
    {
        return [
            ID::make(),

            //...

            $this->getCommentsField(),
            $this->getCommentField(),
        ];
    }

    private function getCommentsField(): HasMany
    {
        return HasMany::make('Comments', resource: CommentResource::class)
            ->fillData($this->getResource()->getItem())
            ->async()
            ->creatable();
    }

    private function getCommentField(): HasOne
    {
        return HasOne::make('Comment', resource: CommentResource::class)
            ->fillData($this->getResource()->getItem())
            ->async();
    }

    protected function mainLayer(): array
    {
        return [
            Tabs::make([
                Tab::make('Basics', parent::mainLayer()),
                Tab::make('Comments', [
                    $this->getResource()->getItem() ? $this->getCommentsField() : 'To add comments, save the article',
                ]),
                Tab::make('Comment', [
                    $this->getResource()->getItem() ? $this->getCommentField() : 'To add comments, save the article',
                ]),
                Tab::make('Table', [
                    TableBuilder::make()
                        ->fields([
                            ID::make(),
                            Text::make('Text')
                        ])
                        ->cast(new ModelCaster(Comment::class))
                        ->items($this->getResource()->getItem()?->comments ?? [])
                ]),
            ]),
        ];
    }

    protected function bottomLayer(): array
    {
        return [];
    }
}
```

> [!NOTE]
> Метод `bottomLayer` обнулен, чтобы не дублировать поля отношений под основной формой

Дополнительно в этом рецепте мы добавили *TableBuilder*, где также выводим данные из отношения, тем самым мы хотим показать что вы не ограниченны только полями, 
а можете выводить дополнительно собственные таблицы и формы

> [!WARNING]
> При более сложной кастомизации страниц необходимо соблюдать правила:
> 1. Не создавать формы внутри форм, это приведет к конфликтам или потребует дополнительных действий (не связано с *MoonShine*),
> 2. Не забывайте наполнять поля данными и приводить к нужному типу.
