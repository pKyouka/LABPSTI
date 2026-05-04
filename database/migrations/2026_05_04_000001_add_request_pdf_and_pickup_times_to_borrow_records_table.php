<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrow_records', function (Blueprint $table) {
            $table->string('request_pdf_path')->nullable()->after('notes');
            $table->string('approval_pdf_path')->nullable()->after('request_pdf_path');
            $table->string('approval_pdf_md5', 32)->nullable()->after('approval_pdf_path');
            $table->string('approval_pdf_sha256', 64)->nullable()->after('approval_pdf_md5');
            $table->timestamp('ready_at')->nullable()->after('approved_at');
            $table->timestamp('pickup_at')->nullable()->after('ready_at');
        });
    }

    public function down(): void
    {
        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropColumn(['request_pdf_path', 'approval_pdf_path', 'approval_pdf_md5', 'approval_pdf_sha256', 'ready_at', 'pickup_at']);
        });
    }
};
