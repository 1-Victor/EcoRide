<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529155928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions ADD user_id INT NOT NULL, ADD admin_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions ADD CONSTRAINT FK_1B8C600EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions ADD CONSTRAINT FK_1B8C600E642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B8C600EA76ED395 ON suspensions (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1B8C600E642B8210 ON suspensions (admin_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions DROP FOREIGN KEY FK_1B8C600EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions DROP FOREIGN KEY FK_1B8C600E642B8210
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B8C600EA76ED395 ON suspensions
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1B8C600E642B8210 ON suspensions
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE suspensions DROP user_id, DROP admin_id
        SQL);
    }
}
