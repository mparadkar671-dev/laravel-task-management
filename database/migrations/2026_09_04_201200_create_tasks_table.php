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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
        $table->enum('status', ['pending', 'in-progress', 'completed'])->default('pending');
        $table->date('due_date')->nullable();
        
        // Foreign key for the user assigned to the task
        $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
        
        // Foreign key for the person who created the task (Admin/Manager)
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

        $table->timestamps();

        // Adding indexes for performance (required by the assignment)
        $table->index('status');
        $table->index('priority');
        $table->index('due_date');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
