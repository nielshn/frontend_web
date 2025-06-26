@extends('layouts.main')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Transaksi</h4>
        <div>
            @can('create_transaction')
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-labeled btn-labeled-start mb-2">
                    <span class="btn-labeled-icon bg-black bg-opacity-20">
                        <i class="icon-plus-circle2"></i>
                    </span> Tambah Transaksi
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Table Transaksi</h5>
        </div>

        <div class="table-responsive">
            <table class="table datatable-button-html5-basic">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Operator</th>
                        <th>Jumlah Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $key => $trx)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $trx['transaction_code'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx['transaction_date'])->format('d-m-Y') }}</td>
                            <td>{{ $trx['transaction_type']['name'] }}</td>
                            <td>{{ $trx['user']['name'] }}</td>
                            <td>{{ count($trx['items']) }}</td>
                            <td>
                                <div class="d-inline-flex">
                                    <a href="#" class="text-info me-2" data-bs-toggle="modal"
                                        data-bs-target="#detailTransaction{{ $trx['id'] }}" title="Detail">
                                        <i class="ph-eye"></i>
                                    </a>
                                    @can('update_transaction')
                                    <a href="#" class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditTransaction{{ $trx['id'] }}">
                                    <i class="ph-pencil"></i> Edit
                                </a>
                                @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Data transaksi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- Modal Edit Transaksi --}}
    @foreach ($transactions as $trx)
        <div class="modal fade" id="modalEditTransaction{{ $trx['id'] }}" tabindex="-1"
            aria-labelledby="modalEditTransactionLabel{{ $trx['id'] }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
               <form action="{{ url('/transaksi/' . $trx['transaction_code']) }}" method="POST">
    @csrf
    @method('PUT')
                    <div class="modal-content rounded-4 shadow-lg">
                        <div class="modal-header bg-gradient text-white">
                            <h5 class="modal-title" id="modalEditTransactionLabel{{ $trx['id'] }}">
                                <i class="fas fa-file-invoice-dollar"></i> <strong>Edit Transaksi</strong> -
                                {{ $trx['transaction_code'] }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Kode Transaksi:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-dark">{{ $trx['transaction_code'] }}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Tanggal:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-dark">{{ \Carbon\Carbon::parse($trx['transaction_date'])->format('d-m-Y') }}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Jenis Transaksi:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-dark">{{ $trx['transaction_type']['name'] }}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Keterangan:</strong>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="description" value="{{ $trx['description'] }}">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Operator:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-dark">{{ $trx['user']['name'] }}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <strong class="text-muted">Jumlah Barang:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-dark">{{ count($trx['items']) }}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <strong class="text-muted">Daftar Barang:</strong>
                                    <table class="table table-striped table-bordered mt-3">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Kode Barang</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trx['items'] as $i => $item)
                                                <tr>
                                                    <td>{{ $item['barang']['nama'] ?? '-' }}</td>
                                                    <td>
                                                        <input type="hidden" name="transaction_type_id" value="{{ $trx['transaction_type']['id'] }}">
                                                        <input type="text" class="form-control" name="items[{{ $i }}][barang_kode]"
                                                            value="{{ $item['barang_kode'] ?? $item['barang']['kode'] ?? $item['kode'] ?? '' }}" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="items[{{ $i }}][quantity]"
                                                            value="{{ $item['quantity'] ?? $item['jumlah'] ?? '' }}" min="1">
                                                    </td>
                                                    <td>{{ $item['barang']['satuan'] ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Modal detail tetap pakai include --}}
    @foreach ($transactions as $trx)
        @include('frontend.transaksi.detail', ['transaction' => $trx])
    @endforeach
@endsection
