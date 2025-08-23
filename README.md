# Whilesmart Eloquent Roles

A flexible roles and permissions package for Laravel applications built on Eloquent ORM.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/whilesmart/eloquent-roles.svg?style=flat-square)](https://packagist.org/packages/whilesmart/eloquent-roles)

## Features

- **Contextual Roles**: Assign roles within specific contexts (e.g., user can be admin in one workspace, member in another)
- **Hierarchical Permissions**: Role-based permissions with inheritance support
- **Eloquent Integration**: Seamlessly integrates with your existing Eloquent models
- **Flexible Architecture**: Support for abilities, permissions, and role assignments
- **Laravel Auto-Discovery**: Automatically registers service provider

## Installation

Install the package via Composer:

```bash
composer require whilesmart/eloquent-roles
```

The package will automatically register its service provider.

## Usage

### Adding Roles to Models

Use the `HasRoles` trait on any model that should have roles:

```php
use Whilesmart\Roles\Traits\HasRoles;

class User extends Model
{
    use HasRoles;
}
```

### Role Management

```php
// Check if user has a role
$user->hasRole('admin');

// Check role within specific context
$user->hasRole('manager', 'workspace', $workspaceId);

// Assign role
$user->assignRole($role);

// Assign role with context
$user->assignRole($role, 'workspace', $workspaceId);
```

### Permission Management

Use the `HasPermissions` trait for permission-based access control:

```php
use Whilesmart\Roles\Traits\HasPermissions;

class User extends Model
{
    use HasRoles, HasPermissions;
}
```

```php
// Check permissions
$user->hasPermission('edit-posts');
$user->hasPermission('manage-users', 'workspace', $workspaceId);
```

## Models

### Role
- **name**: Human-readable role name
- **slug**: URL-friendly identifier (auto-generated)
- **description**: Role description
- **level**: Hierarchical level for role inheritance

### Permission
- **name**: Permission name
- **slug**: URL-friendly identifier
- **description**: Permission description

### RoleAssignment
- **assignable**: Polymorphic relation to any model
- **role_id**: Associated role
- **context_type**: Context model type (optional)
- **context_id**: Context model ID (optional)

## Configuration

The package works out of the box, but you can customize it by publishing the configuration:

```bash
php artisan vendor:publish --provider="Whilesmart\Roles\RolesServiceProvider"
```

## Requirements

- PHP ^8.2
- Laravel ^11.0|^12.0

## License

The MIT License (MIT).

