@if (count($daftarBarang) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Gambar</th>
                    <th>Kategori Barang</th>
                    <th>Stok Tersedia</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarBarang as $barang)
                    <x-item-row :barang="$barang" :loop="$loop" />
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <!-- Tombol tambah keterangan transaksi -->
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-add-description">
            <i class="bi bi-chat-square-text me-1"></i> Tambah Keterangan Transaksi
        </button>
        <!-- Preview deskripsi transaksi -->
        <div id="transaction-description-preview" class="mt-3 text-primary fw-semibold" style="min-height: 24px;"></div>
        <!-- Tombol submit transaksi -->
        <button type="submit" class="btn btn-primary mt-4" id="submit-transaction">
            <i class="bi bi-check2-circle me-2"></i>Simpan Transaksi
        </button>
    </div>
    </div>
@else
    <div class="alert alert-info text-center">
        Belum ada barang yang ditambahkan.
    </div>
@endif
