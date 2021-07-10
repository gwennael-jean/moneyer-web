<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210708162938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_account_share (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, user_id INT NOT NULL, type ENUM(\'VIEW\', \'EDIT\') NOT NULL COMMENT \'(DC2Type:AccountShareType)\', INDEX IDX_532C15209B6B5FBA (account_id), INDEX IDX_532C1520A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_account_share ADD CONSTRAINT FK_532C15209B6B5FBA FOREIGN KEY (account_id) REFERENCES bank_account (id)');
        $this->addSql('ALTER TABLE bank_account_share ADD CONSTRAINT FK_532C1520A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bank_account_share');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}