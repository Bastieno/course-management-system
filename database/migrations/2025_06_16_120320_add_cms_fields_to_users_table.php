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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'lecturer', 'student'])->default('student')->after('password');
            $table->string('profile_picture')->nullable()->after('role');
            $table->string('phone')->nullable()->after('profile_picture');
            $table->string('student_id')->nullable()->unique()->after('phone');
            $table->string('department')->nullable()->after('student_id');
            $table->string('level')->nullable()->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_picture', 'phone', 'student_id', 'department', 'level']);
        });
    }
};
