# Roles

Roles represent a named collection of permissions. They can be assigned globally or within a specific context (team, workspace, project, etc.).

## Role Model

The `Role` model has the following attributes:

| Attribute | Description |
|-----------|-------------|
| `name` | Human-readable name (e.g., "Administrator") |
| `slug` | URL-friendly identifier, auto-generated from name |
| 
| `description` | Optional description |
| `level` | Integer for hierarchical ordering (higher = more access) |

## Creating Roles

### Via Console

```bash
php artisan role:create "Administrator" --description="Full system access" --level=100
php artisan role:create "Editor" --description="Content editor" --level=50
```

### Programmatically

```php
use Whilesmart\Roles\Models\Role;

$role = Role::create([
    'name' => 'Moderator',
    'description' => 'Can moderate content',
    'level' => 30,
]);

// Access the slug
$role->slug; // "moderator"
```

## Role Hierarchy

The `level` attribute enables hierarchical permissions:

- Higher level roles inherit permissions from lower level roles
- A role with level 100 has all permissions of level 50, 10, etc.
- Use when you need tiered access (Admin > Manager > Editor > Viewer)

```php
// Check if role has higher or equal level
$admin->level >= $editor->level; // true
```

## Managing Role Permissions

```php
// Grant permissions to a role
$role->grantPermission('manage-users');
$role->grantPermission('edit-posts');

// Revoke permission
$role->revokePermission('edit-posts');

// Check if role has permission
$role->hasPermission('manage-users');
```

## Querying Roles

```php
// All roles
Role::all();

// Find by slug
Role::where('slug', 'administrator')->first();

// With permissions
Role::with('permissions')->get();
```