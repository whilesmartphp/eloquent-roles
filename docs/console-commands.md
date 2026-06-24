# Console Commands

The package provides Artisan commands for managing roles and permissions.

## Role Commands

### Create Role

```bash
php artisan role:create "Administrator"
php artisan role:create "Editor" --description="Content editor" --level=50
```

| Argument/Option | Description |
|-----------------|-------------|
| `name` | Role name (required) |
| `--description` | Optional description |
| `--level` | Hierarchical level (default: 0) |

### List Roles

```bash
php artisan role:list
```

Outputs table with name, slug, description, level, and permission count.

## Permission Commands

### Create Permission

```bash
php artisan permission:create "Manage Users"
php artisan permission:create "Edit Posts" --description="Create and edit posts"
```

| Argument/Option | Description |
|-----------------|-------------|
| `name` | Permission name (required) |
| `--description` | Optional description |

### List Permissions

```bash
php artisan permission:list
```

Outputs table with name, slug, description, and role count.

## Assignment Commands

### Assign Role

```bash
# Global
php artisan role:assign user 1 administrator

# Contextual
php artisan role:assign user 1 manager --context-type=workspace --context-id=5
```

| Argument/Option | Description |
|-----------------|-------------|
| `model` | Model type (user, team, etc.) |
| `id` | Model ID |
| `role` | Role slug |
| `--context-type` | Context model class |
| `--context-id` | Context model ID |

### Remove Role

```bash
php artisan role:remove user 1 administrator
php artisan role:remove user 1 manager --context-type=workspace --context-id=5
```

### Grant Permission

```bash
# Direct to user
php artisan permission:grant user 1 manage-users

# Contextual
php artisan permission:grant user 1 manage-users --context-type=workspace --context-id=5
```

### Revoke Permission

```bash
php artisan permission:revoke user 1 manage-users
```

## Seeder Command

Seed default roles and permissions:

```bash
php artisan roles:seed
```

This runs `Whilesmart\Roles\Seeders\RolesAndPermissionsSeeder` which creates:
- Roles: Administrator (100), Manager (50), Editor (30), Viewer (10)
- Permissions: manage-users, edit-posts, delete-posts, publish-posts, view-reports
- Role-permission mappings

Customize the seeder at `database/seeders/RolesAndPermissionsSeeder.php`.