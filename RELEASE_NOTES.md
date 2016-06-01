# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Fixed
- Fixed issue with README.md code example, that uses wrong method for cached data retrieval.

## [0.2.0]
### Added
- Interface for extracting raw cache value `ExtractableCacheValueInterface`

### Changed
- `CacheItem::get()` method now ALWAYS returns `null` when `isHit()` returns false. 
- `CacheItemPool::save()` and `CacheItemPool::saveDeferred()` now supports additionally CacheItems that implement `ExtractableCacheValueInterface` with a fallback to previous implementation, that was relying on `CacheItem::get()` 

### Fixed
- Fixed version in `module.xml` file, as it was not matching one in composer.json.

## 0.1.0
### Added
- PSR-6 Implementation
- DI auto-wiring for CacheItemPool
- DI auto-wiring for [EcomDev\CacheKey](https://github.com/EcomDev/CacheKey) library

[Unreleased]: https://github.com/EcomDev/magento-psr6-bridge/compare/0.2.0...HEAD
[0.2.0]: https://github.com/EcomDev/magento-psr6-bridge/compare/0.1.0...0.2.0
