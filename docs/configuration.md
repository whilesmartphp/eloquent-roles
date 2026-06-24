# Configuration

## Publish Config

```bash
php artisan vendor:publish --tag=roles-config
```

Creates `config/roles.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Use UUIDs
    |--------------------------------------------------------------------------
    |
    | If your application uses UUIDs for the User model (or other models
    | using roles), set this to true.
    |
    */
    'use_uuids' => false,
];
```

## UUID Support

If your models use UUID primary keys:

1. Set `use_uuids => true` in config
2. Run migrations (they'll use UUID columns for foreign keys)

```php
// config/roles.php
'use_uuids' => true,
```

## Custom Models

Override default models by extending and binding in a service provider:

```php
use Whilesmart\Roles\Models\Role as BaseRole;
use Whilesmart\Roles\Models\Permission as BasePermission;

class Role extends BaseRole
{
    // Custom logic
}

class Permission extends BasePermission
{
    // Custom logic
}

// In AppServiceProvider
public function boot(): void
{
    $this->app->concording(BaseRole::class, Role::class);
    $this->app->concording(BasePermission::class, Permission::class);
}
```

## Table Names

Default table names (customizable via model):

| Table | Model |
|-------|-------|
| `roles` | `Whilesmart\Roles\Models\Role` |
| `permissions` | `Whilesmart\Roles\Models\Permission` |
| `role_permissions` | Pivot (auto) |
| `role_assignments` | `Whilesmart\Roles\Models\RoleAssignment` |
| `abilities` | `Whilesmart\Roles\Models\Ability` |

Override in your model:

```php
class Role extends BaseRole
{
    protected $table = 'custom_roles';
}
```

## Auto-Discovery

The service provider is auto-discovered via `composer.json` extra:

```json
"extra": {
    "laravel": {
        "providers": [
            "Whilesmart\\Roles\\RolesServiceProvider"
        ]
    }
}
```

To disable, add to `config/app.php`:

```php
'providers' => [
    // ...
    // \Whilesmart\Roles\RolesServiceProvider::class, // comment out
],
```

Then register manually in your way you need custom boot logic.