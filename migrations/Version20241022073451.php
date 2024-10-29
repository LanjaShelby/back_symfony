<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241022073451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reply (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, statut VARCHAR(255) NOT NULL, message_reply LONGTEXT NOT NULL, sender_name VARCHAR(255) NOT NULL, recipient_name VARCHAR(255) NOT NULL, sender_service VARCHAR(255) NOT NULL, recipient_service VARCHAR(255) NOT NULL, INDEX IDX_FDA8C6E0F624B39D (sender_id), INDEX IDX_FDA8C6E0E92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0F624B39D FOREIGN KEY (sender_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0E92F8F78 FOREIGN KEY (recipient_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0F624B39D');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0E92F8F78');
        $this->addSql('DROP TABLE reply');
    }
}
