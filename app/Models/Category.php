<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function bulletins()
    {
        return $this->hasMany(Bulletin::class);
    }

    public function labUsageRecords()
    {
        return $this->hasMany(LabUsageRecord::class);
    }
}
