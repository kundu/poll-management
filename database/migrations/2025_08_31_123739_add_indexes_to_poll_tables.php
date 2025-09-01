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
        // Add indexes to polls table
        Schema::table('polls', function (Blueprint $table) {
            $table->index('status', 'idx_polls_status');
            $table->index('created_by', 'idx_polls_created_by');
        });

        // Add indexes to poll_options table
        Schema::table('poll_options', function (Blueprint $table) {
            $table->index(['poll_id', 'order_index'], 'idx_poll_options_poll_order');
        });

        // Add indexes to votes table
        Schema::table('votes', function (Blueprint $table) {
            $table->index('poll_id', 'idx_votes_poll_id');
            $table->index('poll_option_id', 'idx_votes_poll_option_id');
            $table->index('user_id', 'idx_votes_user_id');
            $table->index('voter_ip', 'idx_votes_voter_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from polls table
        Schema::table('polls', function (Blueprint $table) {
            $table->dropIndex('idx_polls_status');
            $table->dropIndex('idx_polls_created_by');
        });

        // Remove indexes from poll_options table
        Schema::table('poll_options', function (Blueprint $table) {
            $table->dropIndex('idx_poll_options_poll_order');
        });

        // Remove indexes from votes table
        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex('idx_votes_poll_id');
            $table->dropIndex('idx_votes_poll_option_id');
            $table->dropIndex('idx_votes_user_id');
            $table->dropIndex('idx_votes_voter_ip');
        });
    }
};
