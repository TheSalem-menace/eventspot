.PHONY: install setup fixtures tests reset

install:
	composer install

setup:
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	php bin/console doctrine:fixtures:load --no-interaction

reset:
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction

tests:
	php bin/phpunit

test-unit:
	php bin/phpunit tests/Service/

test-functional:
	php bin/phpunit tests/Controller/

report:
	php bin/console app:eventspot:report --upcoming
