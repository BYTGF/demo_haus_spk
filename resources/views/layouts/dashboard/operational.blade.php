<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center p-3">
        <h4>Operational Expenses</h4>
    </div>
    <div class="card-body p-3">
        @if($inputOperationals->isNotEmpty())
            @if($storeFilter !== 'all')
            <div class="status-display mb-3">
                <span class="badge 
                    @if($inputOperationals->first()->status === 'Sedang Direview') bg-warning
                    @elseif($inputOperationals->first()->status === 'Butuh Revisi') bg-danger
                    @elseif($inputOperationals->first()->status === 'Selesai') bg-success
                    @endif">
                        {{ $inputOperationals->first()->status }}
                </span>
            </div>
            @endif
    
            <h3>Total : Rp. {{ number_format($operationalData['Total'] ?? 0, 0, ',', '.') }}</h3>
            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="pieOperational"></canvas>
            </div>          
        @else
            <div class="alert alert-info">
                Belum ada data operational yang diinput.
            </div>
        @endif
    </div>
  
  </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('pieOperational').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Personnel & Facilities', 'Supplies', 'Others'],
            datasets: [{
                data: [
                    {{ $operationalData['Personnel & Facilities'] ?? 0 }},
                    {{ $operationalData['Supplies'] ?? 0 }},
                    {{ $operationalData['Others'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>