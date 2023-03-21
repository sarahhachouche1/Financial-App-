<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Admin;
class ManagerController extends Controller
{
    public function getAll()
    {
       
       $admins = Admin::select('id', 'name', 'email', 'isSuperAdmin')
       ->where('hide', 0)
       ->get();
        return response()->json($admins);
       
    }
    public function removeAdmin($id)
    {
        $admin= Admin::find($id);
        $admin->update(['hide' => 1]);
        return response()->json($admin);
    }
    
    
}


