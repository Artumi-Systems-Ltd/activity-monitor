# Changelog

All notable changes to `varatech/activity-monitor` will be documented in this file.

## [1.0.1] - 2024-01-15

### Added
- ✅ **Laravel 12 Support**: Added full compatibility with Laravel 12.x
- ✅ **Extended PHP Support**: Added support for PHP 8.2 and 8.3
- ✅ **Updated Testing**: Enhanced Orchestra Testbench support for Laravel 12

### Updated
- Updated illuminate/support dependency to include ^12.0
- Updated illuminate/database dependency to include ^12.0
- Updated orchestra/testbench to include ^10.0 for Laravel 12 testing
- Updated PHPUnit to include ^11.0 for modern testing

## [1.0.0] - 2024-01-15

### Added
- Initial release of Laravel User Activity Monitor package
- Automatic logging of user authentication events (login, logout)
- Model event tracking for CRUD operations
- HTTP request logging middleware
- Configurable activity logging with rich metadata
- Easy-to-query Activity model with helpful scopes
- LogsActivity trait for automatic model tracking
- Console commands for cleanup and statistics
- Comprehensive configuration options
- Database migration with proper indexing
- Full test coverage
- Detailed documentation and examples

### Features
- ✅ Automatic activity logging for login/logout events
- ✅ Model event tracking (created, updated, deleted)
- ✅ HTTP request logging with metadata
- ✅ IP address, user agent, and URL tracking
- ✅ Configurable logging options
- ✅ Easy querying with scopes
- ✅ Console commands for management
- ✅ Trait-based model integration
- ✅ JSON properties for custom data
- ✅ Performance optimized with proper indexing 