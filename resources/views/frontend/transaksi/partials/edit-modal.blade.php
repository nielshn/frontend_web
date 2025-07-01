<div class="modal fade" id="modalEditTransaction{{ $transaction['id'] }}" tabindex="-1"
    aria-labelledby="modalEditTransactionLabel{{ $transaction['id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ url('/transactions/kode/' . $transaction['transaction_code']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-gradient text-white">
                    <h5 class="modal-title" id="modalEditTransactionLabel{{ $transaction['id'] }}">
                        <i class="fas fa-file-invoice-dollar"></i> <strong>Edit Transaksi</strong> -
                        {{ $transaction['transaction_code'] }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <input type="hidden" name="transaction_type_id" value="{{ $transaction['transaction_type']['id'] }}">
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong class="text-muted">Kode Transaksi:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="text-dark">{{ $transaction['transaction_code'] }}</span>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong class="text-muted">Tanggal:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="text-dark">
                                {{ \Carbon\Carbon::parse($transaction['transaction_date'])->format('d-m-Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong class="text-muted">Jenis Transaksi:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="text-dark">{{ $transaction['transaction_type']['name'] }}</span>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong class="text-muted">Keterangan:</strong>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="description"
                                value="{{ $transaction['description'] }}">
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
                                    @foreach ($transaction['items'] as $i => $item)
                                        <tr>
                                            <td>{{ $item['barang']['nama'] ?? '-' }}</td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    name="items[{{ $i }}][barang_kode]"
                                                    value="{{ $item['barang_kode'] ?? ($item['barang']['kode'] ?? ($item['kode'] ?? '')) }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control"
                                                    name="items[{{ $i }}][quantity]"
                                                    value="{{ $item['quantity'] ?? ($item['jumlah'] ?? '') }}"
                                                    min="1">
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