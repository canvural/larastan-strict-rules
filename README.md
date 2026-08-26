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

```neon
includes:
    - vendor/canvural/larastan-strict-rules/rules.neon
```

## Disabling rules

You can disable rules using configuration parameters:

```neon
parameters:
    larastanStrictRules:
        noDynamicWhere: false
        noFacade: false
        noGlobalLaravelFunction: false
        noLocalQueryScope: false
        noPropertyAccessor: false
        noValidationInController: false
        scopeShouldReturnQueryBuilder: false
        listenerShouldHaveVoidReturnType: false
```

## Enabling rules one-by-one

If you don't want to start using all the available strict rules at once but only one or two, you can!

You can disable all rules from the included `rules.neon` with:

```neon
parameters:
	larastanStrictRules:
		allRules: false
```

Then you can re-enable individual rules with configuration parameters:

```neon
parameters:
	larastanStrictRules:
		allRules: false
		noDynamicWhere: true
```

## Rules

#### `NoDynamicWhereRule`

This rule disallows the usage of dynamic where methods on Eloquent query builder.

#### `NoFacadeRule`

This rule disallows the usage of Laravel Facades. Also, checks for the real time facade usage.

#### `NoGlobalLaravelFunctionRule`

This rule disallows the usage of global helper functions that comes with Laravel.

If you want to allow some functions, you can use the `allowedGlobalFunctions` parameter. Like so:
```neon
parameters:
    allowedGlobalFunctions:
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

#### `ListenerShouldHaveVoidReturnTypeRule`

This rule makes sure your event listeners have a void return type. 

If you return `false` from an event listener, Laravel will stop the propagation of an event to other listeners. Sometimes this can be useful. But other time it can cause bugs that you will need to debug for hours. So this opinionated rule makes sure you always have `void` return type for your event listeners.

You need to configure this rule by adding the directories that your event listeners are in to the `listenerPaths` parameter:
```neon
parameters:
    listenerPaths:
        - app/Listeners
        - app/DomainA/Listeners
```

If `listenerPaths` is left empty, every class with a `handle` method is checked. Laravel commands and middlewares also use a `handle` method, but those are never reported by this rule.

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

