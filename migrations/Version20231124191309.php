<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231124191309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE billing_address_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE company_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE upload_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE billing_address (id INT NOT NULL, customer_id INT NOT NULL, city VARCHAR(100) NOT NULL, zip_code INT NOT NULL, country_code VARCHAR(2) NOT NULL, address_line_1 VARCHAR(255) NOT NULL, address_line_2 VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6660E4569395C3F3 ON billing_address (customer_id)');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, name VARCHAR(255) NOT NULL, siret VARCHAR(14) DEFAULT NULL, vat_number VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, company_id INT NOT NULL, lastname VARCHAR(45) NOT NULL, firstname VARCHAR(45) NOT NULL, company_name VARCHAR(255) DEFAULT NULL, company_siret VARCHAR(14) DEFAULT NULL, company_vat_number VARCHAR(15) DEFAULT NULL, tel VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81398E09979B1AD6 ON customer (company_id)');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, quote_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90651744DB805178 ON invoice (quote_id)');
        $this->addSql('CREATE TABLE invoice_upload (invoice_id INT NOT NULL, upload_id INT NOT NULL, PRIMARY KEY(invoice_id, upload_id))');
        $this->addSql('CREATE INDEX IDX_AC8B7B4C2989F1FD ON invoice_upload (invoice_id)');
        $this->addSql('CREATE INDEX IDX_AC8B7B4CCCCFBA31 ON invoice_upload (upload_id)');
        $this->addSql('CREATE TABLE payment (id INT NOT NULL, invoice_id INT DEFAULT NULL, payed_amount NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)');
        $this->addSql('CREATE TABLE quote (id INT NOT NULL, emited_at DATE NOT NULL, expired_at DATE NOT NULL, has_been_signed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE upload (id INT NOT NULL, path VARCHAR(255) DEFAULT NULL, checksum TEXT DEFAULT NULL, mime VARCHAR(128) DEFAULT NULL, ext VARCHAR(5) NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE billing_address ADD CONSTRAINT FK_6660E4569395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_upload ADD CONSTRAINT FK_AC8B7B4C2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_upload ADD CONSTRAINT FK_AC8B7B4CCCCFBA31 FOREIGN KEY (upload_id) REFERENCES upload (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649979B1AD6 ON "user" (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649979B1AD6');
        $this->addSql('DROP SEQUENCE billing_address_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE company_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quote_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE upload_id_seq CASCADE');
        $this->addSql('ALTER TABLE billing_address DROP CONSTRAINT FK_6660E4569395C3F3');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09979B1AD6');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744DB805178');
        $this->addSql('ALTER TABLE invoice_upload DROP CONSTRAINT FK_AC8B7B4C2989F1FD');
        $this->addSql('ALTER TABLE invoice_upload DROP CONSTRAINT FK_AC8B7B4CCCCFBA31');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D2989F1FD');
        $this->addSql('DROP TABLE billing_address');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_upload');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE upload');
        $this->addSql('DROP INDEX IDX_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE "user" DROP company_id');
    }
}
