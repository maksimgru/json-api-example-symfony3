# Test Skills - Symfony 3.4 App

## Install
Run `composer install` in your root directory.

## DB Config, Fetch Data, Insert into DB
1. Set your parameters in app/config/parameters.yml
2. Run `php bin/console doctrine:database:create` to create new database.
3. Run `php bin/console doctrine:migrations:migrate` to run migration process and build database schema according to our doctrine entities.
4. Run `php bin/console fetch:soccerway:data` to fetch demo data from external resource and insert into your DB.

## Dev Server
1. Run `php bin/console server:start` for start a dev server. Navigate to `http://localhost:8000`.
2. Run `php bin/console server:stop` for stop a dev server.

## Tests
1. Run `./vendor/bin/simple-phpunit` from project root to run all phpunit tests.
