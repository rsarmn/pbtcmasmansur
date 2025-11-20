@extends('layout')

@section('content')
<style>
  :root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --success: #16a34a;
    --danger: #dc2626;
    --warning: #f59e0b;
    --gray: #6b7280;
    --border: #e5e7eb;
    --bg-light: #f9fafb;
  }

  /* UTILS */
  .container-padding { padding: 24px; }
  .text-muted { color: var(--gray); }
  .text-sm { font-size: 13px; }
  .mt-12 { margin-top: 12px; }
  .mt-20 { margin-top: 20px; }
  .mb-8 { margin-bottom: 8px; }

  /* BUTTONS */
  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap; /* Penting agar tidak pecah */
  }

  .btn-primary { background: var(--primary); color: #fff; }
  .btn-primary:hover { background: var(--primary-hover); }

  .btn-success { background: var(--success); color: #fff; }
  .btn-success:hover { background: #15803d; }

  .btn-danger { background: var(--danger); color: #fff; }
  .btn-danger:hover { background: #b91c1c; }

  .btn-gray { background: var(--gray); color: #fff; }
  .btn-gray:hover { background: #4b5563; }

  .btn-chat { background: #25D366; color: white; }
  .btn-chat:hover { background: #1DA955; }

  /* CARD & TABLE */
  .card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  .table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 14px;
  }

  .table thead {
    background: var(--bg-light);
    text-align: left;
    color: #374151;
  }

  .table th, .table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    vertical-align: top;
  }

  .table tbody tr:hover {
    background: #f3f4f6;
  }

  .detail-row td {
    background: #fafafa;
    padding: 0 !important;
  }

  .detail-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    padding: 20px;
  }

  .detail-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 16px;
  }

  .detail-card strong {
    display: block;
    font-weight: 700;
    margin-bottom: 12px;
    color: #111827;
  }

  .form-group {
    margin-bottom: 15px;
  }

  select, input[type="file"] {
    width: 100%;
    padding: 8px;
    font-size: 14px;
    border: 1px solid var(--border);
    border-radius: 6px;
  }

  select[multiple] {
    min-height: 150px;
    padding: 4px;
  }
</style>

<div class="container-padding">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
    <div>
      <h2 style="font-size: 24px; font-weight: 700; color: #111827;">⏳ Konfirmasi Pembayaran & Booking</h2>
      <p class="text-muted">Tinjau dan konfirmasi pembayaran yang masih pending. Admin dapat menghubungi PIC lewat WhatsApp.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-gray">← Kembali</a>
  </div>

  @if(session('success'))
    <div class="card mt-12" style="border-left: 4px solid var(--success); color: #166534;">
      <strong>✓ {{ session('success') }}</strong>
    </div>
  @endif

  @if($errors->any())
    <div class="card mt-12" style="border-left: 4px solid var(--danger); color: #991b1b;">
      <strong>✗ Terjadi Kesalahan:</strong>
      <ul style="margin-left: 20px; margin-top: 6px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card mt-20" style="padding:0">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama / Jenis</th>
          <th>No. Identitas / PIC</th>
          <th>Tanggal Booking</th>
          <th>Jml Kamar</th>
          <th>Status</th>
          <th style="width:250px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengunjungs as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>
            <strong>{{ $p->nama }}</strong><br>
            <span class="text-sm text-muted">{{ $p->jenis_tamu }}</span>
          </td>
          <td>
            @if(strtolower($p->jenis_tamu) == 'corporate')
              {{ $p->nama_pic }}<br><span class="text-sm text-muted">{{ $p->no_telp_pic }}</span>
            @else
              {{ $p->no_identitas }}
            @endif
          </td>
          <td>{{ $p->check_in }} → {{ $p->check_out }}</td>
          <td>{{ $p->jumlah_kamar ?? '-' }}</td>
          <td><span style="font-weight:600; color:var(--warning);">{{ $p->payment_status_label }}</span></td>
          <td>
            @php
              $phone = preg_replace('/[^0-9+]/','',$p->no_telp_pic ?? '');
              $wa = $phone ? 'https://wa.me/'.ltrim($phone,'0') : null;
            @endphp
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
              @if($wa)
                <a href="{{ $wa }}" target="_blank" class="btn btn-chat" style="padding:6px 12px;">Chat</a>
              @endif
              <form action="{{ route('pengunjung.reject', $p->id) }}" method="POST" onsubmit="return confirm('Tolak booking ini?')">
                @csrf
                <button class="btn btn-danger" type="submit" style="padding:6px 12px;">Tolak</button>
              </form>
              <form action="{{ route('pengunjung.approve', $p->id) }}" method="POST">
                @csrf
                <button class="btn btn-primary" type="submit" style="padding:6px 12px;">Konfirmasi</button>
              </form>
            </div>
          </td>
        </tr>

        <tr class="detail-row">
          <td colspan="7">
            <div class="detail-section">
              <div class="detail-card">
                <strong>Detail Booking</strong>
                <div class="text-sm">
                  <p><b>Nama:</b> {{ $p->nama }}</p>
                  <p><b>Jenis Tamu:</b> {{ $p->jenis_tamu }}</p>
                  @if(strtolower($p->jenis_tamu) == 'corporate')
                    <p><b>Nama Kegiatan:</b> {{ $p->nama_kegiatan ?? '-' }}</p>
                    <p><b>PIC:</b> {{ $p->nama_pic ?? '-' }} ({{ $p->no_telp_pic ?? '-' }})</p>
                  @else
                    <p><b>No Identitas:</b> {{ $p->no_identitas }}</p>
                    <p><b>No Telp:</b> {{ $p->no_telp }}</p>
                  @endif
                  <p><b>Check-in:</b> {{ $p->check_in }}</p>
                  <p><b>Check-out:</b> {{ $p->check_out }}</p>
                  <p><b>Jumlah Kamar:</b> {{ $p->jumlah_kamar ?? '-' }}</p>
                  <p><b>Kamar Assigned:</b> {{ $p->kode_kamar ?? $p->nomor_kamar ?? 'Belum di-assign' }}</p>
                </div>
              </div>

              <div class="detail-card">
                <strong>Upload Bukti Manual</strong>
                @if($p->bukti_pembayaran)
                  @php
                    // Path di DB: public/bukti_pembayaran/xxx.png
                    // Storage::url() akan convert ke: /storage/bukti_pembayaran/xxx.png
                    $fileUrl = \Illuminate\Support\Facades\Storage::url($p->bukti_pembayaran);
                    $isPdf = strtolower(pathinfo($p->bukti_pembayaran, PATHINFO_EXTENSION)) === 'pdf';
                  @endphp
                  <div style="margin-bottom:16px;padding:12px;background:#f0fdf4;border:2px solid #86efac;border-radius:8px">
                    <p class="text-sm" style="color:#059669;margin-bottom:12px;font-weight:700;font-size:14px">✓ File Bukti Pembayaran Sudah Tersimpan</p>
                    
                    @if(!$isPdf)
                      <div style="margin-bottom:12px;text-align:center;background:#fff;padding:8px;border-radius:6px">
                        <img src="{{ $fileUrl }}" alt="Bukti Pembayaran" onerror="this.parentElement.innerHTML='<div style=padding:20px;color:#6b7280>Gambar tidak dapat dimuat atau tidak tersedia. Pastikan <span style=font-weight:bold>php artisan storage:link</span> sudah dijalankan.</div>'" style="max-width:100%;height:auto;max-height:180px;border-radius:4px;border:1px solid #d1d5db">
                      </div>
                    @else
                      <div style="margin-bottom:12px;text-align:center;background:#fff;padding:20px;border-radius:6px;border:1px solid #d1d5db">
                        <div style="font-size:40px;margin-bottom:8px">📄</div>
                        <p style="font-size:12px;color:#6b7280">File PDF</p>
                      </div>
                    @endif
                    
                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary" style="display:block;width:100%;margin-top:0;">
                      @if($isPdf)
                        📄 Lihat Bukti PDF
                      @else
                        🖼️ Lihat Bukti Pembayaran
                      @endif
                    </a>
                  </div>
                @endif
                
                <form action="{{ route('pengunjung.upload_payment', $p->id) }}" method="POST" enctype="multipart/form-data" style="margin-top:12px">
                  @csrf
                  <div class="form-group">
                    <label for="bukti_file_{{ $p->id }}" style="display:block;margin-bottom:6px;font-size:13px;color:#374151;font-weight:500">
                      {{ $p->bukti_pembayaran ? 'Ganti dengan file baru:' : 'Pilih file bukti pembayaran:' }}
                    </label>
                    <input 
                      type="file" 
                      name="bukti_pembayaran" 
                      id="bukti_file_{{ $p->id }}" 
                      accept=".jpg,.jpeg,.png,.pdf" 
                      required 
                      style="font-size:13px;padding:8px;border:1px solid #d1d5db;border-radius:6px;width:100%"
                      onchange="previewFile({{ $p->id }})"
                    >
                    <p class="text-sm text-muted" style="margin-top:6px;font-size:12px">Format: JPG, PNG, PDF (Max 5MB)</p>
                    
                    <div id="preview_{{ $p->id }}" style="display:none;margin-top:12px;padding:10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px">
                      <p style="font-size:12px;color:#6b7280;margin-bottom:8px">Preview:</p>
                      <img id="preview_img_{{ $p->id }}" style="max-width:100%;height:auto;max-height:150px;border-radius:4px;display:none">
                      <p id="preview_pdf_{{ $p->id }}" style="display:none;padding:20px;background:#fff;border:1px solid #d1d5db;border-radius:4px;text-align:center">
                        <span style="font-size:30px;display:block;margin-bottom:4px">📄</span>
                        <span id="preview_filename_{{ $p->id }}" class="text-sm"></span>
                      </p>
                    </div>
                  </div>
                  <button class="btn btn-primary" type="submit" style="margin-top:8px;padding:8px 16px">
                    {{ $p->bukti_pembayaran ? '🔄 Ganti File' : '📤 Upload File' }}
                  </button>
                </form>
              </div>

              <div class="detail-card">
                <strong>Approve & Assign Kamar</strong>
                <form action="{{ route('pengunjung.approve', $p->id) }}" method="POST">
                  @csrf
                  <div class="form-group">
                    <label for="assign_kamar_select_{{ $p->id }}" style="display:block;margin-bottom:6px;font-size:13px;color:#374151;font-weight:500">Pilih kamar (Ctrl/Shift untuk multi-select):</label>
                    <select name="assign_kamar[]" id="assign_kamar_select_{{ $p->id }}" multiple style="min-height: 150px;">
                      @foreach($availableRooms as $k)
                        <option value="{{ $k->kode_kamar ?? $k->nomor_kamar }}">
                          {{ $k->kode_kamar ?? $k->nomor_kamar }} — {{ $k->jenis_kamar }} — {{ $k->gedung }}
                        </option>
                      @endforeach
                    </select>
                    <p class="text-sm text-muted mt-12">{{ $availableRooms->count() }} kamar tersedia</p>
                  </div>
                  <button class="btn btn-success" type="submit">✓ Approve & Assign</button>
                </form>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center; padding:20px;">🎉 Semua booking sudah terkonfirmasi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
function previewFile(id) {
  const fileInput = document.getElementById('bukti_file_' + id);
  const previewContainer = document.getElementById('preview_' + id);
  const previewImg = document.getElementById('preview_img_' + id);
  const previewPdf = document.getElementById('preview_pdf_' + id);
  const previewFilename = document.getElementById('preview_filename_' + id);
  
  const file = fileInput.files[0];
  
  // Sembunyikan semua preview secara default
  previewContainer.style.display = 'none';
  previewImg.style.display = 'none';
  previewPdf.style.display = 'none';

  if (file) {
    // Tampilkan container preview
    previewContainer.style.display = 'block';
    
    const fileType = file.type;
    const fileName = file.name;
    
    if (fileType.startsWith('image/')) {
      // Tampilkan preview gambar
      const reader = new FileReader();
      reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
      };
      reader.readAsDataURL(file);
    } else if (fileType === 'application/pdf') {
      // Tampilkan info PDF
      previewPdf.style.display = 'block';
      previewFilename.textContent = fileName;
    }
  }
}
</script>

@endsection