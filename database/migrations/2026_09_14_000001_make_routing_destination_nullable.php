<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rend service_destination_id NULLABLE pour permettre le routage
     * hiérarchique en 2 temps :
     *  - Étape 1 (BO/Admin -> Chefs de Département) : department_id rempli,
     *    service_destination_id = NULL (en attente de sous-routage).
     *  - Étape 2 (Chef Département -> Chefs de Service) : les deux remplis.
     *
     * Compatible MySQL + SQLite (rebuild pour SQLite qui ne supporte
     * pas MODIFY COLUMN sans doctrine/dbal).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildSqlite();
            return;
        }

        // MySQL : MODIFY sans doctrine/dbal (change() nécessiterait le package).
        DB::statement('ALTER TABLE routings MODIFY service_destination_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE routings MODIFY service_destination_id BIGINT UNSIGNED NOT NULL');
    }

    private function rebuildSqlite(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        DB::statement('DROP TABLE IF EXISTS routings_new');
        DB::statement("DROP INDEX IF EXISTS routings_new_courrier_id_index");
        DB::statement("DROP INDEX IF EXISTS routings_new_service_destination_id_statut_index");
        DB::statement("DROP INDEX IF EXISTS routings_new_department_id_statut_index");

        DB::statement(<<<'SQL'
            CREATE TABLE routings_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                courrier_id INTEGER NOT NULL REFERENCES courriers(id) ON DELETE CASCADE,
                service_source_id INTEGER REFERENCES services(id) ON DELETE SET NULL,
                service_destination_id INTEGER REFERENCES services(id) ON DELETE SET NULL,
                section_destination_id INTEGER REFERENCES sections(id) ON DELETE SET NULL,
                department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
                recipient_user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                routed_by INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                statut VARCHAR NOT NULL DEFAULT 'en_attente',
                sent_at DATETIME NOT NULL,
                received_at DATETIME,
                commentaire TEXT,
                created_at DATETIME,
                updated_at DATETIME
            )
            SQL);

        DB::statement('INSERT INTO routings_new (id, courrier_id, service_source_id, service_destination_id, section_destination_id, department_id, recipient_user_id, routed_by, statut, sent_at, received_at, commentaire, created_at, updated_at) SELECT id, courrier_id, service_source_id, service_destination_id, section_destination_id, department_id, recipient_user_id, routed_by, statut, sent_at, received_at, commentaire, created_at, updated_at FROM routings');
        DB::statement('DROP TABLE routings');
        DB::statement('ALTER TABLE routings_new RENAME TO routings');
        DB::statement('CREATE INDEX IF NOT EXISTS routings_courrier_id_index ON routings (courrier_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS routings_service_destination_statut_index ON routings (service_destination_id, statut)');
        DB::statement('CREATE INDEX IF NOT EXISTS routings_department_statut_index ON routings (department_id, statut)');

        DB::statement('PRAGMA foreign_keys=ON');
    }
};
