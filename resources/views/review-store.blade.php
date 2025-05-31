{{-- resources/views/store/review.blade.php --}}
@extends('layouts.user_type.auth')

@section('content')
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5>C-Level: Store Ratings & Management</h5>
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
            <th>Finance Rating</th>
            <th>Operational Rating</th>
            <th>BD Rating</th>
            <th>Store Rating</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        @forelse($stores as $s)
          <tr>
            <td>{{ $s->store_name }}</td>
            <td>{{ $s->finance_rating ?? '-' }}</td>
            <td>{{ $s->operational_rating ?? '-' }}</td>
            <td>{{ $s->bd_rating ?? '-' }}</td>
            <td>{{ $s->store_rating ?? '-' }}</td>
            <td>
              <form action="{{ route('review-store.update', $s) }}" method="POST">
                @csrf
                <button type="submit"
                    class="btn btn-sm btn-danger"
                    onclick="return confirm('Non-aktifkan store & semua data terkait?')">
                  <i class="fas fa-times-circle"></i> Close Store
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">Tidak ada store aktif.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
