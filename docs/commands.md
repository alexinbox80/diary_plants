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

#clear cache \
php bin/console cache:clear

#clear doctrine cache
php bin/console doctrine:cache:clear-metadata \
php bin/console doctrine:cache:clear-query --env=prod \
php bin/console doctrine:cache:clear-result --env=prod \
php bin/console doctrine:cache:clear-metadata --env=prod

#show route lists \
php bin/console debug:router

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

#check console command
php bin/console debug:container --tag=console.command
