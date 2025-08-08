@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ isset($perangkat) ? 'Edit Perangkat' : 'Tambah Perangkat Baru' }}</h4>
                </div>

                <div class="card-body">
                    <form action="{{ isset($perangkat) ? route('perangkat.update', $perangkat->id) : route('perangkat.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($perangkat))
                            @method('PUT')
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Data Pribadi</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nik">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', isset($perangkat) ? $perangkat->nik : '') }}" required maxlength="16">
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Nomor Induk Kependudukan (16 digit)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', isset($perangkat) ? $perangkat->nama : '') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', isset($perangkat) ? $perangkat->tempat_lahir : '') }}" required>
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', isset($perangkat) ? $perangkat->tanggal_lahir->format('Y-m-d') : '') }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin', isset($perangkat) ? $perangkat->jenis_kelamin : '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', isset($perangkat) ? $perangkat->jenis_kelamin : '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="agama">Agama <span class="text-danger">*</span></label>
                                    <select class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama" required>
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ old('agama', isset($perangkat) ? $perangkat->agama : '') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mt-4">Informasi Kontak</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', isset($perangkat) ? $perangkat->alamat : '') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_hp">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', isset($perangkat) ? $perangkat->no_hp : '') }}" required>
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', isset($perangkat) ? $perangkat->email : '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mt-4">Informasi Jabatan</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jabatan_list as $jab)
                                            <option value="{{ $jab }}" {{ old('jabatan', isset($perangkat) ? $perangkat->jabatan : '') == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                        @endforeach
                                    </select>
                                    @error('jabatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tanggal_bergabung">Tanggal Bergabung <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_bergabung') is-invalid @enderror" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', isset($perangkat) ? $perangkat->tanggal_bergabung->format('Y-m-d') : '') }}" required>
                                    @error('tanggal_bergabung')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="foto">Foto</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if(isset($perangkat) && $perangkat->foto)
                                <div class="form-group mb-3">
                                    <label>Foto Saat Ini</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2 mt-4">Akun Pengguna</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="can_login" name="can_login" value="1" {{ old('can_login', isset($perangkat) && $perangkat->can_login ? true : false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_login">
                                            Izinkan Login
                                        </label>
                                    </div>
                                    <small class="text-muted">Jika dicentang, perangkat dapat login ke sistem</small>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="password-section" style="{{ old('can_login', isset($perangkat) && $perangkat->can_login ? true : false) ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password">Password {{ isset($perangkat) ? '(Kosongkan jika tidak ingin mengubah)' : '' }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ isset($perangkat) ? '' : 'required' }}>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="password_confirmation">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ isset($perangkat) ? '' : 'required' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ isset($perangkat) ? 'Perbarui' : 'Simpan' }}
                                </button>
                                <a href="{{ route('perangkat.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle password section based on can_login checkbox
        $('#can_login').change(function() {
            if($(this).is(':checked')) {
                $('#password-section').show();
            } else {
                $('#password-section').hide();
            }
        });
    });
</script>
@endpush