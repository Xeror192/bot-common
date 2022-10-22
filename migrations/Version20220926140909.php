<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220926140909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          type_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          customer_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          date_from DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          date_to DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bot_customers (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          username VARCHAR(255) NOT NULL,
          phone VARCHAR(255) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bot_yandex_emotions (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          code VARCHAR(30) NOT NULL,
          name VARCHAR(255) NOT NULL,
          score INT NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bot_yandex_observers (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          code VARCHAR(30) NOT NULL,
          enabled TINYINT(1) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bot_yandex_problems (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          code VARCHAR(30) NOT NULL,
          name VARCHAR(255) NOT NULL,
          score INT NOT NULL,
          keywords JSON NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bot_yandex_request_actions (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          observer_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          query VARCHAR(255) NOT NULL,
          arguments JSON NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_types (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          parent_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
          name VARCHAR(255) NOT NULL,
          duration INT NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_days (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\',
          date_from DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          date_to DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP TABLE bot_customers');
        $this->addSql('DROP TABLE bot_yandex_emotions');
        $this->addSql('DROP TABLE bot_yandex_observers');
        $this->addSql('DROP TABLE bot_yandex_problems');
        $this->addSql('DROP TABLE bot_yandex_request_actions');
        $this->addSql('DROP TABLE work_types');
        $this->addSql('DROP TABLE working_days');
    }
}
