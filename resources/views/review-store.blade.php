@extends('layouts.user_type.auth')

@section('content')
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>Decision</h5>
    <form method="GET" action="{{ route('review-store.index') }}">
      <select name="period" onchange="this.form.submit()" class="form-select form-select-sm">
        <option value="6" {{ $periodChoice == '6' ? 'selected' : '' }}>6 Bulan Terakhir</option>
        <option value="12" {{ $periodChoice == '12' ? 'selected' : '' }}>12 Bulan Terakhir</option>
      </select>
    </form>
  </div>
  <div class="card-body">
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
            <span class="badge bg-{{ $s->status == 'Layak Tutup' ? 'danger' : 'success' }}">
              {{ $s->status }}
            </span>
          </td>
          @if (auth()->user()->role->role_name === 'Manager Business Development')
          <td>
            <form action="{{ route('review-store.update', $s) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Non-aktifkan store & semua data terkait?')">
                <i class="fas fa-times-circle"></i> Close Store
              </button>
            </form>
          </td>
          @endif
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center">Tidak ada store aktif.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
    <div class="d-flex justify-content-center mt-4">
        {{ $paginatedStores->links('pagination::bootstrap-5') }}
    </div>
  </div>

  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>Toko yang sudah ditutup</h5>
  </div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Store</th>
          <th>Waktu Tutup</th>
        </tr>
      </thead>
      <tbody>
        @forelse($closed as $close)
        <tr>
          <td>{{ $close->store_name }}</td>
          <td>
            {{ $close->updated_at }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center">Belum ada store yang ditutup.</td>
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
