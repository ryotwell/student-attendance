<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Kelas {{ $xclass->name }}</title>
    <style>
        @page {
            margin: 30px 35px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1f2937;
            font-size: 12px;
        }

        .header {
            margin-bottom: 18px;
            border-bottom: 2px solid #465fff;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #101828;
        }

        .header p {
            margin: 2px 0;
            color: #667085;
            font-size: 11px;
        }

        table.recap {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.recap th {
            background-color: #f9fafb;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.05em;
            color: #667085;
            padding: 8px 10px;
            border-bottom: 1px solid #e4e7ec;
            text-align: left;
        }

        table.recap th.center {
            text-align: center;
        }

        table.recap td {
            padding: 7px 10px;
            border-bottom: 1px solid #f2f4f7;
            font-size: 11px;
        }

        table.recap td.center {
            text-align: center;
        }

        table.recap td.no {
            color: #98a2b3;
            width: 28px;
        }

        .badge {
            display: inline-block;
            min-width: 22px;
            padding: 2px 6px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 10px;
        }

        .badge-hadir { background-color: #dcfce7; color: #166534; }
        .badge-izin { background-color: #dbeafe; color: #1e40af; }
        .badge-sakit { background-color: #fef9c3; color: #854d0e; }
        .badge-alpha { background-color: #fee2e2; color: #991b1b; }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #98a2b3;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Absensi Kelas — {{ $xclass->name }}</h1>
        <p>Tahun Ajaran {{ $xclass->academicYear->name }} &middot; {{ $xclass->academicYear->semester }} &middot; Lintas semua mata pelajaran</p>
        <p>Periode: {{ $monthLabel }}</p>
    </div>

    <table class="recap">
        <thead>
            <tr>
                <th class="center" style="width: 28px;">No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th class="center">Hadir</th>
                <th class="center">Izin</th>
                <th class="center">Sakit</th>
                <th class="center">Alpha</th>
                <th class="center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recap as $index => $row)
                <tr>
                    <td class="center no">{{ $index + 1 }}</td>
                    <td>{{ $row['student']->name }}</td>
                    <td>{{ $row['student']->nis }}</td>
                    <td class="center"><span class="badge badge-hadir">{{ $row['HADIR'] }}</span></td>
                    <td class="center"><span class="badge badge-izin">{{ $row['IZIN'] }}</span></td>
                    <td class="center"><span class="badge badge-sakit">{{ $row['SAKIT'] }}</span></td>
                    <td class="center"><span class="badge badge-alpha">{{ $row['ALPHA'] }}</span></td>
                    <td class="center">{{ $row['rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Belum ada data siswa di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </div>
</body>
</html>