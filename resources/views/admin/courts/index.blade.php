@extends('layouts.admin')

@section('title', 'Manajemen Lapangan - Badminton BookPlay')
@section('page_title', 'Kelola Data Lapangan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Lapangan Badminton</h4>
        <p class="text-muted small mb-0">Kelola informasi fasilitas, tarif sewa per jam, dan status operasional lapangan</p>
    </div>
    <a href="{{ route('admin.courts.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Tambah Lapangan Baru
    </a>
</div>

<div class="card border-0 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th class="ps-4" style="width: 100px;">Foto</th>
                    <th>Nama Lapangan</th>
                    <th>Jenis Lantai</th>
                    <th>Tarif / Jam</th>
                    <th>Status</th>
                    <th>Total Booking</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courts as $court)
                    <tr>
                        <td class="ps-4">
                            <img src="{{ $court->image_url }}" alt="{{ $court->name }}" class="rounded-3" style="width: 70px; height: 50px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $court->name }}</div>
                            <div class="text-muted small">{{ Str::limit($court->description, 50) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $court->floor_type }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">{{ $court->formatted_price }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $court->status === 'available' ? 'bg-success' : 'bg-secondary' }} px-3 py-2 rounded-pill small">
                                {{ $court->status === 'available' ? 'Tersedia' : 'Maintenance' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $court->bookings_count }} Transaksi</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.courts.edit', $court->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>

                                <form action="{{ route('admin.courts.destroy', $court->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lapangan ini? Seluruh riwayat booking lapangan ini juga akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-grid fs-1 d-block mb-2"></i>
                            Belum ada data lapangan. Silakan tambahkan lapangan baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
