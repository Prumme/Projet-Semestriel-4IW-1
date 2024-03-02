<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226202809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE quote_discount_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quote_discount (id INT NOT NULL, quote_id INT DEFAULT NULL, discount_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BDA1C9FDB805178 ON quote_discount (quote_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDA1C9F4C7C611F ON quote_discount (discount_id)');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT FK_BDA1C9FDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT FK_BDA1C9F4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf46a35ccb1');
        $this->addSql('DROP INDEX idx_6b71cbf46a35ccb1');
        $this->addSql('ALTER TABLE quote DROP discounts_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE quote_discount_id_seq CASCADE');
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT FK_BDA1C9FDB805178');
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT FK_BDA1C9F4C7C611F');
        $this->addSql('DROP TABLE quote_discount');
        $this->addSql('ALTER TABLE quote ADD discounts_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf46a35ccb1 FOREIGN KEY (discounts_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6b71cbf46a35ccb1 ON quote (discounts_id)');
    }
}
