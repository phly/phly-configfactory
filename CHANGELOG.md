# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - 2019-02-07

### Added

- [#1](https://github.com/phly/phly-expressive-configfactory/pull/1) adds `ConfigAbstractFactory`, a zend-servicemanager abstract factory for
  auto-wiring configuration services.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2019-02-06

### Added

- Creates `Phly\Expressive\ConfigFactory`, which can be used to retrieve nested
  values from the `config` service. As an example, given the configuration:

  ```php
  return [
      'cache' => [
          'adapters' => [
              'blog' => [
                  'connection' => 'tcp://localhost:6349',
                  'username'   => 'www-data',
                  'prefix'     => 'blog',
              ],
          ],
      ],
  ];
  ```

  a factory could pull the "blog" adapter information using:

  ```php
  $container->get('config-cache.adapters.blog');
  ```

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
