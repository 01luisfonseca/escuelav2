# Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).




# Project steps

## Run sql commands
Require run a couple of commands to have acces to database.

```bash
docker-compose exec db bash
```
It opens bash in virtual Server. Next:

```bash
mysql -u root -p
```
Requires password: jAjy2qpCyLhRy7De

And set a global user
```sql
GRANT ALL ON *.* TO 'i353614_lara3'@'%' IDENTIFIED BY 'jAjy2qpCyLhRy7De';
FLUSH PRIVILEGES;
EXIT;
```

Finally, set .env database host variable to db

## Docker basic commands
* docker-compose exec app bash => Access to command line
* docker-compose exec app php composer.phar install
* docker-compose exec app php artisan key:generate
* docker-compose exec app php artisan migrate
* docker-compose exec app php artisan passport:install
* docker-compose exec app php artisan db:seed