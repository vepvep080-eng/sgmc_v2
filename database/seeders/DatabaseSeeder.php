<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // ServiceSeeder must run first: DepartmentSeeder attaches any
        // service left without a department_id to a default department.
        $this->call(ServiceSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(JeradaOrganigrammeSeeder::class);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
