<?php

use App\Models\Department;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Bouclage de l'organigramme des services de la Commune de Jerada.
 *
 * 1. Renomme 5 services (clé de rattachement : code stable).
 * 2. Supprime 2 services (« Service Technique » ST et SALUBRITE) après
 *    détachement/désactivation de leurs utilisateurs chef de service
 *    (confirmé par le métier le 2026-09-09).
 * 3. Crée un nouveau service (EC-LEG) + son chef de service.
 *
 * Idempotente : peut être relancée sans effet de bord.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1) Renommage des 5 services existants ─────────────────────────────
        $renames = [
            'ADM-FIN' => 'مصلحة المالية المحلية والصفقات',
            'EQT-TP' => 'مصلحة الشؤون التقنية والأشغال والصيانة',
            'RH-BU-ENV' => 'مصلحة الموارد البشرية والشؤون الاقتصادية والشرطة الادارية',
            'URB-TECH' => 'مصلحة التعمير، وشؤون البيئة وتدبير المجال',
            'PT-MOYENNES' => 'مصلحة الممتلكات والشؤون الاجتماعية والثقافية والرياضية والصحية',
        ];

        foreach ($renames as $code => $name) {
            Service::where('code', $code)->update(['name' => $name]);
        }

        // ── 2) Suppression des 2 services : détachement + désactivation des users
        $deletedCodes = ['ST', 'SALUBRITE'];

        foreach (Service::whereIn('code', $deletedCodes)->get() as $service) {
            User::where('service_id', $service->id)->update([
                'service_id' => null,
                'is_active' => false,
            ]);
        }

        Service::whereIn('code', $deletedCodes)->delete();

        // ── 3) Création du nouveau service + chef de service ──────────────────
        $department = Department::updateOrCreate(
            ['code' => 'SEC-ADM-FIN'],
            [
                'name' => 'قسم الشؤون الإدارية والمالية والتصرف',
                'description' => 'Département regroupant les services administratifs, financiers et de gestion.',
                'is_active' => true,
            ]
        );

        $service = Service::updateOrCreate(
            ['code' => 'EC-LEG'],
            [
                'name' => 'مصلحة الحالة المدنية والمصادقة على الوثائق والشؤون القانونية وشؤون المجلس',
                'department_id' => $department->id,
                'description' => null,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        $chefRole = Role::firstOrCreate(
            ['slug' => Role::CHEF_SERVICE],
            ['name' => 'Chef de Service']
        );

        User::updateOrCreate(
            ['email' => 'ec-leg@sgmc.test'],
            [
                'name' => 'Chef Service EC-LEG',
                // Mot de passe temporaire aléatoire : à réinitialiser par un
                // administrateur avant toute première connexion.
                'password' => Hash::make(Str::random(40)),
                'role_id' => $chefRole->id,
                'service_id' => $service->id,
                'is_active' => true,
            ]
        );
    }

    public function down(): void
    {
        // ── Restauration des anciens noms des 5 services ──────────────────────
        $oldNames = [
            'ADM-FIN' => 'مصلحة المالية والصفقات',
            'EQT-TP' => 'مصلحة التجهيزات التقنية والأشغال العمومية',
            'RH-BU-ENV' => 'مصلحة الموارد البشرية والشؤون الاقتصادية وشرطة البيئة',
            'URB-TECH' => 'مصلحة التعمير والبرامج الكبرى وتدبير الممتلكات',
            'PT-MOYENNES' => 'مصلحة الممتلكات والشؤون الاجتماعية والثقافية والرياضية',
        ];

        foreach ($oldNames as $code => $name) {
            Service::where('code', $code)->update(['name' => $name]);
        }

        // ── Recréation des 2 services supprimés + rattachement des users ──────
        $gen = Department::updateOrCreate(
            ['code' => 'GEN'],
            ['name' => 'Direction Générale', 'is_active' => true]
        );

        Service::updateOrCreate(
            ['code' => 'ST'],
            [
                'name' => 'Service Technique',
                'department_id' => $gen->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        $adminFin = Department::updateOrCreate(
            ['code' => 'SEC-ADM-FIN'],
            ['name' => 'قسم الشؤون الإدارية والمالية والتصرف', 'is_active' => true]
        );

        Service::updateOrCreate(
            ['code' => 'SALUBRITE'],
            [
                'name' => 'مصلحة حفظ الصحة والمحافظة على الأماكن والآثار والتراث الجماعي',
                'department_id' => $adminFin->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        $st = Service::where('code', 'ST')->first();
        if ($st !== null) {
            User::where('email', 'chef.st@sgmc.test')->update([
                'service_id' => $st->id,
                'is_active' => true,
            ]);
        }

        $salubrite = Service::where('code', 'SALUBRITE')->first();
        if ($salubrite !== null) {
            User::where('email', 'salubrite@sgmc.test')->update([
                'service_id' => $salubrite->id,
                'is_active' => true,
            ]);
        }

        // ── Retrait du nouveau service et de son chef de service ──────────────
        Service::where('code', 'EC-LEG')->delete();
        User::where('email', 'ec-leg@sgmc.test')->delete();
    }
};