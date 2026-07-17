<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $files = Storage::disk('public')->allFiles('borrow-records');

        foreach ($files as $path) {
            if (Storage::disk('local')->exists($path)) {
                continue;
            }

            Storage::disk('local')->put($path, Storage::disk('public')->get($path));
            Storage::disk('public')->delete($path);
        }
    }

    public function down(): void
    {
        $files = Storage::disk('local')->allFiles('borrow-records');

        foreach ($files as $path) {
            if (Storage::disk('public')->exists($path)) {
                continue;
            }

            Storage::disk('public')->put($path, Storage::disk('local')->get($path));
            Storage::disk('local')->delete($path);
        }
    }
};
