<x-app-layout>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detail Perangkat Desa</h4>
                    <div>
                        <a href="{{ route('perangkat.edit', $perangkat->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('perangkat.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($perangkat->foto)
                                <img src="{{ asset('storage/' . $perangkat->foto) }}" alt="Foto {{ $perangkat->nama }}" class="img-thumbnail" style="max-height: 250px;">
                            @else
                                <img src="{{ asset('images/default-user.png') }}" alt="Default" class="img-thumbnail" style="max-height: 250px;">
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <h4 class="border-bottom pb-2">Informasi Pribadi</h4>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 font-weight-bold">NIK</div>
                                <div class="col-md-9">{{ $perangkat->nik }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 font-weight-bold">Nama Lengkap</div>
                                <div class="col-md-9">{{ $perangkat->nama }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 font-weight-bold">Tempat, Tanggal Lahir</div>
                                <div class="col-md-9">{{ $perangkat->tempat_lahir }}, {{ $perangkat->tanggal_lahir->format('d F Y') }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 font-weight-bold">Jenis Kelamin</div>
                                <div class="col-md-9">{{ $perangkat->jenis_kelamin }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 font-weight-bold">Agama</div>
                                <div class="col-md-9">{{ $perangkat->agama }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 mb-3">
                            <h4 class="border-bottom pb-2">Informasi Kontak</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Alamat</div>
                        <div class="col-md-9">{{ $perangkat->alamat }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Nomor HP</div>
                        <div class="col-md-9">{{ $perangkat->no_hp }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Email</div>
                        <div class="col-md-9">{{ $perangkat->email ?: '-' }}</div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 mb-3">
                            <h4 class="border-bottom pb-2">Informasi Jabatan</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Jabatan</div>
                        <div class="col-md-9">{{ $perangkat->jabatan }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Tanggal Bergabung</div>
                        <div class="col-md-9">{{ $perangkat->tanggal_bergabung->format('d F Y') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Status</div>
                        <div class="col-md-9">
                            <span class="badge badge-{{ $perangkat->aktif ? 'success' : 'danger' }}">
                                {{ $perangkat->aktif ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Dapat Login</div>
                        <div class="col-md-9">
                            <span class="badge badge-{{ $perangkat->can_login ? 'info' : 'secondary' }}">
                                {{ $perangkat->can_login ? 'Ya' : 'Tidak' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 mb-3">
                            <h4 class="border-bottom pb-2">Tunjangan & Potongan</h4>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Tunjangan</h5>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nama Tunjangan</th>
                                                <th class="text-right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($tunjangan as $t)
                                                <tr>
                                                    <td>{{ $t->jenis->nama }}</td>
                                                    <td class="text-right">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center">Tidak ada tunjangan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($tunjangan) > 0)
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td>Total Tunjangan</td>
                                                    <td class="text-right">Rp {{ number_format($tunjangan->sum('jumlah'), 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">Potongan</h5>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nama Potongan</th>
                                                <th class="text-right">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($potongan as $p)
                                                <tr>
                                                    <td>{{ $p->jenis->nama }}</td>
                                                    <td class="text-right">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center">Tidak ada potongan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($potongan) > 0)
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td>Total Potongan</td>
                                                    <td class="text-right">Rp {{ number_format($potongan->sum('jumlah'), 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="{{ route('perangkat.tunjangan-potongan', $perangkat->id) }}" class="btn btn-primary">
                                <i class="fas fa-cog"></i> Kelola Tunjangan & Potongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>