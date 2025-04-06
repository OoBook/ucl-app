<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://github.com/oobook/ucl-app/actions"><img src="https://github.com/oobook/ucl-app/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/oobook/ucl-app"><img src="https://img.shields.io/packagist/dt/oobook/ucl-app" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/oobook/ucl-app"><img src="https://img.shields.io/packagist/v/oobook/ucl-app" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/oobook/ucl-app"><img src="https://img.shields.io/packagist/l/oobook/ucl-app" alt="License"></a>
</p>

## About UCL APP

UCL APP is a web application that allows users to manage their UCL teams.

## Installation

You can install the package via composer:

```bash
composer create-project oobook/ucl-app ucl-app
```

## Usage

After installation, if you didn't run the 'composer create-project' command and clone from the github repository, you can run the following commands to install the dependencies and setup project:

```bash
composer install

php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"

php artisan migrate --seed
```

If you have completed the installation, you can run the following command to start the project:

```bash
php artisan serve
```

or 

If you work on a Docker nginx server, Herd, etc., change APP_URL in the **.env** file to your server's URL.

```bash
...
APP_URL=http://localhost:8000
...
```

## User Management

The project uses Laravel Jetstream for user management. The system must have created a user with the email as following: 

```bash
test@useinsider.com

12345678
```

## Testing

To run the tests, you can use the following command:

```bash
php artisan test
```

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
