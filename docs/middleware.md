# Middleware

Two middleware are provided for route protection: `role` and `permission`.

## Register Middleware

In `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'role' => \Whilesmart\Roles\Middleware\RequireRole::class,
    'permission' => \Whilesmart\Roles\Middleware\RequirePermission::class,
];
```

## Role Middleware

```php
// Single role (global)
Route::middleware('role:administrator')->group(...);

// Single role (contextual)
Route::middleware('role:manager:workspace')->group(...);

// Multiple roles (any match)
Route::middleware('role:administrator|manager')->group(...);
```

### Context Resolution

The middleware resolves context automatically:

1. Route parameter named `{workspace}`, `{team}`, `{project}`, or `{context}`
2. Session key `current_workspace_id` / `current_context_id`
3. Request header `X-Context-ID`

```php
// Route with context parameter
Route::middleware('role:manager:workspace')
    ->get('/workspaces/{workspace}/settings', fn() => ...);
```

## Permission Middleware

```php
// Single permission
Route::middleware('permission:manage-users')->group(...);

// Multiple permissions (any match)
Route::middleware('permission:manage-users|edit-posts')->group(...);

// Contextual permission
Route::middleware('permission:manage-users:workspace')->group(...);
```

## Combining Middleware

```php
// Require role AND permission
Route::middleware(['role:admin', 'permission:manage-users'])->group(...);

// Require role OR permission
Route::middleware(['role:admin|manager'])->group(...); // Has admin OR manager role
```

## Custom Context Resolver

Override in a service provider:

```php
use Whilesmart\Roles\Middleware\RequireRole;

RequireRole::resolveContextUsing(function ($request, $contextType) {
    if ($contextType === 'workspace') {
        return $request->user()?->currentWorkspace?->id;
    }
    return null;
});
```

## Redirect on Failure

By default returns 403. Customize in middleware:

```php
// In RequireRole::handle()
if (! $user->hasRole($role, $contextType, $contextId)) {
    return response()->json(['error' => 'Insufficient role'], 403);
    // or redirect:
    // return redirect()->route('dashboard')->with('error', 'Access denied');
}
```

## API Usage

```php
// For API routes, returns JSON 403
Route::middleware(['permission:manage-users'])->group(function () {
    Route::apiResource('users', UserController::class);
});
```