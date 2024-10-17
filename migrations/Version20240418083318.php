<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418083318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit_type_produit (produit_id INT NOT NULL, type_produit_id INT NOT NULL, INDEX IDX_EB2FDCAFF347EFB (produit_id), INDEX IDX_EB2FDCAF1237A8DE (type_produit_id), PRIMARY KEY(produit_id, type_produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_type_produit ADD CONSTRAINT FK_EB2FDCAFF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_type_produit ADD CONSTRAINT FK_EB2FDCAF1237A8DE FOREIGN KEY (type_produit_id) REFERENCES type_produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit_type_produit DROP FOREIGN KEY FK_EB2FDCAFF347EFB');
        $this->addSql('ALTER TABLE produit_type_produit DROP FOREIGN KEY FK_EB2FDCAF1237A8DE');
        $this->addSql('DROP TABLE produit_type_produit');
        $this->addSql('DROP TABLE type_produit');
    }
}
