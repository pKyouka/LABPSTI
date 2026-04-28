<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Bulletin;
use App\Models\LabUsageRecord;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function show(Category $category)
    {
        $bulletins = Bulletin::with('category')->where('category_id', $category->id)->latest()->take(3)->get();
        $usageCount = LabUsageRecord::where('category_id', $category->id)->count();
        $categories = Category::all()->unique('name');

        return view('labs.show', compact('category', 'bulletins', 'usageCount', 'categories'));
    }

    public function showBulletin(Category $category, Bulletin $bulletin)
    {
        if ((int) $bulletin->category_id !== (int) $category->id) {
            abort(404);
        }

        $bulletin->setRelation('category', $category);

        $categories = Category::all()->unique('name');

        $relatedBulletins = Bulletin::with('category')->where('category_id', $category->id)
            ->where('id', '!=', $bulletin->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('labs.bulletin-show', compact('category', 'bulletin', 'relatedBulletins', 'categories'));
    }
}
