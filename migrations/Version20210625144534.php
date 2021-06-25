<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625144534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, owner_id INT NOT NULL, INDEX IDX_53A23E0A7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_charge (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATE DEFAULT NULL, INDEX IDX_750E8B499B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_resource (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATE DEFAULT NULL, INDEX IDX_8DD1EE919B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bank_charge ADD CONSTRAINT FK_750E8B499B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_resource ADD CONSTRAINT FK_8DD1EE919B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank_charge DROP FOREIGN KEY FK_750E8B499B6B5FBA');
        $this->addSql('ALTER TABLE bank_resource DROP FOREIGN KEY FK_8DD1EE919B6B5FBA');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE bank_charge');
        $this->addSql('DROP TABLE bank_resource');
    }
}
