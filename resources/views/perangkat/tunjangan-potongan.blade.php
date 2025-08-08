@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Kelola Tunjangan & Potongan: {{ $perangkat->nama }}</h4>
                    <div>
                        <a href="{{ route('perangkat.show', $perangkat->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('perangkat.tunjangan-potongan.update', $perangkat->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Tunjangan</h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($jenisTunjangan) > 0)
                                            @foreach($jenisTunjangan as $jenis)
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label for="tunjangan_{{ $jenis->id }}" class="mb-0">
                                                            {{ $jenis->nama }}
                                                            @if($jenis->deskripsi)
                                                                <i class="fas fa-info-circle text-info" data-toggle="tooltip" title="{{ $jenis->deskripsi }}"></i>
                                                            @endif
                                                        </label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input tunjangan-toggle" 
                                                                id="tunjangan_toggle_{{ $jenis->id }}" 
                                                                data-target="tunjangan_{{ $jenis->id }}"
                                                                {{ $tunjanganPerangkat->where('jenis_id', $jenis->id)->count() > 0 ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="tunjangan_toggle_{{ $jenis->id }}"></label>
                                                        </div>
                                                    </div>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" class="form-control tunjangan-input" 
                                                            id="tunjangan_{{ $jenis->id }}" 
                                                            name="tunjangan[{{ $jenis->id }}]" 
                                                            value="{{ $tunjanganPerangkat->where('jenis_id', $jenis->id)->first() ? $tunjanganPerangkat->where('jenis_id', $jenis->id)->first()->jumlah : 0 }}"
                                                            {{ $tunjanganPerangkat->where('jenis_id', $jenis->id)->count() > 0 ? '' : 'disabled' }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info mb-0">
                                                Belum ada jenis tunjangan yang tersedia. 
                                                <a href="{{ route('tunjangan-potongan.index') }}">Tambah jenis tunjangan</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">Potongan</h5>
                                    </div>
                                    <div class="card-body">
                                        @if(count($jenisPotongan) > 0)
                                            @foreach($jenisPotongan as $jenis)
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <label for="potongan_{{ $jenis->id }}" class="mb-0">
                                                            {{ $jenis->nama }}
                                                            @if($jenis->deskripsi)
                                                                <i class="fas fa-info-circle text-info" data-toggle="tooltip" title="{{ $jenis->deskripsi }}"></i>
                                                            @endif
                                                        </label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input potongan-toggle" 
                                                                id="potongan_toggle_{{ $jenis->id }}" 
                                                                data-target="potongan_{{ $jenis->id }}"
                                                                {{ $potonganPerangkat->where('jenis_id', $jenis->id)->count() > 0 ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="potongan_toggle_{{ $jenis->id }}"></label>
                                                        </div>
                                                    </div>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp</span>
                                                        </div>
                                                        <input type="number" class="form-control potongan-input" 
                                                            id="potongan_{{ $jenis->id }}" 
                                                            name="potongan[{{ $jenis->id }}]" 
                                                            value="{{ $potonganPerangkat->where('jenis_id', $jenis->id)->first() ? $potonganPerangkat->where('jenis_id', $jenis->id)->first()->jumlah : 0 }}"
                                                            {{ $potonganPerangkat->where('jenis_id', $jenis->id)->count() > 0 ? '' : 'disabled' }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info mb-0">
                                                Belum ada jenis potongan yang tersedia. 
                                                <a href="{{ route('tunjangan-potongan.index') }}">Tambah jenis potongan</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
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
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Toggle tunjangan inputs
        $('.tunjangan-toggle').change(function() {
            var targetId = $(this).data('target');
            if($(this).is(':checked')) {
                $('#' + targetId).prop('disabled', false);
            } else {
                $('#' + targetId).prop('disabled', true).val(0);
            }
        });
        
        // Toggle potongan inputs
        $('.potongan-toggle').change(function() {
            var targetId = $(this).data('target');
            if($(this).is(':checked')) {
                $('#' + targetId).prop('disabled', false);
            } else {
                $('#' + targetId).prop('disabled', true).val(0);
            }
        });
    });
</script>
@endpush