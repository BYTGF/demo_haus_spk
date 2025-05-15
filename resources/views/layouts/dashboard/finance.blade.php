@if($financeMetrics)
<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Profitability Analysis ({{ $financeMetrics['period'] }})</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <canvas id="profitRadarChart" height="300"></canvas>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <h5>Key Metrics</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Gross Profit Margin
                            <span class="badge bg-primary rounded-pill">{{ $financeMetrics['metrics']['Gross Profit Margin'] }}%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Net Profit Margin
                            <span class="badge bg-info rounded-pill">{{ $financeMetrics['metrics']['Net Profit Margin'] }}%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sales
                            <span class="badge bg-success rounded-pill">@money($financeMetrics['metrics']['Sales'] * 1000)</span>
                        </li>
                    </ul>
                </div>
                @if($financeMetrics['comment'])
                <div class="alert alert-info">
                    <strong>Comments:</strong> {{ $financeMetrics['comment'] }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('profitRadarChart').getContext('2d');
        const financeMetrics = @json($financeMetrics['metrics']);
        
        // Prepare data for radar chart (focusing on the two margins)
        const radarData = {
            labels: ['Gross Profit Margin', 'Net Profit Margin', 'Sales (in thousands)', 'Operational Costs (in thousands)', 'Net Profit (in thousands)'],
            datasets: [{
                label: 'Financial Metrics',
                data: [
                    financeMetrics['Gross Profit Margin'],
                    financeMetrics['Net Profit Margin'],
                    financeMetrics['Sales'],
                    financeMetrics['Operational Costs'],
                    financeMetrics['Net Profit']
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        };

        new Chart(ctx, {
            type: 'radar',
            data: radarData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.r !== null) {
                                    // Format differently for percentages vs monetary values
                                    if (context.label.includes('Margin')) {
                                        label += context.parsed.r + '%';
                                    } else {
                                        label += 'Rp' + (context.parsed.r * 1000).toLocaleString();
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        ticks: {
                            callback: function(value) {
                                // Don't add units to scale numbers
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@endif