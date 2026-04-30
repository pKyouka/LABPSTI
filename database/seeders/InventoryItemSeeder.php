<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $groups = config('inventory_items.groups', []);

        foreach ($groups as $group) {
            $category = (string) Arr::get($group, 'category', 'Umum');

            foreach ((array) Arr::get($group, 'items', []) as $item) {
                $name = (string) Arr::get($item, 'name', '');
                $quantity = max(1, (int) Arr::get($item, 'quantity', 1));

                if ($name === '') {
                    continue;
                }

                $code = Str::slug($category.'-'.$name);

                InventoryItem::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'category' => $category,
                        'description' => Arr::get($item, 'description'),
                        'unit' => (string) Arr::get($item, 'unit', 'unit'),
                        'total_stock' => $quantity,
                        'available_stock' => (int) Arr::get($item, 'available_stock', $quantity),
                        'location' => Arr::get($item, 'location'),
                        'condition' => (string) Arr::get($item, 'condition', 'baik'),
                        'is_active' => (bool) Arr::get($item, 'is_active', true),
                    ]
                );
            }
        }
    }
}
