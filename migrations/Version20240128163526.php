<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128163526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE billing_row_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE billing_row (id INT NOT NULL, quote_id_id INT NOT NULL, product VARCHAR(255) NOT NULL, quantity INT NOT NULL, unit INT NOT NULL, price NUMERIC(10, 2) NOT NULL, vat NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9C038A9772BB1336 ON billing_row (quote_id_id)');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT FK_9C038A9772BB1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ALTER price SET NOT NULL');
        $this->addSql('ALTER TABLE quote ADD billing_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF479D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6B71CBF479D0C0E4 ON quote (billing_address_id)');
        $this->addSql('ALTER TABLE "user" ALTER activate DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER reset_password DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE billing_row_id_seq CASCADE');
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT FK_9C038A9772BB1336');
        $this->addSql('DROP TABLE billing_row');
        $this->addSql('ALTER TABLE "user" ALTER activate SET DEFAULT false');
        $this->addSql('ALTER TABLE "user" ALTER reset_password SET DEFAULT false');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF479D0C0E4');
        $this->addSql('DROP INDEX IDX_6B71CBF479D0C0E4');
        $this->addSql('ALTER TABLE quote DROP billing_address_id');
        $this->addSql('ALTER TABLE product ALTER price DROP NOT NULL');
    }
}
