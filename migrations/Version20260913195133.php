<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Crée les tables métier pour PostgreSQL.
 * La table mentions_legales est déjà créée par Version20260913112500.
 */
final class Version20260913195133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables métier (PostgreSQL) — mentions_legales créée par migration précédente';
    }

    public function up(Schema $schema): void
    {
        // about
        $this->addSql('CREATE TABLE about (id SERIAL PRIMARY KEY NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL)');

        // boisson
        $this->addSql('CREATE TABLE boisson (id SERIAL PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, category VARCHAR(50) DEFAULT NULL, capacity VARCHAR(20) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) DEFAULT NULL)');

        // hero
        $this->addSql('CREATE TABLE hero (id SERIAL PRIMARY KEY NOT NULL, title VARCHAR(255) NOT NULL, sub_title VARCHAR(255) NOT NULL, background VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) DEFAULT NULL)');

        // mentions_legales est créée par Version20260913112500 — on ne la recrée pas ici

        // news
        $this->addSql('CREATE TABLE news (id SERIAL PRIMARY KEY NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) NOT NULL, updated_at TIMESTAMP(0) DEFAULT NULL)');

        // pizza
        $this->addSql('CREATE TABLE pizza (id SERIAL PRIMARY KEY NOT NULL, name VARCHAR(255) NOT NULL, ingredient TEXT NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) DEFAULT NULL, price_medium DOUBLE PRECISION NOT NULL, price_large DOUBLE PRECISION NOT NULL, is_special BOOLEAN NOT NULL)');

        // seo
        $this->addSql('CREATE TABLE seo (id SERIAL PRIMARY KEY NOT NULL, page_name VARCHAR(255) NOT NULL, meta_title VARCHAR(255) NOT NULL, meta_description VARCHAR(255) DEFAULT NULL)');

        // user (mot-clé réservé → guillemets)
        $this->addSql('CREATE TABLE "user" (id SERIAL PRIMARY KEY NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');

        // messenger_messages
        $this->addSql('CREATE TABLE messenger_messages (id SERIAL PRIMARY KEY NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) NOT NULL, available_at TIMESTAMP(0) NOT NULL, delivered_at TIMESTAMP(0) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE seo');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE hero');
        $this->addSql('DROP TABLE boisson');
        $this->addSql('DROP TABLE about');
    }
}
