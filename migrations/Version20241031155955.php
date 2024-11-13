<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241031155955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, message_id_id INT NOT NULL, requester_id_id INT DEFAULT NULL, service_id INT DEFAULT NULL, action_type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BF5476CA80E261BC (message_id_id), INDEX IDX_BF5476CA9C0CF0F6 (requester_id_id), INDEX IDX_BF5476CAED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA80E261BC FOREIGN KEY (message_id_id) REFERENCES messages (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA9C0CF0F6 FOREIGN KEY (requester_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAED5CA9E6 FOREIGN KEY (service_id) REFERENCES services (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA80E261BC');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA9C0CF0F6');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAED5CA9E6');
        $this->addSql('DROP TABLE notification');
    }
}
