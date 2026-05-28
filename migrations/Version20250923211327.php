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
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS marker__letter_group__uniq ON marker (letter, group_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__email__uniq ON "user" (email) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__phone__uniq ON "user" (phone) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS user__refresh_token__uniq ON "user" (refresh_token) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS usage__group_plant_use_date_usable_id_usable_type__uniq ON "usage" (group_id, plant_id, use_date, usable_type, usable_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS watering__group_id_marker_id__uniq ON watering (group_id, marker_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS stimulant__group_id_marker_id__uniq ON stimulant (group_id, marker_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__group_id_marker_id__uniq ON fertilizer (group_id, marker_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS pest__group_id_marker_id__uniq ON pest (group_id, marker_id) WHERE (deleted_at IS NULL)');
        //$this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS analytic__plant_id__uniq ON analytic (plant_id)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS analytic__plant_id__uniq ON analytic (plant_id) WHERE (deleted_at IS NULL)');
        $this->addSql('CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS incident__public_code__uniq ON incident (public_code) WHERE (deleted_at IS NULL)');

        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__marker_id__ind ON fertilizer (marker_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS fertilizer__group_id__ind ON fertilizer (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS offspring__plant_id__ind ON offspring (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS offspring__group_id__ind ON offspring (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS pest__marker_id__ind ON pest (marker_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS pest__group_id__ind ON pest (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS plant__oid__ind ON plant (oid)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__email__ind ON "user" (email)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__phone__ind ON "user" (phone)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user__refresh_token__ind ON "user" (refresh_token)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS stimulant__marker_id__ind ON stimulant (marker_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS stimulant__group_id__ind ON stimulant (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS usage__plant_id__ind ON usage (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS watering__group_id__ind ON watering (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS watering__marker_id__ind ON watering (marker_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS marker__group_id__ind ON marker (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS usage__usable__ind ON usage (usable_type, usable_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS usage__group_id__ind ON usage (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS attachment__attachable__ind ON attachment (attachable_type, attachable_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS attachment__group_id__ind ON attachment (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS repotting__plant_id__ind ON repotting (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS repotting__group_id__ind ON repotting (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS analytic__plant_id__ind ON analytic (plant_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS analytic__group_id__ind ON analytic (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS incident__created_at__ind ON incident (created_at)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user_message__user_id__ind ON "user_message" (user_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user_message__group_id__ind ON "user_message" (group_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user_message__sender_id__ind ON "user_message" (sender_id)');
        $this->addSql('CREATE INDEX CONCURRENTLY IF NOT EXISTS user_message__status__ind ON "user_message" (status)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS plant__oid__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS marker__letter_group__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__email__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__phone__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__refresh_token__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__group_plant_use_date_usable_id_usable_type__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS watering__group_id_marker_id__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS stimulant__group_id_marker_id__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__group_id_marker_id__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS pest__group_id_marker_id__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS analytic__plant_id__uniq');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS incident__public_code__uniq');

        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__marker_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS fertilizer__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS offspring__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS offspring__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS pest__marker_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS pest__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__email__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__phone__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user__refresh_token__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS plant__oid__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS stimulant__marker_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS stimulant__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS watering__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS watering__marker_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS marker__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS usage__usable__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS attachment__attachable__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS attachment__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS repotting__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS repotting__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS analytic__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS analytic__plant_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS incident__created_at__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user_message__user_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user_message__group_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user_message__sender_id__ind');
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS user_message__status__ind');
    }
}
