<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302163544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT FK_9C038A9772BB1336');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT FK_9C038A9772BB1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT fk_9c038a9772bb1336');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT fk_9c038a9772bb1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
