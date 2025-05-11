<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center p-3">
        <h4>Kompetitor</h4>
    </div>
    <div class="card-body p-3">
      @if($inputbds->isNotEmpty())
        @php
            $latest = $inputbds->first(); // Ambil data terbaru
        @endphp

        @if($storeFilter !== 'all')
        <div class="status-display mb-3">
            <span class="badge 
                @if($latest->status === 'Sedang Direview') bg-warning
                @elseif($latest->status === 'Butuh Revisi') bg-danger
                @elseif($latest->status === 'Selesai') bg-success
                @endif">
                {{ $latest->status }}
            </span>
        </div>
        @endif

        <div class="direct-compete m-3">
            <h6>Direct Competition</h6>
            @for($i = 0; $i < $latest->direct_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #ff3d3d;"></i>
            @endfor
        </div>

        <div class="indirect-compete m-3">
            <h6>Indirect Competition</h6>
            @for($i = 0; $i < $latest->indirect_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #3d7dff;"></i>
            @endfor
        </div>

        <div class="subsit-compete m-3">
            <h6>Substitute Competition</h6>
            @for($i = 0; $i < $latest->substitute_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #3dff74;"></i>
            @endfor
        </div>
    @else
        <div class="alert alert-info">
            Belum ada data Business Development yang diinput.
        </div>
    @endif

    </div>
  
  </div>