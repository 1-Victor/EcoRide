<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529155549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD user_id INT NOT NULL, ADD car_sharing_id INT NOT NULL, ADD state_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA2397A2EDE41 FOREIGN KEY (car_sharing_id) REFERENCES car_sharings (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations ADD CONSTRAINT FK_4DA2395D83CC1 FOREIGN KEY (state_id) REFERENCES reservation_states (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4DA239A76ED395 ON reservations (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4DA2397A2EDE41 ON reservations (car_sharing_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4DA2395D83CC1 ON reservations (state_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2397A2EDE41
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2395D83CC1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4DA239A76ED395 ON reservations
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4DA2397A2EDE41 ON reservations
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4DA2395D83CC1 ON reservations
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservations DROP user_id, DROP car_sharing_id, DROP state_id
        SQL);
    }
}
