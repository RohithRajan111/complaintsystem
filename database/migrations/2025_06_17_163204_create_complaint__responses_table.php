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
        Schema::create('complaint__responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->foreignId('Dept_id')->constrained('depts')->onDelete('cascade');
            $table->foreignId('Student_id')->constrained('students')->onDelete('cascade');
            $table->text('response');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint__responses');
    }
};
