<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Index for filtering by department (used by Dept Dashboard)
            $table->index('Dept_id');

            // Index for filtering by status (used by both dashboards for stats)
            $table->index('status');

            // Optional but good for ordering by latest
            $table->index('created_at');

            // A composite index is even better for queries that use both columns
            $table->index(['Dept_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropIndex(['Dept_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['Dept_id', 'status']);
        });
    }
};
