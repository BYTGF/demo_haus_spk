@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <h6>Store Evaluation</h6>
                            @if (auth()->user()->role->role_name === 'Store Manager')
                                <button type="button" class="btn btn-primary" onclick="openCreateStoreInputModal()">
                                    <i class="fas fa-plus me-2"></i> Add New Input
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Store</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Accessibility</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Visibility</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lingkungan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kepadatan Kendaraan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Parkir Motor</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Parkir Mobil</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inputs as $input)
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->store->store_name }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->period->format('M Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $akses = [
                                                        1 => 'Hanya Kendaraan Pribadi',
                                                        2 => '1 Transportasi Umum & Kendaraan Pribadi',
                                                        3 => '2 Transportasi Umum & Kendaraan Pribadi',
                                                        4 => '>2 Transportasi Umum & Kendaraan Pribadi'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $akses[$input->aksesibilitas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $vis = [
                                                        1 => '< 20%',
                                                        2 => '20 - 39%',
                                                        3 => '40 - 59%',
                                                        4 => '60 - 79%',
                                                        5 => '≥ 80%'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $vis[$input->visibilitas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $lingkunganList = [];
                                                    $lingkungan = is_array($input->lingkungan) ? array_map('intval', $input->lingkungan) : json_decode($input->lingkungan, true);
                                                    
                                                    if (is_array($lingkungan)) {
                                                        if (in_array(1, $lingkungan)) $lingkunganList[] = 'Kampus';
                                                        if (in_array(2, $lingkungan)) $lingkunganList[] = 'Sekolah';
                                                        if (in_array(3, $lingkungan)) $lingkunganList[] = 'Perumahan';
                                                    }
                                                @endphp

                                                <p class="text-xs font-weight-bold mb-0">{{ implode(', ', $lingkunganList) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $traffic = [
                                                        1 => 'Macet Parah',
                                                        2 => 'Macet',
                                                        3 => 'Lancar'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $traffic[$input->lalu_lintas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->parkir_mobil }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->parkir_motor }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($input->status === 'Selesai')
                                                    <span class="badge badge-sm bg-gradient-success">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Sedang Direview Manager BD')
                                                    <span class="badge badge-sm bg-gradient-info">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Sedang Direview Manager Area')
                                                    <span class="badge badge-sm bg-gradient-warning">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Butuh Revisi')
                                                    <span class="badge badge-sm bg-gradient-danger">{{ $input->status }}</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">{{ $input->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{-- Aksi role Store Manager --}}
                                                @if (auth()->user()->role->role_name === 'Store Manager')
                                                    @if($input->status === 'Butuh Revisi')
                                                        <button class="btn btn-sm btn-warning px-3 py-2" 
                                                                onclick="openEditStoreInputModal({{ json_encode($input) }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                @endif

                                                {{-- Aksi role Area Manager --}}
                                                @if (auth()->user()->role->role_name === 'Area Manager')
                                                    @if($input->status === 'Sedang Direview Manager Area')
                                                        <button class="btn btn-sm btn-success px-3 py-2" 
                                                                onclick="document.getElementById('approve-area-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <form id="approve-area-form-{{ $input->id }}" 
                                                            action="{{ route('store.approve-area', $input) }}" 
                                                            method="POST" class="d-none">
                                                            @csrf
                                                        </form>

                                                        <button class="btn btn-sm btn-danger reject-btn px-3 py-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal"
                                                                data-input-id="{{ $input->id }}"
                                                                data-approval-level="area">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                @endif

                                                {{-- Aksi role Manager BD --}}
                                                @if (auth()->user()->role->role_name === 'Manager Business Development')
                                                    @if($input->status === 'Sedang Direview Manager BD')
                                                        <button class="btn btn-sm btn-success px-3 py-2" 
                                                                onclick="document.getElementById('approve-bd-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <form id="approve-bd-form-{{ $input->id }}" 
                                                            action="{{ route('store.approve-bd', $input) }}" 
                                                            method="POST" class="d-none">
                                                            @csrf
                                                        </form>

                                                        <button class="btn btn-sm btn-danger reject-btn px-3 py-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal"
                                                                data-input-id="{{ $input->id }}"
                                                                data-approval-level="bd">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Report Selesai</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Store</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Accessibility</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Visibility</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lingkungan</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kepadatan Kendaraan</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Parkir Motor</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Parkir Mobil</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($dones as $done)
                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->store->store_name }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->period->format('M Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $akses = [
                                                        1 => 'Hanya Kendaraan Pribadi',
                                                        2 => '1 Transportasi Umum & Kendaraan Pribadi',
                                                        3 => '2 Transportasi Umum & Kendaraan Pribadi',
                                                        4 => '>2 Transportasi Umum & Kendaraan Pribadi'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $akses[$done->aksesibilitas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $vis = [
                                                        1 => '< 20%',
                                                        2 => '20 - 39%',
                                                        3 => '40 - 59%',
                                                        4 => '60 - 79%',
                                                        5 => '≥ 80%'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $vis[$done->visibilitas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $lingkunganList = [];
                                                    $lingkungan = is_array($done->lingkungan) ? array_map('intval', $done->lingkungan) : json_decode($done->lingkungan, true);
                                                    
                                                    if (is_array($lingkungan)) {
                                                        if (in_array(1, $lingkungan)) $lingkunganList[] = 'Kampus';
                                                        if (in_array(2, $lingkungan)) $lingkunganList[] = 'Sekolah';
                                                        if (in_array(3, $lingkungan)) $lingkunganList[] = 'Perumahan';
                                                    }
                                                @endphp

                                                <p class="text-xs font-weight-bold mb-0">{{ implode(', ', $lingkunganList) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @php
                                                    $traffic = [
                                                        1 => 'Macet Parah',
                                                        2 => 'Macet',
                                                        3 => 'Lancar'
                                                    ];
                                                @endphp
                                                <p class="text-xs font-weight-bold mb-0">
                                                    {{ $traffic[$done->lalu_lintas] ?? '-' }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->parkir_mobil }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->parkir_motor }}</p>
                                            </td>
                    @endforeach
                  </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $dones->links('pagination::bootstrap-5') }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Store Input Modal -->
    <div class="modal fade" id="store-input-modal" tabindex="-1" role="dialog" aria-labelledby="store-input-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Evaluasi Toko</h3>
                        </div>
                        <div class="card-body">
                            <form id="store-input-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="input_id" id="input_id">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="status" value="Sedang Direview Manager Area">

                                <!-- Bagian 1: Informasi Dasar -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informasi Dasar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="period">Periode</label>
                                                    <input type="month" class="form-control" name="period" id="period" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 2: Evaluasi Lokasi -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Evaluasi Lokasi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Aksesibilitas</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="aksesibilitas" id="aksesibilitas_a" value="4" required>
                                                        <label class="form-check-label" for="aksesibilitas_a"> >2 jenis transportasi umum & kendaraan pribadi</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="aksesibilitas" id="aksesibilitas_b" value="1">
                                                        <label class="form-check-label" for="aksesibilitas_b">2 jenis transportasi umum & kendaraan pribadi</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="aksesibilitas" id="aksesibilitas_c" value="2">
                                                        <label class="form-check-label" for="aksesibilitas_c">1 jenis transportasi umum & kendaraan pribadi</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="aksesibilitas" id="aksesibilitas_d" value="1">
                                                        <label class="form-check-label" for="aksesibilitas_d">Hanya Kendaraan Pribadi</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Visibilitas</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="visibilitas" id="visibilitas" required min="0">
                                                        <span class="input-group-text">meter</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 3: Lingkungan Sekitar -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Lingkungan Sekitar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Jenis Lingkungan (Pilih yang sesuai)</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_kampus" value="1">
                                                        <label class="form-check-label" for="lingkungan_kampus">Area Kampus</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_sekolah" value="2">
                                                        <label class="form-check-label" for="lingkungan_sekolah">Area Sekolah</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_perumahan" value="3">
                                                        <label class="form-check-label" for="lingkungan_perumahan">Area Perumahan</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Kepadatan Pengunjung</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="lalu_lintas" id="lalu_lintas" required min="0">
                                                        <span class="input-group-text">orang/m<sup>2</sup></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Kondisi Lalu Lintas</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_1" value="3" required>
                                                        <label class="form-check-label" for="kepadatan_kendaraan_1">Lancar</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_2" value="2">
                                                        <label class="form-check-label" for="kepadatan_kendaraan_2">Macet</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_3" value="1">
                                                        <label class="form-check-label" for="kepadatan_kendaraan_3">Macet Parah</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 4: Fasilitas Parkir -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Fasilitas Parkir</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Kapasitas Parkir Mobil</label>
                                                    <input type="number" class="form-control" name="parkir_mobil" id="parkir_mobil" required min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Kapasitas Parkir Motor</label>
                                                    <input type="number" class="form-control" name="parkir_motor" id="parkir_motor" required min="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bagian 5: Evaluasi Keseluruhan -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Evaluasi Keseluruhan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mt-3">
                                            <label for="comment_input">Catatan Evaluasi</label>
                                            <textarea class="form-control" name="comment_input" id="comment_input" rows="3"></textarea>
                                        </div>

                                        <div class="form-group mt-3" id="comment-review-group">
                                            <label for="comment_review">Komentar Review</label>
                                            <textarea class="form-control" name="comment_review" id="comment_review" rows="3"></textarea>
                                        </div>

                                    </div>
                                </div>

                                <!-- Tombol Form -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i> Simpan
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i> Tutup
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <input type="hidden" name="approval_level" id="approval_level">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Input</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason for rejection:</label>
                            <textarea name="comment_review" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmRejectBtn">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const modal = $('#store-input-modal');
    const form = document.getElementById('store-input-form');
    const methodField = document.getElementById('form-method');
    const modalTitle = document.getElementById('modal-title');
    const inputIdField = document.getElementById('input_id');
    const rejectForm = document.getElementById('rejectForm');
    const approvalLevelField = document.getElementById('approval_level');

    // Open modal for creating new input
    window.openCreateStoreInputModal = function() {
        form.reset();
        form.action = '{{ route("store.store") }}';
        methodField.value = 'POST';
        modalTitle.textContent = 'Create Store Input';

        if (document.getElementById('comment-review-group')) {
            document.getElementById('comment-review-group').style.display = 'none';
        }
        modal.modal('show');
    };

    // Open modal for editing existing input
    window.openEditStoreInputModal = function(input) {
        form.reset();
        form.action = `/store/${input.id}`;
        methodField.value = 'PUT';
        modalTitle.textContent = 'Edit Store Input';
        inputIdField.value = input.id;
        
        // Set basic fields
        document.getElementById('period').value = input.period.substring(0, 7) || '';
        document.getElementById('visibilitas').value = input.visibilitas || '';
        document.getElementById('lalu_lintas').value = input.lalu_lintas || '';
        document.getElementById('parkir_mobil').value = input.parkir_mobil || '';
        document.getElementById('parkir_motor').value = input.parkir_motor || '';
        document.getElementById('comment_input').value = input.comment_input || '';
        const commentReviewField = document.getElementById('comment_review');
        if (commentReviewField) {
            commentReviewField.value = input.comment_review || '';
        }

                if (document.getElementById('comment-review-group')) {
            document.getElementById('comment-review-group').style.display = 'block';
        }
        
        // Set radio buttons (with null checks)
        const aksesibilitasRadio = document.querySelector(`input[name="aksesibilitas"][value="${input.aksesibilitas}"]`);
        if (aksesibilitasRadio) aksesibilitasRadio.checked = true;
        
        const kepadatanRadio = document.querySelector(`input[name="kepadatan_kendaraan"][value="${input.kepadatan_kendaraan}"]`);
        if (kepadatanRadio) kepadatanRadio.checked = true;
        
        // Set checkboxes (handle array or JSON string)
        const lingkunganCheckboxes = document.querySelectorAll('input[name="lingkungan[]"]');
        lingkunganCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        let lingkunganValues = [];
        if (Array.isArray(input.lingkungan)) {
            lingkunganValues = input.lingkungan;
        } else if (typeof input.lingkungan === 'string') {
            try {
                lingkunganValues = JSON.parse(input.lingkungan) || [];
            } catch {
                lingkunganValues = input.lingkungan.split(',');
            }
        }
        
        lingkunganValues.forEach(value => {
            const checkbox = document.querySelector(`input[name="lingkungan[]"][value="${value.toString().trim()}"]`);
            if (checkbox) checkbox.checked = true;
        });
        
        modal.modal('show');
    };

    // For managers to reject
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-input-id');
            const approvalLevel = this.getAttribute('data-approval-level');
            rejectForm.action = `/store/${inputId}/reject`;
            approvalLevelField.value = approvalLevel;
        });
    });

    // Handle confirm rejection
    document.getElementById('confirmRejectBtn').addEventListener('click', function() {
        const formData = new FormData(rejectForm);
        
        fetch(rejectForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok');
        })
        .then(data => {
            if (data.success) {
                $('#rejectModal').modal('hide');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting evaluation');
        });
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        // Validate at least one environment type is selected
        const lingkunganCheckboxes = document.querySelectorAll('input[name="lingkungan[]"]:checked');
        if (lingkunganCheckboxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one environment type');
            return;
        }
        
        // Additional validation can be added here
    });
});
@if(session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif
</script>
@endpush