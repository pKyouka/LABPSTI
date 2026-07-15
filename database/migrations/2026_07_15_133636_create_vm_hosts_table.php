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
        Schema::create('vm_hosts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Host name / identifier
            $table->string('ip_address')->nullable(); // Host IP
            $table->integer('cpu_total')->default(0); // Total CPU cores
            $table->integer('cpu_used')->default(0); // CPU cores currently used
            $table->integer('ram_total')->default(0); // Total RAM in MB
            $table->integer('ram_used')->default(0); // RAM currently used in MB
            $table->integer('storage_total')->default(0); // Total storage in GB
            $table->integer('storage_used')->default(0); // Storage currently used in GB
            $table->boolean('is_active')->default(true); // Active status
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vm_hosts');
    }
};