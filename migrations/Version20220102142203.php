<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220102142203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(25) NOT NULL, prenom VARCHAR(25) NOT NULL, telephone VARCHAR(25) NOT NULL, adresse VARCHAR(255) NOT NULL, mail VARCHAR(70) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, statut_operation_id INT DEFAULT NULL, type_operation_id INT NOT NULL, utilisateur_id INT NOT NULL, libelle VARCHAR(25) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_1981A66DEBDF87BC (statut_operation_id), INDEX IDX_1981A66DC3EF8F86 (type_operation_id), INDEX IDX_1981A66DFB88E14F (utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation_client (operation_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_BAC4BD3744AC3583 (operation_id), INDEX IDX_BAC4BD3719EB6921 (client_id), PRIMARY KEY(operation_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut_operation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_operation (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(25) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(25) NOT NULL, prenom VARCHAR(25) NOT NULL, telephone VARCHAR(25) NOT NULL, adresse VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3D60322AC (role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DEBDF87BC FOREIGN KEY (statut_operation_id) REFERENCES statut_operation (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DC3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE operation_client ADD CONSTRAINT FK_BAC4BD3744AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_client ADD CONSTRAINT FK_BAC4BD3719EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation_client DROP FOREIGN KEY FK_BAC4BD3719EB6921');
        $this->addSql('ALTER TABLE operation_client DROP FOREIGN KEY FK_BAC4BD3744AC3583');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D60322AC');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DEBDF87BC');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DC3EF8F86');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DFB88E14F');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE operation_client');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE statut_operation');
        $this->addSql('DROP TABLE type_operation');
        $this->addSql('DROP TABLE utilisateur');
    }
}
