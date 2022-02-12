**Archived**

This project has been archived as its features have been superseded by the [Shift Workbench](https://laravelshift.com/workbench/).

---


# Shift - Console
A set of useful `artisan` commands to keep your Laravel applications fresh.

## Installation
You can install the Shift Console via composer using the following command:

```sh
composer require --dev laravel-shift/console
```

Shift Console will automatically register itself using [package discovery](https://laravel.com/docs/packages#package-discovery).

## Requirements
Shift Console requires a Laravel application running version 6.0 or higher. **Not running the latest version?** [Run Shift](https://laravelshift.com/shifts).

## Basic Usage
Currently, the Shift Console includes set `artisan` commands under the `shift` namespace. Currently, there is only one command - `shift:check-routes`.

```sh
php artisan shift:check-routes
```

This command checks for _Dead Routes_ by reviewing your application routes for references to undefined controllers, methods, or invalid visibility.

## Contributing
Contributions may be made by submitting a Pull Request against the `master` branch. Any submissions should be complete with tests and adhere to the [PSR-2 code style](https://www.php-fig.org/psr/psr-2/).

You may also contribute by [opening an issue](https://github.com/laravel-shift/console/issues) to report a bug or suggest a new feature.

