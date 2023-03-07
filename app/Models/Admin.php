<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use HasApiTokens, HasFactory,SoftDeletes;
       protected $fillable = [
    'name',
    'email',
    'password',
    'image',
    'isSuperAdmin'
];
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)
                    ->withPivot('created_by', 'updated_by', 'deleted_by')
                    ->withTimestamps();
    }
    public function profits_goals()
    {
        return $this->hasMany(ProfitGoal::class);
    }
     public function categories()
    {
        return $this->hasMany(Category::class);
    }

}
