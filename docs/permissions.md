# Permissions

Permissions represent specific actions a user can perform. They can be granted directly to users or inherited through roles.

## Permission Model

| Attribute | Description |
|-----------|-------------|
| `name` | Human-readable name (e.g., "Manage Users") |
| `slug` | URL-friendly identifier, auto-generated from name |
| `description` | Optional description |

## Creating Permissions

### Via Console

```bash
php artisan permission:create "Manage Users" --description="Create, edit, and delete users"
php artisan permission:create "Edit Posts" --description="Create and edit blog posts"
```

### Programmatically

```php
use Whilesmart\Roles\Models\Permission;

$perm = Permission::create([
    'name' => 'Delete Posts',
    'slug' => 'delete-posts',
    'description' => 'Permanently delete blog posts',
]);
```

## Assigning Permissions

### Direct to User (via HasPermissions trait)

```php
// Grant
$user->grantPermission('manage-users');

// Revoke
$user->revokePermission('manage-users');

// Check
$user->hasPermission('manage-users');
```

### Through Roles (Recommended)

```php
$role = Role::where('slug', 'admin')->first();
$role->grantPermission('manage-users');
$role->grantPermission('edit-posts');

// User inherits via role assignment
$user->assignRole('admin');
$user->hasPermission('manage-users'); // true
```

## Contextual Permissions

Permissions can be scoped to a context (workspace, team, project):

```php
// Grant permission within a specific workspace
$user->grantPermission('manage-users', 'workspace', $workspaceId);

// Check within context
$user->hasPermission('manage-users', 'workspace', $workspaceId);

// User has permission globally OR in this context
```

## Checking Multiple Permissions

```php
// Check if user has ANY of the permissions
$user->hasAnyPermission(['manage-users', 'edit-posts']);

// Check if user has ALL permissions
$user->hasAllPermissions(['manage-users', 'edit-posts']);
```

## Permission Resolution Order

1. Direct user permission grant (highest priority)
2. Direct user permission denial
3. Role-based permission (inherited from assigned roles)
4. Default deny