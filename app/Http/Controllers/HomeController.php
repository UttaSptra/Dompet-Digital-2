<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;

class HomeController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function admin(Request $request) 
    {
    /* Menampilkan seluruh User yang ada di table Users  */
        $users = User::all();
     

    /* Proses pembuatan user  */   

        #validasi apa saja yang dibutuhkan
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed',
            'role_id'=>'required|in:1,2,3',
        ]);

        #proses pembuatan user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::Make($request->password),
            'role_id' => $request->role_id,
        ]);

        if ($request->role_id == 3){
            User::create([
                'id' => $user->id,
                'balance' => 0,
            ]);
        }

    /* Function untuk edit user */   
    $User = User::findOrFail($id);
    
    #validasi/menampilkan data user
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6'
    ]);
    
    $user->name = $request->name;
    $user->email = $request->email;

        return redirect()->route('admin.index')->with('success', 'User has been successfully created!');
    }
}
