<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<form action="{{ route('logout') }}" method="POST" class="d-flex justify-content-end mb-4">
    @csrf
    <button type="submit" class="btn btn-danger">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
    </button>
</form>


{{-------------------------------------------------------------- START ADMIN SPACE--------------------------------------------------------------}}
@if($content == 'admin_content')
<div class="container py-4">
    <h2 class="mb-4">Welcome, {{ Auth::user()->name }}</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        @php
            $summary = [
                ['Jumlah Siswa Terdaftar', $totalSiswa],
                ['Total Saldo Tersimpan', $totalSaldo],
                ['Jumlah Transaksi Top-Up', $totalTopup],
                ['Jumlah Transaksi Withdraw', $totalWithdraw],
                ['Total Transfer Antar Siswa', $totalTransfer],
                ['Pending Transaksi', $totalPending]
            ];
        @endphp

        @foreach($summary as $index => $item)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $item[0] }}</h5>
                        <p class="card-text fs-4">{{ $item[1] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Table Users -->
    <div class="card mb-5">
        <div class="card-body">
            <h4 class="card-title mb-3">Daftar Pengguna</h4>
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
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @switch($user->role_id)
                                    @case(1) Admin @break
                                    @case(2) Bank @break
                                    @default Siswa
                                @endswitch
                            </td>
                            <td>
                                <a href="javascript:void(0)" 
                                   class="btn btn-sm btn-warning" 
                                   data-bs-toggle="modal" 
                                   data-bs-target="#editModal" 
                                   data-user-id="{{ $user->id }}" 
                                   data-user-name="{{ $user->name }}" 
                                   data-user-email="{{ $user->email }}">
                                    Edit
                                </a>

                                <form action="{{ route('admin.delete', $user->id) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Belum ada user.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Tambah User -->
    <div class="card shadow mb-5">
        <div class="card-body">
            <h4 class="mb-3">Tambah Pengguna Baru</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select" required>
                        <option value="1">Admin</option>
                        <option value="2">Bank</option>
                        <option value="3" selected>Siswa</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Tambah User</button>
            </form>
        </div>
    </div>
</div>

    <!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.update') }}" method="POST" class="modal-content" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" name="id" id="userId">

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('userId').value = this.dataset.userId;
                document.getElementById('name').value = this.dataset.userName;
                document.getElementById('email').value = this.dataset.userEmail;
            });
        });
    });
</script>
{{--------------------------------------------------------------END ADMIN SPACE--------------------------------------------------------------}}








{{--------------------------------------------------------------START BANK SPACE--------------------------------------------------------------}}
@elseif($content == 'bank_content')
<div class="container py-5">
    <h2 class="mb-4">Dashboard Bank</h2>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="row">
        <!-- Form Top-Up -->
        <div class="col-md-6">
            <div class="card mb-4 shadow p-4">
                <h4 class="mb-3">Top-up Submission</h4>

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

        <!-- Form Withdraw -->
        <div class="col-md-6">
            <div class="card mb-4 shadow p-4">
                <h4 class="mb-3">Penarikan Tunai</h4>

                <form action="{{ route('bank.cash.withdraw', $user->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Tarik Tunai</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tarik</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Create New User -->
    <div class="card mb-5 shadow p-4">
        <h4 class="mb-3">Create New Siswa</h4>
        <form action="{{ route('bank.create.user') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Add User</button>
        </form>
    </div>

    <!-- Tabel Pengajuan Top-up -->
    <div class="card mb-5 shadow p-4">
        <h4 class="mb-3">Daftar Pengajuan Top-up</h4>
        @if($top_up->isEmpty())
            <p class="text-muted">Tidak ada pengajuan top-up saat ini.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
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
                                    <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                </form>
                                <form action="{{ url('/bank/topups/'.$topup->id.'/reject') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Tabel Daftar User (Siswa) -->
    <div class="card mb-5 shadow p-4">
        <h4 class="mb-3">Daftar Siswa</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Nomor Rekening</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\User::where('role_id', 3)->get() as $siswa)
                        <tr>
                            <td>{{ $siswa->name }}</td>
                            <td>{{ $siswa->email }}</td>
                            <td>{{ $siswa->account_number }}</td>
                            <td>Rp{{ number_format($siswa->balance, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Histori Transaksi -->
    <div class="card mb-5 shadow p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-3">Riwayat Transaksi</h4>
            <a href="{{ route('transaction.print', $user->id) }}" class="btn btn-danger">
                Export PDF
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Jenis Transaksi</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\Transaction::latest()->get() as $trx)
                        <tr>
                            <td>{{ $trx->user->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $trx->type)) }}</td>
                            <td>Rp{{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($trx->status) }}</td>
                            <td>{{ $trx->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{--------------------------------------------------------------END BANK SPACE--------------------------------------------------------------}}







{{--------------------------------------------------------------START SISWA SPACE--------------------------------------------------------------}}
@elseif($content == 'siswa_content')
<div class="bank-mini-section">
    <h2 class="text-center my-4">Dashboard Siswa</h2>

    <div class="container py-4">

        {{-- Informasi Siswa --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Siswa</h5>
                        <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>No. Rekening:</strong> {{ Auth::user()->account_number }}</p>
                        <p><strong>Saldo Saat Ini:</strong> Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

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
                                <label for="target_transfer" class="form-label">No Rekening Tujuan</label>
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

        {{-- Riwayat Transaksi --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Riwayat Transaksi</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Saldo Awal</th>
                                <th>Saldo Setelah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $key => $t)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ ucfirst($t->type) }}</td>
                                    <td>Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t->saldo_awal, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($t->saldo_setelah, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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