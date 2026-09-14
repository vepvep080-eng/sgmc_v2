<?php

namespace App\Http\Requests;

use App\Models\Department;
use App\Models\Role;
use App\Models\Routing;
use App\Models\Section;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoutingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('redirect', Routing::class) ?? false;
    }

    public function rules(): array
    {
        return [
            // Étape 1 (Admin/BO) : choix MULTIPLE de départements.
            'department_ids' => ['nullable', 'array', 'min:1'],
            'department_ids.*' => ['integer', 'exists:departments,id'],
            // Compat : ancien champ singulier du Blade legacy.
            'department_id' => ['nullable', 'exists:departments,id'],
            // Étape 2 (Chef département) : choix MULTIPLE de services.
            'service_destination_ids' => ['nullable', 'array', 'min:1'],
            'service_destination_ids.*' => ['integer', 'exists:services,id'],
            // Compat : ancien champ singulier.
            'service_destination_id' => ['nullable', 'exists:services,id'],
            'section_destination_id' => ['nullable', 'exists:sections,id'],
            'recipient_user_id' => ['nullable', 'exists:users,id'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** IDs départements normalisés (multiple OU ancien singulier). @return array<int> */
    public function departmentIds(): array
    {
        $ids = $this->input('department_ids');

        if ($ids === null && $this->filled('department_id')) {
            $ids = [$this->integer('department_id')];
        }

        return array_values(array_unique(array_map('intval', (array) ($ids ?? []))));
    }

    /** IDs services normalisés (multiple OU ancien singulier). @return array<int> */
    public function serviceDestinationIds(): array
    {
        $ids = $this->input('service_destination_ids');

        if ($ids === null && $this->filled('service_destination_id')) {
            $ids = [$this->integer('service_destination_id')];
        }

        return array_values(array_unique(array_map('intval', (array) ($ids ?? []))));
    }

    public function after(): array
    {
        return [function ($validator): void {
            $user = $this->user();
            $departmentIds = $this->departmentIds();
            $serviceIds = $this->serviceDestinationIds();

            // Étape 1 : Admin / BO -> N départements (AUCUN service direct).
            if ($user?->hasRole(Role::ADMIN, Role::BUREAU_ORDRE)) {
                if ($serviceIds !== []) {
                    $validator->errors()->add('service_destination_ids', 'BO-ADMIN-SERVICE-DIRECT');
                }

                if ($departmentIds === []) {
                    $validator->errors()->add('department_ids', 'BO-ADMIN-DEPT-REQUIRED');
                    return;
                }

                $valid = Department::active()->whereIn('id', $departmentIds)->pluck('id')->all();

                if (count($valid) !== count($departmentIds)) {
                    $validator->errors()->add('department_ids', 'BO-ADMIN-DEPT-INVALID');
                }

                if ($this->filled('recipient_user_id')) {
                    $validator->errors()->add('recipient_user_id', 'BO-ADMIN-NO-RECIPIENT');
                }

                return;
            }

            // Étape 2 : Chef département -> N services de SON département.
            if ($user?->hasRole(Role::CHEF_DEPARTEMENT)) {
                $managed = Department::query()->where('manager_id', $user->id)->first();

                if ($managed === null) {
                    $validator->errors()->add('service_destination_ids', 'CHEF-DEPT-NO-MANAGED');
                    return;
                }

                if ($departmentIds !== []) {
                    $validator->errors()->add('department_ids', 'CHEF-DEPT-NO-OTHER-DEPT');
                    return;
                }

                if ($serviceIds === []) {
                    $validator->errors()->add('service_destination_ids', 'CHEF-DEPT-SERVICE-REQUIRED');
                    return;
                }

                $allowed = Service::whereIn('id', $serviceIds)
                    ->where('department_id', $managed->id)
                    ->where('is_active', true)
                    ->pluck('id')->all();

                if (count($allowed) !== count($serviceIds)) {
                    $validator->errors()->add('service_destination_ids', 'CHEF-DEPT-SERVICE-SCOPE');
                }

                if ($this->filled('recipient_user_id')) {
                    $ok = User::whereKey($this->integer('recipient_user_id'))
                        ->whereIn('service_id', $allowed ?: [-1])
                        ->exists();

                    if (! $ok) {
                        $validator->errors()->add('recipient_user_id', 'CHEF-DEPT-RECIPIENT-SCOPE');
                    }
                }

                return;
            }

            // Étape 3 : Chef de service -> traitement, aucun renvoi.
            $validator->errors()->add('service_destination_ids', 'CHEF-SERVICE-NO-REDIRECT');
        }];
    }

    public function attributes(): array
    {
        return [
            'department_ids' => 'départements destinataires',
            'service_destination_ids' => 'services destinataires',
            'service_destination_id' => 'service destinataire',
            'section_destination_id' => 'section destinataire',
            'commentaire' => 'commentaire',
        ];
    }
}
