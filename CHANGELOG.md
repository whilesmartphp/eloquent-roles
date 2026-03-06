## [1.0.1] - 2026-03-06

### Added
- Route middleware for role and permission checks (`RequireRole`, `RequirePermission`)
- Artisan commands for role and permission management (`CreateRoleCommand`, `CreatePermissionCommand`, `AssignRoleCommand`)
- Base seeder for roles and permissions (`RolesAndPermissionsSeeder`)
- Comprehensive ability tests
- Tests for middleware, commands, and seeder

### Fixed
- Add missing database migrations (`role_permissions`, `role_assignments`, `abilities` tables)

### Changed
- Added `cviebrock/eloquent-sluggable` dependency
- Updated package name

## [1.0.0] - 2025-10-02
- Initial release
