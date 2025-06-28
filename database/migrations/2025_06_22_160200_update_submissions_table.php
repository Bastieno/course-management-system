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
        Schema::table('submissions', function (Blueprint $table) {
            // Add content column for text submissions
            $table->text('content')->after('student_id');

            // Add file_name column to store original file names
            $table->string('file_name')->nullable()->after('content');

            // Make file_path nullable since not all submissions have files
            $table->string('file_path')->nullable()->change();

            // Rename score to grade for consistency
            $table->renameColumn('score', 'grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['content', 'file_name']);

            // Rename grade back to score
            $table->renameColumn('grade', 'score');

            // Make file_path required again
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
