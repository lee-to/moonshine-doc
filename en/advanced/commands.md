# Commands

- [Installation](#install)
- [User](#user)
- [Resource](#resource)
- [Page](#page)
- [Layout](#layout)
- [Component](#component)
- [Field](#field)
- [Controller](#controller)
- [Handler](#handler)
- [Policy](#policy)
- [Type Casting](#type_cast)
- [Publishing](#publish)
- [Apply](#apply)

---

> [!WARNING]
> Use the `space` key to select the appropriate item.

<a name="install"></a>
## Installation

Command to install the `MoonShine` package in your `Laravel` project:

```shell
php artisan moonshine:install
```

Signature:
```
moonshine:install {--u|without-user} {--m|without-migrations} {--l|default-layout} {--a|without-auth} {--d|without-notifications} {--t|tests-mode}
```

Available options:

- `--u|without-user` - without creating a superuser,
- `--m|without-migrations` - without running migrations,
- `--l|default-layout` - select the default template (without prompting for a compact theme),
- `--a|without-auth` - without authentication,
- `--d|without-notifications` - without notifications,
- `--t|tests-mode` - test mode.

> [!NOTE]
> For more details, refer to the [Installation](/docs/{{version}}/installation) section.

<a name="user"></a>
## User

Command to create a superuser:

```shell
php artisan moonshine:user
```

Signature:
```
moonshine:user {--u|username=} {--N|name=} {--p|password=}
```

Available options:

- `--u|username=` - user login/email,
- `--N|name=` - user name,
- `--p|password=` - password.

<a name="resource"></a>
## Resource

Command to create resources:

```shell
php artisan moonshine:resource
```

Signature:
```
moonshine:resource {className?} {--type=} {--m|model=} {--t|title=} {--test} {--pest} {--p|policy} {--base-dir=} {--base-namespace=}
```

Available options:

- `--m|model=` - eloquent model for the resource model,
- `--t|title=` - section title,
- `--type=` - quick selection of resource type (1 - default, 2 - with pages, 3 - empty),
- `--p|policy` - also create Policy,
- `--test` or `--pest` - additionally generate a test class,
- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

When creating a resource, several options are available:

- [Default model resource](/docs/{{version}}/model-resource/fields) - default model resource with the declaration of fields in the methods `indexFields()`, `formFields()` and `detailFields()`,
- [Model resource with pages](/docs/{{version}}/model-resource/pages) - model resource with the publication of the pages `IndexPage`, `FormPage` and `DetailPage`,
- **Empty resource** - empty resource for custom implementations.

After executing the command, a resource file will be created in the `app/MoonShine/Resources` directory.
If a model resource with pages is created, additional pages will be created in the `app/MoonShine/Pages` directory.

Examples:
```shell
php artisan moonshine:resource Post --model=CustomPost --title="Articles"

php artisan moonshine:resource Post --model="App\Models\CustomPost"
```

> [!NOTE]
> For more details, refer to the [Model Resources](/docs/{{version}}/model-resource/index) section.

<a name="page"></a>
## Page

Command to create pages:

```shell
php artisan moonshine:page
```

Signature:
```
moonshine:page {className?} {--force} {--without-register} {--crud} {--dir=} {--extends=} {--base-dir=} {--base-namespace=}
```

Available options:

- `--force` - don't ask for the page type,
- `--without-register` - without automatic registration in the provider,
- `--crud` - creates a group of pages: index, detail, and form,
- `--dir=` - directory where the files will be located relative to `app/MoonShine`, defaults to Page,
- `--extends=` - class that the page will extend, e.g., IndexPage, FormPage, or DetailPage,
- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a default page (or group of pages) will be created in the `app/MoonShine/Pages` directory.

> [!NOTE]
> For more details, refer to the [Page](/docs/{{version}}/page/index) section.

<a name="layout"></a>
## Layout

Command to create a layout:

```shell
php artisan moonshine:layout
```

Signature:
```
moonshine:layout {className?} {--compact} {--full} {--default} {--dir=} {--base-dir=} {--base-namespace=}
```

Available options:

- `--compact` - inherits the compact theme,
- `--full` - inherits the base theme,
- `--default` - set as the default template in the config,
- `--dir=` - directory where the files will be located relative to `app/MoonShine`, defaults to `Layouts`,
- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

> [!NOTE]
> For more details, refer to the [Layout](/docs/{{version}}/appearance/layout) section.

<a name="component"></a>
## Component

Command to create a custom component:

```shell
php artisan moonshine:component
```

Signature:
```
moonshine:component {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a class for the component will be created in the `app/MoonShine/Components` directory, and a `Blade` file will be created in the `resources/views/admin/components` directory.

> [!NOTE]
> For more details, refer to the [Layout](/docs/{{version}}/appearance/layout) section.

<a name="field"></a>
## Field

Command to create a custom field:

```shell
php artisan moonshine:field
```

Signature:
```
moonshine:field {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

When executing the command, you can specify whether the field will extend the base class or another field.

After executing the command, a field class will be created in the `app/MoonShine/Fields` directory, and a `Blade` file will be created in the `/resources/views/admin/fields` directory.

> [!NOTE]
> For more details, refer to the [Field](/docs/{{version}}/fields/index) section.

<a name="controller"></a>
# Controller

Command to create a controller:

```shell
php artisan moonshine:controller
```

Signature:
```
moonshine:controller {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a controller class will be created in the `app/MoonShine/Controllers` directory that can be used in the admin panel routes.

> [!NOTE]
> For more details, refer to the [Controllers](/docs/{{version}}/advanced/controllers) section.

<a name="handler"></a>
## Handler

Command to create a `Handler` class:

```shell
php artisan moonshine:handler
```

Signature:
```
moonshine:handler {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a handler class will be created in the `app/MoonShine/Handlers` directory.

> [!NOTE]
> For more details, refer to the [Handlers](/docs/{{version}}/advanced/handlers) section.

<a name="policy"></a>
## Policy

Command to create a `Policy` class tied to the admin panel user:

```shell
php artisan moonshine:policy
```

After executing the command, a class will be created in the `app/Policies` directory.

> [!NOTE]
> For more details, refer to the [Security > Authorization](/docs/{{version}}/security/authorization) section.

<a name="type_cast"></a>
## Type Casting

Command to create a `TypeCast` class for working with data:

```shell
php artisan moonshine:type-cast
```

Signature:
```
moonshine:type-cast {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a file will be created in the `app/MoonShine/TypeCasts` directory.

> [!NOTE]
> For more details, refer to the [TypeCasts](/docs/{{version}}/advanced/type-casts) section.

<a name="publish"></a>
## Publishing

Command for publishing:

```shell
php artisan moonshine:publish
```

Several options are available for publishing:

- **Assets** - assets for the `MoonShine` admin panel,
- **Assets template** - creates a template for adding custom styles or creating a custom theme for `MoonShine`,
- **System Resources** - system `MoonShineUserResource`, `MoonShineUserRoleResource`, which you can modify,
- **System Forms** - system `LoginForm`, `FiltersForm`, which you can modify,
- **System Pages** - system `ProfilePage`, `LoginPage`, `ErrorPage`, which you can modify.

#### You can specify the publication type directly in the command.

```shell
php artisan moonshine:publish assets
```

Available types:
- assets
- assets-template
- resources
- forms
- pages

<a name="apply"></a>
## Apply

Command to create an apply class:

```shell
php artisan moonshine:apply
```

Signature:
```
moonshine:apply {className?} {--base-dir=} {--base-namespace=}
```

Available options:

- `--base-dir=, --base-namespace=` - change the base directory and namespace of the class.

After executing the command, a file will be created in the `app/MoonShine/Applies` directory.
The created class needs to be registered in the service provider.

> [!NOTE]
> For more details, refer to the [Fields](/docs/{{version}}/fields/basic-methods#apply) section.