<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181216112855 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, title, description, owner, complete_till_ts, insert_ts FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (
                                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                                title VARCHAR(255) NOT NULL COLLATE BINARY,
                                description VARCHAR(255) DEFAULT NULL COLLATE BINARY,
                                priority SMALLINT NOT NULL,
                                owner VARCHAR(255) DEFAULT NULL COLLATE BINARY,
                                complete_till_ts DATE DEFAULT NULL,
                                done BOOLEAN NOT NULL,
                                insert_ts DATETIME NOT NULL,
                                update_ts DATETIME NOT NULL)');
        $this->addSql('INSERT INTO todo (id, title, description, owner, complete_till_ts, insert_ts) SELECT id, title, description, owner, complete_till_ts, insert_ts FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
        $this->addSql('INSERT INTO todo (title, description, priority, owner, complete_till_ts, done, insert_ts, update_ts) VALUES
                            ("Eine Todoliste entwickeln", "Zur Präsentation", 1, "Markus", "2018-12-18", 1, strftime(\'%Y-%m-%d %H:%M:%S\',\'now\'), strftime(\'%Y-%m-%d %H:%M:%S\',\'now\'))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, title, description, owner, complete_till_ts, insert_ts FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, owner VARCHAR(255) DEFAULT NULL, complete_till_ts DATETIME DEFAULT NULL, insert_ts DATETIME DEFAULT CURRENT_TIMESTAMP)');
        $this->addSql('INSERT INTO todo (id, title, description, owner, complete_till_ts, insert_ts) SELECT id, title, description, owner, complete_till_ts, insert_ts FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
    }
}
