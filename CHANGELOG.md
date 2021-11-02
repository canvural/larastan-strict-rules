# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [1.0.0] - 2021-11-02

### Fixed
- PHPStan and Larastan 1.0 compatibility.

## [0.1.7] - 2021-06-29

### Changed
- Require minimum Larastan version 0.7.6

### Fixed
- Fixed issue with `NoGlobalLaravelFunctionRule`
- Fixed compatibility issue with latest Larastan versions.
- Allow wide range of orchestra/testbench dependency

## [0.1.4] - 2020-10-24

### Fixed

- Fixed compatibility issue with latest Larastan versions.

## [0.1.3] - 2020-09-01

### Added
- Added ability to define allowed global functions for the `NoGlobalLaravelFunctionRule` rule.

### Changed
- Requires PHP 7.3 as minimum version.

## [0.1.2] - 2020-07-03

### Added
- Introduced `NoPropertyAccessorRule` to disallow defining and using property accessors in models.

## [0.1.1] - 2020-06-20

### Added
- New rule to disallow Eloquent local query scopes.

## 0.1.0 - 2020-06-20

Initial release

[Unreleased]: https://github.com/canvural/larastan-strict-rules/compare/1.0.0...HEAD
[1.0.0]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.7...1.0.0
[0.1.7]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.4...v0.1.7
[0.1.4]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.3...v0.1.4
[0.1.3]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.2...v0.1.3
[0.1.2]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/canvural/larastan-strict-rules/compare/v0.1.0...HEAD
