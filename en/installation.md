# Installation

- [Requirements](#requirements)
- [Installation via composer](#composer)
- [Panel installation](#install)

---

<a name="requirements"></a>
## Requirements

To work with MoonShine, the following requirements must be met before installation:

- PHP 8.2+
- Laravel 10.48+
- Composer 2+

<a name="composer"></a>
## Installation via Composer

```shell
composer require moonshine/moonshine
```

<a name="install"></a>
## Panel installation

```shell
php artisan moonshine:install
```
> [!TIP]
> Perform the installation only once at the start. After installation, everything can be configured through [configuration](/docs/{{version}}/configuration).

During the installation process, you will be asked to perform:

1. *Authentication*. Enable/disable the `middleware` that checks whether the user has access to the panel.
2. *Migrations*. Necessary if you choose to use the built-in capabilities of `MoonShine` for managing users and roles.
3. *Notifications*. Enable/disable the notification system, and you will also be asked whether to use the database driver for storing notifications in the database.
4. *Template theme*. Standard or compact.
5. *Superuser*. If you chose the migration option, you will be prompted to create a superuser who will gain access to the admin panel with the credentials specified during installation.
6. *Don't forget to star the GitHub repository. Thank you!*

During the installation, the following will be added and executed:

- `php artisan storage:link`
- `app/Providers/MoonShineServiceProvider.php`, and the provider will be added to `bootstrap/providers.php`
- `app/MoonShine`
- `config/moonshine.php`
- `lang/vendor/moonshine`
- `public/vendor/moonshine`
- `app/MoonShine/Pages/Dashboard.php`
- `app/MoonShine/Layouts/MoonShineLayout.php`

After installation, the project will have the following structure:

- `app/MoonShine` — the main directory with resources, pages, and page templates.
    - `app/MoonShine/Pages` — the core of MoonShine consists of pages. Each route in the admin panel renders a page with a set of components. If several pages are combined by a common task, they can be grouped into resources.
    - `app/MoonShine/Resources` — resources are used for logical grouping of pages. Regarding `ModelResource` (CrudResource), such resources immediately include full functionality for CRUD operations and all necessary pages for creating, editing, viewing, and listing records.
    - `app/MoonShine/Layouts/MoonShineLayout.php` — the main template for all pages. Here you can change the structure of the components, appearance, and menu. You can create any number of templates and choose the one you need for each page.
- `app/Providers/MoonShineServiceProvider.php` — this provider registers resources and pages and also specifies global settings. The panel can be configured both through a convenient object in the provider and through the `config/moonshine.php` file.
- `config/moonshine.php` — a file with the main settings of MoonShine. You can leave only the modified keys in it or delete the file altogether, configuring everything through `MoonShineServiceProvider`.

Now everything is ready to use and create your admin panel. You can access it at `/admin`.

We recommend following the documentation step by step to gain a deeper understanding of the concept. The next section is **Configuration**, where you will also find answers on how to proceed if you chose the path of custom authentication implementation and user entities.
