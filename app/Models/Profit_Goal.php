<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit_Goal extends Model
{
    protected $table = 'profits_goals';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    use HasFactory;
}
