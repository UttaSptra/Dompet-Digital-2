<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use app\Models\User;
use App\Models\Transaction;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Pastikan pengguna sudah login
    }
    
    public function dashboard()
    {
        $user = Auth::user();

    if ($user->role_id == 1) {
        // Aksi untuk Admin
        $content = 'admin_content';
    } elseif ($user->role_id == 2) {
        // Aksi untuk Bank
        $content = 'bank_content';
    } elseif ($user->role_id == 3) {
        // Aksi untuk Siswa
        $content = 'siswa_content';
    } else {
        $content = 'default_content'; // Untuk role yang tidak dikenali
    }

    
        $users = User::all();
        $top_up = Transaction::where('status', 'pending')->get(); 
        return view('dashboard', compact('users','user', 'top_up', 'content'));
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
        return redirect()->route('dashboard')->with('success', 'User deleted successfully!');
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
        $transaction = Transaction::findOrFail($id);
        $user = User::find($transaction->user_id);

        // Pastikan transaksi masih pending
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses sebelumnya.');
        }

        $transaction->approved_by = Auth::id(); 
        
        if ($transaction->type === 'top_up') {
            // APPROVE TOP-UP: tambahkan saldo
            $user->balance += $transaction->amount;
            $transaction->status = 'approved';
            $user->save();
            $transaction->save();

            return back()->with('success', 'Top-up approved.');
        } elseif ($transaction->type === 'withdraw') {
            // APPROVE WITHDRAW: kurangi saldo
            if ($user->balance >= $transaction->amount) {
                $user->balance -= $transaction->amount;
                $transaction->status = 'approved';
                $user->save();
                $transaction->save();

                return back()->with('success', 'Withdraw approved.');
            } else {
                return back()->with('error', 'Saldo user tidak mencukupi untuk withdraw.');
            }
        } else {
            return back()->with('error', 'Jenis transaksi tidak dikenali.');
        }
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
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'account_number' => 'required|numeric|exists:users,account_number',
        ]);

        #Ambil user berdasarkan account_number yang diberikan
        $user = User::where('account_number', $request->account_number)->first();

        if (!$user) {
            return back()->with('error', 'Account not found');
        }

        #Mulai transaksi database
        DB::beginTransaction();

        try {
            #Update saldo
            $user->balance += $request->amount;
            $user->save();    

            #Siapkan data transaksi
            $transactionData = [
                'user_id' => $user->id,
                'account_number' => $user->account_number,  
                'type' => 'top_up',
                'amount' => $request->amount,
                'status' => 'approved',
            ];

            #Hanya masukkan target_transfer jika transaksi adalah transfer
            if ($request->type == 'transfer') {
                $transactionData['target_transfer'] = $request->target_transfer;
            }

            #Simpan data transaksi
            Transaction::create($transactionData);

            DB::commit(); // simpan perubahan kalau semua berhasil
            return back()->with('success', 'Top-up successfully added to student account.');
        } catch (\Exception $e) {
            DB::rollBack(); // batalkan semua jika ada error
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function bankwithdraw (Request $request)
    {
        $request->validate([
            'amount'=> 'required|numeric|min:1000',
            'account_number'=> 'required|numeric|exists:users,account_number',
        ]);

        $user = User::where('account_number', $request->account_number)->first();

        if (!$user) {
            return back()->with('error', 'Account not found.');
        }

        if($user->balance < $request->amount){
            return back()->with('error', 'Your balance is insufficient for this transaction.');
        }

        #Mulai transaksi database
        DB::beginTransaction();

        try {
            #Update saldo
            $user->balance -= $request->amount;
            $user->save();    

            #Siapkan data transaksi
            $transactionData = [
                'user_id' => $user->id,
                'account_number' => $user->account_number,  
                'type' => 'withdraw',
                'amount' => $request->amount,
                'status' => 'approved',
            ];


            #Simpan data transaksi
            Transaction::create($transactionData);

            DB::commit(); // simpan perubahan kalau semua berhasil
            return back()->with('success', 'Top-up successfully added to student account.');
        } catch (\Exception $e) {
            DB::rollBack(); // batalkan semua jika ada error
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }

    }


    public function bankcreateuser (Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email', 
            'password'=>'required|min:6|confirmed',
        ]);

        $userData = [
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role_id' => 3,
        ];

        $user = User::create($userData);
        return redirect()->route('dashboard')->with('success', 'User has been successfully created!');
    }
    

//====================Siswa====================\\
    public function siswaindex ($userId)
    {
        $user = User::find($userId); 
        $balance = $user->balance;

        return view('dashboard', compact('user'));
    }

    public function siswatop_up (Request $request)
    {
        $request->validate([
            'amount'=>'required|numeric|min:1000',
        ]);

        $user = Auth::user();

        Transaction::create([
            'user_id'=> Auth::id(),
            'account_number' => $user->account_number,
            'amount'=>$request->amount,
            'type'=>'top_up',
            'status'=>'pending',
        ]);

        return back()->with('success', 'Top-up request successfully submitted!');
    }

    public function siswatransfer(Request $request)
    {
        $request->validate([
            'target_transfer' => 'required|exists:users,account_number',
            'amount' => 'required|numeric|min:1000',
        ]);

        $fromUser = Auth::user();

        if ($request->target_transfer === $fromUser->account_number) {
            return response()->json(['message' => 'You cannot transfer to your own account!'], 400);
        }

        $toUser = User::where('account_number', $request->target_transfer)->first();
        $amount = $request->amount;

        if ($fromUser->balance < $amount) {
            return redirect()->back()->with('success', 'Your balance is insufficient!');
        }

        DB::transaction(function () use ($fromUser, $toUser, $amount) {
            $fromUser->decrement('balance', $amount);
            $toUser->increment('balance', $amount);

            Transaction::create([
                'user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'account_number' => $fromUser->account_number,
                'target_transfer' => $toUser->account_number,
                'amount' => $amount,
                'type' => 'transfer',
                'status' => 'approved',
            ]);
        });

        return redirect()->back()->with('success', 'Transfer successfully!');

    }


    public function siswawithdraw(Request $request)
    {
        $request->validate([
            'amount'=> 'required|numeric|min:1000',
        ]);

        $user = Auth::user();

        #Cek saldo cukup
        if ($user->balance < $request->amount) {
            return back()->with('error', 'Your balance is insufficient for this transaction.');
        }

        #Mulai transaksi database
        DB::beginTransaction();

        try {
            #Update saldo user
            $user->balance -= $request->amount;
            $user->save();

            #Siapkan data transaksi
            $transactionData = [
                'user_id' => $user->id,
                'account_number' => $user->account_number,
                'type' => 'withdraw',
                'amount' => $request->amount,
                'status' => 'pending',
            ];

            #Simpan data transaksi
            Transaction::create($transactionData);

            DB::commit();  

            return back()->with('success', 'Your withdrawal request has been submitted and is waiting for bank approval.');
        } catch (\Exception $e) {
            DB::rollBack();  // Batalkan transaksi jika ada error
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
   

}