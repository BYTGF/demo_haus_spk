
<div class="card mb-4">
    @if($financeMetrics)
    <div class="card-header d-flex justify-content-between align-items-center p-3">
        <h4>Laporan Keuangan</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="profitBarChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fs-6">Gross Profit:</span>
                        <strong class="text-primary">{{ $financeMetrics['gross_margins']->last() }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Net Profit:</span>
                        <strong class="text-info">{{ $financeMetrics['net_margins']->last() }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Difference:</span>
                        <strong>{{ $financeMetrics['gross_margins']->last() - $financeMetrics['net_margins']->last() }}%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('profitBarChart').getContext('2d');
        const financeMetrics = @json($financeMetrics);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: financeMetrics.periods,
                datasets: [
                    {
                        label: 'Gross Profit Margin %',
                        data: financeMetrics.gross_margins,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Net Profit Margin %',
                        data: financeMetrics.net_margins,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
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
                        title: {
                            display: true,
                            text: 'Period'
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