help: ## show this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

restart: down up

up: ## launch docker container
	docker-compose up -d && docker-compose ps

build: ## build docker container
	docker-compose up -d --build

down: ## stop and remove docker container
	docker-compose down

mkmigrate: ## create migration according to actual database structure
	docker-compose exec apache bin/console make:migration

migrate: ## execute database migration
	docker-compose exec apache bin/console doctrine:migrations:migrate --no-interaction
fixload: ## execute alice fixtures
	docker-compose exec apache bin/console hautelook:fixtures:load
