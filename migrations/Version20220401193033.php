<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401193033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("INSERT INTO `customer_roles` (`uuid`, `name`) VALUES ('56c00e15-4d2d-48f6-a151-dbf4a455fb54', 'Пользователь');
INSERT INTO `customer_roles` (`uuid`, `name`) VALUES ('f21fc30e-0044-4d00-ba92-146766b805ca', 'Автор');
");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE customer_roles');
    }
}
