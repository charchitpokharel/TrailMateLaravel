<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // The role column is already created in the users table migration.
    }

    public function down(): void
    {
        // No separate column change to reverse.
    }
};
