<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Service;
use Illuminate\Database\Seeder;

class JeradaOrganigrammeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Départements nouveaux (arabes exacts) ──────────────────────────

        // Département 1 : "Division des Affaires Administratives, Financières et de Gestion"
        $deptAdminFin = Department::updateOrCreate(
            ['code' => 'SEC-ADM-FIN'],
            [
                'name' => 'قسم الشؤون الإدارية والمالية والتصرف',
                'description' => 'Département regroupant les services administratifs, financiers et de gestion.',
                'is_active' => true,
                'manager_id' => null, //À remplir selon la réalité organisationnelle
            ]
        );

        // Département 2 : "Division des Affaires Techniques, de la Planification et du Patrimoine"
        $deptTechPlan = Department::updateOrCreate(
            ['code' => 'SEC-TECH-PLAN'],
            [
                'name' => 'قسم الشؤون التقنية والتخطيط والممتلكات',
                'description' => 'Département regroupant les services techniques, de planification et de patrimoine.',
                'is_active' => true,
                'manager_id' => null, //À remplir selon la réalité organisationnelle
            ]
        );

        // ── Services rattachés au Département 1 ─────────────────────────────
        // Section 1 : "Mise en gestion administrative et financière"
        Service::updateOrCreate(
            ['code' => 'ADM-FIN'],
            [
                'name' => 'مصلحة المالية المحلية والصفقات',
                'department_id' => $deptAdminFin->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        Service::updateOrCreate(
            ['code' => 'RH-BU-ENV'],
            [
                'name' => 'مصلحة الموارد البشرية والشؤون الاقتصادية والشرطة الادارية',
                'department_id' => $deptAdminFin->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        // ── Services rattachés au Département 2 ─────────────────────────────
        // Section 2 : "Mise en œuvre technique et patrimoniale"
        Service::updateOrCreate(
            ['code' => 'URB-TECH'],
            [
                'name' => 'مصلحة التعمير، وشؤون البيئة وتدبير المجال',
                'department_id' => $deptTechPlan->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        Service::updateOrCreate(
            ['code' => 'EQT-TP'],
            [
                'name' => 'مصلحة الشؤون التقنية والأشغال والصيانة',
                'department_id' => $deptTechPlan->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        Service::updateOrCreate(
            ['code' => 'PT-MOYENNES'],
            [
                'name' => 'مصلحة الممتلكات والشؤون الاجتماعية والثقافية والرياضية والصحية',
                'department_id' => $deptTechPlan->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );

        // ── Service de l'état civil / affaires juridiques et du conseil ──
        Service::updateOrCreate(
            ['code' => 'EC-LEG'],
            [
                'name' => 'مصلحة الحالة المدنية والمصادقة على الوثائق والشؤون القانونية وشؤون المجلس',
                'department_id' => $deptAdminFin->id,
                'is_active' => true,
                'manager_id' => null,
            ]
        );
    }
}
