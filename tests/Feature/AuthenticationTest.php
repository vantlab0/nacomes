<?php

namespace Tests\Feature;

use App\Models\DepartmentStudent;
use App\Models\Election;
use App\Models\User;
use Database\Seeders\DepartmentStudentSeeder;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function createEligibleStudent(string $matricNo, string $name = 'Eligible Student'): DepartmentStudent
    {
        return DepartmentStudent::factory()->create([
            'name' => $name,
            'matric_no' => $matricNo,
        ]);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Student Voter Registration');
        $response->assertSee('Matric Number');
        $response->assertSee('Computer Engineering register');
    }

    public function test_students_can_register_with_valid_details_and_matric_is_normalized(): void
    {
        $this->createEligibleStudent('CPE/2021/1042');

        $response = $this->post('/register', [
            'name' => 'Chioma Adeyemi',
            'matric_no' => '  cpe/2021/1042  ',
            'email' => 'chioma@student.edu.ng',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));

        $user = User::where('matric_no', 'CPE/2021/1042')->first();
        $this->assertNotNull($user);
        $this->assertSame('CPE/2021/1042', $user->matric_no);
        $this->assertNull($user->email);
        $this->assertFalse($user->is_admin);
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertNull($user->email_verified_at);
    }

    public function test_registration_accepts_matric_number_with_alternate_separators(): void
    {
        $this->createEligibleStudent('CET/CPE/0155/2025');

        $response = $this->post('/register', [
            'name' => 'Separator Student',
            'matric_no' => 'cet|cpe|0155|2025',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'matric_no' => 'CET/CPE/0155/2025',
        ]);
    }

    public function test_registration_rejects_matric_not_in_department_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Unknown Student',
            'matric_no' => 'COET/99999/2023',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('matric_no');
        $this->assertStringContainsString(
            'not in this department',
            (string) session('errors')->first('matric_no')
        );
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['matric_no' => 'COET/99999/2023']);
    }

    public function test_department_student_register_can_be_imported(): void
    {
        $this->seed(DepartmentStudentSeeder::class);

        $this->assertDatabaseCount('department_students', 286);
        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'COET/10970/2023',
            'level' => 300,
        ]);
    }

    public function test_duplicate_matric_numbers_are_rejected(): void
    {
        $this->createEligibleStudent('CPE/2020/0011');

        User::factory()->create([
            'matric_no' => 'CPE/2020/0011',
            'email' => 'existing@student.edu.ng',
        ]);

        $response = $this->post('/register', [
            'name' => 'Second Student',
            'matric_no' => 'cpe/2020/0011',
            'email' => 'new@student.edu.ng',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors('matric_no');
        $this->assertGuest();
    }

    public function test_duplicate_email_addresses_are_rejected_during_verification(): void
    {
        User::factory()->create([
            'email' => 'registered@student.edu.ng',
        ]);

        $unverifiedUser = User::factory()->unverified()->create([
            'email' => null,
        ]);

        $response = $this->actingAs($unverifiedUser)->post('/email/update', [
            'email' => 'registered@student.edu.ng',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_students_cannot_create_admin_accounts_through_registration(): void
    {
        $this->createEligibleStudent('CPE/2022/9999');

        $this->post('/register', [
            'name' => 'Hacker Student',
            'matric_no' => 'CPE/2022/9999',
            'email' => 'hacker@student.edu.ng',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => '1',
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $user = User::where('matric_no', 'CPE/2022/9999')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_admin);
    }

    public function test_terms_acceptance_is_required_for_registration(): void
    {
        $this->createEligibleStudent('CPE/2022/1111');

        $response = $this->post('/register', [
            'name' => 'Test Student',
            'matric_no' => 'CPE/2022/1111',
            'email' => 'terms@student.edu.ng',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Student Voter Portal');
        $response->assertSee('Matric No or Student Email');
    }

    public function test_students_can_authenticate_using_matric_number(): void
    {
        $user = User::factory()->create([
            'matric_no' => 'CPE/2021/5555',
            'password' => Hash::make('StudentSecret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'cpe/2021/5555',
            'password' => 'StudentSecret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('vote'));
    }

    public function test_students_can_authenticate_using_email_address(): void
    {
        $user = User::factory()->create([
            'email' => 'voter@student.edu.ng',
            'password' => Hash::make('StudentSecret123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'voter@student.edu.ng',
            'password' => 'StudentSecret123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('vote'));
    }

    public function test_students_cannot_authenticate_with_invalid_credentials(): void
    {
        User::factory()->create([
            'matric_no' => 'CPE/2021/7777',
            'password' => Hash::make('CorrectPassword'),
        ]);

        $response = $this->post('/login', [
            'login' => 'CPE/2021/7777',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'login' => 'rate_limited@student.edu.ng',
                'password' => 'WrongPass',
            ]);
        }

        // 6th attempt should be blocked by RateLimiter
        $response = $this->post('/login', [
            'login' => 'rate_limited@student.edu.ng',
            'password' => 'WrongPass',
        ]);

        $response->assertSessionHasErrors('login');
        $errors = session('errors')->get('login');
        $this->assertStringContainsString('Too many login attempts', $errors[0]);
    }

    public function test_authenticated_student_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_guests_cannot_access_voting_desk(): void
    {
        $response = $this->get('/vote');

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_students_cannot_access_voting_desk_and_are_redirected(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();

        $response = $this->actingAs($unverifiedUser)->get('/vote');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $unverifiedUser = User::factory()->unverified()->create();

        $response = $this->actingAs($unverifiedUser)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertSee('Enter Verification Code');
        $response->assertSee('Resend Code');
    }

    public function test_email_can_be_verified_with_valid_6_digit_code(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create([
            'verification_code' => '654321',
            'verification_code_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->actingAs($user)->post('/email/verify', [
            'code' => '654321',
        ]);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->verification_code);
        $response->assertRedirect(route('vote'));
        $response->assertSessionHas('status');
    }

    public function test_invalid_or_expired_code_is_rejected(): void
    {
        $user = User::factory()->unverified()->create([
            'verification_code' => '654321',
            'verification_code_expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->post('/email/verify', [
            'code' => '654321',
        ]);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertSessionHasErrors('code');
    }

    public function test_email_can_be_verified_via_signed_url(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('vote'));
        $response->assertSessionHas('status');
    }

    public function test_verified_students_can_access_voting_desk(): void
    {
        $election = Election::factory()->create(['status' => 'open']);
        $verifiedUser = User::factory()->create();

        $response = $this->actingAs($verifiedUser)->get('/vote');

        $response->assertStatus(200);
        $response->assertSee($election->title);
        $response->assertSee($verifiedUser->name);
        $response->assertSee($verifiedUser->matric_no);
    }

    public function test_password_reset_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Reset Account Password');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        $user = User::factory()->create([
            'email' => 'reset_me@student.edu.ng',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'reset_me@student.edu.ng',
        ]);

        $response->assertSessionHas('status');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'student_pass@edu.ng',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student_pass@edu.ng',
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('BrandNewPassword123!', $user->fresh()->password));
    }
}
