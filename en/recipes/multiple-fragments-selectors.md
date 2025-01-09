# Multiple selectors and fragments

An example of several selectors at once:

```php
public function multipleSelectors(): MoonShineJsonResponse
{
    return MoonShineJsonResponse::make()->html([
        '.selector1' => 'here 1',
        '.selector2' => 'here 2',
    ]);
}

protected function components(): iterable
{
    return [
        ActionButton::make('Test')->method('multipleSelectors', selector: [
            '.selector1',
            '.selector2',
        ]),
        
        Div::make([])->class('selector1'),
        Div::make([])->class('selector2'),
    ];
}
```

An example of asynchronous updating using selectors from several fragments:

```php
ActionButton::make('Fragments', $this->getRouter()->getEndpoints()->toPage($this, extra: [
    'fragment' => [
        '.selector1' => '_content1',
        '.selector2' => '_content2',
    ]
]))->async(selector: [
    '.selector1',
    '.selector2',
]),

Div::make([])->class('selector1'),
Div::make([])->class('selector2'),

Fragment::make([
    time(),
])->name('_content1'),

Fragment::make([
    time(),
])->name('_content2'),
```