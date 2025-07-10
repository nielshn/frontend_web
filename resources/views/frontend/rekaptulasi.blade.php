@extends('layouts.main')

@section('content')
    <div class="row mb-4">
        <div class="col-md-4">
            <label for="filterRange">Pilih Rentang Waktu:</label>
            <select id="filterRange" class="form-select">
                <option value="today">Hari Ini</option>
                <option value="thisWeek" selected>Minggu Ini</option>
                <option value="thisMonth">Bulan Ini</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div class="col-md-4 d-none" id="customRangeInputs">
            <label>Tanggal Mulai:</label>
            <input type="date" id="startDate" class="form-control">
            <label class="mt-2">Tanggal Akhir:</label>
            <input type="date" id="endDate" class="form-control">
        </div>
    </div>

    <div id="cardContainerWrapper" class="mb-4">
        <div class="card-container-3d">
            <div class="row" id="cardContainer"></div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i> Grafik Rekapitulasi Transaksi</h5>
        </div>
        <div class="card-body">
            <canvas id="rekapChart" height="100"></canvas>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i> Tabel Rekapitulasi</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered" id="rekapTable">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        @foreach ($all_types as $type)
                            <th>{{ $type }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <style>
        .card.card-3d {
            transition: box-shadow 0.3s, transform 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            background: #fff;
            border-radius: 16px;
            border: none;
        }

        .card.card-3d:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
            transform: translateY(-6px) scale(1.03);
            z-index: 2;
        }
/* 
        .card-container-3d {
            background: #ffd;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
            padding: 32px 24px;
            margin-bottom: 32px;
        } */
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <script>
        const rawData = @json($rekap);
        const allTypes = @json($all_types);
        const rekapSummary = @json($rekap_summary);
        const baseUrl = "{{ url('laporan-transaksi') }}";
        let rekapChart;

        function getRangeDates(range) {
            const today = moment().format("YYYY-MM-DD");
            let start, end;

            if (range === "today") {
                start = end = today;
            } else if (range === "thisWeek") {
                start = moment().startOf("isoWeek").format("YYYY-MM-DD");
                end = moment().endOf("isoWeek").format("YYYY-MM-DD");
            } else if (range === "thisMonth") {
                start = moment().startOf("month").format("YYYY-MM-DD");
                end = moment().endOf("month").format("YYYY-MM-DD");
            } else {
                start = document.getElementById("startDate").value;
                end = document.getElementById("endDate").value;
            }
            return {
                start,
                end
            };
        }

        function filterData(start, end) {
            const result = {};
            for (const date in rawData) {
                if (date >= start && date <= end) {
                    result[date] = rawData[date];
                }
            }
            return result;
        }

        function renderCards(start, end, filteredData) {
            const container = document.getElementById('cardContainer');
            container.innerHTML = '';

            const counts = {};
            for (const date in filteredData) {
                const data = filteredData[date];
                for (const type in data) {
                    counts[type] = (counts[type] || 0) + data[type];
                }
            }

            for (const [id, item] of Object.entries(rekapSummary)) {
                const count = counts[item.name] || 0;
                const card = document.createElement('div');
                card.className = 'col-md-3 mb-3';
                card.innerHTML = `
                <a href="${baseUrl}?transaction_type_id=${id}&start_date=${start}&end_date=${end}" class="text-decoration-none">
                    <div class="card card-3d h-100">
                        <div class="card-body d-flex align-items-center" style="min-height:120px;">
                            <div class="me-3">
                                <i class="bi bi-list-check fs-1 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 text-dark-emphasis">${count}</h4>
                                <span class="text-secondary">${item.name}</span>
                            </div>
                        </div>
                    </div>
                </a>`;
                container.appendChild(card);
            }
        }

        function renderChart(data) {
            const labels = Object.keys(data).sort();
            const datasets = allTypes.map((type, index) => ({
                label: type,
                data: labels.map(date => data[date][type] || 0),
                borderColor: `hsl(${index * 60}, 70%, 50%)`,
                backgroundColor: `hsla(${index * 60}, 70%, 50%, 0.3)`,
                fill: true,
                tension: 0.4
            }));

            const ctx = document.getElementById('rekapChart').getContext('2d');
            if (rekapChart) rekapChart.destroy();

            rekapChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "top"
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.dataset.label}: ${ctx.raw}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: "Tanggal"
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Jumlah Transaksi"
                            }
                        }
                    }
                }
            });
        }

        function renderTable(data) {
            const tbody = document.querySelector('#rekapTable tbody');
            tbody.innerHTML = '';

            const dates = Object.keys(data).sort();
            dates.forEach(date => {
                let row = `<tr><td>${date}</td>`;
                allTypes.forEach(type => {
                    row += `<td>${data[date][type] || 0}</td>`;
                });
                row += '</tr>';
                tbody.innerHTML += row;
            });
        }

        function updateUI() {
            const range = document.getElementById("filterRange").value;
            const {
                start,
                end
            } = getRangeDates(range);
            if (!start || !end) return;
            const filtered = filterData(start, end);
            renderChart(filtered);
            renderCards(start, end, filtered);
            renderTable(filtered);
        }

        document.getElementById("filterRange").addEventListener("change", function() {
            const isCustom = this.value === "custom";
            document.getElementById("customRangeInputs").classList.toggle("d-none", !isCustom);
            updateUI();
        });

        document.getElementById("startDate").addEventListener("change", updateUI);
        document.getElementById("endDate").addEventListener("change", updateUI);
        window.addEventListener("load", updateUI);
    </script>
@endpush
