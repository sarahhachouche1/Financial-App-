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
    protected $fillable = ['email', 'title', 'description' , 'amount', 'currency','type','date','start_date','end_date','category_id','admin_id','frequency','Paid','updated_by'];
    public function admins()
    {
       return $this->belongsToMany(Admin::class, 'admins_transactions')
                    ->withPivot('admin_id', 'updated_by', 'deleted_by')
                    ->withTimestamps();

    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
 
    public function created_by_admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
