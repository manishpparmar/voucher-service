# Voucher service

This repository holds the source of a **voucher service**. A small demo for a service application written in PHP 7.4 and Symfony 5 using Doctrine and Messenger Component to generate vouchers for orders.

For correct working inside the project, please note following dependencies:

- Infrastructure:
    - [Docker](https://www.docker.com/)
    - [Docker Compose](https://docs.docker.com/compose/)
- Dependent Services:
    - [MariaDB](https://mariadb.org/)
    - [RabbitMQ](https://www.rabbitmq.com/)
    - [SQLite](https://www.sqlite.org/index.html)
- Coding language:
    - [PHP 7.4](https://www.php.net/releases/7_4_0.php)
- Framework and components:
    - [Symfony 5](https://symfony.com/5) with [Symfony Flex](https://symfony.com/doc/current/setup/flex.html)
    - [Doctrine](https://www.doctrine-project.org/) and [Doctrine ORM](https://symfony.com/doc/current/doctrine.html)
    - [Symfony Messenger Component](https://symfony.com/doc/current/components/messenger.html)
- Code quality and test development:
    - [PHPUnit](https://phpunit.de/)
    - [PHPStan](https://phpstan.org/)
    - [PHP Coding Standards Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

**Note:** For more detailed dependencies take a look into the configurations:

* Docker Compose config: [`docker-compose.yml`](docker-compose.yml)
* Application Docker config: [`app/Dockerfile`](app/Dockerfile)
* Symfony application config: [`app/composer.json`](app/composer.json)

To get involved into the development of this project you need to get a local copy of this repository:

```bash
git clone git@github.com:bassix/voucher-service.git
cd voucher-service
```

_**Note:** This project is based on the [GitFlow](http://nvie.com/posts/a-successful-git-branching-model/) branching model and workflow._

Before the application environment can be started we need to generate an environment specific configuration specially for local development:

```bash
./env.sh
```

**Note:** the `.env` file holds the following main environment variables:

* The `HOST_UID` and `HOST_GID` of your local user and this ID's are used inside the container to get access to the volumes with right permissions.
* The `PHP_DATE_TIMEZONE` to configure the time zone the application is running. The most common is `Europe/Berlin`. The default value is set to `UTC`.
* The `OPCACHE_VALIDATE_TIMESTAMPS` to activate `1` or deactivate `0` the opcache rebuild by each request. Set this to `0` in development mode.

Now lets start the Docker Compose environment:

```bash
docker-compose up -d --build
```

_**Note:** The current configuration is binding the local project as a volume into the container._

Install all relevant dependencies:

```bash
docker exec --user www-data app composer install --dev
```

## Voucher application

The main part of this service is the Symfony application it self. Following process is the main part of the implementation:

![](docs/process/order-voucher-service.png)

[Draw.io](https://draw.io) source: [docs/process/order-voucher-service.drawio](docs/process/order-voucher-service.drawio)

All resources for the service application are located inside the `app/` directory. To run the following commands, change to this location.

### Order a voucher

For a better development and testing of the functionality the app provides some helpful console commands:

* Generate a fake order to get a voucher:

    ```bash
    docker exec --user www-data app bin/console app:order-voucher:create
    ```

* Generate an order message to get a voucher:
 
    ```bash
    docker exec --user www-data app bin/console app:order-voucher:message
    ```

* List all existing orders and vouchers:
 
    ```bash
    docker exec --user www-data app bin/console app:order-voucher:list
    ```

### Doctrine

By default, the database schema will be created by migration (or database fixtures located at `mariadb/fixtures/voucher.sql`).

Alternative the Doctrine commands can be used to start with an empty database:

```bash
docker exec --user www-data app bin/console doctrine:database:create
docker exec --user www-data app bin/console make:migration
docker exec --user www-data app bin/console doctrine:migrations:migrate
```

**Note:** if something went horribly wrong, you can start from the ground up by recreating the whole database (all existing migrations should be deleted):

```bash
docker exec --user www-data app bin/console doctrine:database:drop --force
```

### Code quality tools

_**Note:** Why ever, the SQLite connection isn't working :/ So please use this workaround:_

```bash
docker exec --user www-data app bin/console doctrine:database:drop --force
docker exec --user www-data app bin/console doctrine:database:create
docker exec --user www-data app bin/phpunit
```

Run [PHPUnit](https://phpunit.de/) tests:

```bash
docker exec --user www-data app bin/phpunit
```

Run [phpstan](https://github.com/phpstan/phpstan) to make statical analyse of the code. (Level from 0 to 7, where 0 is the most loose, 7 is the strongest. 0 is default):

```bash
docker exec --user www-data app vendor/phpstan/phpstan/phpstan analyse --level 7
```

Run [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to fix errors in code (use `--dry-run` option only to see errors):

```bash
docker exec --user www-data app vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix
```

Documentation and constructor with more detailed information could be found at [https://mlocati.github.io/php-cs-fixer-configurator](https://mlocati.github.io/php-cs-fixer-configurator).

## Service application creation

This service is based on Symfony Flex. The following steps were performed to create the basic project and application structure.

### Create new Symfony application

Create a new project from the Symfony Flex recipe:

```bash
composer create-project symfony/skeleton app
```

Install Doctrine support via the orm Symfony pack, as well as the MakerBundle, which will help generate some code:

```bash
composer require symfony/orm-pack
```

After the entities were created the initial schema migration was generated:

```bash
docker exec --user www-data app bin/console doctrine:migrations:diff
```

### Install code quality tools

The PHPUnit Testing Framework:

```bash
composer require --dev symfony/phpunit-bridge
```

PHPStan Symfony Framework extensions and rules:

```bash
composer require --dev phpstan/phpstan-symfony
```

PHP Coding Standards Fixer Symfony Framework extensions and rules:

```bash
composer require --dev friendsofphp/php-cs-fixer
```
