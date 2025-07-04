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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Student_id')->constrained()->onDelete('cascade');
            $table->foreignId('Dept_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('attachment_path')->nullable();
            $table->enum('status', [
                'pending',
                'solved',
                'checking',
                'rejected',
                'withdrawn',
            ])->default('pending');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
