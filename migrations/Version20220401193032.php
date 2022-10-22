<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401193032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_sessions (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          refresh_token VARCHAR(255) DEFAULT NULL,
          finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE confirmation_codes (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          code VARCHAR(255) NOT NULL,
          attempt VARCHAR(255) NOT NULL,
          valid DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          email VARCHAR(255) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_roles (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          name VARCHAR(255) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customers (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          role_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          instagram VARCHAR(255) DEFAULT NULL,
          telegram VARCHAR(255) DEFAULT NULL,
          first_name VARCHAR(255) DEFAULT NULL,
          second_name VARCHAR(100) DEFAULT NULL,
          avatar VARCHAR(100) DEFAULT NULL,
          UNIQUE INDEX UNIQ_62534E21ABFE1C6F (user_uuid),
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sms (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          text VARCHAR(255) NOT NULL,
          is_sent TINYINT(1) NOT NULL,
          phone VARCHAR(255) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (
          uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\',
          password VARCHAR(255) NOT NULL,
          last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
          email VARCHAR(255) NOT NULL,
          PRIMARY KEY(uuid)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auth_sessions');
        $this->addSql('DROP TABLE confirmation_codes');
        $this->addSql('DROP TABLE customer_roles');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE sms');
        $this->addSql('DROP TABLE users');
    }
}
