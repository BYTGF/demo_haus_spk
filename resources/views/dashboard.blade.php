@extends('layouts.user_type.auth')

@php
    $activeComponents = 0;


    if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Operational') {
        $activeComponents++;
    }

    if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Business Development Staff') {
        $activeComponents++;
    }

    // Tentuin col-nya berdasarkan jumlah komponen aktif
    $colClass = match($activeComponents) {
        1 => 'col-12',
        2 => 'col-6',
        default => 'col-12'
    };
@endphp


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

            @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Area Manager' || auth()->user()->role->role_name === 'Business Development Staff')
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
    @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Area Manager' || auth()->user()->role->role_name === 'Store Manager')
    <div class="row">
      <div class="col-12">
        @include('layouts.dashboard.store')
      </div>
    </div>
    @endif
    <div class="row m-3">
      @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Finance')
      
        @include('layouts.dashboard.finance')
      @endif

      @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Operational')
      <div class="{{ $colClass }}">
        @include('layouts.dashboard.operational')
      </div>
      @endif

      @if(auth()->user()->role->role_name === 'Manager Business Development' || auth()->user()->role->role_name === 'C-Level' || auth()->user()->role->role_name === 'Business Development Staff')
      <div class="{{ $colClass }}">
        @include('layouts.dashboard.bd')
      </div>
      @endif
    </div>

    
    


@endsection


@push('dashboard')
  @stack('scripts')
@endpush

