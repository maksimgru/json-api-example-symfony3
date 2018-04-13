<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413201010 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, home_team_id INT NOT NULL, away_team_id INT NOT NULL, start_at DATETIME DEFAULT NULL, score VARCHAR(255) DEFAULT NULL, INDEX IDX_B50A2CB19C4C13F6 (home_team_id), INDEX IDX_B50A2CB145185D02 (away_team_id), UNIQUE INDEX home_away_start_uniq (home_team_id, away_team_id, start_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, place INT NOT NULL, played INT NOT NULL, wins INT NOT NULL, draws INT NOT NULL, losses INT NOT NULL, points INT NOT NULL, UNIQUE INDEX UNIQ_C4E0A61F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB19C4C13F6 FOREIGN KEY (home_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB145185D02 FOREIGN KEY (away_team_id) REFERENCES team (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB19C4C13F6');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB145185D02');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE team');
    }
}
