# Команды

- [Установка](#install)
- [Пользователь](#user)
- [Ресурс](#resource)
- [Страница](#page)
- [Layout](#layout)
- [Компонент](#component)
- [Поле](#field)
- [Контроллер](#controller)
- [Обработчик](#handler)
- [Политика](#policy)
- [Приведение типов](#type_cast)
- [Публикация](#publish)
- [Apply](#apply)

---

> [!WARNING]
> Для выбора соответствующего пункта необходимо использовать клавишу `пробел`.

<a name="install"></a>
## Установка

Команда для установки пакета `MoonShine` в ваш проект `Laravel`:

```shell
php artisan moonshine:install
```

Доступные опции:

- `-u`, `--without-user` - без создания супер-пользователя,
- `-m`, `--without-migrations` - без выполнения миграций,
- `-l`, `--default-layout` - выбор шаблона по умолчанию (без вопроса про компактную тему),
- `-a`, `--without-auth` - без аутентификации,
- `-d`, `--without-notifications` - без уведомлений.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Установка](/docs/{{version}}/installation).

<a name="user"></a>
## Пользователь

Команда, позволяющая создать супер-пользователя:

```shell
php artisan moonshine:user
```

Доступные опции:

- `--u|username=` - логин/email пользователя,
- `--N|name=` - имя пользователя,
- `--p|password=` - пароль.

<a name="resource"></a>
## Ресурс

Команда для создания ресурсов:

```shell
php artisan moonshine:resource
```

Доступные опции:

- `--m|model=` - Eloquent модель для модельного ресурса,
- `--t|title=` - заголовок раздела,
- `--type=` - Быстрый выбор типа ресурса (1 - по умолчанию, 2 - со страницами, 3 - пустой),
- `--p|policy` - Также создать Policy,
- `--test` или `--pest` - дополнительно сгенерировать тестовый класс,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

При создании `Resource` доступно несколько вариантов:

- **[Модельный ресурс по умолчанию](/docs/{{version}}/model-resource/fields)** - модельный ресурс по умолчанию,
- **[Модельный ресурс со страницами](/docs/{{version}}/model-resource/pages)** - модельный ресурс со страницами,
- **Пустой ресурс** - пустой ресурс для кастомных реализаций.

После выполнения команды в директории `app/MoonShine/Resources/` будет создан файл ресурса.
Если создается модельный ресурс со страницами, в директории `app/MoonShine/Pages` будут созданы дополнительные страницы.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Модельные ресурсы](/docs/{{version}}/model-resource/index).

<a name="page"></a>
## Страница

Команда создает страницу для админ-панели:

```shell
php artisan moonshine:page
```

- `--crud` - создает группу страниц: индексную, детальную и форму,
- `--without-register` - без автоматической регистрации в провайдере,
- `--dir=` - директория, в которой будут располагаться файлы относительно `app/MoonShine`, по умолчанию Page,
- `--extends=` - класс, который будет расширять страница, например IndexPage, FormPage или DetailPage,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

После выполнения команды в директории `app/MoonShine/Pages` будет создана страница по умолчанию (или группа страниц).

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Страница](https:///docs/{{version}}/page/index).

<a name="layout"></a>
## Layout

Команда создает шаблон для админ-панели:

```shell
php artisan moonshine:layout
```

- `--compact` - наследует компактную тему,
- `--full` - наследует базовую тему,
- `--default` - установить в конфиге как шаблон по умолчанию
- `--dir=` - директория, в которой будут располагаться файлы относительно `app/MoonShine`, по умолчанию `Layouts`,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

После выполнения команды в директории `app/MoonShine/Layouts` будет создан шаблон.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Layout](https:///docs/{{version}}/page/index).

<a name="component"></a>
## Компонент

Команда создает пользовательский компонент:

```shell
php artisan moonshine:component
```

После выполнения команды в директории `app/MoonShine/Components` будет создан класс для компонента, а в директории `resources/views/admin/components` - файл `Blade`.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Layout](/docs/{{version}}/appearance/layout).

<a name="field"></a>
## Поле

Команда позволяет создать пользовательское поле:

```shell
php artisan moonshine:field
```

При выполнении команды можно указать, будет ли поле расширять базовый класс или другое поле.

После выполнения команды в директории `app/MoonShine/Fields` будет создан класс поля, а в директории `/resources/views/admin/fields` - файл `Blade`.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Поле](/docs/{{version}}/fields/index).

<a name="controller"></a>
# Контроллер

Команда для создания контроллера:

```shell
php artisan moonshine:controller
```

После выполнения команды в директории `app/MoonShine/Controllers` будет создан класс контроллера, который можно использовать в маршрутах админ-панели.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Контроллеры](/docs/{{version}}/advanced/controllers).

<a name="handler"></a>
## Обработчик

Команда создает класс `Handler`:

```shell
php artisan moonshine:handler
```

После выполнения команды в директории `app/MoonShine/Handlers` будет создан класс обработчика.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Handlers](/docs/{{version}}/advanced/handlers).

<a name="policy"></a>
## Политика

Команда создает `Policy`, привязанную к пользователю админ-панели:

```shell
php artisan moonshine:policy
```

После выполнения команды в директории `app/Policies` будет создан класс.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Авторизация](/docs/{{version}}/security/authorization).

<a name="type_cast"></a>
## Приведение типов

Команда создает класс `TypeCast` для работы с данными:

```shell
php artisan moonshine:type-cast
```

После выполнения команды в директории `app/MoonShine/TypeCasts` будет создан файл.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [TypeCasts](/docs/{{version}}/advanced/type-casts).

<a name="publish"></a>
## Публикация

Команда для публикации:

```shell
php artisan moonshine:publish
```

Для публикации доступно несколько вариантов:

- **Assets** - ассеты админ-панели `MoonShine`,
- **Assets template** - создает шаблон для добавления собственных стилей или создания собственной темы для `MoonShine`,
- **System Resources** - системные `MoonShineUserResource`, `MoonShineUserRoleResource`, которые вы можете изменить,
- **System Forms** - системные `LoginForm`, `FiltersForm`, которые вы можете изменить,
- **System Pages** - системные `ProfilePage`, `LoginPage`, `ErrorPage`, которые вы можете изменить.

#### Вы можете сразу указать тип публикации в команде.

```shell
php artisan moonshine:publish assets
```

Доступные типы:
- assets
- assets-template
- resources
- forms
- pages

<a name="apply"></a>
## Apply

Команда для создания класса apply:

```shell
php artisan moonshine:apply
```

После выполнения команды в директории `app/MoonShine/Applies` будет создан файл. Созданный класс необходимо зарегистрировать в сервис-провайдере.
