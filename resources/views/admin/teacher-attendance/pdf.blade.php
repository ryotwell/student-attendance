<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Absensi Guru
    </title>

    <style>

        @page {
            margin: 25px 25px 35px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #555;
        }

        .filter-box {
            border: 1px solid #ddd;
            background: #f8f8f8;
            padding: 10px;
            margin-bottom: 15px;
        }

        .filter-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .filter-box td {
            border: none;
            padding: 3px 5px;
        }

        .filter-label {
            width: 100px;
            font-weight: bold;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
        }

        .attendance-table th {
            background: #f1f1f1;
            border: 1px solid #bbb;
            padding: 7px 5px;
            text-align: center;
            font-size: 9px;
        }

        .attendance-table td {
            border: 1px solid #ccc;
            padding: 6px 5px;
            font-size: 9px;
        }

        .center {
            text-align: center;
        }

        .status-hadir {
            color: #198754;
            font-weight: bold;
        }

        .status-izin {
            color: #997404;
            font-weight: bold;
        }

        .status-sakit {
            color: #b02a37;
            font-weight: bold;
        }

        .status-alpha {
            color: #555;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            color: #777;
            font-size: 8px;
        }

    </style>

</head>


<body>

    {{-- ================================================= --}}
    {{-- HEADER --}}
    {{-- ================================================= --}}

    <div class="header">

        <h1>
            LAPORAN ABSENSI GURU
        </h1>

        <p>
            Data Kehadiran Guru dan Guru BK
        </p>

    </div>


    {{-- ================================================= --}}
    {{-- FILTER --}}
    {{-- ================================================= --}}

    <div class="filter-box">

        <table>

            @if ($filterDate)

                <tr>

                    <td class="filter-label">
                        Tanggal
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}
                    </td>

                </tr>

            @elseif ($filterMonth)

                <tr>

                    <td class="filter-label">
                        Periode
                    </td>

                    <td>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $filterMonth)->translatedFormat('F Y') }}
                    </td>

                </tr>

            @else

                <tr>

                    <td class="filter-label">
                        Periode
                    </td>

                    <td>
                        Semua Data
                    </td>

                </tr>

            @endif


            @if ($teacher)

                <tr>

                    <td class="filter-label">
                        Guru
                    </td>

                    <td>

                        {{ $teacher->name }}

                        @if ($teacher->role === 'GURU_BK')
                            (Guru BK)
                        @else
                            (Guru)
                        @endif

                    </td>

                </tr>

            @endif


            @if ($filterStatus)

                <tr>

                    <td class="filter-label">
                        Status
                    </td>

                    <td>

                        @switch(strtoupper($filterStatus))

                            @case('HADIR')
                                Hadir
                                @break

                            @case('IZIN')
                                Izin
                                @break

                            @case('SAKIT')
                                Sakit
                                @break

                            @case('ALPHA')
                                Tidak Hadir
                                @break

                            @default
                                {{ $filterStatus }}

                        @endswitch

                    </td>

                </tr>

            @endif


            @if ($filterSearch)

                <tr>

                    <td class="filter-label">
                        Pencarian
                    </td>

                    <td>
                        {{ $filterSearch }}
                    </td>

                </tr>

            @endif


            <tr>

                <td class="filter-label">
                    Dicetak
                </td>

                <td>
                    {{ now()->translatedFormat('d F Y H:i') }}
                </td>

            </tr>

        </table>

    </div>


    {{-- ================================================= --}}
    {{-- DATA --}}
    {{-- ================================================= --}}

    <table class="attendance-table">

        <thead>

            <tr>

                <th width="4%">
                    No
                </th>

                <th width="20%">
                    Nama Guru
                </th>

                <th width="12%">
                    Role
                </th>

                <th width="13%">
                    Tanggal
                </th>

                <th width="12%">
                    Status
                </th>

                <th width="10%">
                    Jam Masuk
                </th>

                <th width="10%">
                    Jam Pulang
                </th>

                <th width="19%">
                    Keterangan
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse ($attendances as $attendance)

                <tr>

                    <td class="center">
                        {{ $loop->iteration }}
                    </td>


                    <td>
                        {{ $attendance->user?->name ?? '-' }}
                    </td>


                    <td class="center">

                        @if ($attendance->user?->role === 'GURU_BK')
                            Guru BK
                        @else
                            Guru
                        @endif

                    </td>


                    <td class="center">

                        {{ $attendance->date?->translatedFormat('d F Y') ?? '-' }}

                    </td>


                    <td class="center">

                        @if ($attendance->status === 'HADIR')

                            <span class="status-hadir">
                                Hadir
                            </span>

                        @elseif ($attendance->status === 'IZIN')

                            <span class="status-izin">
                                Izin
                            </span>

                        @elseif ($attendance->status === 'SAKIT')

                            <span class="status-sakit">
                                Sakit
                            </span>

                        @else

                            <span class="status-alpha">
                                Tidak Hadir
                            </span>

                        @endif

                    </td>


                    <td class="center">

                        @if ($attendance->check_in)

                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}

                        @else

                            -

                        @endif

                    </td>


                    <td class="center">

                        @if ($attendance->check_out)

                            {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}

                        @else

                            -

                        @endif

                    </td>


                    <td>

                        {{ $attendance->note ?? '-' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="empty"
                    >
                        Tidak ada data absensi guru.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- ================================================= --}}
    {{-- FOOTER --}}
    {{-- ================================================= --}}

    <div class="footer">

        Laporan Absensi Guru —
        Dicetak pada {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>