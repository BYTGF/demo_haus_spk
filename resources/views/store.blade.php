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
                                    <i class="fas fa-plus me-2"></i> New Evaluation
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
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->aksesibilitas_label }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->visibilitas_label }}</p>
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
                                                @if (auth()->user()->role->role_name === 'Store Manager')
                                                    @if($input->status === 'Butuh Revisi')
                                                        <button class="btn btn-sm btn-warning px-3 py-2" 
                                                                onclick="openEditStoreInputModal({{ json_encode($input) }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                                
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($dones as $done)
                        <tr>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $input->store->store_name }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $input->period->format('M Y') }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $input->aksesibilitas_label }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $input->visibilitas_label }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $done->rating }}</p>
                            </td>
                        </tr>
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
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Store Evaluation</h3>
                        </div>
                        <div class="card-body">
                            <form id="store-input-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="input_id" id="input_id">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="status" value="Sedang Direview Manager Area">

                                <!-- Section 1: Basic Info -->
                                {{-- <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="period">Period</label>
                                                    <input type="month" class="form-control" name="period" id="period" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Section 2: Location Evaluation -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Location Evaluation</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Accessibility</label>
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
                                                    <label>Visibility</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="visibilitas" id="visibilitas_1" value="1" required>
                                                        <label class="form-check-label" for="visibilitas_1">1 - Very Low</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="visibilitas" id="visibilitas_2" value="2">
                                                        <label class="form-check-label" for="visibilitas_2">2 - Low</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="visibilitas" id="visibilitas_3" value="3">
                                                        <label class="form-check-label" for="visibilitas_3">3 - Medium</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="visibilitas" id="visibilitas_4" value="4">
                                                        <label class="form-check-label" for="visibilitas_4">4 - High</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 3: Surroundings -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Surroundings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Environment Type (Select all that apply)</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_kampus" value="1">
                                                        <label class="form-check-label" for="lingkungan_kampus">Campus Area</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_sekolah" value="2">
                                                        <label class="form-check-label" for="lingkungan_sekolah">School Area</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lingkungan[]" id="lingkungan_perumahan" value="3">
                                                        <label class="form-check-label" for="lingkungan_perumahan">Residential Area</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Lalu lintas</label>
                                                    <input type="number" class="form-control" name="lalu_lintas" id="lalu_lintas" required min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Traffic Conditions</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_1" value="1" required>
                                                        <label class="form-check-label" for="kepadatan_kendaraan_1">1 - Very Low</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_2" value="2">
                                                        <label class="form-check-label" for="kepadatan_kendaraan_2">2 - Low</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_3" value="3">
                                                        <label class="form-check-label" for="kepadatan_kendaraan_3">3 - Medium</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="kepadatan_kendaraan" id="kepadatan_kendaraan_4" value="4">
                                                        <label class="form-check-label" for="kepadatan_kendaraan_4">4 - High</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: Parking -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Parking Facilities</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Car Parking Capacity</label>
                                                    <input type="number" class="form-control" name="parkir_mobil" id="parkir_mobil" required min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Motorcycle Parking Capacity</label>
                                                    <input type="number" class="form-control" name="parkir_motor" id="parkir_motor" required min="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 5: Rating & Comments -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Overall Evaluation</h6>
                                    </div>
                                    <div class="card-body">
    
                                        <div class="form-group mt-3">
                                            <label for="comment_input">Evaluation Notes</label>
                                            <textarea class="form-control" name="comment_input" id="comment_input" rows="3"></textarea>
                                        </div>
                                        
                                        @if(auth()->user()->role->role_name === 'Area Manager' || 
                                            auth()->user()->role->role_name === 'Business Development Manager')
                                            <div class="form-group mt-3">
                                                <label for="comment_review">Review Comments</label>
                                                <textarea class="form-control" name="comment_review" id="comment_review" rows="3"></textarea>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Form Buttons -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i> Submit
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-2"></i> Close
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
                        <h5 class="modal-title">Reject Evaluation</h5>
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
        modalTitle.textContent = 'Create Store Evaluation';
        modal.modal('show');
    };

    // Open modal for editing existing input
    window.openEditStoreInputModal = function(input) {
        form.reset();
        form.action = `/store/${input.id}`;
        methodField.value = 'PUT';
        modalTitle.textContent = 'Edit Store Evaluation';
        inputIdField.value = input.id;
        
        // Fill form with input data
        document.getElementById('period').value = input.period.substring(0, 7);
        document.getElementById('parkir_mobil').value = input.parkir_mobil;
        document.getElementById('parkir_motor').value = input.parkir_motor;
        document.getElementById('kepadatan_kendaraan').value = input.kepadatan_kendaraan;
        document.getElementById('rating').value = input.rating;
        document.getElementById('comment_input').value = input.comment_input;
        document.getElementById('comment_review').value = input.comment_review;
        
        // Set radio buttons
        document.querySelector(`input[name="aksesibilitas"][value="${input.aksesibilitas}"]`).checked = true;
        document.querySelector(`input[name="visibilitas"][value="${input.visibilitas}"]`).checked = true;
        document.querySelector(`input[name="kepadatan_kendaraan"][value="${input.kepadatan_kendaraan}"]`).checked = true;
        
        // Set checkboxes (assuming lingkungan is stored as comma-separated values)
        if (input.lingkungan) {
            const lingkunganValues = input.lingkungan.split(',');
            lingkunganValues.forEach(value => {
                const checkbox = document.querySelector(`input[name="lingkungan[]"][value="${value.trim()}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
        
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