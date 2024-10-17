<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422101513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pret_produit (pret_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_5B9F23771B61704B (pret_id), INDEX IDX_5B9F2377F347EFB (produit_id), PRIMARY KEY(pret_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pret_produit ADD CONSTRAINT FK_5B9F23771B61704B FOREIGN KEY (pret_id) REFERENCES pret (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pret_produit ADD CONSTRAINT FK_5B9F2377F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pret_produit DROP FOREIGN KEY FK_5B9F23771B61704B');
        $this->addSql('ALTER TABLE pret_produit DROP FOREIGN KEY FK_5B9F2377F347EFB');
        $this->addSql('DROP TABLE pret_produit');
    }
}
