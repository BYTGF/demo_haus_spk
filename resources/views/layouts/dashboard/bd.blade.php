<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center p-3">
        <h4>Kompetitor</h4>
    </div>
    <div class="card-body p-3">
      @if($inputbds->isNotEmpty())
        @php
            $latest = $inputbds->first(); // Ambil data terbaru
            $direct_competition = $latest->direct_competition;
            $indirect_competition = $latest->indirect_competition;
            $substitute_competition = $latest->substitute_competition;
            // if ($latest->direct_competition == 0) {
            //     $direct_competition = 1;
            // }elseif ($latest->direct_competition > 0 && $latest->direct_competition <= 5) {
            //     $direct_competition = 2;
            // }elseif ($latest->direct_competition > 5 && $latest->direct_competition <= 10) {
            //     $direct_competition = 3;
            // }elseif ($latest->direct_competition > 10 && $latest->direct_competition <= 15) {
            //     $direct_competition = 4;
            // }else {
            //     $direct_competition = 5;
            // }

            // if ($latest->indirect_competition == 0) {
            //     $indirect_competition = 1;
            // }elseif ($latest->indirect_competition > 0 && $latest->indirect_competition <= 5) {
            //     $indirect_competition = 2;
            // }elseif ($latest->indirect_competition > 5 && $latest->indirect_competition <= 10) {
            //     $indirect_competition = 3;
            // }elseif ($latest->indirect_competition > 10 && $latest->indirect_competition <= 15) {
            //     $indirect_competition = 4;
            // }else {
            //     $indirect_competition = 5;
            // }

            // if ($latest->substitute_competition == 0) {
            //     $substitute_competition = 1;
            // }elseif ($latest->substitute_competition > 0 && $latest->substitute_competition <= 5) {
            //     $substitute_competition = 2;
            // }elseif ($latest->substitute_competition > 5 && $latest->substitute_competition <= 10) {
            //     $substitute_competition = 3;
            // }elseif ($latest->substitute_competition > 10 && $latest->substitute_competition <= 15) {
            //     $substitute_competition = 4;
            // }else {
            //     $substitute_competition = 5;
            // }
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
                <h6>Direct Competition <span><i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title=" Menawarkan produk sejenis dan memiliki target pasar yang sama, Contoh (Es teh Indonesia, Teguk, dll )"></i></span></h6>
            @for($i = 0; $i < $direct_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #ff3d3d;"></i>
            @endfor
        </div>

        <div class="indirect-compete m-3">
            <h6>Indirect Competition<span> <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title="Memiliki skala lebih besar dari baik dari segi penjualan, penguasaan pasar, atau distribusi, Contoh (Chatime, Lawson, dll)"></i></span></h6>
            @for($i = 0; $i < $indirect_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #3d7dff;"></i>
            @endfor
        </div>

        <div class="subsit-compete m-3">
            <h6>Substitute Competition<span><i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title="Menawarkan produk berbeda tetapi dapat menggantikan atau memenuhi kebutuhan yang sama dari konsumen, Contoh (Kopi Kenangan, Tomoro, dll )"></i></span></h6>
            @for($i = 0; $i < $substitute_competition; $i++)
                <i class="fa-solid fa-glass-water fa-2xl" style="color: #3dff74;"></i>
            @endfor
        </div>
    @else
        <div class="alert">
            Belum ada data Business Development yang diinput.
        </div>
    @endif

    </div>
  
  </div>