<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('vm_borrow_requests')
            ->whereNotNull('vm_password')
            ->where('vm_password', '!=', '')
            ->get(['id', 'vm_password']);

        foreach ($rows as $row) {
            try {
                Crypt::decryptString($row->vm_password);
                continue; // already encrypted
            } catch (\Throwable) {
                // plaintext → encrypt
            }

            DB::table('vm_borrow_requests')
                ->where('id', $row->id)
                ->update([
                    'vm_password' => Crypt::encryptString($row->vm_password),
                ]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('vm_borrow_requests')
            ->whereNotNull('vm_password')
            ->where('vm_password', '!=', '')
            ->get(['id', 'vm_password']);

        foreach ($rows as $row) {
            try {
                $plain = Crypt::decryptString($row->vm_password);
            } catch (\Throwable) {
                continue;
            }

            DB::table('vm_borrow_requests')
                ->where('id', $row->id)
                ->update(['vm_password' => $plain]);
        }
    }
};
