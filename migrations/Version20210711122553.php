<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210711122553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bank_charge_distribution (id INT AUTO_INCREMENT NOT NULL, charge_id INT NOT NULL, type ENUM(\'VIEW\', \'FIFTY_FIFTY\', \'RESOURCE_PERCENT\') NOT NULL COMMENT \'(DC2Type:ChargeDistributionType)\', UNIQUE INDEX UNIQ_9748605F55284914 (charge_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE charge_distribution_user (charge_distribution_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_865FD6DAD3AA8DD (charge_distribution_id), INDEX IDX_865FD6DA76ED395 (user_id), PRIMARY KEY(charge_distribution_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bank_charge_distribution ADD CONSTRAINT FK_9748605F55284914 FOREIGN KEY (charge_id) REFERENCES bank_charge (id)');
        $this->addSql('ALTER TABLE charge_distribution_user ADD CONSTRAINT FK_865FD6DAD3AA8DD FOREIGN KEY (charge_distribution_id) REFERENCES bank_charge_distribution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE charge_distribution_user ADD CONSTRAINT FK_865FD6DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE charge_distribution_user DROP FOREIGN KEY FK_865FD6DAD3AA8DD');
        $this->addSql('DROP TABLE bank_charge_distribution');
        $this->addSql('DROP TABLE charge_distribution_user');
    }
}
