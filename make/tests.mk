PHPUNIT = php bin/phpunit

test: entity-test model-test vo-test service-test cli-test form-test web-test infra-test func-test

entity-test:
	@echo "--- Running Entity Tests ---"
	$(PHPUNIT) tests/Unit/Domain/Entity

model-test:
	@echo "--- Running Model Tests ---"
	$(PHPUNIT) tests/Unit/Domain/Model

vo-test:
	@echo "--- Running Value Object Tests ---"
	$(PHPUNIT) tests/Unit/Domain/ValueObject

service-test:
	@echo "--- Running Service Tests ---"
	$(PHPUNIT) tests/Unit/Domain/Service

cli-test:
	@echo "--- Running Command Line Tests ---"
	$(PHPUNIT) tests/Unit/Controller/Cli

form-test:
	@echo "--- Running Form Tests ---"
	$(PHPUNIT) tests/Unit/Controller/Form

web-test:
	@echo "--- Running Web Controller and Manager Tests ---"
	$(PHPUNIT) tests/Unit/Controller/Web/Dashboard

func-test:
	@echo "--- Running Functional Web Tests ---"
	$(PHPUNIT) tests/Functional/Controller/Web/Dashboard

infra-test:
	@echo "--- Running Unit Tests from Infrastructure ---"
	$(PHPUNIT) tests/Unit/Infrastructure
