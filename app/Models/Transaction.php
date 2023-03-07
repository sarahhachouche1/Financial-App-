<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $fillable = [ 'title', 'description' , 'amount', 'currency','type','date','start_date','end_date','category_id','created_by','frequency','Paid'];
    public function admins()
    {
       return $this->belongsToMany(Admin::class, 'admins_transactions')
                    ->withPivot('created_by', 'updated_by', 'deleted_by')
                    ->withTimestamps();

    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
