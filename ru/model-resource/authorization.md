# Авторизация

- [Policy](#policy)
- [Собственная логика](#is-can)

---

<a name="policy"></a>
## Policy

> [!NOTE]
> Подробнее об использовании политик в MoonShine читайте в разделе [Безопасность > Авторизация](/docs/{{version}}/security/authorization).

<a name="is-can"></a>
## Собственная логика

Также вы можете переопределить метод `isCan()` в ресурсе и реализовать собственную логику или дополнить текущую.

```php
// torchlight! {"summaryCollapsedIndicator": "namespaces"}
// [tl! collapse:1]
use MoonShine\Laravel\Enums\Ability;

protected function isCan(Ability $ability): bool
{
    return parent::isCan($ability);
}
```
