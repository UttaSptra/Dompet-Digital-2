<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .no-print { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a href="{{ url()->previous() }}" style="margin-left: 10px;">🔙 Kembali</a>
    </div>

    <h2>Riwayat Transaksi</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>

    <table>
    <thead>
    <tr>
        <th>#</th>
        @if ($user->role_id == 2)
            <th>Nama Siswa</th>
        @endif
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Jumlah</th>
        <th>Saldo Awal</th>
        <th>Saldo Setelah</th>
    </tr>
</thead>
<tbody>
    @forelse ($transactions as $key => $data)
        <tr>
            <td>{{ $key + 1 }}</td>
            @if ($user->role_id == 2)
                <td>{{ $data['transaction']->user->name }}</td>
            @endif
            <td>{{ \Carbon\Carbon::parse($data['transaction']->created_at)->format('d-m-Y') }}</td>
            <td>{{ $data['transaction']->type }}</td>
            <td>Rp {{ number_format($data['transaction']->amount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</td>
            <td>Rp {{ number_format($data['saldo_setelah'], 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
        </tr>
    @endforelse
</tbody>


    </table>

</body>
</html>
