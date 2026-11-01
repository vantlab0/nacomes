<?php

namespace Tests\Feature;

use App\Models\DepartmentStudent;
use App\Models\Election;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentRegistrationToggleTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_registration_is_open_by_default(): void
    {
        $this->assertTrue(Setting::registrationIsOpen());

        $response = $this->get('/register');
        $response->assertStatus(200);

        $homeResponse = $this->get('/');
        $homeResponse->assertSee(route('register'));
    }

    public function test_visiting_register_when_closed_redirects_to_home_with_error(): void
    {
        Setting::setRegistrationOpen(false);
        $this->assertFalse(Setting::registrationIsOpen());

        $response = $this->get('/register');
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Student registration is currently closed. Please check back later.');

        // Follow redirect to ensure error flash message is rendered
        $followResponse = $this->followingRedirects()->get('/register');
        $followResponse->assertStatus(200);
        $followResponse->assertSee('Student registration is currently closed. Please check back later.');
    }

    public function test_submitting_registration_form_is_blocked_when_closed(): void
    {
        Setting::setRegistrationOpen(false);

        DepartmentStudent::create([
            'matric_no' => 'ENG/2021/001',
            'full_name' => 'John Doe',
        ]);

        $payload = [
            'name' => 'John Doe',
            'matric_no' => 'ENG/2021/001',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => '1',
        ];

        $response = $this->post('/register', $payload);
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Student registration is currently closed.');

        $this->assertDatabaseMissing('users', [
            'matric_no' => 'ENG/2021/001',
        ]);
    }

    public function test_register_links_are_hidden_from_public_navbar_when_closed(): void
    {
        Setting::setRegistrationOpen(false);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee(route('register'));
        // Login link remains visible
        $response->assertSee(route('login'));
    }

    public function test_existing_students_can_still_login_and_vote_when_registration_closed(): void
    {
        Setting::setRegistrationOpen(false);

        $student = User::factory()->create([
            'matric_no' => 'ENG/2021/005',
            'email' => 'student@school.edu.ng',
            'password' => Hash::make('Secret123!'),
            'email_verified_at' => now(),
        ]);

        $loginResponse = $this->post('/login', [
            'login' => 'ENG/2021/005',
            'password' => 'Secret123!',
        ]);

        $loginResponse->assertRedirect(route('vote'));
        $this->assertAuthenticatedAs($student);

        $election = Election::firstOrCreate([], [
            'title' => 'NACOMES General Elections',
            'status' => 'open',
        ]);
        $election->update(['status' => 'open']);

        $voteDeskResponse = $this->actingAs($student)->get('/vote');
        $voteDeskResponse->assertStatus(200);
    }

    public function test_admin_can_toggle_registration_via_settings_page(): void
    {
        $admin = $this->createAdmin();

        $this->assertTrue(Setting::registrationIsOpen());

        // Uncheck registration_open
        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'registration_open' => '0',
        ]);

        $response->assertRedirect();
        $this->assertFalse(Setting::registrationIsOpen());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'registration_closed',
            'description' => 'Closed student registration.',
            'user_id' => $admin->id,
        ]);

        // Check registration_open back
        $response2 = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'registration_open' => '1',
        ]);

        $response2->assertRedirect();
        $this->assertTrue(Setting::registrationIsOpen());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'registration_opened',
            'description' => 'Opened student registration.',
            'user_id' => $admin->id,
        ]);
    }

    public function test_admin_can_toggle_registration_via_quick_toggle_endpoint(): void
    {
        $admin = $this->createAdmin();

        $this->assertTrue(Setting::registrationIsOpen());

        // Toggle to closed
        $response = $this->actingAs($admin)->post(route('admin.registration.toggle'));
        $response->assertRedirect();
        $response->assertSessionHas('status', 'Student registration has been closed.');
        $this->assertFalse(Setting::registrationIsOpen());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'registration_closed',
            'description' => 'Closed student registration.',
            'user_id' => $admin->id,
        ]);

        // Toggle back to open
        $response2 = $this->actingAs($admin)->post(route('admin.registration.toggle'));
        $response2->assertRedirect();
        $response2->assertSessionHas('status', 'Student registration has been opened.');
        $this->assertTrue(Setting::registrationIsOpen());

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'registration_opened',
            'description' => 'Opened student registration.',
            'user_id' => $admin->id,
        ]);
    }

    public function test_guest_and_non_admin_cannot_access_toggle_route(): void
    {
        $response = $this->post(route('admin.registration.toggle'));
        $response->assertRedirect(route('admin.login'));

        $student = User::factory()->create([
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        $studentResponse = $this->actingAs($student)->post(route('admin.registration.toggle'));
        $studentResponse->assertStatus(403);
    }
}
