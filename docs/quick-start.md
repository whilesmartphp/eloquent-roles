# Quick Start

## 1. Add Traits to Your Model

```php
use Whilesmart\Roles\Traits\HasRoles;
use Whilesmart\Roles\Traits\HasPermissions;

class User extends Model
{
    use HasRoles, HasPermissions;
}
```

## 2. Create Roles and Permissions

Use the console commands:

```bash
# Create a role
php artisan role:create "Administrator" --description="Full access" --level=100

# Create a permission
php artisan permission:create "manage-users" --description="Manage user accounts"
```

Or create via seeder:

```php
use Whilesmart\Roles\Models\Role;
use Whilesmart\Roles\Models\Permission;

$admin = Role::create(['name' => 'Administrator', 'level' => 100]);
$editor = Role::create(['name' => 'Editor', 'level' => 50]);

$manageUsers = Permission::create(['name' => 'Manage Users', 'slug' => 'manage-users']);
$editPosts = Permission::create(['name' => 'Edit Posts', 'slug' => 'edit-posts']);

$admin->grantPermission('manage-users');
$editor->grantPermission('edit-posts');
```

## 3. Assign Roles

```php
// Global role (no context)
$user->assignRole('administrator');

// Contextual role (e.g., per workspace/team)
$user->assignRole('manager', 'workspace', $workspaceId);
```

## 4. Check Roles and Permissions

```php
// Global checks
$user->hasRole('administrator');
$user->hasPermission('manage-users');

// Contextual checks
$user->hasRole('manager', 'workspace', $workspaceId);
$user->hasPermission('manage-users', 'workspace', $workspaceId);

// Multiple roles
$user->hasAnyRole(['administrator', 'manager']);
```

## 5. Use Middleware

```php
// routes/web.php
Route::middleware(['role:administrator'])->group(function () {
    // Admin-only routes
});

Route::middleware(['permission:manage-users'])->group(function () {
    // User management routes
});
```