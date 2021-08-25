<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210825163550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_account (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_53A23E0AB03A8386 (created_by_id), INDEX IDX_53A23E0A7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_account_share (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id INT NOT NULL, type ENUM(\'VIEW\', \'EDIT\') NOT NULL COMMENT \'(DC2Type:AccountShareType)\', INDEX IDX_532C15209B6B5FBA (account_id), INDEX IDX_532C1520A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_charge (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATE DEFAULT NULL, INDEX IDX_750E8B499B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_charge_distribution (id INT AUTO_INCREMENT NOT NULL, charge_id INT NOT NULL, type ENUM(\'VIEW\', \'FIFTY_FIFTY\', \'RESOURCE_PERCENT\') NOT NULL COMMENT \'(DC2Type:ChargeDistributionType)\', UNIQUE INDEX UNIQ_9748605F55284914 (charge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_charge_distribution_user (charge_distribution_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_115AC9EEAD3AA8DD (charge_distribution_id), INDEX IDX_115AC9EEA76ED395 (user_id), PRIMARY KEY(charge_distribution_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bank_resource (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, name VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATE DEFAULT NULL, INDEX IDX_8DD1EE919B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, identifier VARCHAR(80) NOT NULL, expiry_date_time DATETIME NOT NULL, is_revoked TINYINT(1) NOT NULL, INDEX IDX_F7FA86A419EB6921 (client_id), INDEX IDX_F7FA86A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_client (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(100) NOT NULL, name VARCHAR(50) NOT NULL, redirect_uri VARCHAR(255) NOT NULL, is_actif TINYINT(1) NOT NULL, is_confidential TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_refresh_token (id INT AUTO_INCREMENT NOT NULL, access_token_id INT NOT NULL, identifier VARCHAR(255) NOT NULL, expiry_date_time DATETIME NOT NULL, is_revoked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_55DCF7552CCB2688 (access_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, is_admin TINYINT(1) NOT NULL, password VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0AB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bank_account ADD CONSTRAINT FK_53A23E0A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bank_account_share ADD CONSTRAINT FK_532C15209B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_account_share ADD CONSTRAINT FK_532C1520A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bank_charge ADD CONSTRAINT FK_750E8B499B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_charge_distribution ADD CONSTRAINT FK_9748605F55284914 FOREIGN KEY (charge_id) REFERENCES bank_charge (id)');
        $this->addSql('ALTER TABLE bank_charge_distribution_user ADD CONSTRAINT FK_115AC9EEAD3AA8DD FOREIGN KEY (charge_distribution_id) REFERENCES bank_charge_distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_charge_distribution_user ADD CONSTRAINT FK_115AC9EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bank_resource ADD CONSTRAINT FK_8DD1EE919B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A419EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE oauth_refresh_token ADD CONSTRAINT FK_55DCF7552CCB2688 FOREIGN KEY (access_token_id) REFERENCES oauth_access_token (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bank_account_share DROP FOREIGN KEY FK_532C15209B6B5FBA');
        $this->addSql('ALTER TABLE bank_charge DROP FOREIGN KEY FK_750E8B499B6B5FBA');
        $this->addSql('ALTER TABLE bank_resource DROP FOREIGN KEY FK_8DD1EE919B6B5FBA');
        $this->addSql('ALTER TABLE bank_charge_distribution DROP FOREIGN KEY FK_9748605F55284914');
        $this->addSql('ALTER TABLE bank_charge_distribution_user DROP FOREIGN KEY FK_115AC9EEAD3AA8DD');
        $this->addSql('ALTER TABLE oauth_refresh_token DROP FOREIGN KEY FK_55DCF7552CCB2688');
        $this->addSql('ALTER TABLE oauth_access_token DROP FOREIGN KEY FK_F7FA86A419EB6921');
        $this->addSql('ALTER TABLE bank_account DROP FOREIGN KEY FK_53A23E0AB03A8386');
        $this->addSql('ALTER TABLE bank_account DROP FOREIGN KEY FK_53A23E0A7E3C61F9');
        $this->addSql('ALTER TABLE bank_account_share DROP FOREIGN KEY FK_532C1520A76ED395');
        $this->addSql('ALTER TABLE bank_charge_distribution_user DROP FOREIGN KEY FK_115AC9EEA76ED395');
        $this->addSql('ALTER TABLE oauth_access_token DROP FOREIGN KEY FK_F7FA86A4A76ED395');
        $this->addSql('DROP TABLE bank_account');
        $this->addSql('DROP TABLE bank_account_share');
        $this->addSql('DROP TABLE bank_charge');
        $this->addSql('DROP TABLE bank_charge_distribution');
        $this->addSql('DROP TABLE bank_charge_distribution_user');
        $this->addSql('DROP TABLE bank_resource');
        $this->addSql('DROP TABLE oauth_access_token');
        $this->addSql('DROP TABLE oauth_client');
        $this->addSql('DROP TABLE oauth_refresh_token');
        $this->addSql('DROP TABLE user');
    }
}
