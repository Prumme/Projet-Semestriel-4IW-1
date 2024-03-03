<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303174848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT FK_BDA1C9FDB805178');
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT FK_BDA1C9F4C7C611F');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT FK_BDA1C9FDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT FK_BDA1C9F4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT fk_bda1c9fdb805178');
        $this->addSql('ALTER TABLE quote_discount DROP CONSTRAINT fk_bda1c9f4c7c611f');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT fk_bda1c9fdb805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_discount ADD CONSTRAINT fk_bda1c9f4c7c611f FOREIGN KEY (discount_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
