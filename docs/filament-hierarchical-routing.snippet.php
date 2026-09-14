<?php

/**
 * Référence Filament — Routage hiérarchique des courriers (Commune Jerada).
 * ---------------------------------------------------------------------------
 * Ce fichier est une DOCUMENTATION / SNIPPET, pas du code chargé par Laravel :
 * Filament n'est pas installé dans ce projet (stack Blade actuelle, cf.
 * composer.json), donc ce fichier reste en docs/ pour une migration future.
 *
 * Rôles :
 *  - Admin / Agent BO : envoi vers 1..N départements (Étape 1).
 *  - Chef de département : dispatch vers 1..N services de SON département (Étape 2).
 *  - Chef de service : traitement uniquement (Étape 3, aucun renvoi).
 *
 * Garde-fous serveur correspondants (déjà implémentés) :
 *  - App\Http\Requests\StoreRoutingRequest (validation + after() par rôle)
 *  - App\Policies\RoutingPolicy::routeHierarchically()
 *  - App\Services\HierarchicalRoutingService (source unique des options)
 */

// --- Dans App\Filament\Resources\CourrierResource (futur) -------------------
// use App\Models\Department;
// use App\Models\Role;
// use App\Models\Service;
// use App\Services\HierarchicalRoutingService;
// use Filament\Forms\Components\CheckboxList;
// use Filament\Forms\Components\Textarea;
// use Filament\Forms\Get;
//
// public static function routingFormSchema(): array
// {
//     return [
//         // Étape 1 — visible Admin / Agent BO uniquement :
//         // choix MULTIPLE parmi les chefs de département (= départements).
//         CheckboxList::make('department_ids')
//             ->label('Envoyer à (départements)')
//             ->options(fn (HierarchicalRoutingService $h) =>
//                 $h->targetableDepartments(auth()->user())->pluck('name', 'id'))
//             ->visible(fn (): bool =>
//                 auth()->user()?->hasRole(Role::ADMIN, Role::BUREAU_ORDRE) ?? false)
//             ->required()
//             ->bulkToggleable(),
//
//         // Étape 2 — visible Chef de département uniquement :
//         // choix MULTIPLE filtré à SON département (via Service.department_id).
//         CheckboxList::make('service_destination_ids')
//             ->label('Dispatcher vers (mes services)')
//             ->options(function (HierarchicalRoutingService $h): array {
//                 $user = auth()->user();
//                 $managed = $h->managedDepartment($user);
//
//                 return Service::query()
//                     ->where('is_active', true)
//                     ->where('department_id', $managed?->id)
//                     ->orderBy('name')
//                     ->pluck('name', 'id')
//                     ->all();
//             })
//             ->visible(fn (): bool =>
//                 auth()->user()?->hasRole(Role::CHEF_DEPARTEMENT) ?? false)
//             ->required()
//             ->bulkToggleable(),
//
//         Textarea::make('commentaire')->label('Commentaire')->maxLength(1000),
//     ];
// }
//
// // La Policy reste l'autorité finale (anti-manipulation du select côté client) :
// // $user->can('routeHierarchically', [$courrier, $departmentIds, $serviceIds])
// // voir App\Policies\RoutingPolicy::routeHierarchically().
