{{-- filepath: resources/views/frontend/Laporan/Laporantransaksi.blade.php --}}
@extends('layouts.main')

@section('content')
    @include('components.flash-message')

    <div class="d-flex justify-content-between mb-3">
        <form id="filter-form" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="date" name="start_date" class="form-control form-control-sm w-auto"
                value="{{ request('start_date') }}">
            <span class="align-self-center">s/d</span>
            <input type="date" name="end_date" class="form-control form-control-sm w-auto"
                value="{{ request('end_date') }}">

            <select class="form-select form-select-sm w-auto" name="transaction_type_id" aria-label="Filter">
                <option value="">Pilih Transaksi</option>
                @foreach ($transactionTypes as $tt)
                    <option value="{{ $tt['id'] }}" @selected(request('transaction_type_id') == $tt['id'])>
                        {{ $tt['name'] }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
            <a href="{{ route('laporan.transaksi') }}" class="btn btn-sm btn-light">Reset</a>
        </form>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 w-100 w-sm-auto">Table Transaksi</h5>
            <div class="d-flex gap-2 flex-wrap w-100 w-sm-auto">

                {{-- Export PDF --}}
                @if (request('transaction_type_id'))
                    <a href="{{ route('transactions.exportPdfByType', [
                        'id' => request('transaction_type_id'),
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                    ]) }}"
                        class="btn btn-outline-danger btn-sm w-100 w-sm-auto mb-2 mb-sm-0" target="_blank">
                        <i class="ph-file-pdf"></i> Laporan PDF
                        ({{ $transactionTypes->firstWhere('id', request('transaction_type_id'))['name'] ?? 'Per Jenis' }})
                    </a>
                @else
                    <a href="{{ route('transactions.exportPdf', [
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                    ]) }}"
                        class="btn btn-outline-danger btn-sm w-100 w-sm-auto mb-2 mb-sm-0" target="_blank">
                        <i class="ph-file-pdf"></i> Laporan PDF (Semua)
                    </a>
                @endif

                {{-- Export Excel --}}
                @if (request('transaction_type_id'))
                    <a href="{{ route('transactions.exportExcelByType', [
                        'id' => request('transaction_type_id'),
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                    ]) }}"
                        class="btn btn-outline-success btn-sm w-100 w-sm-auto" target="_blank">
                        <i class="icon-file-excel"></i> Laporan Excel
                        ({{ $transactionTypes->firstWhere('id', request('transaction_type_id'))['name'] ?? 'Per Jenis' }})
                    </a>
                @else
                    <a href="{{ route('transactions.exportExcel', [
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                    ]) }}"
                        class="btn btn-outline-success btn-sm w-100 w-sm-auto" target="_blank">
                        <i class="icon-file-excel"></i> Laporan Excel (Semua)
                    </a>
                @endif
            </div>
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
                        <th>Gudang</th>
                        <th>Barang</th>
                        <th>Jumlah & Satuan</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $key => $trx)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $trx['transaction_code'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx['transaction_date'])->format('d-m-Y') }}</td>
                            <td>{{ $trx['transaction_type']['name'] ?? '-' }}</td>
                            <td>{{ $trx['user']['name'] ?? '-' }}</td>
                            <td>{{ count($trx['items']) }}</td>
                            <td>
                                {{ $trx['items'][0]['gudang']['nama'] ?? '-' }}
                            </td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach ($trx['items'] as $item)
                                        <li>{{ $item['barang']['nama'] ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach ($trx['items'] as $item)
                                        <li>{{ $item['quantity'] ?? 0 }} {{ $item['barang']['satuan'] ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center">
                                <a href="#" class="text-info" data-bs-toggle="modal"
                                    data-bs-target="#deskripsiModal{{ $key }}" title="Lihat Deskripsi">
                                    <i class="bi bi-eye-fill fs-5"></i>
                                </a>

                                <!-- Modal -->
                                <div class="modal fade" id="deskripsiModal{{ $key }}" tabindex="-1"
                                    aria-labelledby="deskripsiModalLabel{{ $key }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deskripsiModalLabel{{ $key }}">
                                                    Deskripsi Transaksi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ $trx['description'] ?? 'Tidak ada deskripsi.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Data transaksi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
