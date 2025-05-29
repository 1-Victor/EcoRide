<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250529161116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD user_id INT NOT NULL, ADD brand_id INT NOT NULL, ADD energy_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD CONSTRAINT FK_1FCE69FAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD CONSTRAINT FK_1FCE69FA44F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles ADD CONSTRAINT FK_1FCE69FAEDDF52D FOREIGN KEY (energy_id) REFERENCES energies (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FCE69FAA76ED395 ON vehicles (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FCE69FA44F5D008 ON vehicles (brand_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1FCE69FAEDDF52D ON vehicles (energy_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles DROP FOREIGN KEY FK_1FCE69FAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles DROP FOREIGN KEY FK_1FCE69FA44F5D008
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles DROP FOREIGN KEY FK_1FCE69FAEDDF52D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1FCE69FAA76ED395 ON vehicles
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1FCE69FA44F5D008 ON vehicles
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_1FCE69FAEDDF52D ON vehicles
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE vehicles DROP user_id, DROP brand_id, DROP energy_id
        SQL);
    }
}
