<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PublicPollControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $user;
    protected Poll $activePoll;
    protected Poll $inactivePoll;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin and regular user
        $this->admin = User::factory()->create([
            'user_type' => User::USER_TYPE_ADMIN
        ]);

        $this->user = User::factory()->create([
            'user_type' => User::USER_TYPE_USER
        ]);

        // Create active poll
        $this->activePoll = Poll::factory()->create([
            'created_by' => $this->admin->id,
            'status' => 1, // active
            'allow_guest_voting' => true,
        ]);

        PollOption::factory()->count(3)->create([
            'poll_id' => $this->activePoll->id
        ]);

        // Create inactive poll
        $this->inactivePoll = Poll::factory()->create([
            'created_by' => $this->admin->id,
            'status' => 0, // inactive
            'allow_guest_voting' => true,
        ]);

        PollOption::factory()->count(2)->create([
            'poll_id' => $this->inactivePoll->id
        ]);
    }

    public function test_can_get_active_polls()
    {
        $response = $this->getJson('/api/polls');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Polls retrieved successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'polls',
                    'pagination'
                ]
            ]);

        // Should only return active polls
        $polls = $response->json('data.polls');
        $this->assertCount(1, $polls);
        $this->assertEquals($this->activePoll->id, $polls[0]['id']);
    }

    public function test_can_get_active_poll_details()
    {
        $response = $this->getJson("/api/polls/{$this->activePoll->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Poll details retrieved successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'poll' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'allow_guest_voting',
                        'has_voted',
                        'options'
                    ]
                ]
            ]);

        $this->assertEquals(false, $response->json('data.poll.has_voted'));
    }

    public function test_cannot_get_inactive_poll_details()
    {
        $response = $this->getJson("/api/polls/{$this->inactivePoll->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Poll is not available'
            ]);
    }

    public function test_authenticated_user_can_vote()
    {
        $option = $this->activePoll->options->first();
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => $option->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vote submitted successfully'
            ]);

        $this->assertDatabaseHas('votes', [
            'poll_id' => $this->activePoll->id,
            'poll_option_id' => $option->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_guest_can_vote_when_allowed()
    {
        $option = $this->activePoll->options->first();

        $response = $this->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => $option->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Vote submitted successfully'
            ]);

        $this->assertDatabaseHas('votes', [
            'poll_id' => $this->activePoll->id,
            'poll_option_id' => $option->id,
            'user_id' => null,
        ]);
    }

    public function test_guest_cannot_vote_when_not_allowed()
    {
        // Update poll to disallow guest voting
        $this->activePoll->update(['allow_guest_voting' => false]);
        $option = $this->activePoll->options->first();

        $response = $this->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => $option->id
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Guest voting is not allowed for this poll'
            ]);
    }

    public function test_user_cannot_vote_twice()
    {
        $option = $this->activePoll->options->first();
        $token = $this->user->createToken('test_token')->plainTextToken;

        // First vote
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => $option->id
        ]);

        // Second vote attempt
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => $option->id
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
                'message' => 'You have already voted on this poll'
            ]);
    }

    public function test_vote_validates_option_exists()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => 999999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['poll_option_id']);
    }

    public function test_vote_validates_required_fields()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/polls/{$this->activePoll->id}/vote", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['poll_option_id']);
    }

    public function test_can_get_poll_results()
    {
        // Add some votes
        $option1 = $this->activePoll->options->first();
        $option2 = $this->activePoll->options->skip(1)->first();

        for ($i = 0; $i < 3; $i++) {
            Vote::create([
                'poll_id' => $this->activePoll->id,
                'poll_option_id' => $option1->id,
                'user_id' => User::factory()->create()->id,
                'voter_ip' => null,
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            Vote::create([
                'poll_id' => $this->activePoll->id,
                'poll_option_id' => $option2->id,
                'user_id' => User::factory()->create()->id,
                'voter_ip' => null,
            ]);
        }

        $response = $this->getJson("/api/polls/{$this->activePoll->id}/results");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Poll results retrieved successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'poll' => [
                        'id',
                        'title',
                        'description',
                        'total_votes'
                    ],
                    'results' => [
                        '*' => [
                            'id',
                            'option_text',
                            'order_index',
                            'votes_count',
                            'percentage'
                        ]
                    ]
                ]
            ]);

        $this->assertEquals(5, $response->json('data.poll.total_votes'));
    }

    public function test_cannot_get_inactive_poll_results()
    {
        $response = $this->getJson("/api/polls/{$this->inactivePoll->id}/results");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Poll results are not available'
            ]);
    }

    public function test_poll_shows_has_voted_for_authenticated_user()
    {
        $option = $this->activePoll->options->first();
        $token = $this->user->createToken('test_token')->plainTextToken;

        // Vote first
        Vote::create([
            'poll_id' => $this->activePoll->id,
            'poll_option_id' => $option->id,
            'user_id' => $this->user->id,
        ]);

        // Check poll details
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/polls/{$this->activePoll->id}");

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json('data.poll.has_voted'));
    }

    public function test_returns_404_for_nonexistent_poll()
    {
        $response = $this->getJson('/api/polls/999999');

        $response->assertStatus(404);
    }

    public function test_returns_422_for_vote_with_invalid_option()
    {
        $response = $this->postJson("/api/polls/{$this->activePoll->id}/vote", [
            'poll_option_id' => 999999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['poll_option_id']);
    }

    public function test_returns_404_for_results_of_nonexistent_poll()
    {
        $response = $this->getJson('/api/polls/999999/results');

        $response->assertStatus(404);
    }
}
