<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209160926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF479D0C0E4');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF479D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf479d0c0e4');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf479d0c0e4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
