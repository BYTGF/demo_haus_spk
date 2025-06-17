@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <h6>Operational Input</h6>
                            @if (auth()->check() && auth()->user()->role->role_name === 'Operational')
                                <button type="button" class="btn btn-primary" onclick="openCreateOperationalInputModal()">
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
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gaji & Upah</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sewa</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilitas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perlengkapan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lain-lain</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inputs as $input)
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ optional($input->period)->format('M Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ optional($input->store)->store_name }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">          
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->gaji_upah) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->sewa) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->utilitas) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->perlengkapan) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->lain_lain) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->total) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($input->status === 'Selesai')
                                                    <span class="badge bg-success">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Sedang Direview')
                                                    <span class="badge bg-warning">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Butuh Revisi')
                                                    <span class="badge bg-danger">{{ $input->status }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $input->status }}</span>
                                                @endif
                                            </td>                            
                                            <td class="text-center">
                                                @if (auth()->check() && auth()->user()->role->role_name === 'Operational')
                                                    @if($input->status === 'Butuh Revisi')
                                                        <button class="btn btn-xs btn-warning px-3 py-2 edit-btn"
                                                            onclick="openEditOperationalInputModal({{ json_encode($input) }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if (auth()->check() && auth()->user()->role->role_name === 'Manager Business Development')
                                                    @if($input->status === 'Sedang Direview')
                                                        <button class="btn btn-xs btn-success px-3 py-2" 
                                                                onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <form id="approve-form-{{ $input->id }}" 
                                                            action="{{ route('operational.approve', $input) }}" 
                                                            method="POST" class="d-none">
                                                            @csrf
                                                        </form>

                                                        <button class="btn btn-xs btn-danger reject-btn px-3 py-2" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal"
                                                                data-input-id="{{ $input->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $inputs->links('pagination::bootstrap-5') }}
                            </div>
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gaji & Upah</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sewa</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilitas</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perlengkapan</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lain-lain</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($dones as $done)
                        <tr>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $done->period->format('M Y') }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $done->store->store_name }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">            
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->gaji_upah) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->sewa) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->utilitas) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->perlengkapan) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->lain_lain) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->total) }}</p>
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
    

      {{-- Modal --}}
      
    
    <!-- Reject Modal (for managers) -->
    <div class="modal fade" id="operational-input-modal" tabindex="-1" role="dialog" aria-labelledby="operational-input-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Operational Input</h3>
                        </div>
                        <div class="card-body">
                            <form id="operational-input-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="id" id="input_id">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="status" value="Sedang Direview">

                                <!-- Section 1: Basic Info -->
                                <div class="card mb-3">
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
                                </div>
                            
                                <!-- Section 1: Fixed Costs -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Biaya Tetap</h6>
                                        <small class="text-muted">Biaya operasional yang jumlahnya relatif tetap setiap periode</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gaji_upah">
                                                        Gaji & Upah
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Total gaji karyawan termasuk tunjangan dan bonus"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="gaji_upah" id="gaji_upah" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sewa">
                                                        Sewa
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Biaya sewa lokasi/tempat usaha"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="sewa" id="sewa" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            
                                <!-- Section 2: Variable Costs -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Biaya Variabel</h6>
                                        <small class="text-muted">Biaya operasional yang jumlahnya bervariasi sesuai volume kegiatan</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="utilitas">
                                                        Utilitas
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Biaya listrik, air, gas, telepon, internet"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="utilitas" id="utilitas" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="perlengkapan">
                                                        Perlengkapan
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Biaya alat tulis, bahan habis pakai, dll"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="perlengkapan" id="perlengkapan" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="lain_lain">
                                                        Lain-lain
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Biaya operasional lain yang tidak termasuk kategori di atas"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="lain_lain" id="lain_lain" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            
                                <!-- Section 3: Total -->
                                <!-- Section 3: Total -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Total Biaya Operasional</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="total">
                                                        Total
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Jumlah total semua biaya operasional"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="total" id="total" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            
                                <!-- Comments Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Komentar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="comment_input">
                                                Komentar Input
                                                <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                   title="Berikan penjelasan tambahan tentang data yang diinput"></i>
                                            </label>
                                            <textarea class="form-control" name="comment_input" id="comment_input" rows="2"></textarea>
                                        </div>
                                        
                                        <div class="form-group" id="comment-review-group">
                                            <label for="comment_review">
                                                Komentar Review
                                                <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                   title="Komentar dari reviewer (jika ada)"></i>
                                            </label>
                                            <textarea class="form-control" name="comment_review" id="comment_review" rows="2"></textarea>
                                        </div>
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
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
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
    document.addEventListener('DOMContentLoaded', function () {
        const calculateTotal = () => {
            const gaji_upah = parseFloat(document.getElementById('gaji_upah')?.value) || 0;
            const sewa = parseFloat(document.getElementById('sewa')?.value) || 0;
            const utilitas = parseFloat(document.getElementById('utilitas')?.value) || 0;
            const perlengkapan = parseFloat(document.getElementById('perlengkapan')?.value) || 0;
            const lain_lain = parseFloat(document.getElementById('lain_lain')?.value) || 0;

            const total = gaji_upah + sewa + utilitas + perlengkapan + lain_lain;
            document.getElementById('total').value = total.toFixed(2);
        };

        // Add event listeners AFTER function is defined
        const amountFields = ['gaji_upah', 'sewa', 'utilitas', 'perlengkapan', 'lain_lain'];
        amountFields.forEach(field => {
            document.getElementById(field)?.addEventListener('change', calculateTotal);
        });

        // Initial calculation AFTER function is defined
        calculateTotal();

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const modal = $('#operational-input-modal');
        const form = document.getElementById('operational-input-form');
        const rejectForm = document.getElementById('rejectForm');
        const methodField = document.getElementById('form-method');
        const modalTitle = document.getElementById('modal-title');
        const inputIdField = document.getElementById('input_id');
        const statusField = document.getElementById('status');
        const commentReviewGroup = document.getElementById('comment-review-group');

        const rejectModal = document.getElementById('rejectModal');
        let currentInputId = null;
    
        // Open modal for creating new input
        window.openCreateOperationalInputModal = function () {
            form.reset();
            form.action = '{{ route("operational.store") }}';
            methodField.value = 'POST';
            modalTitle.textContent = 'Create Operational Input';
            
            // Hide comment review field for operational users
            if (document.getElementById('comment-review-group')) {
                document.getElementById('comment-review-group').style.display = 'none';
            }
            
            modal.modal('show');
        };
    
        // Open modal for editing existing input
        window.openEditOperationalInputModal = function(inputData) {
        // Parse the input if it's a string
        const input = typeof inputData === 'string' ? JSON.parse(inputData) : inputData;
        
        form.reset();
        form.action = `/operational/${input.id}`;
        methodField.value = 'PUT';
        modalTitle.textContent = 'Edit Operational Input';
        
        // Format period correctly (from "May 2025" to "2025-05")

        // Format Rupiah helper function
        const formatRupiah = (value) => {
            return value ? value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") : '';
        };

        // Safely set values only if elements exist
        const setValueIfExists = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value || '';
        };

        setValueIfExists('input_id', input.id);
        setValueIfExists('period', input.period.substring(0, 7));
        setValueIfExists('gaji_upah', formatRupiah(input.gaji_upah));
        setValueIfExists('sewa', formatRupiah(input.sewa));
        setValueIfExists('utilitas', formatRupiah(input.utilitas));
        setValueIfExists('perlengkapan', formatRupiah(input.perlengkapan));
        setValueIfExists('lain_lain', formatRupiah(input.lain_lain));
        setValueIfExists('total', formatRupiah(input.total));
        setValueIfExists('comment_input', input.comment_input);
        setValueIfExists('comment_review', input.comment_review);

        // Handle status field if it exists
        if (statusField) {
            statusField.value = input.status;
            if (typeof handleStatusChange === 'function') {
                handleStatusChange(input.status);
            }
        }

        if (commentReviewGroup) {
            commentReviewGroup.style.display = 'block';
        }

        modal.modal('show');
    };
    
        // Handle status change for managers
        if (statusField) {
            statusField.addEventListener('change', function() {
                handleStatusChange(this.value);
            });
        }
    
        function handleStatusChange(status) {
            const commentReviewGroup = document.getElementById('comment-review-group');
            if (commentReviewGroup) {
                if (status === 'Sedang Direview' || status === 'Butuh Revisi') {
                    commentReviewGroup.style.display = 'block';
                } else {
                    commentReviewGroup.style.display = 'none';
                }
            }
        }
    
        // Calculate total when any of the amount fields change
        
    
        // For managers to reject
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentInputId = this.getAttribute('data-input-id');
                document.getElementById('rejectForm').action = `/operational/${currentInputId}/reject`;
            });
        });
      

      // Handle confirm rejection
        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            const form = document.getElementById('rejectForm');
            const formData = new FormData(form);
            
            fetch(form.action, {
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
                alert('Error rejecting input');
            });
        });
    
    
        // Reset on modal close
        modal.on('hidden.bs.modal', function () {
            form.reset();
            commentReviewGroup.style.display = 'none';
        });
    });
    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif
    </script>
@endpush