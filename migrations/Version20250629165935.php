<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629165935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__calendar AS SELECT id, owner_id, title, description, position, color, created_at, updated_at FROM calendar');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('CREATE TABLE calendar (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INTEGER NOT NULL, color VARCHAR(6) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_6EA9A1467E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO calendar (id, owner_id, title, description, position, color, created_at, updated_at) SELECT id, owner_id, title, description, position, color, created_at, updated_at FROM __temp__calendar');
        $this->addSql('DROP TABLE __temp__calendar');
        $this->addSql('CREATE INDEX IDX_6EA9A1467E3C61F9 ON calendar (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, calendar_id, owner_id, title, description, start_date, start_time, end_date, end_time, created_at, updated_at FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, calendar_id INTEGER NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , start_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_3BAE0AA7A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3BAE0AA77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO event (id, calendar_id, owner_id, title, description, start_date, start_time, end_date, end_time, created_at, updated_at) SELECT id, calendar_id, owner_id, title, description, start_date, start_time, end_date, end_time, created_at, updated_at FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
        $this->addSql('CREATE INDEX IDX_3BAE0AA77E3C61F9 ON event (owner_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7A40A2C8 ON event (calendar_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, owner_id, title, description, due_date, due_time, completed, created_at, updated_at FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, due_date DATE NOT NULL --(DC2Type:date_immutable)
        , due_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , completed BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, owner_id, title, description, due_date, due_time, completed, created_at, updated_at) SELECT id, owner_id, title, description, due_date, due_time, completed, created_at, updated_at FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB257E3C61F9 ON task (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, first_name, last_name, email, roles, password, created_at, updated_at FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO user (id, first_name, last_name, email, roles, password, created_at, updated_at) SELECT id, first_name, last_name, email, roles, password, created_at, updated_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__calendar AS SELECT id, owner_id, created_at, updated_at, title, description, position, color FROM calendar');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('CREATE TABLE calendar (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INTEGER NOT NULL, color VARCHAR(6) DEFAULT NULL, CONSTRAINT FK_6EA9A1467E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO calendar (id, owner_id, created_at, updated_at, title, description, position, color) SELECT id, owner_id, created_at, updated_at, title, description, position, color FROM __temp__calendar');
        $this->addSql('DROP TABLE __temp__calendar');
        $this->addSql('CREATE INDEX IDX_6EA9A1467E3C61F9 ON calendar (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, calendar_id, owner_id, created_at, updated_at, title, description, start_date, start_time, end_date, end_time FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, calendar_id INTEGER NOT NULL, owner_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
        , start_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , end_date DATE NOT NULL --(DC2Type:date_immutable)
        , end_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , CONSTRAINT FK_3BAE0AA7A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3BAE0AA77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO event (id, calendar_id, owner_id, created_at, updated_at, title, description, start_date, start_time, end_date, end_time) SELECT id, calendar_id, owner_id, created_at, updated_at, title, description, start_date, start_time, end_date, end_time FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7A40A2C8 ON event (calendar_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA77E3C61F9 ON event (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__task AS SELECT id, owner_id, created_at, updated_at, title, description, due_date, due_time, completed FROM task');
        $this->addSql('DROP TABLE task');
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, title VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, due_date DATE NOT NULL --(DC2Type:date_immutable)
        , due_time TIME DEFAULT NULL --(DC2Type:time_immutable)
        , completed BOOLEAN NOT NULL, CONSTRAINT FK_527EDB257E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO task (id, owner_id, created_at, updated_at, title, description, due_date, due_time, completed) SELECT id, owner_id, created_at, updated_at, title, description, due_date, due_time, completed FROM __temp__task');
        $this->addSql('DROP TABLE __temp__task');
        $this->addSql('CREATE INDEX IDX_527EDB257E3C61F9 ON task (owner_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, created_at, updated_at, first_name, last_name, email, roles, password FROM "user"');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('CREATE TABLE "user" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO "user" (id, created_at, updated_at, first_name, last_name, email, roles, password) SELECT id, created_at, updated_at, first_name, last_name, email, roles, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    }
}
