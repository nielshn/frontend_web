<!-- Modal View Barang -->
<div class="modal fade" id="detailBarang{{ $barang['id'] }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Bagian Gambar Barang dan QR Code -->
                <div class="row mb-3">
                    <div class="col-md-6 text-center">
                        <p class="fw-bold">Gambar Barang</p>
                        @if (!empty($barang['barang_gambar']))
                            <img src="{{ $barang['barang_gambar'] }}" class="img-fluid img-thumbnail"
                                style="max-width: 200px; max-height: 200px;" alt="Gambar Barang">
                        @else
                            <p class="text-muted">Tidak ada gambar</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-center">
                        <p class="fw-bold">QR Code</p>
                        @php
                            $qrCodeBaseUrl = rtrim(config('api.qr_code'), '/') . '/qr_code/';
                            $qrCodeFormats = ['png', 'jpg', 'jpeg'];
                            $qrCodeUrl = null;

                            foreach ($qrCodeFormats as $format) {
                                $tempUrl = $qrCodeBaseUrl . $barang['barang_kode'] . '.' . $format;
                                $headers = @get_headers($tempUrl);
                                if ($headers && strpos($headers[0], '200')) {
                                    $qrCodeUrl = $tempUrl;
                                    break;
                                }
                            }
                        @endphp
                        @if ($qrCodeUrl)
                            <img src="{{ $qrCodeUrl }}" class="img-fluid img-thumbnail"
                                style="max-width: 200px; max-height: 200px;" alt="QR Code">
                        @else
                            <p class="text-muted">Tidak tersedia</p>
                        @endif
                    </div>
                </div>

                <!-- Bagian Detail Barang -->
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Kode Barang:</div>
                    <div class="col-md-8">{{ $barang['barang_kode'] }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Barang:</div>
                    <div class="col-md-8">{{ $barang['barang_nama'] }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Harga:</div>
                    <div class="col-md-8">Rp {{ number_format($barang['barang_harga'], 0, ',', '.') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Kategori:</div>
                    <div class="col-md-8">{{ $barang['category'] ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Jenis Barang:</div>
                    <div class="col-md-8">{{ $barang['jenisBarang'] ?? '-' }}</div>
                </div>

                <!-- Bagian Gudang -->
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Gudang:</div>
                    <div class="col-md-8">
                        @if (count($barang['gudangs']) > 0)
                            <ul class="list-group">
                                @foreach ($barang['gudangs'] as $gudang)
                                    <li class="list-group-item">
                                        <strong>{{ $gudang['name'] }}</strong> - Stok Tersedia:
                                        {{ $gudang['stok_tersedia'] }}
                                        - Dipinjam:
                                        @if (($barang['category'] ?? '') == 'habis pakai')
                                            -
                                        @else
                                            {{ $gudang['stok_dipinjam'] ?? 0 }}
                                        @endif
                                        - Maintenance:
                                        @if (($barang['category'] ?? '') == 'habis pakai')
                                            -
                                        @else
                                            {{ $gudang['stok_maintenance'] ?? 0 }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">Tidak ada gudang</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
