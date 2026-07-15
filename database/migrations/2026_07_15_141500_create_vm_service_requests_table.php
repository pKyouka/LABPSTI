<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vm_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vm_borrow_request_id')->constrained('vm_borrow_requests')->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('category', [
                'open_port',
                'install_software',
                'edit_config',
                'add_storage',
                'add_ram',
                'add_cpu',
                'reset_vm',
                'other'
            ]);
            $table->string('title');
            $table->text('description');
            $table->string('port_number')->nullable();
            $table->string('software_name')->nullable();
            $table->string('config_path')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vm_service_requests');
    }
};