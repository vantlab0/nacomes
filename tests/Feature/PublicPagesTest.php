<?php

namespace Tests\Feature;

use App\Models\Election;
use App\Models\User;
use Database\Seeders\ElectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the homepage returns status 200 and contains essential brand and section content.
     */
    public function test_homepage_loads_successfully_with_brand_and_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('NACOMES');
        $response->assertSee('National Association of Computer Engineering Students');
        $response->assertSee('Vote Now');
        $response->assertSee('Explore Candidates');
        $response->assertSee('Powered by');
        $response->assertSee('Vantlab');
        $response->assertSee('How to Participate');
    }

    /**
     * Test the candidates directory loads successfully.
     */
    public function test_candidates_page_loads_successfully(): void
    {
        $this->seed(ElectionSeeder::class);

        $response = $this->get('/candidates');

        $response->assertStatus(200);
        $response->assertSee('Candidates Directory');
        $response->assertSee('Office of the President');
        $response->assertSee('Powered by');
        $response->assertSee('Vantlab');
    }

    /**
     * Test the student login portal page loads successfully.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Student Voter Portal');
        $response->assertSee('Matric No or Student Email');
        $response->assertSee('Powered by');
        $response->assertSee('Vantlab');
    }

    /**
     * Test the voting desk redirects guests to login.
     */
    public function test_voting_desk_redirects_guests_to_login(): void
    {
        $response = $this->get('/vote');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test the voting desk loads successfully for authenticated and verified students.
     */
    public function test_voting_desk_page_loads_successfully_for_verified_students(): void
    {
        Election::factory()->create(['status' => 'open']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/vote');

        $response->assertStatus(200);
        $response->assertSee('Official Student Digital Ballot');
        $response->assertSee('Powered by');
        $response->assertSee('Vantlab');
    }
}
