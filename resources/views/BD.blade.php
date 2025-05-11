@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex flex-row justify-content-between">
                <h6>Business Development Input</h6>
                @if (auth()->user()->role->role_name === 'Business Development Staff')
                  <button type="button" class="btn btn-primary" onclick="openCreateBDInputModal()">
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Direct Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Substitute Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Indirect Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach ($inputs as $input)
                          <tr data-input-id= {{ $input->id }}>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $input->period->format('M Y') }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->store->store_name }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">          
                                  <p class="text-xs font-weight-bold mb-0">{{ number_format($input->direct_competition) }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ number_format($input->substitute_competition) }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ number_format($input->indirect_competition) }}</p>
                              </td>
                  
                                <td class="align-middle text-center text-sm">
                                    @if ($input->status === 'Selesai')
                                        <span class="badge badge-sm bg-gradient-success">{{ $input->status }}</span>
                                    @elseif ($input->status === 'Sedang Direview')
                                        <span class="badge badge-sm bg-gradient-warning">{{ $input->status }}</span>
                                    @elseif ($input->status === 'Butuh Revisi')
                                        <span class="badge badge-sm bg-gradient-danger">{{ $input->status }}</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">{{ $input->status }}</span>
                                    @endif
                                </td>                            
                                <td class="text-center">
                                  <!-- Staff: Only show "Revise" if status is "Butuh Revisi" -->
                                    @if (auth()->user()->role->role_name === 'Business Development Staff')
                                        @if($input->status === 'Butuh Revisi')
                                            <button class="btn btn-xs btn-warning px-3 py-2" 
                                            onclick="openEditBDInputModal({
                                                id: {{ $input->id }},
                                                direct_competition: {{ $input->direct_competition }},
                                                substitute_competition: {{ $input->substitute_competition }},
                                                indirect_competition: {{ $input->indirect_competition }},
                                                rating: {{ $input->rating ?? 'null' }},
                                                comment_input: `{{ addslashes($input->comment_input ?? '') }}`,
                                                comment_review: `{{ addslashes($input->comment_review ?? '') }}`,
                                                status: `{{ $input->status }}`
                                            })">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                    @endif
                            
                                    <!-- Manager: Show Approve/Reject only if status is "Sedang Direview" -->
                                    @if (auth()->user()->role->role_name === 'Manager Business Development')
                                        @if($input->status === 'Sedang Direview')
                                            <button class="btn btn-xs btn-success px-3 py-2" 
                                                    onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <form id="approve-form-{{ $input->id }}" 
                                                  action="{{ route('bd.approve', $input) }}" 
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Direct Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Substitute Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Indirect Competition</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
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
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($done->direct_competition) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($done->substitute_competition) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ number_format($done->indirect_competition) }}</p>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs font-weight-bold mb-0">{{ $done->rating ?? 'N/A' }}</p>
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
      
    
    <!-- BD Input Modal -->
    <div class="modal fade" id="bd-input-modal" tabindex="-1" role="dialog" aria-labelledby="bd-input-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Business Development Input</h3>
                        </div>
                        <div class="card-body">
                            <form id="bd-input-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="id" id="input_id">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="status" value="Sedang Direview">
                            
                                <!-- Store Selection -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Store Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="store_id">Select Store</label>
                                            <select class="form-control" name="store_id" id="store_id" required>
                                                <option value="">-- Select Store --</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Competition Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Competition Analysis</h6>
                                        <small class="text-muted">Analysis of competition in the store's area</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="direct_competition">
                                                        Direct Competition
                                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title="Number of businesses offering similar products/services"></i>
                                                    </label>
                                                    <input type="number" class="form-control" name="direct_competition" id="direct_competition" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="substitute_competition">
                                                        Substitute Competition
                                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title="Number of businesses offering alternative solutions"></i>
                                                    </label>
                                                    <input type="number" class="form-control" name="substitute_competition" id="substitute_competition" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="indirect_competition">
                                                        Indirect Competition
                                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                        title="Number of businesses competing for the same customer budget"></i>
                                                    </label>
                                                    <input type="number" class="form-control" name="indirect_competition" id="indirect_competition" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <!-- Comments Section -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Comments</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="comment_input">
                                                Input Comments
                                                <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                title="Additional explanations about the data"></i>
                                            </label>
                                            <textarea class="form-control" name="comment_input" id="comment_input" rows="2"></textarea>
                                        </div>
                                        
                                        <div class="form-group" id="comment-review-group">
                                            <label for="comment_review">
                                                Review Comments
                                                <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                title="Comments from reviewer (if any)"></i>
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
    
    <!-- View Modal -->
    <div class="modal fade" id="viewBdModal" tabindex="-1" role="dialog" aria-labelledby="viewBdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBdModalLabel">Business Development Input Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Period:</strong> <span id="viewPeriod"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Store:</strong> <span id="viewStore"></span></p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p><strong>Direct Competition:</strong> <span id="viewDirect"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Substitute Competition:</strong> <span id="viewSubstitute"></span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Indirect Competition:</strong> <span id="viewIndirect"></span></p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <p><strong>Comments:</strong></p>
                        <p id="viewComments" class="text-muted"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        const modal = $('#bd-input-modal');
        const form = document.getElementById('bd-input-form');
        const methodField = document.getElementById('form-method');
        const modalTitle = document.getElementById('modal-title');
        const inputIdField = document.getElementById('input_id');
        const commentReviewGroup = document.getElementById('comment-review-group');
        const storeSelect = document.getElementById('store_id');

        document.querySelectorAll('tbody tr[data-input-id]').forEach(row => {
        row.style.cursor = 'pointer';
        
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button or link
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button, a')) {
                return;
            }
            
            const inputId = this.getAttribute('data-input-id');
            if (inputId) {
                console.log('Clicked row ID:', inputId);
                fetch(`/bd/${inputId}?t=${new Date().getTime()}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        // Populate modal with data
                        document.getElementById('viewPeriod').textContent = data.period;
                        document.getElementById('viewStore').textContent = data.store.store_name;
                        document.getElementById('viewDirect').textContent = data.direct_competition;
                        document.getElementById('viewSubstitute').textContent = data.substitute_competition;
                        document.getElementById('viewIndirect').textContent = data.indirect_competition;
                        document.getElementById('viewComments').textContent = data.comment_input || 'No comments';
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('viewBdModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading data');
                    });
            }
        });
    });
    
        // Open modal for creating new input
        window.openCreateBDInputModal = function () {
            form.reset();
            form.action = '{{ route("bd.store") }}';
            methodField.value = 'POST';
            modalTitle.textContent = 'Create Business Development Input';
            
            // Reset store selection
            if (storeSelect) {
                storeSelect.value = '';
            }
            
            // Hide comment review field for BD users
            if (commentReviewGroup) {
                commentReviewGroup.style.display = 'none';
            }
            
            modal.modal('show');
        };
    
        // Open modal for editing existing input
        window.openEditBDInputModal = function (input) {
            form.reset();
            form.action = `/bd/${input.id}`;
            methodField.value = 'PUT';
            modalTitle.textContent = 'Edit Business Development Input';
            
            // Safely set values only if elements exist
            const setValueIfExists = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            };

            setValueIfExists('input_id', input.id);
            setValueIfExists('store_id', input.store_id);
            setValueIfExists('direct_competition', input.direct_competition);
            setValueIfExists('substitute_competition', input.substitute_competition);
            setValueIfExists('indirect_competition', input.indirect_competition);
            setValueIfExists('comment_input', input.comment_input);
            setValueIfExists('comment_review', input.comment_review);

            if (commentReviewGroup) {
                commentReviewGroup.style.display = 'block';
            }
    
            modal.modal('show');
        };

    
        // For managers to reject
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentInputId = this.getAttribute('data-input-id');
                document.getElementById('rejectForm').action = `/bd/${currentInputId}/reject`;
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
            if (commentReviewGroup) {
                commentReviewGroup.style.display = 'none';
            }
        });
    });
    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif
    </script>
@endpush