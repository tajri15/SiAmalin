@extends('admin.layouts.app')

@section('title', 'Edit Data Presensi')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Data Presensi</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Karyawan: {{ $presensi->karyawan->nama_lengkap ?? $presensi->nik }}
                - Tanggal: {{ \Carbon\Carbon::parse($presensi->tgl_presensi)->isoFormat('D MMMM YYYY') }}
            </h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.presensi.update', $presensi->_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik_display" class="form-label">NIK</label>
                        <input type="text" id="nik_display" class="form-control" value="{{ $presensi->nik }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nama_display" class="form-label">Nama Karyawan</label>
                        <input type="text" id="nama_display" class="form-control" value="{{ $presensi->karyawan->nama_lengkap ?? 'N/A' }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="tgl_presensi_edit" class="form-label">Tanggal Presensi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tgl_presensi_edit') is-invalid @enderror" id="tgl_presensi_edit" name="tgl_presensi_edit" value="{{ old('tgl_presensi_edit', \Carbon\Carbon::parse($presensi->tgl_presensi)->format('Y-m-d')) }}" required>
                    @error('tgl_presensi_edit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jam_in_edit" class="form-label">Jam Masuk (HH:MM:SS)</label>
                        <input type="time" step="1" class="form-control @error('jam_in_edit') is-invalid @enderror" id="jam_in_edit" name="jam_in_edit" value="{{ old('jam_in_edit', $presensi->jam_in) }}">
                        @error('jam_in_edit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if($presensi->foto_in)
                        <small class="form-text text-muted">Foto Masuk: <a href="{{ asset('storage/' . $presensi->foto_in) }}" target="_blank">Lihat</a></small><br>
                        <small class="form-text text-muted">Lokasi Masuk: {{ $presensi->lokasi_in ?? '-' }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jam_out_edit" class="form-label">Jam Pulang (HH:MM:SS)</label>
                        <input type="time" step="1" class="form-control @error('jam_out_edit') is-invalid @enderror" id="jam_out_edit" name="jam_out_edit" value="{{ old('jam_out_edit', $presensi->jam_out) }}">
                        @error('jam_out_edit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                         @if($presensi->foto_out)
                        <small class="form-text text-muted">Foto Pulang: <a href="{{ asset('storage/' . $presensi->foto_out) }}" target="_blank">Lihat</a></small><br>
                        <small class="form-text text-muted">Lokasi Pulang: {{ $presensi->lokasi_out ?? '-' }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="alert alert-warning small mt-3">
                    <strong>Perhatian:</strong> Mengubah data presensi akan tercatat. Pastikan perubahan yang dilakukan sudah benar dan dapat dipertanggungjawabkan. Foto dan lokasi asli tidak dapat diubah melalui form ini.
                </div>


                <div class="mt-4">
                    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('admin.presensi.rekapitulasi') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
