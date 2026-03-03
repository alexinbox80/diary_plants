<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923211327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create db index for entities';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS plant__oid__uniq ON plant (oid) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS status__letter__uniq ON status (letter) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__email__uniq ON "user" (email) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__phone__uniq ON "user" (phone) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__refresh_token__uniq ON "user" (refresh_token) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__letter__uniq ON fertilizer (letter) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS pest__letter__uniq ON pest (letter) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS stimulant__letter__uniq ON stimulant (letter) WHERE (deleted_at IS NULL)');

        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__plant_id__ind ON fertilizer (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__group_id__ind ON fertilizer (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS offspring__plant_id__ind ON offspring (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS offspring__group_id__ind ON offspring (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS pest__plant_id__ind ON pest (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS plant__oid__ind ON plant (oid)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__email__ind ON "user" (email)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__phone__ind ON "user" (phone)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__refresh_token__ind ON "user" (refresh_token)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS stimulant__plant_id__ind ON stimulant (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS usage__plant_id__ind ON usage (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS usage__usable__ind ON usage (usable_type, usable_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS attachment__attachable__ind ON attachment (attachable_type, attachable_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS attachment__group_id__ind ON attachment (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS plant__oid__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS status__letter__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__email__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__phone__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__refresh_token__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__letter__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS pest__letter__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS stimulant__letter__uniq');

        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS offspring__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS offspring__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS pest__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__email__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__phone__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__refresh_token__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS plant__oid__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS stimulant__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__usable__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS attachment__attachable__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS attachment__group_id__ind');
    }
}
