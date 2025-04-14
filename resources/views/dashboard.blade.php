<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<body class="bg-light">

{{--------------------------------------------------------------ADMIN SPACE--------------------------------------------------------------}}
<div class="container py-5">
    {{-- Box Daftar User --}}
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">List User</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
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




{{--------------------------------------------------------------BANK SPACE--------------------------------------------------------------}}
<div class="container mt-4">
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
    <button type="submit" class="btn btn-primary">Deposit</button>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>