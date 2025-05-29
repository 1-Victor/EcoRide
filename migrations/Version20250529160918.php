<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529160918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences ADD user_id INT NOT NULL, ADD preference_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F60A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F60D81022C0 FOREIGN KEY (preference_id) REFERENCES preferences (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_402A6F60A76ED395 ON user_preferences (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_402A6F60D81022C0 ON user_preferences (preference_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences DROP FOREIGN KEY FK_402A6F60A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences DROP FOREIGN KEY FK_402A6F60D81022C0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_402A6F60A76ED395 ON user_preferences
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_402A6F60D81022C0 ON user_preferences
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_preferences DROP user_id, DROP preference_id
        SQL);
    }
}
