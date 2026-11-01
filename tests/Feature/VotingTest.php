<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_voting_booth(): void
    {
        $response = $this->get('/vote');

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_students_cannot_access_voting_booth(): void
    {
        $unverifiedStudent = User::factory()->unverified()->create();

        $response = $this->actingAs($unverifiedStudent)->get('/vote');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_verified_students_can_view_active_voting_booth_with_positions(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id, 'title' => 'President']);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
            'name' => 'Adebayo Ogundipe',
        ]);

        $response = $this->actingAs($student)->get('/vote');

        $response->assertStatus(200);
        $response->assertSee('President');
        $response->assertSee('Adebayo Ogundipe');
        $response->assertSee('Select Candidate');
    }

    public function test_voting_booth_displays_closed_screen_when_election_is_not_open(): void
    {
        $student = User::factory()->create();
        Election::factory()->closed()->create(['title' => 'Concluded Election']);

        $response = $this->actingAs($student)->get('/vote');

        $response->assertStatus(200);
        $response->assertSee('Voting Booth Currently Closed');
        $response->assertSee('Concluded Election');
    }

    public function test_students_can_successfully_submit_ballot(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create(['status' => 'open']);
        $position1 = Position::factory()->create(['election_id' => $election->id, 'title' => 'President', 'display_order' => 1]);
        $position2 = Position::factory()->create(['election_id' => $election->id, 'title' => 'Vice President', 'display_order' => 2]);

        $candidate1 = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position1->id,
            'name' => 'Candidate Alpha',
        ]);
        $candidate2 = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position2->id,
            'name' => 'Candidate Beta',
        ]);

        $response = $this->actingAs($student)->post('/vote', [
            'election_id' => $election->id,
            'votes' => [
                $position1->id => $candidate1->id,
                $position2->id => $candidate2->id,
            ],
        ]);

        $response->assertRedirect(route('vote'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('votes', [
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position1->id,
            'candidate_id' => $candidate1->id,
        ]);

        $this->assertDatabaseHas('votes', [
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position2->id,
            'candidate_id' => $candidate2->id,
        ]);

        // Accessing /vote now shows completed ballot receipt
        $receiptResponse = $this->actingAs($student)->get('/vote');
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Your Vote Has Been Recorded');
        $receiptResponse->assertSee('Candidate Alpha');
        $receiptResponse->assertSee('Candidate Beta');
    }

    public function test_duplicate_vote_submission_is_prevented_by_validation_and_database_constraints(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate1 = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);
        $candidate2 = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);

        // Record first vote
        Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate1->id,
        ]);

        // Attempt second vote submission for the same position
        $response = $this->actingAs($student)->post('/vote', [
            'election_id' => $election->id,
            'votes' => [
                $position->id => $candidate2->id,
            ],
        ]);

        $response->assertSessionHasErrors('votes');

        // Verify the original vote was not modified
        $this->assertDatabaseHas('votes', [
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate1->id,
        ]);

        $this->assertDatabaseMissing('votes', [
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate2->id,
        ]);
    }

    public function test_database_level_unique_constraint_blocks_concurrent_duplicate_votes(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create();
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);

        Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
        ]);

        $this->expectException(QueryException::class);

        // Direct DB attempt simulating concurrent insertion
        Vote::create([
            'election_id' => $election->id,
            'user_id' => $student->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate->id,
        ]);
    }

    public function test_cannot_vote_in_closed_election(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->closed()->create();
        $position = Position::factory()->create(['election_id' => $election->id]);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);

        $response = $this->actingAs($student)->post('/vote', [
            'election_id' => $election->id,
            'votes' => [
                $position->id => $candidate->id,
            ],
        ]);

        $response->assertSessionHasErrors('election_id');
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_cannot_vote_for_candidate_belonging_to_another_position(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create(['status' => 'open']);
        $positionA = Position::factory()->create(['election_id' => $election->id, 'title' => 'Position A']);
        $positionB = Position::factory()->create(['election_id' => $election->id, 'title' => 'Position B']);

        $candidateFromB = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $positionB->id,
        ]);

        // Attempt to vote for Candidate B under Position A
        $response = $this->actingAs($student)->post('/vote', [
            'election_id' => $election->id,
            'votes' => [
                $positionA->id => $candidateFromB->id,
            ],
        ]);

        $response->assertSessionHasErrors("votes.{$positionA->id}");
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_cannot_vote_for_unpublished_candidate(): void
    {
        $student = User::factory()->create();
        $election = Election::factory()->create(['status' => 'open']);
        $position = Position::factory()->create(['election_id' => $election->id]);

        $unpublishedCandidate = Candidate::factory()->unpublished()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
        ]);

        $response = $this->actingAs($student)->post('/vote', [
            'election_id' => $election->id,
            'votes' => [
                $position->id => $unpublishedCandidate->id,
            ],
        ]);

        $response->assertSessionHasErrors("votes.{$position->id}");
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_candidates_page_groups_by_position_and_hides_results_when_not_authorized(): void
    {
        $election = Election::factory()->create([
            'status' => 'open',
        ]);
        $position = Position::factory()->create([
            'election_id' => $election->id,
            'title' => 'General Secretary',
        ]);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
            'name' => 'Emeka Nnamdi',
        ]);

        $response = $this->get('/candidates');

        $response->assertStatus(200);
        $response->assertSee('General Secretary');
        $response->assertSee('Emeka Nnamdi');
        $response->assertSee('Live Public Poll Progress');
        $response->assertSee('Live Poll Data');
        $response->assertSee('Vote Share');
    }

    public function test_candidate_detail_page_loads_successfully(): void
    {
        $election = Election::factory()->create();
        $position = Position::factory()->create(['election_id' => $election->id, 'title' => 'Public Relations Officer']);
        $candidate = Candidate::factory()->create([
            'election_id' => $election->id,
            'position_id' => $position->id,
            'name' => 'Aisha Abubakar',
        ]);

        $response = $this->get("/candidates/{$candidate->id}");

        $response->assertStatus(200);
        $response->assertSee('Aisha Abubakar');
        $response->assertSee('Public Relations Officer');
        $response->assertSee('Candidate Profile &amp; Office Details', false);
    }
}
