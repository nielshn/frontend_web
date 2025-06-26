@extends('layouts.main')
@section('content')
    @include('components.flash-message')

    <div class="container py-5">
        <!-- Modal Deskripsi Transaksi -->
        <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Keterangan Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal-description" class="form-label">Keterangan Transaksi</label>
                            <textarea id="modal-description" class="form-control" rows="3" placeholder="Contoh: Barang masuk untuk proyek A"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="save-description">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">Input Barang</h2>
            <p class="text-muted">Pilih metode input dan masukkan data barang</p>
        </div>

        <!-- Toggle Button -->
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group shadow" role="group" aria-label="Metode Input">
                <button type="button" class="btn btn-outline-primary active px-4 py-2 fs-5" id="btn-scan">
                    <i class="bi bi-upc-scan me-2"></i>Scan Barcode
                </button>
                <button type="button" class="btn btn-outline-secondary px-4 py-2 fs-5" id="btn-manual">
                    <i class="bi bi-keyboard me-2"></i>Input Manual
                </button>
            </div>
        </div>

        <!-- Scanner Section -->
        <div id="scanner-section" class="d-flex justify-content-center mb-4">
            <div id="reader" class="border rounded shadow-lg" style="width:100%; max-width:400px; aspect-ratio:1/1;">
            </div>
        </div>

        <!-- Manual Input -->
        <div id="manual-section" class="d-none d-flex justify-content-center mb-4">
            <div class="w-100 position-relative">
                <label for="manual-input" class="form-label text-muted">Masukkan Kode Barang</label>
                <input type="text" id="manual-input" class="form-control shadow-sm" placeholder="Contoh: BRG123">

                <!-- Dropdown untuk hasil pencarian -->
                <ul id="search-results" class="mt-2 list-group text-dark"
                    style="display: none; position: absolute; width: 100%; max-height: 200px; overflow-y: auto; background-color: white; border: 1px solid #ccc; z-index: 1000; border-radius: 5px;">
                </ul>

                <!-- Pesan Error dan Success -->
                <p id="manual-error" class="text-danger mt-2 d-none">Kode barang tidak valid.</p>
                <p id="manual-success" class="text-success mt-2 d-none">Kode barang ditemukan!</p>
            </div>
        </div>

        <!-- Scan Result -->
        <p id="scan-result" class="text-success fw-bold text-center"></p>

        <!-- Reset Button -->
        <form method="GET" action="{{ route('kode_barang.reset') }}" id="reset-form">
            @csrf
            <button class="btn btn-outline-danger mb-4">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Daftar
            </button>
        </form>

        <!-- Transaction Form -->
        <form id="transaction-form" method="POST">
            @csrf
            <div class="mb-4">
                <label for="transaction-type" class="form-label">Tipe Transaksi:</label>
                <select name="transaction_type_id" id="transaction-type" class="form-select">
                    <option value="" disabled selected>-- Pilih Tipe Transaksi --</option>
                    @foreach ($transactionTypes as $type)
                        <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Daftar Barang Table -->
            <div id="tabel-barang">
                @include('frontend.transaksi.partials.table', ['daftarBarang' => $daftarBarang])
            </div>
        </form>
    </div>
@endsection

@push('js')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const scannerSound = new Audio("{{ asset('scanner.mp3') }}");

        document.addEventListener('DOMContentLoaded', function() {
            const btnScan = document.getElementById('btn-scan');
            const btnManual = document.getElementById('btn-manual');
            const scannerSection = document.getElementById('scanner-section');
            const manualSection = document.getElementById('manual-section');
            const manualError = document.getElementById('manual-error');
            const manualSuccess = document.getElementById('manual-success');

            btnScan.addEventListener('click', () => {
                btnScan.classList.add('active');
                btnManual.classList.remove('active');
                scannerSection.classList.remove('d-none');
                manualSection.classList.add('d-none');
            });

            btnManual.addEventListener('click', () => {
                btnManual.classList.add('active');
                btnScan.classList.remove('active');
                manualSection.classList.remove('d-none');
                scannerSection.classList.add('d-none');
                manualError.classList.add('d-none');
                manualSuccess.classList.add('d-none');
            });

            const scanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: 250
            });
            scanner.render(onScanSuccess);

            document.getElementById('manual-input').addEventListener('input', handleManualInput);
        });

        let lastScannedTime = 0;

        function onScanSuccess(decodedText) {
            const now = new Date().getTime();
            if (now - lastScannedTime < 1000) return;
            lastScannedTime = now;

            document.getElementById('scan-result').innerText = 'Scanned: ' + decodedText;
            setTimeout(() => sendCode(decodedText), 1000);
        }

        function handleManualInput(e) {
            const kode = e.target.value;
            if (kode.length > 0) {
                fetchBarangSuggestion(kode);
                sendCode(kode);
            } else {
                $('#search-results').hide();
                $('#manual-error').addClass('d-none');
                $('#manual-success').addClass('d-none');
            }
        }

        function fetchBarangSuggestion(keyword) {
            $.ajax({
                url: '{{ route('search.barang') }}',
                method: 'GET',
                data: {
                    keyword
                },
                success: function(data) {
                    let resultsHtml = '';
                    if (data.length > 0) {
                        data.forEach(barang => {
                            resultsHtml += `
                        <li class="list-group-item result-item" data-kode="${barang.barang_kode}"
                            style="color: black; cursor: pointer;">
                            ${barang.barang_nama} (Kode: ${barang.barang_kode})
                        </li>`;
                        });
                    } else {
                        resultsHtml = `
                    <li class="list-group-item disabled" style="color: black;">
                        Tidak ada hasil
                    </li>`;
                    }
                    $('#search-results').html(resultsHtml).show();
                }
            });
        }

        // handler click item otomatis
        $(document).on('click', '.result-item', function() {
            const kode = $(this).data('kode');
            $('#manual-input').val(kode);
            sendCode(kode);
            $('#search-results').hide();
        });

        function sendCode(kode) {
            fetch("{{ route('kode_barang.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        kode
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        $('#tabel-barang').html(data.html);
                        $('#manual-error').addClass('d-none');
                        $('#manual-success').removeClass('d-none');
                        scannerSound.play();
                    } else {
                        $('#manual-error').removeClass('d-none');
                        $('#manual-success').addClass('d-none');
                    }
                })
                .catch(() => {
                    $('#manual-error').removeClass('d-none');
                    $('#manual-success').addClass('d-none');
                });
        }

        function validateQuantityInput(input) {
            let value = parseInt(input.value);
            if (isNaN(value) || value < 1) {
                input.value = 1;
            }
        }

        function removeItem(kode) {
            if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                fetch("{{ route('kode_barang.remove') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                        body: JSON.stringify({
                            kode
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const itemRow = document.getElementById('item-' + kode);
                            if (itemRow) itemRow.remove();
                            document.getElementById('scan-result').innerText = 'Barang berhasil dihapus!';
                        } else {
                            alert('Gagal menghapus barang: ' + data.message);
                        }
                    })
                    .catch(() => {
                        alert('Terjadi kesalahan saat menghapus barang.');
                    });
            }
        }

        let transactionDescription = '';

        // Tampilkan modal saat klik tombol tambah keterangan
        $(document).on('click', '#btn-add-description', function() {
            $('#modal-description').val(transactionDescription);
            $('#descriptionModal').modal('show');
        });

        // Simpan deskripsi transaksi dan tampilkan preview
        document.getElementById('save-description').addEventListener('click', function() {
            transactionDescription = $('#modal-description').val();
            $('#transaction-description-preview').text(transactionDescription);
            $('#descriptionModal').modal('hide');
        });

        // Submit transaksi, kirim deskripsi transaksi ke backend
        document.getElementById('transaction-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const transactionTypeId = document.getElementById('transaction-type').value;
            if (!transactionTypeId) {
                showFlashMessage('danger', 'Pilih tipe transaksi terlebih dahulu.');
                return;
            }

            let items = [];
            document.querySelectorAll('[data-barang-kode]').forEach(row => {
                const kode = row.getAttribute('data-barang-kode');
                const qtyInput = row.querySelector('.quantity-input');
                if (qtyInput) {
                    const qty = parseInt(qtyInput.value);
                    if (qty > 0) {
                        items.push({
                            barang_kode: kode,
                            quantity: qty
                        });
                    }
                }
            });

            if (items.length === 0) {
                showFlashMessage('danger', 'Tidak ada barang yang ditambahkan.');
                return;
            }

            fetch("{{ route('transactions.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        transaction_type_id: parseInt(transactionTypeId),
                        description: transactionDescription,
                        items: items
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw data;
                    }
                    showFlashMessage('success', data.message || 'Transaksi berhasil!');
                    setTimeout(() => {
                        window.location.href = "{{ route('transactions.index') }}";
                    }, 2500); // Perbesar delay agar alert terlihat
                })
                .catch(error => {
                    const errorMessage = error?.message || 'Gagal menyimpan transaksi.';
                    showFlashMessage('danger', errorMessage);
                });
        });

        function showFlashMessage(type, message) {
            const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
            const flashContainer = document.querySelector('.flash-message-container') || createFlashMessageContainer();
            flashContainer.innerHTML = alertHtml;
        }

        function createFlashMessageContainer() {
            const container = document.createElement('div');
            container.classList.add('flash-message-container');
            document.querySelector('.container')?.prepend(container);
            return container;
        }
    </script>
@endpush
