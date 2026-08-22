<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_arabic_is_the_default_locale_and_uses_a_right_to_left_layout(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('dir="rtl"', false);
    }

    public function test_french_locale_is_stored_in_session_and_uses_a_left_to_right_layout(): void
    {
        $this->get(route('language.update', 'fr'))->assertRedirect();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('lang="fr"', false)
            ->assertSee('dir="ltr"', false);
    }

    public function test_an_arbitrary_locale_is_refused(): void
    {
        $this->get('/language/en')->assertNotFound();
    }

    public function test_the_dashboard_renders_in_french_and_arabic_without_affecting_business_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee(__('app.dashboard', [], 'ar'));

        $this->actingAs($user)->withSession(['locale' => 'fr'])->get(route('dashboard'))
            ->assertOk()
            ->assertSee('lang="fr"', false)
            ->assertSee('dir="ltr"', false)
            ->assertSee(__('app.dashboard', [], 'fr'));
    }

    public function test_french_and_arabic_translation_keys_are_identical(): void
    {
        $french = require lang_path('fr/app.php');
        $arabic = require lang_path('ar/app.php');

        $this->assertSame(
            array_keys(Arr::dot($french)),
            array_keys(Arr::dot($arabic)),
        );
    }
}
