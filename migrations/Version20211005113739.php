<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211005113739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_charge_group (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, charge_distribution_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_BB7CAFF69B6B5FBA (account_id), UNIQUE INDEX UNIQ_BB7CAFF6AD3AA8DD (charge_distribution_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_resource_group (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_D123422E9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_charge_group ADD CONSTRAINT FK_BB7CAFF69B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_charge_group ADD CONSTRAINT FK_BB7CAFF6AD3AA8DD FOREIGN KEY (charge_distribution_id) REFERENCES bank_charge_distribution (id)');
        $this->addSql('ALTER TABLE bank_resource_group ADD CONSTRAINT FK_D123422E9B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_charge ADD charge_distribution_id INT DEFAULT NULL, ADD charge_group_id INT DEFAULT NULL, ADD month DATE DEFAULT NULL COMMENT \'(DC2Type:MonthType)\'');
        $this->addSql('ALTER TABLE bank_charge ADD CONSTRAINT FK_750E8B49AD3AA8DD FOREIGN KEY (charge_distribution_id) REFERENCES bank_charge_distribution (id)');
        $this->addSql('ALTER TABLE bank_charge ADD CONSTRAINT FK_750E8B4958BC65D4 FOREIGN KEY (charge_group_id) REFERENCES bank_charge_group (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_750E8B49AD3AA8DD ON bank_charge (charge_distribution_id)');
        $this->addSql('CREATE INDEX IDX_750E8B4958BC65D4 ON bank_charge (charge_group_id)');
        $this->addSql('ALTER TABLE bank_charge_distribution DROP FOREIGN KEY FK_9748605F55284914');
        $this->addSql('DROP INDEX UNIQ_9748605F55284914 ON bank_charge_distribution');
        $this->addSql('ALTER TABLE bank_charge_distribution DROP charge_id');
        $this->addSql('ALTER TABLE bank_resource ADD resource_group_id INT DEFAULT NULL, ADD month DATE DEFAULT NULL COMMENT \'(DC2Type:MonthType)\'');
        $this->addSql('ALTER TABLE bank_resource ADD CONSTRAINT FK_8DD1EE9150D813EA FOREIGN KEY (resource_group_id) REFERENCES bank_resource_group (id)');
        $this->addSql('CREATE INDEX IDX_8DD1EE9150D813EA ON bank_resource (resource_group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank_charge DROP FOREIGN KEY FK_750E8B4958BC65D4');
        $this->addSql('ALTER TABLE bank_resource DROP FOREIGN KEY FK_8DD1EE9150D813EA');
        $this->addSql('DROP TABLE bank_charge_group');
        $this->addSql('DROP TABLE bank_resource_group');
        $this->addSql('ALTER TABLE bank_charge DROP FOREIGN KEY FK_750E8B49AD3AA8DD');
        $this->addSql('DROP INDEX UNIQ_750E8B49AD3AA8DD ON bank_charge');
        $this->addSql('DROP INDEX IDX_750E8B4958BC65D4 ON bank_charge');
        $this->addSql('ALTER TABLE bank_charge DROP charge_distribution_id, DROP charge_group_id, DROP month');
        $this->addSql('ALTER TABLE bank_charge_distribution ADD charge_id INT NOT NULL');
        $this->addSql('ALTER TABLE bank_charge_distribution ADD CONSTRAINT FK_9748605F55284914 FOREIGN KEY (charge_id) REFERENCES bank_charge (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9748605F55284914 ON bank_charge_distribution (charge_id)');
        $this->addSql('DROP INDEX IDX_8DD1EE9150D813EA ON bank_resource');
        $this->addSql('ALTER TABLE bank_resource DROP resource_group_id, DROP month');
    }
}
