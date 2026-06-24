# Contextual Roles

Contextual roles allow the same user to have different roles in different contexts (workspaces, teams, projects, etc.).

## What Are Contexts?

A context is any model that scopes role assignments. Examples:
- `Workspace` / `Team` / `Organization`
- `Project`
- `Tenant` (multi-tenancy)
- Any Eloquent model

## Assigning Contextual Roles

```php
use App\Models\Workspace;

$workspace = Workspace::find(1);

// Assign 'manager' role in this workspace only
$user->assignRole('manager', 'workspace', $workspace->id);

// Or pass the model directly
$user->assignRole('manager', $workspace);
```

## Checking Contextual Roles

```php
// Check in specific workspace
$user->hasRole('manager', 'workspace', $workspace->id);

// Check with model
$user->hasRole('manager', $workspace);

// Get all roles in a context
$roles = $user->getRolesInContext('workspace', $workspace->id);
// Returns ['manager', 'editor', ...]
```

## Global vs Contextual

| Type | Use Case |
|------|----------|
| Global | System-wide roles (super-admin, support) |
| Contextual | Per-tenant, per-team, per-project access |

```php
// Global (no context)
$user->assignRole('super-admin');
$user->hasRole('super-admin'); // true everywhere

// Contextual
$user->assignRole('admin', 'workspace', 1);
$user->hasRole('admin'); // false globally
$user->hasRole('admin', 'workspace', 1); // true
```

## Multiple Contexts

A user can have different roles in different workspaces:

```php
$user->assignRole('owner', 'workspace', 1);
$user->assignRole('member', 'workspace', 2);
$user->assignRole('viewer', 'project', 5);
```

## Context in Abilities

```php
// Grant ability within a context
$user->grantAbility('post:edit', $post, 'workspace', $workspace->id);

// Check
$user->hasAbility('post:edit', $post, 'workspace', $workspace->id);
```

## Middleware with Context

```php
Route::middleware(['role:manager:workspace'])->group(function () {
    // Requires 'manager' role in current workspace
});
```

The middleware extracts context from the request (route parameter, session, etc.).

## Database Schema

The `role_assignments` table stores context:

```sql
role_assignments
- id
- assignable_type  -- e.g., "App\Models\User"
- assignable_id    -- e.g., 1
- role_id          -- FK to roles
- context_type     -- e.g., "App\Models\Workspace"
- context_id       -- e.g., 5
```

## Querying Contextual Assignments

```php
// All assignments for a user in a workspace
$assignments = $user->roleAssignments()
    ->where('context_type', 'App\Models\Workspace')
    ->where('context_id', $workspace->id)
    ->with('role')
    ->get();
```