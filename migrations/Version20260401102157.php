<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260401102157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carte_trello (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, position INT NOT NULL, colonne_id INT NOT NULL, INDEX IDX_EDF750FD213EAC9D (colonne_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE colonne_trello (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, position INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE carte_trello ADD CONSTRAINT FK_EDF750FD213EAC9D FOREIGN KEY (colonne_id) REFERENCES colonne_trello (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carte_trello DROP FOREIGN KEY FK_EDF750FD213EAC9D');
        $this->addSql('DROP TABLE carte_trello');
        $this->addSql('DROP TABLE colonne_trello');
    }
}
