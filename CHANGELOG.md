# Release Notes

## [v3.1.0](https://github.com/bert-w/runescape-hiscores-api/compare/v3.0.0...v3.1.0) - 2022-04-24

- Added Guzzle 7 optional upgrade

## [v3.0.0](https://github.com/bert-w/runescape-hiscores-api/compare/v2.0.0...v3.0.0) - 2022-03-10

### Changed

- The total level calculation now uses `level = 1` for skills that were not found in the Hiscores. This gives a more
accurate estimation. Previously, this was not taken into account so the total level was actually an underestimation.
- The `Player` class should now be constructed with 3 parameters instead of 2 (previously, the skills and minigames
were merged as one array):
  - `new Player($name, array $skills, array $minigames)`

### Added

- This changelog.
- Added extra unit tests for more test cases.

## [v2.0.0](https://github.com/bert-w/runescape-hiscores-api/compare/v1.0.0...v2.0.0) - 2021-08-30

## [v1.0.0](https://github.com/bert-w/runescape-hiscores-api/compare/v1.0.0...v1.0.0) - 2020-03-05
