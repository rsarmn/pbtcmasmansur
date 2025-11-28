@extends('layout')
@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white rounded-lg p-6 shadow">
    <h3 class="text-lg font-bold mb-4">Edit Kamar</h3>
    <form action="{{ route('kamar.update', $kamar->id) }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="block text-sm font-medium">Nomor Kamar</label>
  <input name="kode_kamar" value="{{ $kamar->kode_kamar ?? $kamar->nomor_kamar }}" class="w-full border rounded px-3 py-2" readonly>
      </div>
      <div class="mb-3 grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium">Jenis Kamar</label>
          <input name="jenis_kamar" value="{{ $kamar->jenis_kamar }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Gedung</label>
          <input name="gedung" value="{{ $kamar->gedung }}" class="w-full border rounded px-3 py-2">
        </div>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium">Harga (Rp)</label>
        <input name="harga" type="number" value="{{ $kamar->harga }}" class="w-full border rounded px-3 py-2">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium">Fasilitas</label>
        <input name="fasilitas" value="{{ $kamar->fasilitas }}" class="w-full border rounded px-3 py-2">
      </div>
      <div class="mb-3">
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
          <option value="kosong" {{ $kamar->status=='kosong'?'selected':'' }}>kosong</option>
          <option value="terisi" {{ $kamar->status=='terisi'?'selected':'' }}>terisi</option>
        </select>
      </div>
      <div class="flex gap-3 justify-end">
        <a href="{{ route('kamar.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
        <button class="px-4 py-2 bg-[var(--brand)] text-white rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection
