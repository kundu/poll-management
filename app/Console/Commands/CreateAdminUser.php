<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--name= : Admin user name}
                            {--email= : Admin user email}
                            {--password= : Admin user password}
                            {--interactive : Run in interactive mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the Poll Management System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Poll Management System - Admin User Creation');
        $this->newLine();

        // Check if running in interactive mode or if all required options are provided
        if ($this->option('interactive') || !$this->hasAllRequiredOptions()) {
            return $this->interactiveMode();
        }

        return $this->nonInteractiveMode();
    }

    /**
     * Check if all required options are provided
     */
    private function hasAllRequiredOptions(): bool
    {
        return $this->option('name') &&
               $this->option('email') &&
               $this->option('password');
    }

    /**
     * Run in interactive mode with prompts
     */
    private function interactiveMode(): int
    {
        $this->info('📝 Interactive Admin User Creation');
        $this->newLine();

        // Get admin details
        $name = $this->getAdminName();
        $email = $this->getAdminEmail();
        $password = $this->getAdminPassword();

        return $this->createAdminUser($name, $email, $password);
    }

    /**
     * Run in non-interactive mode using provided options
     */
    private function nonInteractiveMode(): int
    {
        $this->info('⚡ Non-Interactive Admin User Creation');
        $this->newLine();

        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        return $this->createAdminUser($name, $email, $password);
    }

    /**
     * Get admin name with validation
     */
    private function getAdminName(): string
    {
        do {
            $name = $this->ask('👤 Enter admin name');

            if (empty($name)) {
                $this->error('❌ Name is required!');
                continue;
            }

            if (strlen($name) < 2) {
                $this->error('❌ Name must be at least 2 characters long!');
                continue;
            }

            if (strlen($name) > 255) {
                $this->error('❌ Name must not exceed 255 characters!');
                continue;
            }

            break;
        } while (true);

        return $name;
    }

    /**
     * Get admin email with validation
     */
    private function getAdminEmail(): string
    {
        do {
            $email = $this->ask('📧 Enter admin email');

            if (empty($email)) {
                $this->error('❌ Email is required!');
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('❌ Please enter a valid email address!');
                continue;
            }

            // Check if email already exists
            if (User::where('email', $email)->exists()) {
                $this->error('❌ Email already exists! Please use a different email.');
                continue;
            }

            break;
        } while (true);

        return $email;
    }

    /**
     * Get admin password with validation
     */
    private function getAdminPassword(): string
    {
        do {
            $password = $this->secret('🔒 Enter admin password');

            if (empty($password)) {
                $this->error('❌ Password is required!');
                continue;
            }

            if (strlen($password) < 6) {
                $this->error('❌ Password must be at least 6 characters long!');
                continue;
            }

            $confirmPassword = $this->secret('🔒 Confirm admin password');

            if ($password !== $confirmPassword) {
                $this->error('❌ Passwords do not match!');
                continue;
            }

            break;
        } while (true);

        return $password;
    }

    /**
     * Create the admin user
     */
    private function createAdminUser(string $name, string $email, string $password): int
    {
        try {
            // Validate input
            $validator = Validator::make([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ], [
                'name' => 'required|string|min:2|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                $this->error('❌ Validation failed:');
                foreach ($validator->errors()->all() as $error) {
                    $this->error("   - {$error}");
                }
                return 1;
            }

            // Create admin user
            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'user_type' => User::USER_TYPE_ADMIN,
            ]);

            $this->newLine();
            $this->info('✅ Admin user created successfully!');
            $this->newLine();

            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $admin->id],
                    ['Name', $admin->name],
                    ['Email', $admin->email],
                    ['User Type', 'Admin'],
                    ['Created At', $admin->created_at->format('Y-m-d H:i:s')],
                ]
            );

            $this->newLine();
            $this->info('🔑 Login Credentials:');
            $this->line("   Email: {$email}");
            $this->line("   Password: {$password}");
            $this->newLine();

            $this->info('💡 You can now login to the admin panel using these credentials.');
            $this->info('📚 Use the Postman collection to test the API endpoints.');

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Failed to create admin user:');
            $this->error($e->getMessage());
            return 1;
        }
    }
}
