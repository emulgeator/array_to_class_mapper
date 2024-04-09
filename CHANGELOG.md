# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.1.0] - 2024-04-08

### Added
- Casting empty string to null in case of nullable `int` or `float`


## [1.0.0] - 2024-03-26

### Added
- Mapping different bool representations to booleans


## [0.2.0] - 2024-03-05

### Added
- When a key in the input array has a special character, the library will try to find the property by removing the special characters

## [0.1.4] - 2023-11-16

### Fixed

- Allowing nullable arrays


## [0.1.3] - 2022-11-28

### Fixed

- When an array typed property had an array type declared in docblock the mapper removed the keys of the given array. This has been fixed.

### Added

- This changelog :)
