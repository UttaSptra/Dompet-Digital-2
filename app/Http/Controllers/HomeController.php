<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use app\Models\User;
use App\Models\Transaction;


class HomeController extends Controller
{
    public function dashboard()
    {
        $users = User::all();
        $top_up = Transaction::where('status', 'pending')->get(); 
        return view('dashboard', compact('users', 'top_up'));
    }

    //====================Admin====================\\
    public function adminindex() 
    {
        #Menampilkan seluruh User yang ada di table Users
        $users = User::all();
        return view('dashboard', compact('users', ));
    }
    public function admincreate()
    {
        return view('admin.index');
        
    }
    public function adminstore(Request $request)
    {
        #validasi apa saja yang dibutuhkan
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed',
            'role_id'=>'required|in:1,2,3',
        ]);

        #proses pembuatan user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ];
            
            if ($request->role_id == 3) {
                $userData['balance'] = 0;
            } else {
                $userData['balance'] = null;
            }
        $user = User::create($userData);
            return redirect()->route('dashboard')->with('success', 'User has been successfully created!');
    }
    public function adminedit($id)
    {
        $user = User::findOrFail($id);
        return view('dashboard');  
    }
    public function adminupdate(Request $request)
    {
        
        $user = User::findOrFail($request->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'. $request->id,
            'password' => 'nullable|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if($request->filled('password')){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'User updated successfully!');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();    
        return redirect()->route('admin.index')->with('success', 'User deleted successfully!');
    }

    //====================Bank====================\\

    public function bankindex()
    {
        #Menampilkan seluruh User yang melakukan pengajuan top up di table Users
        $user = Transaction::all();
        $top_up = Transaction::where('status', 'pending')->get();
        return view('dashboard', compact('user', 'top_up'));
    }

    public function bankapprove($id)
    {
        $top_up = Transaction::findOrFail($id);
        $top_up->update([
            'status'=>'approved'
        ]);

        #update saldo
        $user = Transaction::firstOrCreate(['id' => $top_up->id]);
        $user->balance += $top_up->amount;
        $user->save();

        return back()->with('success', 'Top-up Approved');
    }

    public function bankreject($id) {
        $topup = Transaction::findOrFail($id);
        $topup->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('error', 'Top-up Rejected');
    }

    public function bankcashdeposit(Request $request, $userId)
    {
        #Menemukan user yang akan menerima deposit (siswa)
        $user = User::findOrFail($userId);

        #Mengecek apakah user tersebut adalah siswa (role_id = 2)
        if ($user->role_id !== 3) {
            return back()->with('error', 'Hanya siswa yang bisa menerima deposit!');
        }

        #Menambahkan saldo ke akun siswa
        $user->balance += $request->amount; // Menambahkan jumlah deposit ke balance
        $user->save(); // Simpan perubahan saldo

        #Membuat transaksi untuk mencatat deposit
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'approved', // Karena bank yang menyetujui
            'type' => 'deposit', // Jenis transaksi deposit
            'target_transfer' => null, // Tidak ada transfer ke user lain
        ]);

        #Memberikan notifikasi sukses
        return back()->with('success', 'Deposit Successfully!');
    }


}