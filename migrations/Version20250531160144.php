<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250531160144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_car_sharings (user_id INT NOT NULL, car_sharings_id INT NOT NULL, INDEX IDX_5733EE8BA76ED395 (user_id), INDEX IDX_5733EE8BE6D0E2FD (car_sharings_id), PRIMARY KEY(user_id, car_sharings_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_car_sharings ADD CONSTRAINT FK_5733EE8BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_car_sharings ADD CONSTRAINT FK_5733EE8BE6D0E2FD FOREIGN KEY (car_sharings_id) REFERENCES car_sharings (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_car_sharings DROP FOREIGN KEY FK_5733EE8BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_car_sharings DROP FOREIGN KEY FK_5733EE8BE6D0E2FD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_car_sharings
        SQL);
    }
}
