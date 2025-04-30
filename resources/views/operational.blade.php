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
                @if (auth()->user()->role->role_name === 'Operational')
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
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gaji & Upah</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sewa</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Utilitas</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perlengkapan</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lain-lain</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-secondary opacity-7"></th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach ($inputs as $input)
                          <tr>
                            <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->store->store_name }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">          
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->gaji_upah }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->sewa }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->utilitas }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->perlengkapan }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->lain_lain }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->total }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $input->rating }}</p>
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
                                    @if (auth()->user()->role->role_name === 'Operational')
                                        @if($input->status === 'Butuh Revisi')
                                            <button class="btn btn-sm btn-warning" 
                                            onclick="openEditOperationalInputModal({
                                                id: {{ $input->id }},
                                                gaji_upah: {{ $input->gaji_upah }},
                                                sewa: {{ $input->sewa }},
                                                utilitas: {{ $input->utilitas }},
                                                perlengkapan: {{ $input->perlengkapan }},
                                                lain_lain: {{ $input->lain_lain }},
                                                rating: {{ $input->rating }},
                                                comment_input: `{{ addslashes($input->comment_input) }}`,
                                                comment_review: `{{ addslashes($input->comment_review) }}`,
                                                store_id: {{ $input->store_id }},
                                                status: `{{ $input->status }}`
                                            })">
                                                <i class="fas fa-edit"></i> Revise
                                            </button>
                                        @endif
                                    @endif
                            
                                    <!-- Manager: Show Approve/Reject only if status is "Sedang Direview" -->
                                    @if (auth()->user()->role->role_name === 'Manager Business Development')
                                        @if($input->status === 'Sedang Direview')
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <form id="approve-form-{{ $input->id }}" 
                                                  action="{{ route('operational.approve', $input) }}" 
                                                  method="POST" class="d-none">
                                                @csrf
                                            </form>
                        
                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal"
                                                    data-input-id="{{ $input->id }}">
                                                <i class="fas fa-times"></i> Reject
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
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Toko</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gaji & Upah</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sewa</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Utilitas</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perlengkapan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lain-lain</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rating</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($dones as $done)
                        <tr>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->store->store_name }}</p>
                            </td>
                            <td>            
                                <p class="text-xs font-weight-bold mb-0">{{ $done->gaji_upah }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->sewa }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->utilitas }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->perlengkapan }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->lain_lain }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->total }}</p>
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
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Modal --}}
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
    
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gaji_upah">Gaji & Upah</label>
                                            <input type="number" class="form-control" name="gaji_upah" id="gaji_upah" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sewa">Sewa</label>
                                            <input type="number" class="form-control" name="sewa" id="sewa" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="utilitas">Utilitas</label>
                                            <input type="number" class="form-control" name="utilitas" id="utilitas" required>
                                        </div>
                                    </div>
                                </div>
                                
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="perlengkapan">Perlengkapan</label>
                                                <input type="number" class="form-control" name="perlengkapan" id="perlengkapan" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lain_lain">Lain-lain</label>
                                                <input type="number" class="form-control" name="lain_lain" id="lain_lain" required>
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
                                </div>
    
                                <!-- Comment input field -->
                                <div class="form-group">
                                    <label for="comment_input">Komentar Input</label>
                                    <textarea class="form-control" name="comment_input" id="comment_input" rows="3" placeholder="Masukkan komentar" required></textarea>
                                </div>
    
                                <!-- Only show comment review field for managers -->
                                {{-- @if(auth()->user()->role->role_name === 'Manager Business Development') --}}
                                    <div class="form-group" id="comment-review-group">
                                        <label for="comment_review">Komentar Review</label>
                                        <textarea class="form-control" name="comment_review" id="comment_review" rows="3" placeholder="Masukkan komentar review"></textarea>
                                    </div>
    
                                    {{-- <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="Sedang Direview">Sedang Direview</option>
                                            <option value="Butuh Revisi">Butuh Revisi</option>
                                            <option value="Selesai">Selesai</option>
                                        </select>
                                    </div> --}}
                                {{-- @if --}}
                                    <input type="hidden" name="status" value="Sedang Direview">
                                {{-- @endif --}}
    
                                <div class="form-group">
                                    <label for="store_id">Store</label>
                                    <select class="form-control" name="store_id" id="store_id" required>
                                        <option value="">Pilih Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->store_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
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
            document.getElementById('store_id').value = '';
            
            // Hide comment review field for operational users
            if (document.getElementById('comment-review-group')) {
                document.getElementById('comment-review-group').style.display = 'none';
            }
            
            modal.modal('show');
        };
    
        // Open modal for editing existing input
        window.openEditOperationalInputModal = function (input) {
            form.reset();
            form.action = `/operational/${input.id}`;
            methodField.value = 'PUT';
            modalTitle.textContent = 'Edit Operational Input';
            
            // Safely set values only if elements exist
            const setValueIfExists = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            };

            setValueIfExists('input_id', input.id);
            setValueIfExists('gaji_upah', input.gaji_upah);
            setValueIfExists('sewa', input.sewa);
            setValueIfExists('utilitas', input.utilitas);
            setValueIfExists('perlengkapan', input.perlengkapan);
            setValueIfExists('lain_lain', input.lain_lain);
            setValueIfExists('rating', input.rating);
            setValueIfExists('comment_input', input.comment_input);
            setValueIfExists('comment_review', input.comment_review);
            setValueIfExists('store_id', input.store_id);

            // Handle status field if it exists
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
        const amountFields = ['gaji_upah', 'sewa', 'utilitas', 'perlengkapan', 'lain_lain'];
        amountFields.forEach(field => {
            document.getElementById(field)?.addEventListener('change', calculateTotal);
        });
        
        function calculateTotal() {
            let total = 0;
            amountFields.forEach(field => {
                const value = parseFloat(document.getElementById(field).value) || 0;
                total += value;
            });
            // If you want to display the total somewhere, you can add it here
        }
    
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