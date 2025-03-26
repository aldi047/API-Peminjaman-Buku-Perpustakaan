<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .page {
            width: 100%;
            padding: 20px;
            position: relative;
            page-break-before: always;
            margin-bottom: 30px;
        }

        .page:first-child {
            page-break-before: avoid;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page">
        <h2 class="text-center">Laporan Peminjaman</h2>

        @foreach ($peminjamanList as $peminjaman)
            <table>
                <thead>
                    <tr>
                        <th colspan="4" class="text-center">Peminjaman ID: {{ $peminjaman->peminjaman_id }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Waktu Peminjaman:</strong> <br>{{ $peminjaman->waktu_peminjaman }}</td>
                        <td><strong>Durasi Peminjaman:</strong> <br>{{ $peminjaman->durasi_peminjaman_in_days }} Hari</td>
                        <td rowspan="2">
                            <strong>Nama Buku:</strong> {{ $peminjaman->detailBuku->nama }} <br>
                            <strong>ISBN:</strong> {{ $peminjaman->detailBuku->isbn }} <br>
                            <strong>Pengarang:</strong> {{ $peminjaman->detailBuku->pengarang }} <br>
                        </td>
                        <td rowspan="2" class="text-center">
                            <img
                            src="{{storage_path("app/public/{$peminjaman->detailBuku->foto}")}}"
                            width="100">
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Waktu Pengembalian:</strong> <br>{{ $peminjaman->waktu_pengembalian }}</td>
                        <td><strong>Total Denda:</strong> <br>Rp {{ number_format($peminjaman->total_denda, 0, ',', '.') }}</td>
                    </tr>

                    <tr>
                        <td><strong>Peminjam:</strong> <br>{{ $peminjaman->detailPeminjam->nama }} <br>({{ $peminjaman->detailPeminjam->email }})</td>
                        <td><strong>Petugas:</strong> <br>{{ $peminjaman->detailPetugas->nama }} <br>({{ $peminjaman->detailPetugas->email }})</td>
                        <td colspan="2"><strong>Total Keterlambatan:</strong> {{ $peminjaman->total_keterlambatan_in_days }} Hari</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
