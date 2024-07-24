<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724154539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE addresses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE addresses (id INT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, zip_code VARCHAR(10) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN addresses.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN addresses.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE companies ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE companies DROP address');
        $this->addSql('ALTER TABLE companies ADD CONSTRAINT FK_8244AA3AF5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8244AA3AF5B7AF75 ON companies (address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE companies DROP CONSTRAINT FK_8244AA3AF5B7AF75');
        $this->addSql('DROP SEQUENCE addresses_id_seq CASCADE');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP INDEX UNIQ_8244AA3AF5B7AF75');
        $this->addSql('ALTER TABLE companies ADD address INT NOT NULL');
        $this->addSql('ALTER TABLE companies DROP address_id');
    }
}
