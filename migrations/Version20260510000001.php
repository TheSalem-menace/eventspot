<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260510000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add participant to inscription, ManyToMany tags, update relations';
    }

    public function up(Schema $schema): void
    {
        // ManyToMany join table: evenement_tag_evenement
        $this->addSql('CREATE TABLE IF NOT EXISTS evenement_tag_evenement (
            evenement_id INT NOT NULL,
            tag_evenement_id INT NOT NULL,
            INDEX IDX_evenement (evenement_id),
            INDEX IDX_tag (tag_evenement_id),
            PRIMARY KEY(evenement_id, tag_evenement_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add participant_id to inscription if missing
        $this->addSql('ALTER TABLE inscription ADD COLUMN IF NOT EXISTS participant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT IF NOT EXISTS FK_inscription_user FOREIGN KEY (participant_id) REFERENCES `user` (id)');

        // Drop old single tag column from evenement if it exists
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY IF EXISTS FK_evenement_tag');
        $this->addSql('ALTER TABLE evenement DROP COLUMN IF EXISTS tags_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS evenement_tag_evenement');
    }
}
