<div class="container">
  <div class="card h-100 p-4 ">
    @if ($storeMetrics)
    <h5 class="mb-4"><strong>SHOWING</strong> – DATA KONDISI LOKASI</h5>

    <div class="row g-3">
      {{-- AKSESIBILITAS --}}
      <div class="col-md-6">
        <div class="card h-100 shadow-lg bg-body rounded border border-primary">
          <div class="card-body text-center">
            <h6>Aksesibilitas 🚗</h6>
            @php
                $aksesVal = $storeMetrics->aksesibilitas;
                $aksesLabels = ['Sangat Terbatas','Terbatas', 'Dapat Dijangkau', 'Sangat Dapat Dijangkau'];
                $aksesColors = ['danger', 'warning', 'dark', 'success'];
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-2">
              @foreach($aksesLabels as $i => $label)
                <div>
                  <span class="dot bg-{{ $aksesColors[$i] }} @if($aksesVal == $i + 1) border border-3 border-dark @endif"></span>
                </div>
              @endforeach
            </div>
            <span class="badge bg-{{ $aksesColors[$aksesVal - 1] }}">
              {{ strtoupper($aksesLabels[$aksesVal - 1]) }}
            </span>
          </div>
        </div>
      </div>

      {{-- VISIBILITAS --}}
      <div class="col-md-6">
        <div class="card h-100 shadow-lg bg-body rounded border border-primary">
          <div class="card-body text-center">
            <h6>Visibilitas 👁️</h6>
            @php
              if($storeMetrics->visibilitas < 20){
                $vis = 1; 
              }else if($storeMetrics->visibilitas >= 20 && $storeMetrics->visibilitas < 40){
                $vis = 2; 
              }else if($storeMetrics->visibilitas >= 40 && $storeMetrics->visibilitas < 60){
                $vis = 3; 
              }else if($storeMetrics->visibilitas >= 60 && $storeMetrics->visibilitas < 80){
                $vis = 4; 
              }else if($storeMetrics->visibilitas >= 80){
                $vis = 5; 
              }
            @endphp
            <div class="d-flex flex-column align-items-center">
              @for ($i = 5; $i >= 1; $i--)
                <div class="w-100 mb-1" style="height: 10px; background-color: {{ $i <= $vis ? '#3399ff' : '#e0e0e0' }}; width: {{ 40 + ($i * 10) }}%;"></div>
              @endfor
            </div>
          </div>
        </div>
      </div>

      {{-- LALU LINTAS --}}
      <div class="col-md-4">
        <div class="card h-100 shadow-lg bg-body rounded border border-primary">
          <div class="card-body text-center">
            <h6>Lalu Lintas (Traffic) 🚶</h6>
            @php
                $traffic = $storeMetrics->lalu_lintas;
                $max = 10;
                $percent = ($traffic / $max) * 100;
            @endphp
            <div class="mb-2">
              @for ($i = 1; $i <= $max; $i++)
                <i class="fas fa-person-walking {{ $i <= $traffic ? 'text-danger' : 'text-secondary' }}"></i>
              @endfor
            </div>
            <div class="progress mb-1" style="height: 10px;">
              <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
            </div>
            <span class="badge {{ $traffic > 7 ? 'bg-danger' : 'bg-success' }}">
              {{ $traffic > 7 ? 'Sangat Padat' : 'Lancar' }}
            </span>
          </div>
        </div>
      </div>

      {{-- LINGKUNGAN --}}
      <div class="col-md-4">
        <div class="card h-100 shadow-lg bg-body rounded border border-primary">
          <div class="card-body text-center">
            <h6>Lingkungan Sekitar</h6>
            @php
              $icons = ["1" => 'fa-graduation-cap', "2" => 'fa-school', "3" => 'fa-house'];
              $lingkunganData = is_string($storeMetrics->lingkungan) ? json_decode($storeMetrics->lingkungan, true) : $storeMetrics->lingkungan;
            @endphp
            @foreach ($icons as $key => $icon)
              @if (in_array($key, $lingkunganData))
                <i class="fas {{ $icon }} fa-2x m-2 text-success"></i>
              @else
                <i class="fas {{ $icon }} fa-2x m-2 text-danger"></i>
              @endif
            @endforeach
          </div>
        </div>
      </div>

      {{-- AREA PARKIR --}}
      <div class="col-md-4">
        <div class="card h-100 shadow-lg bg-body rounded border border-primary">
          <div class="card-body text-center">
            <h6>Area Parkir</h6>
            <p>
              @php
                $maxIcons = 5;
                $parkirMobil = min($storeMetrics->parkir_mobil, $maxIcons); // Batasi maks 5 ikon

              @endphp
              
              @for ($i = 0; $i < $parkirMobil; $i++)
                <i class="fa-solid fa-car-side {{ $i < $storeMetrics->parkir_mobil ? 'text-success' : 'text-secondary' }}"></i>
              @endfor
            </p>

            <p>
              @php
                $parkirMotor = min($storeMetrics->parkir_motor, $maxIcons); // Batasi maks 5 ikon
              @endphp
              
              @for ($i = 0; $i < $parkirMotor; $i++)
                <i class="fa-solid fa-motorcycle {{ $i < $storeMetrics->parkir_motor ? 'text-success' : 'text-secondary' }}"></i>
              @endfor
            </p>
                @php
                if($storeMetrics->parkir_mobil == 1 && $storeMetrics->parkir_motor == 3){
                  $statusParkir = "CUKUP";
                  $statusParkirBadge = 'info';
                }else if($storeMetrics->parkir_mobil >= 1 && $storeMetrics->parkir_motor >= 3){
                  $statusParkir = "LUAS";
                  $statusParkirBadge = 'success';
                }else if($storeMetrics->parkir_mobil <= 0 && $storeMetrics->parkir_motor == 3){
                  $statusParkir = "TERBATAS";
                  $statusParkirBadge = 'warning';
                }else if($storeMetrics->parkir_mobil <= 0 && $storeMetrics->parkir_motor < 3){
                  $statusParkir = "SANGAT TERBATAS";
                  $statusParkirBadge = 'danger';
                }else{
                  $statusParkir = "CUKUP";
                  $statusParkirBadge = 'secondary';
                }
            @endphp
            <p class="badge bg-{{ $statusParkirBadge }}">
              {{ $statusParkir }}
            </p>
          </div>
        </div>
      </div>


    </div>
    @else
    <h5 class="mb-4">Pilih Store dan Periode untuk melihat Dashboard Kondisi Lokasi</h5>
    @endif
  </div>
</div>


