@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    <h1 class="mb-4 fw-bold">Manajemen Kamar</h1>

    <div class="card p-3 shadow-sm">
        <table class="table table-bordered">
            <thead style="background:#a0203c; color:white;">
                <tr>
                    <th>#</th>
                    <th>Kode Kamar</th>
                    <th>Jenis</th>
                    <th>Gedung</th>
                    <th>Harga</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($kamars as $kamar)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kamar->kode_kamar }}</td>
                    <td>{{ $kamar->jenis_kamar }}</td>
                    <td>{{ $kamar->gedung }}</td>
                    <td>Rp{{ number_format($kamar->harga,0,',','.') }}</td>
                    <td>
                        @if($kamar->foto)
                            <img src="{{ asset('storage/kamar/' . $kamar->foto) }}" 
                                 width="80" 
                                 class="rounded border">
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        <button 
                            class="btn btn-warning btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal{{ $kamar->id }}">
                            Edit
                        </button>
                    </td>
                </tr>

                {{-- Modal Edit Kamar --}}
                <div class="modal fade" id="editModal{{ $kamar->id }}">
                    <div class="modal-dialog">
                        <form 
                            action="{{ route('admin.kamar.update', $kamar->id) }}" 
                            method="POST" 
                            enctype="multipart/form-data" 
                            class="modal-content">
                            
                            @csrf
                            
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Kamar {{ $kamar->kode_kamar }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                
                                <label class="fw-bold">Harga</label>
                                <input type="number" 
                                    name="harga" 
                                    class="form-control mb-3"
                                    value="{{ $kamar->harga }}" required>

                                <label class="fw-bold">Ganti Foto (opsional)</label>
                                <input type="file" 
                                    name="foto" 
                                    class="form-control mb-2" accept="image/*">

                                @if($kamar->foto)
                                <small class="text-muted d-block mb-3">
                                    Foto saat ini:
                                    <img src="{{ asset('storage/kamar/'.$kamar->foto) }}" 
                                         width="80" class="rounded border ms-2">
                                </small>
                                @endif

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" 
                                        class="btn btn-secondary" 
                                        data-bs-dismiss="modal">Batal</button>
                            </div>

                        </form>
                    </div>
                </div>

                @endforeach
            </tbody>

        </table>
    </div>
</div>
@endsection
