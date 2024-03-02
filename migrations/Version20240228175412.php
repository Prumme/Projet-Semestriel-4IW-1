<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228175412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_upload DROP CONSTRAINT fk_ac8b7b4c2989f1fd');
        $this->addSql('ALTER TABLE invoice_upload DROP CONSTRAINT fk_ac8b7b4ccccfba31');
        $this->addSql('DROP TABLE invoice_upload');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE invoice_upload (invoice_id INT NOT NULL, upload_id INT NOT NULL, PRIMARY KEY(invoice_id, upload_id))');
        $this->addSql('CREATE INDEX idx_ac8b7b4ccccfba31 ON invoice_upload (upload_id)');
        $this->addSql('CREATE INDEX idx_ac8b7b4c2989f1fd ON invoice_upload (invoice_id)');
        $this->addSql('ALTER TABLE invoice_upload ADD CONSTRAINT fk_ac8b7b4c2989f1fd FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_upload ADD CONSTRAINT fk_ac8b7b4ccccfba31 FOREIGN KEY (upload_id) REFERENCES upload (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
