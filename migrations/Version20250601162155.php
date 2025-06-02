<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601162155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE passenger_confirmation (id INT AUTO_INCREMENT NOT NULL, passenger_id INT NOT NULL, car_sharing_id INT NOT NULL, confirmed TINYINT(1) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_96ED59354502E565 (passenger_id), INDEX IDX_96ED59357A2EDE41 (car_sharing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passenger_confirmation ADD CONSTRAINT FK_96ED59354502E565 FOREIGN KEY (passenger_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passenger_confirmation ADD CONSTRAINT FK_96ED59357A2EDE41 FOREIGN KEY (car_sharing_id) REFERENCES car_sharings (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE passenger_confirmation DROP FOREIGN KEY FK_96ED59354502E565
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE passenger_confirmation DROP FOREIGN KEY FK_96ED59357A2EDE41
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE passenger_confirmation
        SQL);
    }
}
