<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Beranda</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fafafa;
      margin: 0;
      padding: 30px;
      color: #333;
    }

    h2, h3 {
      color: #b1193f;
    }

    form {
      background: white;
      border-radius: 12px;
      padding: 40px;
      width: 100%;
      max-width: 1200px;
      margin: auto;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    label {
      font-weight: bold;
      min-width: 100px;
      white-space: nowrap;
    }

    input[type="text"],
    input[type="email"],
    input[type="file"],
    textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: opacity 0.3s, border-color 0.3s;
      flex: 1;
      width: 100%;
    }

    input:focus {
      border-color: #b1193f;
      outline: none;
    }

    .success {
      color: green;
      margin-bottom: 20px;
    }

    .grid-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px 30px;
      width: 100%;
    }

    .input-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .full-row {
      margin-top: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Tombol Simpan bergaya modern */
    .btn-save {
      background: linear-gradient(135deg, #b1193f, #d6295b);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(177,25,63,0.3);
    }

    .btn-save:hover {
      background: linear-gradient(135deg, #8e1332, #b1193f);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(177,25,63,0.4);
    }

    .btn-save svg {
      width: 16px;
      height: 16px;
      fill: white;
    }

    .slider-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      .grid-2 {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <h2>Update Konten Beranda</h2>

  @if(session('success'))
    <div class="success">{{ session('success') }}</div>
  @endif

  <form id="form-beranda" action="{{ route('beranda.update', 1) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Bagian Kontak -->
    <div class="grid-2">
      <div>
        <div class="input-row">
          <label for="instagram">Instagram:</label>
          <input type="text" id="instagram" name="instagram" value="{{ $data->instagram }}" style="opacity: 0.5;">
          <button type="button" class="btn-save" data-field="instagram">
            
            Simpan
          </button>
        </div>

        <div class="input-row">
          <label for="email">Email:</label>
          <input type="email" id="email" name="email" value="{{ $data->email }}" style="opacity: 0.5;">
          <button type="button" class="btn-save" data-field="email">
            
            Simpan
          </button>
        </div>
      </div>

      <div>
        <div class="input-row">
          <label for="whatsapp">WhatsApp:</label>
          <input type="text" id="whatsapp" name="whatsapp" value="{{ $data->whatsapp }}" style="opacity: 0.5;">
          <button type="button" class="btn-save" data-field="whatsapp">
            
            Simpan
          </button>
        </div>

        <div class="input-row">
          <label for="location">Location:</label>
          <input type="text" id="location" name="location" value="{{ $data->location }}" style="opacity: 0.5;">
          <button type="button" class="btn-save" data-field="location">
            
            Simpan
          </button>
        </div>
      </div>
    </div>

    <div class="full-row">
      <label for="maps_link">Maps Link:</label>
      <input type="text" id="maps_link" name="maps_link" value="{{ $data->maps_link }}" style="opacity: 0.5;">
      <button type="button" class="btn-save" data-field="maps_link">
        
        Simpan
      </button>
    </div>

    <!-- SLIDER 1 -->
    <h3>Slider 1</h3>
    @if($data->slider1_image)
      <img src="{{ asset($data->slider1_image) }}" width="150">
    @endif
    <div class="slider-row">
      <label>Gambar Baru:</label>
      <input type="file" name="slider1_image" id="slider1_image">
      <label>Teks Slider 1:</label>
      <textarea name="slider1_text" rows="3" style="width: 100%;">{{ $data->slider1_text }}</textarea>
      <button type="button" class="btn-save" data-field="slider1">
        
        Simpan
      </button>
    </div>

    <!-- SLIDER 2 -->
    <h3>Slider 2</h3>
    @if($data->slider2_image)
      <img src="{{ asset($data->slider2_image) }}" width="150">
    @endif
    <div class="slider-row">
      <label>Gambar Baru:</label>
      <input type="file" name="slider2_image" id="slider2_image">
      <label>Teks Slider 2:</label>
      <textarea name="slider2_text" rows="3" style="width: 100%;">{{ $data->slider2_text }}</textarea>
      <button type="button" class="btn-save" data-field="slider2">
        
        Simpan
      </button>
    </div>
  </form>

  <script>
    const form = document.getElementById('form-beranda');
    const fields = [
      'instagram', 'email', 'whatsapp', 'location', 'maps_link',
      'slider1_text', 'slider2_text'
    ];

    const oldValues = {};
    fields.forEach(id => {
      const el = document.getElementById(id);
      if (el) oldValues[id] = el.value;
    });

    // Ubah opacity saat mengetik
    fields.forEach(id => {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('input', () => {
        el.style.opacity = el.value !== oldValues[id] ? '1' : '0.5';
      });
    });

    // Tombol simpan per bagian
    document.querySelectorAll('.btn-save').forEach(button => {
      button.addEventListener('click', () => {
        const field = button.dataset.field;
        let confirmMessage = '';

        if (field.startsWith('slider')) {
          confirmMessage = `Apakah benar ingin menyimpan perubahan pada ${field.replace('_', ' ')}?`;
        } else {
          const el = document.getElementById(field);
          const newVal = el?.value?.trim();
          const oldVal = oldValues[field];
          if (newVal === oldVal) {
            alert(`Tidak ada perubahan pada ${field}.`);
            return;
          }
          confirmMessage = `Apakah benar ingin merubah ${field.replace('_', ' ')} menjadi "${newVal}"?`;
        }

        if (confirm(confirmMessage)) {
          form.submit();
        }
      });
    });
  </script>
</body>
</html>
