# Abilities & Conditions

Abilities provide fine-grained authorization beyond simple role/permission checks. They support:
- Subject-specific grants (e.g., "edit this specific post")
- Conditional logic (e.g., "only if user owns the post")
- Actions that don't map to predefined permissions

## Ability Model

| Attribute | Description |
|-----------|-------------|
| `action` | Action string (e.g., `post:edit`, `permission:manage-users`) |
| `subject_type` | Optional model class (e.g., `App\Models\Post`) |
| `subject_id` | Optional model ID |
| `context_type` | Optional context type |
| `context_id` | Optional context ID |
| `allowed` | Boolean — grant (`true`) or deny (`false`) |
| `conditions` | Array of condition rules |

## Granting Abilities

### Simple Action

```php
// Grant ability to edit any post
$user->grantAbility('post:edit');

// Check
$user->hasAbility('post:edit'); // true
```

### Subject-Specific

```php
// Grant edit on a specific post
$user->grantAbility('post:edit', $post);

// Check against that post
$user->hasAbility('post:edit', $post); // true
```

### With Conditions

```php
// Grant edit only if user owns the post
$user->grantAbility('post:edit', $post, null, null, [
    'owner' => true,
]);

// Check evaluates conditions
$user->hasAbility('post:edit', $post); // true only if $post->owner_id === $user->id
```

### With Context

```php
// Grant in specific workspace
$user->grantAbility('post:edit', $post, 'workspace', $workspaceId);
```

## Built-in Conditions

| Condition | Description |
|-----------|-------------|
| `owner` | Subject's `owner_id` matches user's ID |
| `status` | Subject's `status` equals value |

```php
// Using status condition
$user->grantAbility('post:publish', $post, null, null, [
    'status' => 'draft',
]);
// Only allows publish if post status is 'draft'
```

## Custom Conditions

Override `evaluateCondition` in your model:

```php
use Whilesmart\Roles\Traits\HasPermissions;

class User extends Model
{
    use HasPermissions;

    protected function evaluateCondition(string $condition, $value, $subject): bool
    {
        if ($condition === 'can_moderate') {
            return $this->hasRole('moderator');
        }
        return parent::evaluateCondition($condition, $value, $subject);
    }
}
```

## Abilities vs Permissions

| Feature | Permissions | Abilities |
|---------|-------------|-----------|
| Scope | Global or contextual | Global, contextual, or subject-specific |
| Conditions | No | Yes (owner, status, custom) |
| Use case | Role-based access (RBAC) | Attribute-based access (ABAC) |
| Inheritance | Via roles | Direct grants only |

## Revoking Abilities

```php
// Revoke specific ability
$user->revokeAbility('post:edit', $post);

// Revoke all post:edit abilities
$user->abilities()->where('action', 'post:edit')->delete();
```