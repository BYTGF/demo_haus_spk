@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <h6>Store Input</h6>
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksesibilitas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Visibilitas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lingkungan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lalu Lintas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Area Parkir</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-secondary opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inputs as $input)
                                        <tr>
                                            <td class="align-middle text-center text-sm">          
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->period }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->store->store_name }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">          
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->aksesibilitas }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->visibilitas }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->lingkungan }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->lalu_lintas }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->area_parkir }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ $input->rating }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($input->status === 'Selesai')
                                                    <span class="badge badge-sm bg-gradient-success">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Sedang Direview Manager BD')
                                                    <span class="badge badge-sm bg-gradient-warning">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Sedang Direview Manager Area')
                                                    <span class="badge badge-sm bg-gradient-info">{{ $input->status }}</span>
                                                @elseif ($input->status === 'Butuh Revisi')
                                                    <span class="badge badge-sm bg-gradient-danger">{{ $input->status }}</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-secondary">{{ $input->status }}</span>
                                                @endif
                                            </td>                            
                                            <td class="text-center">
                                                @if (auth()->user()->role->role_name === 'Store Manager')
                                                    @if($input->status === 'Butuh Revisi')
                                                        <a href="{{ route('store.edit', $input) }}" 
                                                          class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Revise
                                                        </a>
                                                    @endif
                                                @endif
                                    
                                                @if (auth()->user()->role->role_name === 'Area Manager')
                                                    @if($input->status === 'Sedang Direview Manager Area')
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <form id="approve-form-{{ $input->id }}" 
                                                              action="{{ route('store.approve-area', $input) }}" 
                                                              method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                    
                                                        <button class="btn btn-sm btn-danger reject-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal"
                                                                data-input-id="{{ $input->id }}"
                                                                data-approval-level="area">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    @endif
                                                @endif

                                                @if (auth()->user()->role->role_name === 'Manager Business Development')
                                                    @if($input->status === 'Sedang Direview Manager BD')
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <form id="approve-form-{{ $input->id }}" 
                                                              action="{{ route('store.approve-bd', $input) }}" 
                                                              method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                    
                                                        <button class="btn btn-sm btn-danger reject-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal"
                                                                data-input-id="{{ $input->id }}"
                                                                data-approval-level="bd">
                                                            <i class="fas fa-times"></i> Reject
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

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Completed Reports</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksesibilitas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Visibilitas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lingkungan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lalu Lintas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Area Parkir</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dones as $done)
                                        <tr>
                                            <td>            
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->period }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->store->store_name }}</p>
                                            </td>
                                            <td>            
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->aksesibilitas }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->visibilitas }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->lingkungan }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->lalu_lintas }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->area_parkir }}</p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $done->rating }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-success">{{ $done->status }}</span>
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
        <div class="modal fade" id="store-input-modal" tabindex="-1" role="dialog" aria-labelledby="store-input-modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-left">
                                <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Store Input</h3>
                            </div>
                            <div class="card-body">
                                <form id="store-input-form" method="POST">
                                    @csrf
                                    <input type="hidden" name="_method" id="form-method" value="POST">
                                    <input type="hidden" name="input_id" id="input_id">
                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="aksesibilitas">Aksesibilitas</label>
                                                <select class="form-control" name="aksesibilitas" id="aksesibilitas" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="visibilitas">Visibilitas</label>
                                                <select class="form-control" name="visibilitas" id="visibilitas" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lingkungan">Lingkungan</label>
                                                <select class="form-control" name="lingkungan" id="lingkungan" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lalu_lintas">Lalu Lintas</label>
                                                <select class="form-control" name="lalu_lintas" id="lalu_lintas" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="area_parkir">Area Parkir</label>
                                                <select class="form-control" name="area_parkir" id="area_parkir" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rating">Rating</label>
                                                <select class="form-control" name="rating" id="rating" required>
                                                    <option value="">Pilih Nilai</option>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="comment_input">Komentar Input</label>
                                        <textarea class="form-control" name="comment_input" id="comment_input" rows="3" placeholder="Masukkan komentar" required></textarea>
                                    </div>
        
                                    <div class="form-group" id="comment-review-group" style="display: none;">
                                        <label for="comment_review">Komentar Review</label>
                                        <textarea class="form-control" name="comment_review" id="comment_review" rows="3" placeholder="Masukkan komentar review"></textarea>
                                    </div>
        
                                    @if(auth()->user()->role->role_name === 'Manager Business Development')
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="Sedang Direview">Sedang Direview</option>
                                                <option value="Butuh Revisi">Butuh Revisi</option>
                                                <option value="Selesai">Selesai</option>
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="Sedang Direview">
                                    @endif
        
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reject Modal (for managers) -->
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
    </div>
</main>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#store-input-modal');
        const form = document.getElementById('store-input-form');
        const rejectForm = document.getElementById('rejectForm');
        const methodField = document.getElementById('form-method');
        const modalTitle = document.getElementById('modal-title');
        const inputIdField = document.getElementById('input_id');
        const statusField = document.getElementById('status');
        const commentReviewGroup = document.getElementById('comment-review-group');

        const rejectModal = document.getElementById('rejectModal');
        let currentInputId = null;
    
        // Open modal for creating new input
        window.openCreateStoreInputModal = function () {
            form.reset();
            form.action = '{{ route("store.store") }}';
            methodField.value = 'POST';
            modalTitle.textContent = 'Create Store Input';
            
            // Hide comment review field by default
            commentReviewGroup.style.display = 'none';
            
            modal.modal('show');
        };
    
        // Open modal for editing existing input
        window.openEditStoreInputModal = function (input) {
            form.reset();
            form.action = `/store/${input.id}`;
            methodField.value = 'PUT';
            modalTitle.textContent = 'Edit Store Input';
            inputIdField.value = input.id;
    
            // Fill form with input data
            document.getElementById('aksesibilitas').value = input.aksesibilitas;
            document.getElementById('visibilitas').value = input.visibilitas;
            document.getElementById('lingkungan').value = input.lingkungan;
            document.getElementById('lalu_lintas').value = input.lalu_lintas;
            document.getElementById('area_parkir').value = input.area_parkir;
            document.getElementById('rating').value = input.rating;
            document.getElementById('comment_input').value = input.comment_input;
            document.getElementById('comment_review').value = input.comment_review;
    
            // Handle status field (for managers)
            if (statusField) {
                statusField.value = input.status;
                handleStatusChange(input.status);
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
            if (status === 'Sedang Direview' || status === 'Butuh Revisi') {
                commentReviewGroup.style.display = 'block';
            } else {
                commentReviewGroup.style.display = 'none';
            }
        }
    
        // For managers to reject
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentInputId = this.getAttribute('data-input-id');
                const approvalLevel = this.getAttribute('data-approval-level');
                document.getElementById('rejectForm').action = `/store/${currentInputId}/reject`;
                document.getElementById('approval_level').value = approvalLevel;
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
    
        // Form validation
        form.addEventListener('submit', function(e) {
            // Get all required elements
            const statusField = document.getElementById('status');
            const commentReviewField = document.getElementById('comment_review');
            
            // Determine if validation is needed
            const isManager = @json(auth()->user()->role->role_name === 'Manager Business Development');
            const status = statusField ? statusField.value : 'Sedang Direview';
            const needsComment = isManager && (status === 'Sedang Direview' || status === 'Butuh Revisi');
            
            // Validate only if required
            if (needsComment && (!commentReviewField || !commentReviewField.value.trim())) {
                e.preventDefault();
                alert('Komentar review diperlukan untuk status ini');
                commentReviewField?.focus();
                return;
            }
        });
    
        // Reset on modal close
        modal.on('hidden.bs.modal', function () {
            form.reset();
            commentReviewGroup.style.display = 'none';
        });
    });
</script>
@endpush