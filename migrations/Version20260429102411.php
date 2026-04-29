<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260429102411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, note INT NOT NULL, created_at DATETIME NOT NULL, utilisateur_id INT DEFAULT NULL, INDEX IDX_8F91ABF0FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE carte_trello ADD CONSTRAINT FK_EDF750FD213EAC9D FOREIGN KEY (colonne_id) REFERENCES colonne_trello (id)');
        $this->addSql('ALTER TABLE carte_trello ADD CONSTRAINT FK_EDF750FDFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0FB88E14F');
        $this->addSql('DROP TABLE avis');
        $this->addSql('ALTER TABLE carte_trello DROP FOREIGN KEY FK_EDF750FD213EAC9D');
        $this->addSql('ALTER TABLE carte_trello DROP FOREIGN KEY FK_EDF750FDFB88E14F');
    }
}
