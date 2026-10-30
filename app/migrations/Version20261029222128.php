<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261029222128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS project'); // because we are recreating it
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_translation (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(5) NOT NULL, title VARCHAR(255) NOT NULL, excerpt LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, content JSON NOT NULL, project_id INT NOT NULL, UNIQUE INDEX unique_project_locale_slug (project_id, locale, slug), INDEX IDX_7CA6B294166D1F9C (project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE project_translation ADD CONSTRAINT FK_7CA6B294166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_translation DROP FOREIGN KEY FK_7CA6B294166D1F9C');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_translation');
    }
}
