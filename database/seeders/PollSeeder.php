<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('user_type', User::USER_TYPE_ADMIN)->first();

        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'System Admin',
                'email' => 'admin@pollsystem.com',
                'user_type' => User::USER_TYPE_ADMIN,
            ]);
        }

        // Get regular users for voting
        $users = User::where('user_type', User::USER_TYPE_USER)->get();

        // Create comprehensive poll data
        $pollsData = [
            [
                'title' => 'What is your favorite programming language for web development?',
                'description' => 'Help us understand which programming languages are most popular among developers for building web applications.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'JavaScript', 'order_index' => 1],
                    ['option_text' => 'PHP', 'order_index' => 2],
                    ['option_text' => 'Python', 'order_index' => 3],
                    ['option_text' => 'Java', 'order_index' => 4],
                    ['option_text' => 'C#', 'order_index' => 5],
                    ['option_text' => 'Ruby', 'order_index' => 6],
                ]
            ],
            [
                'title' => 'Which database system do you prefer for your projects?',
                'description' => 'Select your preferred database management system for application development.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'MySQL', 'order_index' => 1],
                    ['option_text' => 'PostgreSQL', 'order_index' => 2],
                    ['option_text' => 'MongoDB', 'order_index' => 3],
                    ['option_text' => 'SQLite', 'order_index' => 4],
                    ['option_text' => 'Redis', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'What is your preferred frontend framework?',
                'description' => 'Choose the frontend framework you use most often for building user interfaces.',
                'allow_guest_voting' => false, // Only authenticated users
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'React', 'order_index' => 1],
                    ['option_text' => 'Vue.js', 'order_index' => 2],
                    ['option_text' => 'Angular', 'order_index' => 3],
                    ['option_text' => 'Svelte', 'order_index' => 4],
                    ['option_text' => 'Alpine.js', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'How do you prefer to deploy your applications?',
                'description' => 'Select your preferred deployment method for web applications.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Cloud Platforms (AWS, Azure, GCP)', 'order_index' => 1],
                    ['option_text' => 'VPS/Dedicated Servers', 'order_index' => 2],
                    ['option_text' => 'Shared Hosting', 'order_index' => 3],
                    ['option_text' => 'Docker Containers', 'order_index' => 4],
                    ['option_text' => 'Serverless Functions', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'What is your experience level in software development?',
                'description' => 'Help us understand the experience distribution in our community.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Beginner (0-2 years)', 'order_index' => 1],
                    ['option_text' => 'Intermediate (2-5 years)', 'order_index' => 2],
                    ['option_text' => 'Advanced (5-10 years)', 'order_index' => 3],
                    ['option_text' => 'Expert (10+ years)', 'order_index' => 4],
                ]
            ],
            [
                'title' => 'Which development methodology do you follow?',
                'description' => 'Select the development methodology you use in your projects.',
                'allow_guest_voting' => false, // Only authenticated users
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Agile/Scrum', 'order_index' => 1],
                    ['option_text' => 'Waterfall', 'order_index' => 2],
                    ['option_text' => 'Kanban', 'order_index' => 3],
                    ['option_text' => 'DevOps', 'order_index' => 4],
                    ['option_text' => 'No specific methodology', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'What is your preferred IDE or code editor?',
                'description' => 'Choose your favorite development environment for coding.',
                'allow_guest_voting' => true,
                'status' => 0, // Inactive (for testing)
                'options' => [
                    ['option_text' => 'Visual Studio Code', 'order_index' => 1],
                    ['option_text' => 'PhpStorm/IntelliJ', 'order_index' => 2],
                    ['option_text' => 'Sublime Text', 'order_index' => 3],
                    ['option_text' => 'Vim/Neovim', 'order_index' => 4],
                    ['option_text' => 'Atom', 'order_index' => 5],
                    ['option_text' => 'Eclipse', 'order_index' => 6],
                ]
            ],
            [
                'title' => 'How important is code documentation in your projects?',
                'description' => 'Rate the importance of code documentation in your development workflow.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Very Important', 'order_index' => 1],
                    ['option_text' => 'Important', 'order_index' => 2],
                    ['option_text' => 'Somewhat Important', 'order_index' => 3],
                    ['option_text' => 'Not Very Important', 'order_index' => 4],
                    ['option_text' => 'Not Important at All', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'Which testing approach do you use most?',
                'description' => 'Select the testing methodology you implement in your projects.',
                'allow_guest_voting' => false, // Only authenticated users
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Unit Testing', 'order_index' => 1],
                    ['option_text' => 'Integration Testing', 'order_index' => 2],
                    ['option_text' => 'End-to-End Testing', 'order_index' => 3],
                    ['option_text' => 'Manual Testing', 'order_index' => 4],
                    ['option_text' => 'No Testing', 'order_index' => 5],
                ]
            ],
            [
                'title' => 'What is your preferred project management tool?',
                'description' => 'Choose the project management tool you use for organizing your work.',
                'allow_guest_voting' => true,
                'status' => 1, // Active
                'options' => [
                    ['option_text' => 'Jira', 'order_index' => 1],
                    ['option_text' => 'Trello', 'order_index' => 2],
                    ['option_text' => 'Asana', 'order_index' => 3],
                    ['option_text' => 'GitHub Projects', 'order_index' => 4],
                    ['option_text' => 'Notion', 'order_index' => 5],
                    ['option_text' => 'No specific tool', 'order_index' => 6],
                ]
            ],
        ];

        foreach ($pollsData as $pollData) {
            // Create poll
            $poll = Poll::create([
                'title' => $pollData['title'],
                'description' => $pollData['description'],
                'status' => $pollData['status'],
                'allow_guest_voting' => $pollData['allow_guest_voting'],
                'created_by' => $admin->id,
            ]);

            // Create poll options
            foreach ($pollData['options'] as $optionData) {
                $option = PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $optionData['option_text'],
                    'order_index' => $optionData['order_index'],
                ]);

                // Add votes for this option (realistic distribution)
                $this->addVotesToOption($poll, $option, $users);
            }
        }

        $this->command->info('✅ Polls seeded successfully!');
        $this->command->info("📊 Created " . count($pollsData) . " polls with realistic voting data.");
    }

    /**
     * Add realistic votes to a poll option
     */
    private function addVotesToOption(Poll $poll, PollOption $option, $users): void
    {
        // Generate realistic vote counts based on option position and poll type
        $baseVotes = rand(5, 25); // Base number of votes

        // Adjust votes based on option position (first options tend to get more votes)
        $positionMultiplier = 1.0 - (($option->order_index - 1) * 0.15);
        $positionMultiplier = max(0.3, $positionMultiplier); // Minimum 30% of base votes

        $voteCount = (int) ($baseVotes * $positionMultiplier);

        // Add some randomness
        $voteCount += rand(-3, 3);
        $voteCount = max(0, $voteCount); // Ensure non-negative

        for ($i = 0; $i < $voteCount; $i++) {
            // 70% chance of authenticated vote, 30% chance of guest vote (if allowed)
            if (rand(1, 100) <= 70 && $users->count() > 0 && $poll->allow_guest_voting) {
                // Authenticated vote
                $user = $users->random();

                // Check if user already voted on this poll
                $existingVote = Vote::where('poll_id', $poll->id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$existingVote) {
                    Vote::create([
                        'poll_id' => $poll->id,
                        'poll_option_id' => $option->id,
                        'user_id' => $user->id,
                        'voter_ip' => null,
                    ]);
                }
            } else {
                // Guest vote (only if allowed)
                if ($poll->allow_guest_voting) {
                    $ip = $this->generateRandomIp();

                    // Check if IP already voted on this poll
                    $existingVote = Vote::where('poll_id', $poll->id)
                        ->where('voter_ip', $ip)
                        ->first();

                    if (!$existingVote) {
                        Vote::create([
                            'poll_id' => $poll->id,
                            'poll_option_id' => $option->id,
                            'user_id' => null,
                            'voter_ip' => $ip,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Generate a random IP address for guest votes
     */
    private function generateRandomIp(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }
}
