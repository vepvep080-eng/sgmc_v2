<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rattache chaque chef de département à son département via
     * departments.manager_id (clé du routage hiérarchique Étape 1 -> Étape 2).
     * Idempotent : updateOrCreate par code, manager cherché par e-mail.
     */
    public function up(): void
    {
        $map = [
            'SEC-ADM-FIN' => 'sec-admfin@sgmc.test',
            'SEC-TECH-PLAN' => 'sec-techplan@sgmc.test',
        ];

        foreach ($map as $code => $email) {
            $user = User::where('email', $email)->first();

            if ($user === null) {
                continue;
            }

            Department::where('code', $code)->update(['manager_id' => $user->id]);
        }
    }

    public function down(): void
    {
        Department::whereIn('code', ['SEC-ADM-FIN', 'SEC-TECH-PLAN'])->update(['manager_id' => null]);
    }
};
