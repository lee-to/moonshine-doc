# Несколько селекторов и фрагментов

Пример сразу нескольких селекторов:

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

Пример асинхронного обновления по селекторам из нескольких фрагментов:

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