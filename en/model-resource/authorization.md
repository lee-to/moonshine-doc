# Authorization

- [Policy](#policy)
- [Custom Logic](#is-can)

---

<a name="policy"></a>
## Policy

> [!NOTE]
> For more information about using policies in MoonShine, see [Security > Authorization](/docs/{{version}}/security/authorization) section.

<a name="is-can"></a>
## Custom Logic

You can also override the `isCan()` method in the resource and implement your own logic or supplement the current one.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Laravel\Enums\Ability;

protected function isCan(Ability $ability): bool
{
    return parent::isCan($ability);
}
```
