<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250531010053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles DROP FOREIGN KEY FK_1FCE69FA44F5D008
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1FCE69FA44F5D008 ON vehicles
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD brand VARCHAR(100) NOT NULL, DROP brand_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD brand_id INT NOT NULL, DROP brand
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD CONSTRAINT FK_1FCE69FA44F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FCE69FA44F5D008 ON vehicles (brand_id)
        SQL);
    }
}
