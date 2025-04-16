<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    
<form action="{{ route('logout') }}" method="POST" class="text-end mb-4">
    @csrf
    <button type="submit" class="btn btn-danger">Logout</button>
</form>

{{-------------------------------------------------------------- START ADMIN SPACE--------------------------------------------------------------}}
@if($content == 'admin_content')
<div class="container py-4">
    <h2 class="mb-4">Welcome, {{ Auth::user()->name }}</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Jumlah Siswa Terdaftar</h5>
            <p class="card-text fs-4">125</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Total Saldo Tersimpan</h5>
            <p class="card-text fs-4">110</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Jumlah Transaksi Top-Up</h5>
            <p class="card-text fs-4">15</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Jumlah Transaksi Withdraw</h5>
            <p class="card-text fs-4">125</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"> Total Transfer Antar Siswa</h5>
            <p class="card-text fs-4">110</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Pending Transaksi</h5>
            <p class="card-text fs-4">15</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel data pengguna -->
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->role_id == 1)
                        <span>Admin</span>
                    @elseif($user->role_id == 2)
                        <span>Bank</span>
                    @else
                        <span>Siswa</span>
                    @endif
                </td>
                <td>
                <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-user-email="{{ $user->email }}">Edit</a>

                    <form action="{{ route("admin.delete", $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')    
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach

            @if ($users->isEmpty())
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada user.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

    
<div class="container py-5">
    <div class="card mb-4 shadow">
        <div class="card-body">
            <div class="container">
                <h1 class="mb-0">Create New User</h1>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select name="role_id" id="form-control">
                            <option value="1">Admin</option>
                            <option value="2">Bank</option>
                            <option value="3" selected>Siswa</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Modal Edit User --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.update') }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="userId">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    //Ambil semua tombol edit
    const editButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
    
    editButtons.forEach(button => {
      button.addEventListener('click', function () {
        const userId = this.getAttribute('data-user-id');
        const userName = this.getAttribute('data-user-name');
        const userEmail = this.getAttribute('data-user-email');
        
        //Isi form modal dengan data pengguna
        document.getElementById('userId').value = userId;
        document.getElementById('name').value = userName;
        document.getElementById('email').value = userEmail;
      });
    });
  });
</script>
</body>
{{--------------------------------------------------------------END ADMIN SPACE--------------------------------------------------------------}}








{{--------------------------------------------------------------START BANK SPACE--------------------------------------------------------------}}
@elseif($content == 'bank_content')
<div class="siswa-section">
    <h2>Siswa Dashboard</h2>
    <div class="container py-5">
        <div class="row">
            <!-- Kartu di kiri -->
            <div class="col-md-6">
                <div class="card mb-4 shadow p-4">
                    <h2 class="mb-4">Top-up Submission List</h2>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <form action="{{ route('bank.cash.deposit', $user->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Deposit</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                        </div>

                        <div class="mb-3">
                    <label for="account_number" class="form-label">Nomor Rekening</label>
                    <input type="text" class="form-control" id="account_number" name="account_number" required>
                </div>
                        <button type="submit" class="btn btn-primary">Deposit</button>
                    </form>
                </div>
            </div>

            <!-- Kartu di kanan -->
            <div class="col-md-6">
                <div class="card mb-4 shadow p-4">
                    <h2 class="mb-4">Penarikan Tunai</h2>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('bank.cash.withdraw', $user->id) }}" method="POST">
                    @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Tarik Tunai</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Tarik</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container py-5">
    <div class="card mb-4 shadow">
        <div class="container">
            <h1 class="mb-0">Create New User</h1>
            <form action="{{ route('bank.create.user') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Add User</button>
            </form>
        </div>
    </div>
</div>
    {{-- Box Daftar User --}}
    
<div class="container mt-4">  
</form>
    <div class="card shadow-sm p-4">
        @if($top_up->isEmpty())
            <p class="text-center text-muted">Tidak ada pengajuan top-up saat ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($top_up as $topup)
                            <tr>
                                <td>{{ $topup->user->name }}</td>
                                <td>Rp{{ number_format($topup->amount, 0, ',', '.') }}</td>
                                <td>
                                    <form action="{{ url('/bank/topups/'.$topup->id.'/approve') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Setujui</button>
                                    </form>
                                    <form action="{{ url('/bank/topups/'.$topup->id.'/reject') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            

        @endif
    </div>
</div>
{{--------------------------------------------------------------END BANK SPACE--------------------------------------------------------------}}







{{--------------------------------------------------------------START SISWA SPACE--------------------------------------------------------------}}
@elseif($content == 'siswa_content')
<div class="bank-mini-section">
                <h2>Siswa Dashboard</h2>
<div class="container py-4">
    <h2 class="mb-4">Dompet Digital Siswa</h2>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        {{-- Top-up --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title">Top-up</h5>
                    <form action="{{ route('siswa.topup') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Top-up</label>
                            <input type="number" class="form-control" name="amount" min="1000" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Top-up</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Withdraw --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tarik Tunai</h5>
                    <form action="{{ route('siswa.withdraw') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Penarikan</label>
                            <input type="number" class="form-control" name="amount" min="1000" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Tarik Tunai</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Transfer --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title">Transfer ke Siswa Lain</h5>
                    <form action="{{ route('siswa.transfer') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="to_account" class="form-label">No Rekening Tujuan</label>
                            <input type="text" class="form-control" name="target_transfer" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Transfer</label>
                            <input type="number" class="form-control" name="amount" min="1000" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="default-section">
                <p>Welcome to the Dashboard!</p>
</div>
@endif
{{--------------------------------------------------------------END SISWA SPACE--------------------------------------------------------------}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>