@extends('layouts.user_type.auth')

@section('content')
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5>Decision</h5>
        @if(isset($meanScore))
          <small class="text-muted">Nilai Rata-rata: {{ $meanScore }}</small>
        @endif
      </div>
      <form method="GET" action="{{ route('review-store.index') }}" id="simulate-form" class="d-flex align-items-center gap-3">
        <select name="period" id="period-select" class="form-select form-select-sm">
          <option value="6" {{ $periodChoice == '6' ? 'selected' : '' }}>6 Bulan Terakhir</option>
          <option value="12" {{ $periodChoice == '12' ? 'selected' : '' }}>12 Bulan Terakhir</option>
        </select>
        <button type="submit" class="btn btn-primary">Simulasi</button>
      </form>
    </div>

    @if(request()->has('period'))
    <div class="card-body" id="decision-table-wrapper">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <table class="table table-striped">
        <thead>
          <tr>
            <th>Store</th>
            <th>Status</th>
            <th>Kelengkapan Data</th>
            @if (auth()->user()->role->role_name === 'Manager Business Development')
              <th>Action</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($paginatedStores as $s)
          <tr>
            <td>{{ $s->store_name }}</td>
            <td>
              <span class="badge bg-{{ 
                  $s->status == 'Layak Tutup' ? 'danger' : 
                  ($s->status == 'Layak Buka' ? 'success' : 'warning') 
              }}">
                {{ $s->status }}
                @if($s->data_complete && isset($s->above_mean))
                  ({{ $s->above_mean ? 'Di Atas' : 'Di Bawah' }} Rata-rata)
                @endif
              </span>
            </td>
            <td>
              @if($s->status == 'Data Belum Lengkap')
                <div class="progress">
                  <div class="progress-bar bg-warning" role="progressbar" 
                      style="width: {{ $s->completeness }}%; height:20px" 
                      aria-valuenow="{{ $s->completeness }}" 
                      aria-valuemin="0" 
                      aria-valuemax="100">
                    {{ round($s->completeness, 1) }}%
                  </div>
                </div>
              @else
                <span class="badge bg-success">Lengkap</span>
              @endif
            </td>
            @if (auth()->user()->role->role_name === 'Manager Business Development')
            <td>
              @if($s->status == 'Layak Tutup' && $s->data_complete)
                <form action="{{ route('review-store.update', $s) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Non-aktifkan store & semua data terkait?')">
                    <i class="fas fa-times-circle"></i> Tutup Toko
                  </button>
                </form>
              @else
                <button class="btn btn-sm btn-secondary" disabled>
                  <i class="fas fa-ban"></i> Tidak Tersedia
                </button>
              @endif
            </td>
            @endif
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">Tidak ada store aktif.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-4">
          {{ $paginatedStores->links('pagination::bootstrap-5') }}
      </div>
    </div>
    @endif
  </div>

  <!-- Closed Stores Section -->
  <div class="card">
    <div class="card-header">
      <h5>Closed Stores</h5>
    </div>
    <div class="card-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Store</th>
            <th>Area</th>
            <th>Tanggal Penutupan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($closed as $store)
          <tr>
            <td>{{ $store->store_name }}</td>
            <td>{{ $store->area->area_name ?? '-' }}</td>
            <td>{{ $store->updated_at->format('d/m/Y') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="text-center">Tidak ada store yang ditutup.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-4">
          {{ $closed->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
@endsection
