<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vm_borrow_requests', function (Blueprint $table) {
            $table->enum('reset_status', ['none','pending','approved','rejected'])->default('none')->after('status');
            $table->text('reset_reason')->nullable()->after('reset_status');
            $table->timestamp('reset_requested_at')->nullable()->after('reset_reason');
            $table->foreignId('reset_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('reset_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('vm_borrow_requests', function (Blueprint $table) {
            $table->dropColumn(['reset_status','reset_reason','reset_requested_at','reset_approved_by']);
        });
    }
};
