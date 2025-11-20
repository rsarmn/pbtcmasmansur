<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? 'Data Booking' }}</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#222}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
    .org{font-weight:700;font-size:16px}
    .meta{font-size:11px;color:#555}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #ddd;padding:8px;text-align:left}
    th{background:#f5d7de;color:#7b1a2e}
  </style>
</head>
<body>
  <div class="header">
    <div>
      <div class="org">{{ $meta['organization'] ?? '' }}</div>
      <div class="meta">Lokasi: {{ $meta['location'] ?? '' }}</div>
    </div>
    <div style="text-align:right">
      <div style="font-weight:700">{{ $meta['title'] ?? '' }}</div>
      <div class="meta">Tanggal: {{ $meta['date'] ?? now()->format('d M Y') }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>No Identitas</th>
        <th>Jenis Tamu</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Nomor Kamar</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bookings as $i => $b)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $b->nama }}</td>
        <td>{{ $b->no_identitas ?? '-' }}</td>
        <td>{{ ucfirst($b->jenis_tamu) }}</td>
        <td>{{ $b->check_in }}</td>
        <td>{{ $b->check_out }}</td>
  <td>{{ $b->kode_kamar ?? $b->nomor_kamar }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div style="margin-top:18px;font-size:11px;color:#666">Dicetak pada: {{ now()->format('d M Y H:i') }}</div>
</body>
</html>