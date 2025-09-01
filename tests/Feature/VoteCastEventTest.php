<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Events\VoteCast;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteCastEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_cast_event_contains_correct_data()
    {
        // Arrange
        $user = User::factory()->create(['user_type' => 2]); // Admin
        $poll = Poll::factory()->create([
            'title' => 'Test Poll',
            'status' => 1,
            'created_by' => $user->id,
            'allow_guest_voting' => true
        ]);

        $option = PollOption::factory()->create([
            'poll_id' => $poll->id,
            'option_text' => 'Test Option',
            'order_index' => 1
        ]);

        // Act
        $event = new VoteCast($poll, $option, 5, 10, 50.0);
        $broadcastData = $event->broadcastWith();

        // Assert
        $this->assertEquals($poll->id, $broadcastData['poll_id']);
        $this->assertEquals('Test Poll', $broadcastData['poll_title']);
        $this->assertEquals($option->id, $broadcastData['option_id']);
        $this->assertEquals('Test Option', $broadcastData['option_text']);
        $this->assertEquals(5, $broadcastData['vote_count']);
        $this->assertEquals(10, $broadcastData['total_votes']);
        $this->assertEquals(50.0, $broadcastData['percentage']);
        $this->assertArrayHasKey('timestamp', $broadcastData);
    }

    public function test_vote_cast_event_broadcasts_on_correct_channel()
    {
        // Arrange
        $user = User::factory()->create(['user_type' => 2]); // Admin
        $poll = Poll::factory()->create(['status' => 1, 'created_by' => $user->id]);
        $option = PollOption::factory()->create(['poll_id' => $poll->id]);

        // Act
        $event = new VoteCast($poll, $option, 1, 1, 100.0);
        $channels = $event->broadcastOn();

        // Assert
        $this->assertCount(1, $channels);
        $this->assertEquals('admin-votes', $channels[0]->name);
    }

    public function test_vote_cast_event_has_correct_broadcast_name()
    {
        // Arrange
        $user = User::factory()->create(['user_type' => 2]); // Admin
        $poll = Poll::factory()->create(['status' => 1, 'created_by' => $user->id]);
        $option = PollOption::factory()->create(['poll_id' => $poll->id]);

        // Act
        $event = new VoteCast($poll, $option, 1, 1, 100.0);
        $broadcastName = $event->broadcastAs();

        // Assert
        $this->assertEquals('vote.cast', $broadcastName);
    }
}
