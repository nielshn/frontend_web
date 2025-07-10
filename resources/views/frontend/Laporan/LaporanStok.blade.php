@extends('layouts.main')

@section('content')
    @include('components.flash-message')

    <div class="d-flex justify-content-between mb-3"></div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 w-100 w-sm-auto">Laporan Stok Barang</h5>
            <div class="d-flex gap-2 flex-wrap w-100 w-sm-auto">
                <a href="{{ route('laporan.stok.exportPDF') }}"
                    class="btn btn-outline-danger btn-sm w-100 w-sm-auto mb-2 mb-sm-0" target="_blank">
                    <i class="ph-file-pdf"></i> Laporan PDF
                </a>
                <a href="{{ route('laporan.stok.exportExcel') }}" class="btn btn-outline-success btn-sm w-100 w-sm-auto"
                    target="_blank">
                    <i class="icon-file-excel"></i> Laporan Excel
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table id="stokTable" class="table datatable-button-html5-basic">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kode</th>
                        <th>Kategory Barang</th>
                        <th>Gambar Barang</th>
                        <th>Gudang</th>
                        <th>Stok Tersedia</th>
                        <th>Stok Maintenance</th>
                        <th>Stok Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($barangs as $barang)
                        @if (!empty($barang['gudangs']))
                            @foreach ($barang['gudangs'] as $gudang)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $barang['barang_nama'] ?? '-' }}</td>
                                    <td>{{ $barang['barang_kode'] ?? '-' }}</td>
                                    <td>{{ $barang['category'] ?? '-' }}</td>
                                    <td>
                                        @if (!empty($barang['barang_gambar']))
                                            <img src="{{ $barang['barang_gambar'] }}" class="img-thumbnail" width="80"
                                                alt="Gambar Barang">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>{{ $gudang['name'] ?? '-' }}</td>
                                    <td>{{ $gudang['stok_tersedia'] ?? 0 }}</td>
                                    <td>
                                        @if (($barang['category'] ?? '') == 'habis pakai')
                                            -
                                        @else
                                            {{ $gudang['stok_maintenance'] ?? 0 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (($barang['category'] ?? '') == 'habis pakai')
                                            -
                                        @else
                                            {{ $gudang['stok_dipinjam'] ?? 0 }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $barang['barang_nama'] ?? '-' }}</td>
                                <td>{{ $barang['barang_kode'] ?? '-' }}</td>
                                <td>{{ $barang['category'] ?? '-' }}</td>
                                <td>
                                    @if (!empty($barang['barang_gambar']))
                                        <img src="{{ $barang['barang_gambar'] }}" class="img-thumbnail" width="80"
                                            alt="Gambar Barang">
                                    @else
                                        <span class="text-muted">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td colspan="4" class="text-muted text-center">Tidak ada data gudang</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Data barang belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
