<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proxmox_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // label admin, mis. "Cluster Lab A"
            $table->string('host'); // https://192.168.1.10:8006
            $table->string('user')->default('root@pam');
            $table->text('password'); // encrypted cast
            $table->string('node')->nullable(); // default node name
            $table->string('storage')->default('local-zfs');
            $table->string('bridge')->default('vmbr0');
            $table->unsignedInteger('template_vmid')->nullable();
            $table->boolean('verify_ssl')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proxmox_servers');
    }
};
