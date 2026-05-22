# Wsmallnews system cms modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wsmallnews/cms.svg?style=flat-square)](https://packagist.org/packages/wsmallnews/cms)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/wsmallnews/cms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/wsmallnews/cms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/wsmallnews/cms/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/wsmallnews/cms/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wsmallnews/cms.svg?style=flat-square)](https://packagist.org/packages/wsmallnews/cms)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require wsmallnews/cms:^1.0
```

Installing this package will publish the configuration files and migration files of both the third-party dependency package and the current package:

```bash
php artisan sn-cms:install
```

You can publish only the config file individually:

```bash
php artisan vendor:publish --tag="cms-config"
```

Publish and run only the migrations individually:

```bash
php artisan vendor:publish --tag="cms-migrations"
php artisan migrate
```

Multi language support, you can publish the language files using:

```bash
php artisan vendor:publish --tag="sn-support-translations"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="cms-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$cms = new Wsmallnews\Cms();
echo $cms->echoPhrase('Hello, Wsmallnews!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [smallnews@qq.com](https://github.com/Wsmallnews)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
