docker-compose up -d \
docker exec -it php sh

#make new entity \
php bin/console make:entity

#create migrations \
php bin/console doctrine:migrations:diff

#displays actual config values \
php bin/console debug:config doctrine
php bin/console config:dump-reference doctrine

#show information about mapped entities \
php bin/console doctrine:mapping:info

#migrate migrations \
php bin/console doctrine:migrations:migrate

#show sql for update \
php bin/console doctrine:schema:update --dump-sql

#clear cache \
php bin/console cache:clear

#clear doctrine cache
php bin/console doctrine:cache:clear-metadata \
php bin/console doctrine:cache:clear-query --env=prod \
php bin/console doctrine:cache:clear-result --env=prod \
php bin/console doctrine:cache:clear-metadata --env=prod

#show route lists \
php bin/console debug:router
php bin/console debug:router dashboard.plants.index
php bin/console debug:firewall main

#drop all tables in database \
php bin/console doctrine:schema:drop --full-database --force

#migrtion status \
php bin/console doctrine:migrations:status

#generate migration \
php bin/console doctrine:migrations:generate

#Validate the mapping files \
php bin/console doctrine:schema:validate

#Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata \
php bin/console doctrine:schema:update --dump-sql

#test environment \
php bin/console doctrine:schema:drop --full-database --force --env=test \
php bin/console doctrine:database:create --env=test \
php bin/console doctrine:migrations:migrate --env=test

php bin/console debug:dotenv

#executes arbitrary SQL directly from the command line \
php bin/console dbal:run-sql "SELECT * FROM \"user\""
php bin/console doctrine:query:sql "SELECT * FROM \"user\""

#migrations lists \
php bin/console d:m:list

#rollback to prev migration \
php bin/console doctrine:migrations:migrate prev

php bin/console doctrine:migrations:execute --up DoctrineMigrations\\Version20250402090731 --no-interaction
php bin/console doctrine:migrations:execute --down DoctrineMigrations\\Version20250402090731 --no-interaction

#check console command \
php bin/console debug:container --tag=console.command

#Registered Listeners for "kernel.controller" Event \
php bin/console debug:event-dispatcher kernel.controller

#Make DB for tests \
php bin/console --env=test doctrine:database:create

#Make migration for testing \
php bin/console --env=test doctrine:migrations:migrate --no-interaction

#Check test environment \
php bin/console debug:dotenv --env=test | grep DATABASE_URL

#messure speed \
curl -o /dev/null -s -w 'Total: %{time_total}s\n' http://localhost:8080/en/dashboard/plants-paginated

#docker build \
docker compose up --build -d 

#Scheduler \
php bin/console debug:scheduler
php bin/console debug:scheduler default

#Test run \
php bin/console messenger:consume scheduler_default -vv

#Show message bus \
php bin/console messenger:stats
php bin/console dbal:run-sql "SELECT * FROM messenger_messages"

#show prev version from old commit \
bash-3.2$ git show HEAD~3:composer.lock | grep -A 2 '"name": "phpunit/phpunit"'
"name": "phpunit/phpunit",
"version": "12.3.5",
"source": {
bash-3.2$ cat composer.lock | grep -A 2 '"name": "phpunit/phpunit"';
"name": "phpunit/phpunit",
"version": "12.5.24",
"source": {
