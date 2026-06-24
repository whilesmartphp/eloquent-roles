# Installation

## Requirements

- PHP 8.1+
- Laravel 10.x or 11.x

## Install via Composer

```bash
composer require whilesmart/eloquent-roles
```

The service provider is auto-discovered.

## Publish Migrations

```bash
php artisan vendor:publish --tag=roles-migrations
php artisan migrate
```

This creates the following tables:
- `roles` — role definitions (name, slug, description, level)
- `permissions` — permission definitions (name, slug, description)
- `role_permissions` — pivot table linking roles to permissions
- `role_assignments` — contextual role assignments (user + role + optional context)
- `abilities` — fine-grained ability grants with conditions

## Publish Config (Optional)

```bash
php artisan vendor:publish --tag=roles-config
```

This creates `config/roles.php` with UUID support option.