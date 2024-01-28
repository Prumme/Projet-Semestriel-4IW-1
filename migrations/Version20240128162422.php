<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128162422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE quote_signature_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quote_signature (id INT NOT NULL, data_base64_uri TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE quote ADD signature_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4ED61183A FOREIGN KEY (signature_id) REFERENCES quote_signature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B71CBF4ED61183A ON quote (signature_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF4ED61183A');
        $this->addSql('DROP SEQUENCE quote_signature_id_seq CASCADE');
        $this->addSql('DROP TABLE quote_signature');
        $this->addSql('DROP INDEX UNIQ_6B71CBF4ED61183A');
        $this->addSql('ALTER TABLE quote DROP signature_id');
    }
}
