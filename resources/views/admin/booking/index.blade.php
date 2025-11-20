@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Booking List</h2>

    {{-- Filter Jenis Tamu --}}
    <form action="{{ route('admin.booking') }}" method="GET" class="mb-3">
        <div class="d-flex gap-2 align-items-center">
            <label class="fw-bold">Filter:</label>
            <select name="filter" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">Semua</option>
                <option value="individu" {{ request('filter') == 'individu' ? 'selected' : '' }}>Individu</option>
                <option value="corporate" {{ request('filter') == 'corporate' ? 'selected' : '' }}>Corporate</option>
            </select>
        </div>
    </form>

    {{-- TABEL BOOKING --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>Nama</th>
                    <th>Jenis Tamu</th>
                    <th>Kamar</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($bookings as $b)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        {{ $b->nama ?? $b->nama_pic ?? '-' }}
                    </td>

                    <td class="text-center">
                        <span class="badge {{ $b->jenis_tamu == 'individu' ? 'bg-primary' : 'bg-success' }}">
                            {{ ucfirst($b->jenis_tamu) }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{ $b->kamar->jenis_kamar ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($b->check_in)->format('d M Y') }}
                    </td>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($b->check_out)->format('d M Y') }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-warning text-dark">Pending Payment</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Tidak ada data booking ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
