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
        Schema::table('BVSCalls', function (Blueprint $table) {
            // Add indexes for performance optimization
            if (!$this->indexExists('BVSCalls', 'BVSCalls_call_time_index')) {
                $table->index('CallTime', 'BVSCalls_call_time_index');
            }
            
            if (!$this->indexExists('BVSCalls', 'BVSCalls_call_from_index')) {
                $table->index('CallFrom', 'BVSCalls_call_from_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('BVSCalls', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex('BVSCalls_call_time_index');
            $table->dropIndex('BVSCalls_call_from_index');
        });
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $index)
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        return $doctrineTable->hasIndex($index);
    }
};
