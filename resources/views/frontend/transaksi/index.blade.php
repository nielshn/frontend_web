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

    @include('components.flash-message')
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
                            <td>{{ \Carbon\Carbon::parse($trx['transaction_date'])->format('d-m-Y H:m:s') }}</td>
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
                                        @if (\Carbon\Carbon::parse($trx['transaction_date'])->diffInHours(now()) < 24)
                                            <a href="#" class="text-warning me-2" data-bs-toggle="modal" title="Edit"
                                                data-bs-target="#modalEditTransaction{{ $trx['id'] }}">
                                                <i class="ph-pencil-simple"></i>
                                            </a>
                                        @endif
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


    {{-- Modal detail tetap pakai include --}}
    @foreach ($transactions as $trx)
        @include('frontend.transaksi.detail', ['transaction' => $trx])
        @include('frontend.transaksi.partials.edit-modal', ['transaction' => $trx])
    @endforeach
@endsection
