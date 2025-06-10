<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_page_is_accessible_to_guests()
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Welcome to StrixBudget');
        $response->assertSee('Getting Started - Registration');
        $response->assertSee('Create Account');
    }

    public function test_help_page_shows_registration_section_for_guests()
    {
        $response = $this->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Getting Started - Registration');
        $response->assertSee('Obtain a Registration Key');
        $response->assertSee('Complete Registration');
        $response->assertSee('Start Using the System');
    }

    public function test_help_page_is_accessible_to_authenticated_users()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Welcome to StrixBudget');
        $response->assertDontSee('Getting Started - Registration');
    }

    public function test_help_page_shows_admin_section_for_admin_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Welcome to StrixBudget');
        $response->assertSee('Administrator Panel');
        $response->assertSee('User Management');
        $response->assertSee('Registration Keys');
        $response->assertSee('Go to Admin Panel');
    }

    public function test_help_page_does_not_show_admin_section_for_regular_users()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/help');

        $response->assertStatus(200);
        $response->assertSee('Welcome to StrixBudget');
        $response->assertDontSee('Administrator Panel');
        $response->assertDontSee('User Management');
        $response->assertDontSee('Registration Keys');
    }

    public function test_welcome_page_has_help_button()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('User Guide & Help');
        $response->assertSee(route('help.index'));
    }

    public function test_help_page_shows_bulgarian_translations_for_bulgarian_users()
    {
        $user = User::factory()->create(['locale' => 'bg']);

        // Симулираме българска локализация
        app()->setLocale('bg');

        $response = $this->actingAs($user)->get('/help');

        $response->assertStatus(200);
        // Проверяваме дали се показват българските преводи
        $response->assertSee('Ръководство за потребителя и помощ');
        $response->assertSee('Добре дошли в StrixBudget');
    }

    public function test_help_page_shows_bulgarian_admin_section_for_bulgarian_admin()
    {
        $admin = User::factory()->create(['role' => 'admin', 'locale' => 'bg']);

        // Симулираме българска локализация
        app()->setLocale('bg');

        $response = $this->actingAs($admin)->get('/help');

        $response->assertStatus(200);
        // Проверяваме дали се показват българските преводи за админ секцията
        $response->assertSee('Административен панел');
        $response->assertSee('Управление на потребители');
        $response->assertSee('Ключове за регистрация');
    }
}
