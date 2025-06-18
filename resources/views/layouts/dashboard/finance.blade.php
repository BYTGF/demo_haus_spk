<div class="col-8">
    <div class="card mb-4">
        @if($financeMetrics)
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h4>Laporan Keuangan</h4>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 400px;">
                <canvas id="financeLineChart"></canvas>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('financeLineChart').getContext('2d');
                const data = @json($financeMetrics);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.periods,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: data.penjualan,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Laba Bersih',
                                data: data.laba_bersih,
                                borderColor: '#1cc88a',
                                backgroundColor: 'rgba(28, 200, 138, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Biaya Operasional',
                                data: data.biaya_operasional,
                                borderColor: '#e74a3b',
                                backgroundColor: 'rgba(231, 74, 59, 0.2)',
                                fill: false,
                                tension: 0.4
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nilai'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Periode'
                                }
                            }
                        }
                    }
                });
            });
        </script>
        @endpush
        @else
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-4">Pilih Store dan Periode untuk melihat Dashboard Keuangan</h5>
        </div>
        @endif
    </div>
</div>

<div class="col-4">
    <div class="card mb-4">
        @if($financeBarMetrics)
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h4>Profit Margin</h4>
            <span class="badge bg-{{ $financeBarMetrics['status'] == 'approved' ? 'success' : ($financeBarMetrics['status'] == 'rejected' ? 'danger' : 'warning') }}">
                {{ $financeBarMetrics['status'] }}
            </span>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height: 400px;">
                <canvas id="financeBarChart"></canvas>
            </div>
            <div class="mt-3 text-center">
                <small class="text-muted">Periode: {{ $financeBarMetrics['period'] }}</small>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('financeBarChart').getContext('2d');
                const data = @json($financeBarMetrics);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Gross Profit Margin', 'Net Profit Margin'],
                        datasets: [{
                            label: 'Profit Margin (%)',
                            data: [data.gross_profit_margin, data.net_profit_margin],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.raw}%`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Percentage (%)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
        @endpush
        @else
        <div class="card-header d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-4">Pilih Store dan Periode untuk melihat Profit Margin</h5>
        </div>
        @endif
    </div>
</div>