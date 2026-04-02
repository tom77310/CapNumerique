<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402092508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_trello ADD CONSTRAINT FK_EDF750FD213EAC9D FOREIGN KEY (colonne_id) REFERENCES colonne_trello (id)');
        $this->addSql('ALTER TABLE utilisateur ADD sexe VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_trello DROP FOREIGN KEY FK_EDF750FD213EAC9D');
        $this->addSql('ALTER TABLE utilisateur DROP sexe');
    }
}
