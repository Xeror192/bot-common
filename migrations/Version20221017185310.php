<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017185310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking_notifications (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          booking_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          customer_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          is_sent TINYINT(1) NOT NULL,
          message TINYINT(1) NOT NULL,
          buttons JSON NOT NULL,
          params JSON NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE booking_notifications');
    }
}
