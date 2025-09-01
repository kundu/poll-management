<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PollControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $user;

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
    }

    /** @test */
    public function admin_can_create_poll()
    {
        $pollData = [
            'title' => 'Test Poll',
            'description' => 'Test Description',
            'allow_guest_voting' => true,
            'options' => [
                [
                    'option_text' => 'Option 1',
                    'order_index' => 1
                ],
                [
                    'option_text' => 'Option 2',
                    'order_index' => 2
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/polls', $pollData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Poll created successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'allow_guest_voting',
                    'created_by',
                    'options'
                ]
            ]);

        $this->assertDatabaseHas('polls', [
            'title' => 'Test Poll',
            'description' => 'Test Description',
            'allow_guest_voting' => true,
            'created_by' => $this->admin->id,
            'status' => 0
        ]);

        $this->assertDatabaseCount('poll_options', 2);
    }

    /** @test */
    public function regular_user_cannot_create_poll()
    {
        $pollData = [
            'title' => 'Test Poll',
            'description' => 'Test Description',
            'allow_guest_voting' => true,
            'options' => [
                [
                    'option_text' => 'Option 1',
                    'order_index' => 1
                ],
                [
                    'option_text' => 'Option 2',
                    'order_index' => 2
                ]
            ]
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/admin/polls', $pollData);

        $response->assertStatus(403);
    }

    /** @test */
    public function poll_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/polls', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'allow_guest_voting', 'options']);
    }

    /** @test */
    public function poll_creation_validates_title_length()
    {
        $pollData = [
            'title' => str_repeat('a', 101), // Exceeds 100 characters
            'allow_guest_voting' => true,
            'options' => [
                [
                    'option_text' => 'Option 1',
                    'order_index' => 1
                ],
                [
                    'option_text' => 'Option 2',
                    'order_index' => 2
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/polls', $pollData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /** @test */
    public function poll_creation_validates_minimum_options()
    {
        $pollData = [
            'title' => 'Test Poll',
            'allow_guest_voting' => true,
            'options' => [
                [
                    'option_text' => 'Option 1',
                    'order_index' => 1
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/polls', $pollData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['options']);
    }

    /** @test */
    public function poll_creation_validates_maximum_options()
    {
        $options = [];
        for ($i = 1; $i <= 11; $i++) {
            $options[] = [
                'option_text' => "Option {$i}",
                'order_index' => $i
            ];
        }

        $pollData = [
            'title' => 'Test Poll',
            'allow_guest_voting' => true,
            'options' => $options
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/polls', $pollData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['options']);
    }

    /** @test */
    public function admin_can_get_polls_list()
    {
        // Create some test polls
        Poll::factory()->count(3)->create([
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/polls');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'polls',
                    'pagination'
                ]
            ]);
    }

    /** @test */
    public function admin_can_get_poll_details()
    {
        $poll = Poll::factory()->create([
            'created_by' => $this->admin->id
        ]);

        PollOption::factory()->count(3)->create([
            'poll_id' => $poll->id
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/polls/{$poll->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
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
                        'created_by',
                        'options'
                    ]
                ]
            ]);
    }

    /** @test */
    public function returns_404_for_nonexistent_poll()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/polls/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function admin_can_update_poll_status()
    {
        $poll = Poll::factory()->create([
            'created_by' => $this->admin->id,
            'status' => 0 // inactive
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson("/api/admin/polls/{$poll->id}/status", [
                'status' => 1
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Poll status updated successfully'
            ]);

        $this->assertDatabaseHas('polls', [
            'id' => $poll->id,
            'status' => 1
        ]);
    }

    /** @test */
    public function poll_status_update_validates_status_value()
    {
        $poll = Poll::factory()->create([
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->patchJson("/api/admin/polls/{$poll->id}/status", [
                'status' => 2 // invalid status
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function returns_404_when_updating_nonexistent_poll_status()
    {
        $response = $this->actingAs($this->admin)
            ->patchJson('/api/admin/polls/999/status', [
                'status' => 1
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function regular_user_cannot_update_poll_status()
    {
        $poll = Poll::factory()->create([
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/admin/polls/{$poll->id}/status", [
                'status' => 1
            ]);

        $response->assertStatus(403);
    }
}
