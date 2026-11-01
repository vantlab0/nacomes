<?php

namespace Tests\Feature;

use App\Mail\VerifyEmailCodeMail;
use App\Models\Candidate;
use App\Models\DepartmentStudent;
use App\Models\Election;
use App\Models\Position;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'electoral.chairman@nacomes.org',
            'is_admin' => true,
            'password' => Hash::make('ChairmanSecret123#'),
        ], $attributes));
    }

    public function test_guests_are_redirected_from_admin_dashboard_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_students_cannot_access_admin_dashboard_and_receive_403(): void
    {
        $student = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($student)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_login_screen_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Electoral Administration Portal');
        $response->assertSee('Authorized electoral officers and departmental administrators only.');
    }

    public function test_admin_can_authenticate_via_admin_login(): void
    {
        $admin = $this->createAdmin([
            'email' => 'admin@nacomes.org',
            'password' => Hash::make('SecurePassword123#'),
        ]);

        $response = $this->post('/admin/login', [
            'login' => 'admin@nacomes.org',
            'password' => 'SecurePassword123#',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_student_cannot_authenticate_via_admin_login(): void
    {
        User::factory()->create([
            'email' => 'student@school.edu.ng',
            'is_admin' => false,
            'password' => Hash::make('StudentPass123#'),
        ]);

        $response = $this->post('/admin/login', [
            'login' => 'student@school.edu.ng',
            'password' => 'StudentPass123#',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_can_logout(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_admin_can_view_voters_list_with_search_and_filters(): void
    {
        $admin = $this->createAdmin();

        $student1 = User::factory()->create([
            'name' => 'Chioma Okeke',
            'matric_no' => 'CPE/2020/001',
            'is_admin' => false,
        ]);

        $student2 = User::factory()->create([
            'name' => 'Babatunde Fash',
            'matric_no' => 'CPE/2021/045',
            'is_admin' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin/voters?q=Chioma');

        $response->assertStatus(200);
        $response->assertSee('Chioma Okeke');
        $response->assertDontSee('Babatunde Fash');
    }

    public function test_admin_can_suspend_and_reactivate_a_student_voter(): void
    {
        $admin = $this->createAdmin();
        $student = User::factory()->create([
            'is_admin' => false,
            'is_suspended' => false,
        ]);

        // Suspend
        $response = $this->actingAs($admin)->post(route('admin.voters.toggle-suspension', $student));

        $response->assertSessionHas('status');
        $this->assertTrue($student->fresh()->isSuspended());

        // Reactivate
        $response = $this->actingAs($admin)->post(route('admin.voters.toggle-suspension', $student));

        $response->assertSessionHas('status');
        $this->assertFalse($student->fresh()->isSuspended());
    }

    public function test_suspending_a_voter_preserves_their_existing_votes(): void
    {
        $admin = $this->createAdmin();
        $student = User::factory()->create(['is_admin' => false]);
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id, 'position_id' => $position->id]);

        $vote = Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
        ]);

        // Suspend the student
        $this->actingAs($admin)->post(route('admin.voters.toggle-suspension', $student));

        $this->assertTrue($student->fresh()->isSuspended());
        $this->assertDatabaseHas('votes', [
            'id' => $vote->id,
            'user_id' => $student->id,
            'candidate_id' => $candidate->id,
        ]);
    }

    public function test_admin_cannot_suspend_another_admin_or_themselves(): void
    {
        $admin1 = $this->createAdmin(['email' => 'admin1@nacomes.org']);
        $admin2 = $this->createAdmin(['email' => 'admin2@nacomes.org']);

        $response = $this->actingAs($admin1)->post(route('admin.voters.toggle-suspension', $admin2));

        $response->assertSessionHasErrors('error');
        $this->assertFalse($admin2->fresh()->isSuspended());
    }

    public function test_admin_can_create_new_election_position(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)->post(route('admin.positions.store'), [
            'title' => 'Director of Socials',
            'description' => 'Oversees departmental entertainment and cultural events.',
            'display_order' => 5,
            'is_published' => 1,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('positions', [
            'title' => 'Director of Socials',
            'display_order' => 5,
            'is_published' => true,
        ]);
    }

    public function test_admin_can_update_election_position(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create([
            'election_id' => $election->id,
            'title' => 'Financial Secretary',
            'display_order' => 3,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.positions.update', $position), [
            'title' => 'Treasurer & Financial Secretary',
            'display_order' => 4,
            'description' => 'Updated office duties',
            'is_published' => 1,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
            'title' => 'Treasurer & Financial Secretary',
            'display_order' => 4,
        ]);
    }

    public function test_admin_can_delete_position_when_no_votes_exist(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create([
            'election_id' => $election->id,
            'title' => 'Assistant General Secretary',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.positions.destroy', $position));

        $response->assertRedirect(route('admin.positions.index'));
        $this->assertDatabaseMissing('positions', [
            'id' => $position->id,
        ]);
    }

    public function test_admin_cannot_delete_position_when_votes_exist(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id, 'position_id' => $position->id]);
        $student = User::factory()->create(['is_admin' => false]);

        Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.positions.destroy', $position));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
        ]);
    }

    public function test_admin_can_add_candidate_with_photo_upload(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);

        $photo = UploadedFile::fake()->create('candidate_portrait.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('admin.candidates.store', $position), [
            'name' => 'Olamide Bakare',
            'matric_no' => 'CPE/2020/088',
            'academic_level' => '400 Level',
            'photo' => $photo,
            'is_published' => 1,
        ]);

        $response->assertSessionHas('status');
        $candidate = Candidate::where('name', 'Olamide Bakare')->first();
        $this->assertNotNull($candidate);
        $this->assertNotNull($candidate->photo_url);
    }

    public function test_admin_can_update_candidate_details(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
            'name' => 'Original Name',
            'academic_level' => '300 Level',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.candidates.update', $candidate), [
            'name' => 'Updated Name',
            'academic_level' => '400 Level',
            'matric_no' => 'CPE/2020/099',
            'is_published' => 1,
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'name' => 'Updated Name',
            'academic_level' => '400 Level',
        ]);
    }

    public function test_admin_can_delete_candidate_when_no_votes_exist(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.candidates.destroy', $candidate));

        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('candidates', [
            'id' => $candidate->id,
        ]);
    }

    public function test_admin_cannot_delete_candidate_when_votes_exist(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create(['election_id' => $election->id, 'position_id' => $position->id]);
        $student = User::factory()->create(['is_admin' => false]);

        Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.candidates.destroy', $candidate));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
        ]);
    }

    public function test_admin_can_update_election_schedule_and_details(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'open', 'title' => 'Original Title']);

        $response = $this->actingAs($admin)->put(route('admin.election.update'), [
            'title' => 'NACOMES 2026/2027 General Elections',
            'description' => 'Updated description for the election.',
            'starts_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('elections', [
            'id' => $election->id,
            'title' => 'NACOMES 2026/2027 General Elections',
        ]);
    }

    public function test_admin_can_toggle_election_status(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($admin)->post(route('admin.election.toggle-status'), [
            'status' => 'open',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals('open', $election->fresh()->status);
    }

    public function test_admin_can_toggle_results_publication_visibility(): void
    {
        $admin = $this->createAdmin();
        $election = Election::factory()->create(['status' => 'closed', 'results_published' => false]);

        $response = $this->actingAs($admin)->post(route('admin.election.toggle-results'), [
            'results_published' => '1',
        ]);

        $response->assertSessionHas('status');
        $this->assertTrue($election->fresh()->results_published);
    }

    public function test_artisan_make_admin_command_creates_or_promotes_an_admin_user(): void
    {
        $this->artisan('make:admin', [
            '--email' => 'new.commissioner@nacomes.org',
            '--name' => 'Electoral Commissioner',
            '--matric' => 'ADMIN/2026/01',
            '--password' => 'CommissionKey2026!',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'new.commissioner@nacomes.org',
            'name' => 'Electoral Commissioner',
            'matric_no' => 'ADMIN/2026/01',
            'is_admin' => true,
        ]);
    }

    public function test_admin_dashboard_contains_add_candidate_and_view_elections_quick_actions(): void
    {
        $admin = $this->createAdmin();
        Election::factory()->create(['status' => 'open']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('+ Add Candidate');
        $response->assertSee('View Elections');
        $response->assertDontSee('Admin Authenticated');
    }

    public function test_admin_can_view_settings_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('System Settings');
        $response->assertSee('Website Branding &amp; Visual Identity', false);
        $response->assertSee('SMTP Email Server Configuration');
    }

    public function test_admin_can_update_settings(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();

        $logo = UploadedFile::fake()->create('custom_logo.png', 200, 'image/png');
        $icon = UploadedFile::fake()->create('favicon.png', 32, 'image/png');

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'site_logo' => $logo,
            'site_icon' => $icon,
            'mail_host' => 'smtp.testmail.com',
            'mail_port' => '587',
            'mail_username' => 'test@nacomes.edu.ng',
            'mail_password' => 'SecureSmtpSecret123',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'official@nacomes.edu.ng',
            'mail_from_name' => 'NACOMES Electoral Board',
        ]);

        $response->assertSessionHas('status');
        $this->assertSame('smtp.testmail.com', Setting::get('mail_host'));
        $this->assertSame('587', (string) Setting::get('mail_port'));
        $this->assertSame('official@nacomes.edu.ng', Setting::get('mail_from_address'));
        $this->assertNotNull(Setting::get('site_logo'));
        $this->assertNotNull(Setting::get('site_icon'));
    }

    public function test_admin_can_send_test_email(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.settings.test-email'), [
            'test_recipient' => 'diagnostics@nacomes.edu.ng',
        ]);

        $response->assertSessionHas('status');
        Mail::assertSent(VerifyEmailCodeMail::class, function ($mail) {
            return $mail->hasTo('diagnostics@nacomes.edu.ng');
        });
    }

    public function test_guests_are_redirected_from_matric_register_to_admin_login(): void
    {
        $this->get(route('admin.department-students.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_students_cannot_access_matric_register_or_import(): void
    {
        $student = User::factory()->create(['is_admin' => false]);
        $file = UploadedFile::fake()->createWithContent('matrics.csv', "matric_no\nCET/CPE/0001/2025\n");

        $this->actingAs($student)
            ->get(route('admin.department-students.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('admin.department-students.import'), ['file' => $file])
            ->assertForbidden();

        $this->assertDatabaseCount('department_students', 0);
    }

    public function test_admin_can_view_department_student_register(): void
    {
        $admin = $this->createAdmin();
        DepartmentStudent::factory()->create([
            'name' => 'Register Student',
            'matric_no' => 'CET/CPE/0155/2025',
            'level' => 100,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.department-students.index'));

        $response->assertOk();
        $response->assertSee('Upload Matric Numbers');
        $response->assertSee('CET/CPE/0155/2025');
        $response->assertSee('Register Student');
        $response->assertSee(DepartmentStudent::DEPARTMENT);
    }

    public function test_admin_can_search_and_filter_department_students(): void
    {
        $admin = $this->createAdmin();
        DepartmentStudent::factory()->create([
            'name' => 'Alice One',
            'matric_no' => 'CET/CPE/0100/2025',
            'level' => 100,
        ]);
        DepartmentStudent::factory()->create([
            'name' => 'Bob Two',
            'matric_no' => 'CET/CPE/0200/2025',
            'level' => 200,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.department-students.index', ['level' => 'all']))
            ->assertOk()
            ->assertSee('Alice One')
            ->assertSee('Bob Two');

        $this->actingAs($admin)
            ->get(route('admin.department-students.index', ['q' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice One')
            ->assertDontSee('Bob Two');

        $this->actingAs($admin)
            ->get(route('admin.department-students.index', ['level' => 200]))
            ->assertOk()
            ->assertSee('Bob Two')
            ->assertDontSee('Alice One');
    }

    public function test_admin_can_import_department_students_and_update_existing_records(): void
    {
        $admin = $this->createAdmin();
        DepartmentStudent::factory()->create([
            'name' => 'Original Student',
            'matric_no' => 'CET/CPE/0001/2025',
            'level' => 100,
        ]);

        $csv = <<<'CSV'
        matric_no,name,level
        CET/CPE/0001/2025,Updated Student,100
        CET/CPE/0002/2025,New Student,200
        CSV;

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent('matrics.csv', $csv),
        ]);

        $response->assertSessionHas('status', 'Import complete: 1 added and 1 updated.');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0001/2025',
            'name' => 'Updated Student',
            'level' => 100,
        ]);
        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0002/2025',
            'name' => 'New Student',
            'level' => 200,
            'department' => DepartmentStudent::DEPARTMENT,
        ]);
    }

    public function test_admin_can_import_a_plain_text_list_of_matric_numbers(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent(
                'matrics.txt',
                "cet|cpe|0003|2025\n\nCET/CPE/0004/2025\n"
            ),
        ]);

        $response->assertSessionHas('status', 'Import complete: 2 added and 0 updated.');

        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0003/2025',
            'department' => DepartmentStudent::DEPARTMENT,
        ]);
        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0004/2025',
            'department' => DepartmentStudent::DEPARTMENT,
        ]);
    }

    public function test_admin_can_import_semicolon_separated_csv_files(): void
    {
        $admin = $this->createAdmin();
        $csv = "matric_no;name;level\nCET/CPE/0007/2025;Semicolon Student;100\n";

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent('matrics.csv', $csv),
        ]);

        $response->assertSessionHas('status', 'Import complete: 1 added and 0 updated.');

        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0007/2025',
            'name' => 'Semicolon Student',
            'level' => 100,
        ]);
    }

    public function test_department_student_import_does_not_erase_existing_details_with_blank_values(): void
    {
        $admin = $this->createAdmin();
        DepartmentStudent::factory()->create([
            'name' => 'Existing Student',
            'matric_no' => 'CET/CPE/0006/2025',
            'level' => 300,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent(
                'matrics.txt',
                "CET/CPE/0006/2025\n"
            ),
        ]);

        $response->assertSessionHas('status', 'Import complete: 0 added and 1 updated.');

        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0006/2025',
            'name' => 'Existing Student',
            'level' => 300,
            'department' => DepartmentStudent::DEPARTMENT,
        ]);
    }

    public function test_department_student_import_rejects_unsupported_file_types(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->create('students.pdf', 10, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('department_students', 0);
    }

    public function test_department_student_import_reports_rows_with_missing_matric_numbers(): void
    {
        $admin = $this->createAdmin();

        $csv = <<<'CSV'
        matric_no,name
        ,Missing Matric
        CET/CPE/0005/2025,Valid Student
        CSV;

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent('matrics.csv', $csv),
        ]);

        $response->assertSessionHas(
            'status',
            'Import complete: 1 added and 0 updated. 1 row(s) skipped.'
        );
        $response->assertSessionHas('import_errors', function (array $errors): bool {
            return count($errors) === 1 && str_contains($errors[0], 'Row 2');
        });

        $this->assertDatabaseHas('department_students', [
            'matric_no' => 'CET/CPE/0005/2025',
            'name' => 'Valid Student',
        ]);
        $this->assertDatabaseMissing('department_students', [
            'name' => 'Missing Matric',
        ]);
    }

    public function test_department_student_import_requires_a_matric_column_when_a_header_is_used(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.department-students.import'), [
            'file' => UploadedFile::fake()->createWithContent(
                'matrics.csv',
                "name,level\nJohn Doe,100\n"
            ),
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('department_students', 0);
    }

    public function test_admin_can_download_the_department_student_csv_template(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.department-students.template'))
            ->assertOk()
            ->assertDownload('department-students-template.csv');
    }
}
