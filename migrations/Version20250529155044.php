<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529155044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings ADD user_id INT NOT NULL, ADD vehicle_id INT NOT NULL, ADD status_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF9289A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF9289545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicles (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF92896BF700BD FOREIGN KEY (status_id) REFERENCES car_sharing_states (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7FF9289A76ED395 ON car_sharings (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7FF9289545317D1 ON car_sharings (vehicle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D7FF92896BF700BD ON car_sharings (status_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF9289A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF9289545317D1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF92896BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D7FF9289A76ED395 ON car_sharings
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D7FF9289545317D1 ON car_sharings
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D7FF92896BF700BD ON car_sharings
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_sharings DROP user_id, DROP vehicle_id, DROP status_id
        SQL);
    }
}
