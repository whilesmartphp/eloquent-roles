.PHONY: help up down install test pint pint-test lint build serve clean prepare restart logs shell

# Default target
help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@egrep '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'

# Docker operations
up: ## Start docker containers
	docker compose up -d

down: ## Stop docker containers
	docker compose down

restart: ## Restart docker containers
	docker compose restart

logs: ## Show container logs
	docker compose logs -f

shell: ## Access container shell
	docker compose exec app bash

# Development operations
install: up ## Install dependencies
	docker compose exec app composer install

test: ## Run tests
	docker compose exec app ./vendor/bin/testbench package:test

pint: ## Run Laravel Pint code formatter
	docker compose exec app composer pint

pint-test: ## Test code formatting without applying changes
	docker compose exec app composer pint:test

lint: pint ## Alias for pint

build: ## Build the workbench
	docker compose exec app composer build

serve: ## Start the development server
	docker compose exec app composer serve

clean: ## Clear caches and clean up
	docker compose exec app composer clear

prepare: ## Prepare the package
	docker compose exec app composer prepare

autoload: ## Dump composer autoloader
	docker compose exec app composer dump-autoload

# Combined operations
fresh: down up install ## Fresh start: down, up, install
	@echo "Environment is ready!"

setup: fresh test ## Complete setup with tests
	@echo "Setup complete!"

check: pint-test test ## Run all checks (formatting and tests)
	@echo "All checks passed!"
