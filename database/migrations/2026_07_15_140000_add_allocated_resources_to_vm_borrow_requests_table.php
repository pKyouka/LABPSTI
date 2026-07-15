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
        Schema::table('vm_borrow_requests', function (Blueprint $table) {
            $table->integer('allocated_cpu')->nullable()->after('host_ip')->default(1);
            $table->integer('allocated_ram')->nullable()->after('allocated_cpu')->default(1024);
            $table->integer('allocated_storage')->nullable()->after('allocated_ram')->default(10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vm_borrow_requests', function (Blueprint $table) {
            $table->dropColumn(['allocated_cpu', 'allocated_ram', 'allocated_storage']);
        });
    }
};