## larastan-strict-rules

Extra strict and opinionated PHPStan rules for Laravel.

[![Tests](https://github.com/canvural/larastan-strict-rules/workflows/Tests/badge.svg)](https://github.com/canvural/larastan-strict-rules/actions)
[![codecov](https://codecov.io/gh/canvural/larastan-strict-rules/branch/master/graph/badge.svg)](https://codecov.io/gh/canvural/larastan-strict-rules)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%20Max-brightgreen.svg?style=flat&logo=php)](https://phpstan.org)

## Installation

You can install the package via composer:

```bash
composer require --dev canvural/larastan-strict-rules
```

To enable all the rules, include `rules.neon` in your project's PHPStan config:

```
includes:
    - vendor/canvural/larastan-strict-rules/rules.neon
```


## Enabling rules one-by-one
If you don't want to start using all the available strict rules at once but only one or two, you can! Just don't include the whole `rules.neon` from this package in your configuration, but look at its contents and copy only the rules you want to your configuration under the `services` key. For example:

```
services:
    -
        class: Vural\LarastanStrictRules\Rules\NoDynamicWhereRule
        tags:
            - phpstan.rules.rule
    -
        class: Vural\LarastanStrictRules\Rules\NoFacadeRule
        tags:
            - phpstan.rules.rule
```

## Rules

#### `NoDynamicWhereRule`

This rule disallows the usage of dynamic where methods on Eloquent query builder.

#### `NoFacadeRule`

This rule disallows the usage of Laravel Facades. Also, checks for the real time facade usage.

#### `NoGlobalLaravelFunctionRule`

This rule disallows the usage of global helper functions that comes with Laravel.

If you want to allow some functions, you can use the `allowedFunctions` parameter for this rule. Like so:
```neon
-
    class: Vural\LarastanStrictRules\Rules\NoGlobalLaravelFunctionRule
    arguments:
        allowedFunctions:
            - app
            - event
```

#### `NoValidationInControllerRule`

This rule disallows validating the request in controllers.

#### `ScopeShouldReturnQueryBuilderRule`

This rule makes sure `Illuminate\Database\Eloquent\Builder` instance is returned from `Eloquent` local query scopes.

#### `NoLocalQueryScopeRule`

This rule disallows the usage of local model query scopes all together.

#### `NoPropertyAccessorRule`

This rule disallows the usage of model property accessors.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

People:
- [Can Vural](https://github.com/canvural)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

