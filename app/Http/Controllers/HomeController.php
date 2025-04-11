<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use app\Models\User;

class HomeController extends Controller
{
    public function dashboard()
    {
        $users = User::all();
        return view('dashboard', compact('users'));
    }

    //====================Admin====================\\
    public function adminindex() 
    {
        #Menampilkan seluruh User yang ada di table Users
        $users = User::all();
        return view('admin.index', compact('users'));
    }
    public function admincreate()
    {
        return view('admin.index');
        
    }
    public function adminstore()
    {
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
            return redirect()->route('admin.index')->with('success', 'User has been successfully created!');
    }
    public function adminedit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.index');  
    }
    public function adminupdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email'. $id,
            'password' => 'nullable|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if($request->filled('password')){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.index')->with('success', 'User updated successfully!');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();    
        return redirect()->route('admin.index')->with('success', 'User deleted successfully!');
    }

    //====================Bank====================\\
}