Lang: _en_ [ru](README.ru.md)

# Typography

- [Title](#title)
- [Navigation](#navigations)
- [Divider](#divider)
- [Subtitle](#subtitle)
- [Content](#content)
- [Code Example](#code)
- [Lists](#list)
- [Tabs](#tabs)
- [Alerts](#alert)
- [Images](#images)

___

<a name="title"></a>
## Title

The section title is the first and mandatory element of the page.

```html
# Title
```

<a name="navigations"></a>
## Navigation

If the section is large, it should be divided into subsections and a navigation menu should be created.

The navigation menu is a list with links to the subsection. The subsection headings should have an anchor specified.

```html
- [Subtitle 1](#subtitle-1)
- [Subtitle 2](#subtitle-2)
```

> [!NOTE]
> `Kebab-case` is used to separate words in links.

<a name="divider"></a>
## Divider

After navigation (content), a divider should be specified.

```
---
```

<a name="subtitle"></a>
## Subtitle

Subsection headings are specified with a link for easy copying of the link to a specific section of the documentation.

```html
## Subtitle
```

If [Navigation](#navigations) is used, an anchor should be added before the heading:

```html
<a name="anchor"></a>
## Subtitle
```

For the name of the first item, it is often necessary to use the name `Basics`, instead of similar `Start`, `Introduction`, etc.
```html
<a name="basics"></a>
## Basics
```

If a component is described that inherits from another class, and there is a `Basics` item in the navigation, then the description of inheritance is written strictly after this item.
```html
<a name="basics"></a>
## Basics

Inherits from [Select](/docs/{version}/fields/select).

\* has the same capabilities.

```

If the basic methods are described in another section of the documentation, then write it like this

```html
<a name="basics"></a>
## Basics

Contains all [Basic methods](#/docs/{version}/fields/basic-methods.md).
```

<a name="content"></a>
## Content

In addition to `markdown` tags, `html-tags` are allowed.

> [!WARNING]
> All sentences should end with a period.

It is desirable to synchronize the texts in the **ru** and **en** versions of the sections line by line.

<a name="code"></a>
## Code Example

- single apostrophe ``` ` ``` is used to format methods, classes, etc.,
- method names should end with parentheses, for example: `setLabel()`,
- triple apostrophes ` ``` ` with the programming language specified are used to format code blocks, and the block should start on a new line,
- for all classes used in examples, you need to specify use in alphabetical order and wrap them in collapse.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\UI\Fields\Text;

Text::make('Title')
```
or
```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:start]
use MoonShine\UI\Fields\Text; // [tl! collapse:end]

Text::make('Title')
```

<a name="list"></a>
## Lists

```html
- list items end with a comma,
- a period is placed after the last one.
```

<a name="tabs"></a>
## Tabs

```
~~~tabs

tab: Tab 1
Content tab 1

tab: Tab 2
Content tab 2

~~~
```

<a name="alert"></a>
## Alerts

The documentation uses several types of alerts:

```
> [!NOTE]
> Simple notification.
```

```
> [!WARNING]
> Warning.
```

```
> [!TIP]
> Tips.
```

<a name="images"></a>
## Images

Images are added to the `/resources/screenshots` directory.

The link is specified - https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/filename.png

Example:

![belongs_to_many](https://raw.githubusercontent.com/moonshine-software/doc/3.x/resources/screenshots/belongs_to_many.png)
