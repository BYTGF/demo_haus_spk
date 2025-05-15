<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">

                    <!-- Store Metrics (only shown when store and period selected) -->
                    @if($storeMetrics)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">
                                    {{ $storeMetrics['store_name'] }} - 
                                    {{ \Carbon\Carbon::parse($storeMetrics['period'])->format('F Y') }}
                                    <span class="badge bg-{{ $storeMetrics['status'] == 'Selesai' ? 'success' : 'warning' }} float-end">
                                        {{ $storeMetrics['status'] }}
                                    </span>
                                </h4>
                            </div>
                            <div class="card-body">
                                @foreach($storeMetrics['metrics'] as $metricName => $metric)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>
                                                <strong>{{ $metricName }}</strong>
                                                <small class="text-muted ms-2">{{ $metric['description'] }}</small>
                                            </span>
                                            <span>{{ $metric['value'] }}/{{ $metric['max'] }}</span>
                                        </div>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                @if($metric['value']/$metric['max'] >= 0.7) bg-success
                                                @elseif($metric['value']/$metric['max'] >= 0.4) bg-warning
                                                @else bg-danger
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ ($metric['value']/$metric['max']) * 100 }}%" 
                                                aria-valuenow="{{ $metric['value'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="{{ $metric['max'] }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Please select both a store and a period to view performance metrics.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script>
$(document).ready(function() {
    // Handle filter changes
    $('#storeFilter, #periodFilter').change(function() {
        const storeId = $('#storeFilter').val();
        const period = $('#periodFilter').val();
        
        // Redirect with new filters
        window.location.href = "{{ route('dashboard') }}?store_filter=" + storeId + "&period_filter=" + period;
    });
});
</script> --}}
