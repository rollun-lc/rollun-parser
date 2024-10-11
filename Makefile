init: docker-down-clear docker-pull docker-build docker-up composer-install development-enable
up: docker-up
down: docker-down
restart: docker-down docker-up
test: composer-test
development-enable: composer-development-enable
development-disable: composer-development-disable

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

composer-install:
	docker-compose exec php-fpm composer install

composer-development-enable:
	docker-compose exec php-fpm composer development-enable

composer-development-disable:
	docker-compose exec php-fpom

composer-da:
	docker-compose exec php-fpm composer dumpautoload

composer-test:
	docker-compose exec php-fpm composer test