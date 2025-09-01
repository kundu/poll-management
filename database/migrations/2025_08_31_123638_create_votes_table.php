<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained('poll_options')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('voter_ip', 45)->nullable()->comment('IPv4 or IPv6 address');
            $table->timestamps();

            // Unique constraints to prevent duplicate votes
            $table->unique(['poll_id', 'user_id'], 'unique_auth_vote');
            $table->unique(['poll_id', 'voter_ip'], 'unique_guest_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
