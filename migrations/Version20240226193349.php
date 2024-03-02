<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226193349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE discount_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE discount (id INT NOT NULL, type INT NOT NULL, value NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE billing_address DROP CONSTRAINT FK_6660E4569395C3F3');
        $this->addSql('ALTER TABLE billing_address ADD CONSTRAINT FK_6660E4569395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT FK_9C038A9772BB1336');
        $this->addSql('ALTER TABLE billing_row ADD discount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT FK_9C038A974C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT FK_9C038A9772BB1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C038A974C7C611F ON billing_row (discount_id)');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF479D0C0E4');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF47E3C61F9');
        $this->addSql('ALTER TABLE quote ADD discounts_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF46A35CCB1 FOREIGN KEY (discounts_id) REFERENCES discount (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF479D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF47E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6B71CBF46A35CCB1 ON quote (discounts_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT FK_9C038A974C7C611F');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF46A35CCB1');
        $this->addSql('DROP SEQUENCE discount_id_seq CASCADE');
        $this->addSql('DROP TABLE discount');
        $this->addSql('ALTER TABLE billing_address DROP CONSTRAINT fk_6660e4569395c3f3');
        $this->addSql('ALTER TABLE billing_address ADD CONSTRAINT fk_6660e4569395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf479d0c0e4');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf47e3c61f9');
        $this->addSql('DROP INDEX IDX_6B71CBF46A35CCB1');
        $this->addSql('ALTER TABLE quote DROP discounts_id');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf479d0c0e4 FOREIGN KEY (billing_address_id) REFERENCES billing_address (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf47e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE billing_row DROP CONSTRAINT fk_9c038a9772bb1336');
        $this->addSql('DROP INDEX UNIQ_9C038A974C7C611F');
        $this->addSql('ALTER TABLE billing_row DROP discount_id');
        $this->addSql('ALTER TABLE billing_row ADD CONSTRAINT fk_9c038a9772bb1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
