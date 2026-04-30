PHPUNIT = php bin/phpunit

test: app-test entity-test model-test vo-test service-test event-test cli-test form-test web-test infra-test func-test repo-test

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

event-test:
	@echo "--- Running Event Tests ---"
	$(PHPUNIT) tests/Unit/Domain/Event
	$(PHPUNIT) tests/Unit/Domain/EventSubscriber

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

repo-test:
	@echo "--- Running Integration Repository Tests ---"
	$(PHPUNIT) tests/Integration/Infrastructure/Repository
	$(PHPUNIT) tests/Integration/Service

app-test:
	@echo "--- Running Unit Tests from Application ---"
	$(PHPUNIT) tests/Unit/Application
