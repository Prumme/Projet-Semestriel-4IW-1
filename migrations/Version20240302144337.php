<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302144337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing_address DROP CONSTRAINT FK_6660E4569395C3F3');
        $this->addSql('ALTER TABLE billing_address ADD CONSTRAINT FK_6660E4569395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF479D0C0E4');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF47E3C61F9');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF479D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF47E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf479d0c0e4');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf47e3c61f9');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf479d0c0e4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf47e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_address DROP CONSTRAINT fk_6660e4569395c3f3');
        $this->addSql('ALTER TABLE billing_address ADD CONSTRAINT fk_6660e4569395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
