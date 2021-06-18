<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210619062835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth_access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT NOT NULL, identifier VARCHAR(80) NOT NULL, expiry_date_time DATETIME NOT NULL, is_revoked TINYINT(1) NOT NULL, INDEX IDX_F7FA86A419EB6921 (client_id), INDEX IDX_F7FA86A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_client (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(100) NOT NULL, name VARCHAR(50) NOT NULL, redirect_uri VARCHAR(255) NOT NULL, is_actif TINYINT(1) NOT NULL, is_confidential TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_refresh_token (id INT AUTO_INCREMENT NOT NULL, access_token_id INT NOT NULL, identifier VARCHAR(255) NOT NULL, expiry_date_time DATETIME NOT NULL, is_revoked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_55DCF7552CCB2688 (access_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A419EB6921 FOREIGN KEY (client_id) REFERENCES oauth_client (id)');
        $this->addSql('ALTER TABLE oauth_access_token ADD CONSTRAINT FK_F7FA86A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE oauth_refresh_token ADD CONSTRAINT FK_55DCF7552CCB2688 FOREIGN KEY (access_token_id) REFERENCES oauth_access_token (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oauth_refresh_token DROP FOREIGN KEY FK_55DCF7552CCB2688');
        $this->addSql('ALTER TABLE oauth_access_token DROP FOREIGN KEY FK_F7FA86A419EB6921');
        $this->addSql('DROP TABLE oauth_access_token');
        $this->addSql('DROP TABLE oauth_client');
        $this->addSql('DROP TABLE oauth_refresh_token');
    }
}
