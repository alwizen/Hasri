<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Siswa</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
        }

        .kop p {
            margin: 2px 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .ttd {
            margin-top: 40px;
            text-align: right;
        }

        .ttd img {
            height: 80px;
        }
    </style>
</head>

<body>

    @php
        $setting = \App\Models\SchoolSetting::first();
    @endphp

    <div class="kop">
        <h2>{{ $setting?->school_name }}</h2>
        <p>{{ $setting?->school_address }}</p>
        <p>
            Telp: {{ $setting?->phone }}
            | Email: {{ $setting?->email }}
        </p>
    </div>

    <h3>
        Rekap Absensi Siswa
    </h3>

    <p>
        <strong>Nama:</strong> {{ $student->full_name }} <br>
        <strong>Kelas:</strong> {{ optional($student->classRoom)->name }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $row)
                <tr>
                    <td>{{ optional($row->attendance_date)?->format('d M Y') }}</td>
                    <td>{{ optional($row->check_in_at)?->format('H:i') }}</td>
                    {{-- <td>{{ optional($row->check_out_at)?->format('H:i') ?? '-' }}</td> --}}
                    <td>{{ $row->check_out_at ? $row->check_out_at->format('H:i') : '-' }}</td>

                    <td>{{ ucfirst($row->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <p>
            {{ now()->format('d F Y') }}<br>
            Kepala Sekolah
        </p>

        @php
            $signaturePath = $setting?->principal_signature
                ? public_path('storage/ttd/' . $setting->principal_signature)
                : null;
        @endphp

        @if ($signaturePath && file_exists($signaturePath))
            <img src="{{ $signaturePath }}" style="height: 80px;">
        @else
            <div style="height: 80px;"></div>
        @endif

        <p><strong>{{ $setting?->principal_name }}</strong></p>
    </div>

</body>

</html>
