<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajoute la table des mentions légales éditables depuis l'administration.
 * Contenu et titre stockés en base afin d'éviter tout texte métier en dur dans les templates.
 */
final class Version20260913112500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table mentions_legales éditable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE IF NOT EXISTS mentions_legales_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $table = $schema->createTable('mentions_legales');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $table->addColumn('page_name', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('meta_title', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('meta_description', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('title', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('content', 'text', ['notnull' => true]);
        $table->addColumn('updated_at', 'datetime_immutable', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['page_name'], 'UNIQ_MENTIONS_LEGALES_PAGE_NAME');
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('mentions_legales')) {
            $schema->dropTable('mentions_legales');
        }
        $this->addSql('DROP SEQUENCE IF EXISTS mentions_legales_id_seq CASCADE');
    }
}
