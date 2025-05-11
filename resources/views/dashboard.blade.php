@extends('layouts.user_type.auth')

@section('content')
    @if(auth()->user()->role->role_name === 'Manager Business Development')
     @include('layouts.dashboard.mbd')
    @endif
    <div class="row">
      <div class="col-6">
        
      </div>
      <div class="col-6">
        <form method="GET" action="{{ route('dashboard') }}" class="mb-3 d-flex gap-2">
            <select name="period_filter" class="form-select" style="max-width: 200px">
                <option value="all">Semua Periode</option>
                @foreach($availablePeriods as $period)
                    <option value="{{ $period }}" {{ $periodFilter == $period ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($period)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>

            @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'Area Manager')
            <select name="store_filter" class="form-select" style="max-width: 200px">
                <option value="all">Semua Store</option>
                @foreach($stores as $id => $name)
                    <option value="{{ $id }}" {{ $storeFilter == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-8">
        @include('layouts.dashboard.operational')
      </div>
      <div class="col-4">
        @include('layouts.dashboard.bd')
      </div>
    </div>
    
    


@endsection


@push('dashboard')
  @stack('scripts')
@endpush

