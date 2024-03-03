<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220133850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE price_id_seq CASCADE');
        $this->addSql('ALTER TABLE price DROP CONSTRAINT fk_cac822d94584665a');
        $this->addSql('DROP TABLE price');
        $this->addSql('ALTER TABLE product ADD price DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE price_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE price (id INT NOT NULL, product_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(45) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_cac822d94584665a ON price (product_id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT fk_cac822d94584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product DROP price');
    }
}
