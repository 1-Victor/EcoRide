<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529155743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD author_id INT NOT NULL, ADD target_user_id INT NOT NULL, ADD car_sharing_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F6C066AFE FOREIGN KEY (target_user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F7A2EDE41 FOREIGN KEY (car_sharing_id) REFERENCES car_sharings (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6970EB0FF675F31B ON reviews (author_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6970EB0F6C066AFE ON reviews (target_user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6970EB0F7A2EDE41 ON reviews (car_sharing_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FF675F31B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F6C066AFE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F7A2EDE41
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6970EB0FF675F31B ON reviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6970EB0F6C066AFE ON reviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6970EB0F7A2EDE41 ON reviews
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reviews DROP author_id, DROP target_user_id, DROP car_sharing_id
        SQL);
    }
}
