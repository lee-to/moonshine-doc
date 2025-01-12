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

Сигнатура:
```
moonshine:install {--u|without-user} {--m|without-migrations} {--l|default-layout} {--a|without-auth} {--d|without-notifications} {--t|tests-mode}
```

Доступные опции:

- `--u|without-user` - без создания супер-пользователя,
- `--m|without-migrations` - без выполнения миграций,
- `--l|default-layout` - выбор шаблона по умолчанию (без вопроса про компактную тему),
- `--a|without-auth` - без аутентификации,
- `--d|without-notifications` - без уведомлений,
- `--t|tests-mode` - тестовый режим.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Установка](/docs/{{version}}/installation).

<a name="user"></a>
## Пользователь

Команда для создания супер-пользователя:

```shell
php artisan moonshine:user
```

Сигнатура:
```
moonshine:user {--u|username=} {--N|name=} {--p|password=}
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

Сигнатура:
```
moonshine:resource {className?} {--type=} {--m|model=} {--t|title=} {--test} {--pest} {--p|policy} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--m|model=` - eloquent модель для модельного ресурса,
- `--t|title=` - заголовок раздела,
- `--type=` - быстрый выбор типа ресурса (1 - по умолчанию, 2 - со страницами, 3 - пустой),
- `--p|policy` - также создать Policy,
- `--test` или `--pest` - дополнительно сгенерировать тестовый класс,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

При создании ресурса доступно несколько вариантов:

- [Default model resource](/docs/{{version}}/model-resource/fields) - модельный ресурс по умолчанию с объявлением полей в методах `indexFields`, `formFields` и `detailFields`,
- [Model resource with pages](/docs/{{version}}/model-resource/pages) - модельный ресурс c публикацией страниц `IndexPage`, `FormPage` и `DetailPage`,
- **Empty resource** - пустой ресурс для кастомных реализаций.

После выполнения команды в директории `app/MoonShine/Resources/` будет создан файл ресурса.
Если создается модельный ресурс со страницами, то в директории `app/MoonShine/Pages` будут созданы дополнительные страницы.

Примеры:
```shell
php artisan moonshine:resource Post --model=CustomPost --title="Articles"

php artisan moonshine:resource Post --model="App\Models\CustomPost"
```

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Модельные ресурсы](/docs/{{version}}/model-resource/index).

<a name="page"></a>
## Страница

Команда для создания страниц:

```shell
php artisan moonshine:page
```

Сигнатура:
```
moonshine:page {className?} {--force} {--without-register} {--crud} {--dir=} {--extends=} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--force` - не спрашивать тип страницы,
- `--without-register` - без автоматической регистрации в провайдере,
- `--crud` - создает группу страниц: индексную, детальную и форму,
- `--dir=` - директория, в которой будут располагаться файлы относительно `app/MoonShine`, по умолчанию Page,
- `--extends=` - класс, который будет расширять страница, например IndexPage, FormPage или DetailPage,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

После выполнения команды в директории `app/MoonShine/Pages` будет создана страница по умолчанию (или группа страниц).

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Страница](/docs/{{version}}/page/index).

<a name="layout"></a>
## Layout

Команда для создания layout'а:

```shell
php artisan moonshine:layout
```

Сигнатура:
```
moonshine:layout {className?} {--compact} {--full} {--default} {--dir=} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--compact` - наследует компактную тему,
- `--full` - наследует базовую тему,
- `--default` - установить в конфиге как шаблон по умолчанию,
- `--dir=` - директория, в которой будут располагаться файлы относительно `app/MoonShine`, по умолчанию `Layouts`,
- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Layout](/docs/{{version}}/appearance/layout).

<a name="component"></a>
## Компонент

Команда для создания пользовательского компонента:

```shell
php artisan moonshine:component
```

Сигнатура:
```
moonshine:component {className?} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

После выполнения команды в директории `app/MoonShine/Components` будет создан класс для компонента, а в директории `resources/views/admin/components` - файл `Blade`.

> [!NOTE]
> Для более подробной информации обратитесь к разделу [Layout](/docs/{{version}}/appearance/layout).

<a name="field"></a>
## Поле

Команда для создания пользовательского поля:

```shell
php artisan moonshine:field
```

Сигнатура:
```
moonshine:field {className?} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

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

Сигнатура:
```
moonshine:controller {className?} {--base-dir=} {--base-namespace=}
```

Доступные опции:

- `--base-dir=, --base-namespace=` - изменить базовую директорию и неймспейс класса.

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
