@extends('layouts.admin')

@section('title', 'Tambah Lapangan Baru - Badminton BookPlay')
@section('page_title', 'Tambah Lapangan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <h5 class="fw-bold text-dark mb-0">Form Tambah Lapangan Badminton</h5>
                <a href="{{ route('admin.courts.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('admin.courts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small">Nama Lapangan <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Lapangan 5 (Karpet Vinyl)" required>
                    @error('name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="floor_type" class="form-label fw-semibold small">Jenis Lantai <span class="text-danger">*</span></label>
                        <select name="floor_type" id="floor_type" class="form-select @error('floor_type') is-invalid @enderror" required>
                            <option value="Karpet Vinyl" {{ old('floor_type') === 'Karpet Vinyl' ? 'selected' : '' }}>Karpet Vinyl BWF</option>
                            <option value="Kayu Parquet" {{ old('floor_type') === 'Kayu Parquet' ? 'selected' : '' }}>Kayu Parquet Interlocking</option>
                            <option value="Semen Halus" {{ old('floor_type') === 'Semen Halus' ? 'selected' : '' }}>Semen Halus</option>
                            <option value="Sintetis Lainnya" {{ old('floor_type') === 'Sintetis Lainnya' ? 'selected' : '' }}>Sintetis Lainnya</option>
                        </select>
                        @error('floor_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="price_per_hour" class="form-label fw-semibold small">Tarif Sewa Per Jam (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price_per_hour" id="price_per_hour" class="form-control @error('price_per_hour') is-invalid @enderror" value="{{ old('price_per_hour', 50000) }}" min="1000" step="5000" required>
                        @error('price_per_hour')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold small">Status Operasional <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Tersedia (Bisa dibooking)</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Masa Perawatan (Maintenance / Ditutup)</option>
                    </select>
                    @error('status')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold small">Deskripsi & Keunggulan Lapangan</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsikan fasilitas seperti jenis karpet, pencahayaan LED, ventilasi...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label fw-semibold small">Foto Lapangan (Opsional, JPG/PNG maks 2MB)</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Jika dikosongkan, sistem akan menggunakan gambar representasi lapangan standar.</small>
                    @error('image')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">
                        <i class="bi bi-save me-1"></i> Simpan Lapangan
                    </button>
                    <a href="{{ route('admin.courts.index') }}" class="btn btn-light border px-4 rounded-pill">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
