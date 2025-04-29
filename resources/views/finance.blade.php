@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <div class="d-flex flex-row justify-content-between">
                <h6>Review Input</h6>
                @if (auth()->user()->role->role_name === 'Finance')
                  <button type="button" class="btn btn-primary" onclick="openCreateFinancialReviewModal()">
                      <i class="fas fa-plus me-2"></i> Add New Review
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
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Neraca Keuangan</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Arus Kas</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Profitabilitas</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Store</th>
                        <th class="text-secondary opacity-7">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach ($reviews as $review)
                          <tr>
                            <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $review->store->store_name }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">          
                                  <p class="text-xs font-weight-bold mb-0">{{ $review->neraca_keuangan }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $review->arus_kas }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $review->arus_kas }}</p>
                              </td>
                              <td class="align-middle text-center text-sm">
                                  <p class="text-xs font-weight-bold mb-0">{{ $review->profitabilitas }}</p>
                              </td>
                  
                                <td class="align-middle text-center text-sm">
                                    @if ($review->status === 'selesai')
                                        <span class="badge badge-sm bg-gradient-success">{{ $review->status }}</span>
                                    @elseif ($review->status === 'Sedang Direview')
                                        <span class="badge badge-sm bg-gradient-warning">{{ $review->status }}</span>
                                    @elseif ($review->status === 'Butuh Revisi')
                                        <span class="badge badge-sm bg-gradient-danger">{{ $review->status }}</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-secondary">{{ $review->status }}</span>
                                    @endif
                                </td>                            
                                <td class="text-center">
                                  <!-- Staff: Only show "Revise" if status is "Butuh Revisi" -->
                                    @if (auth()->user()->role->role_name === 'Finance')
                                        @if($review->status === 'Butuh Revisi')
                                            <a href="{{ route('finance.edit', $review) }}" 
                                              class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Revise
                                            </a>
                                        @endif
                                    @endif
                        
                                    <!-- Manager: Show Approve/Reject only if status is "Sedang Direview" -->
                                    @if (auth()->user()->role->role_name === 'Manager Business Development')
                                        @if($review->status === 'Sedang Direview')
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="document.getElementById('approve-form-{{ $review->id }}').submit()">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <form id="approve-form-{{ $review->id }}" 
                                                  action="{{ route('finance.approve', $review) }}" 
                                                  method="POST" class="d-none">
                                                @csrf
                                            </form>
                        
                                            <button class="btn btn-sm btn-danger reject-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#rejectModal"
                                                    data-review-id="{{ $review->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
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
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Neraca Keuangan</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Arus Kas</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Profitabilitas</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Store</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($dones as $done)
                        <tr>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->store->store_name }}</p>
                            </td>
                            <td>            
                                <p class="text-xs font-weight-bold mb-0">{{ $done->neraca_keuangan }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->arus_kas }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->arus_kas }}</p>
                            </td>
                            <td>
                                <p class="text-xs font-weight-bold mb-0">{{ $done->profitabilitas }}</p>
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
      <div class="modal fade" id="financial-review-modal" tabindex="-1" role="dialog" aria-labelledby="financial-review-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Financial Review</h3>
                        </div>
                        <div class="card-body">
                            <form id="financial-review-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="finance_id" id="finance_id">
                                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="neraca_keuangan">Neraca Keuangan</label>
                                            <select class="form-control" name="neraca_keuangan" id="neraca_keuangan" required>
                                                <option value="">Pilih Nilai</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="arus_kas">Arus Kas</label>
                                            <select class="form-control" name="arus_kas" id="arus_kas" required>
                                                <option value="">Pilih Nilai</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="profitabilitas">Profitabilitas</label>
                                            <select class="form-control" name="profitabilitas" id="profitabilitas" required>
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
    
                                @if(auth()->user()->role === 'manager')
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
                      <h5 class="modal-title">Reject Review</h5>
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
      

      {{-- <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Projects table</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center justify-content-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Budget</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Completion</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm rounded-circle me-2" alt="spotify">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Spotify</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$2,500</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">working</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">60%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-invision.svg" class="avatar avatar-sm rounded-circle me-2" alt="invision">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Invision</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$5,000</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">done</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">100%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-jira.svg" class="avatar avatar-sm rounded-circle me-2" alt="jira">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Jira</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$3,400</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">canceled</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">30%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-danger" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="30" style="width: 30%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-slack.svg" class="avatar avatar-sm rounded-circle me-2" alt="slack">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Slack</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$1,000</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">canceled</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">0%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" style="width: 0%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-webdev.svg" class="avatar avatar-sm rounded-circle me-2" alt="webdev">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Webdev</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$14,000</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">working</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">80%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-info" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="80" style="width: 80%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2">
                          <div>
                            <img src="../assets/img/small-logos/logo-xd.svg" class="avatar avatar-sm rounded-circle me-2" alt="xd">
                          </div>
                          <div class="my-auto">
                            <h6 class="mb-0 text-sm">Adobe XD</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-sm font-weight-bold mb-0">$2,300</p>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold">done</span>
                      </td>
                      <td class="align-middle text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="me-2 text-xs font-weight-bold">100%</span>
                          <div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle">
                        <button class="btn btn-link text-secondary mb-0" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-ellipsis-v text-xs"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div> --}}
    </div>
  </main>
  
  @endsection

  @push('js')
  <<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#financial-review-modal');
        const form = document.getElementById('financial-review-form');
        const rejectForm = document.getElementById('rejectForm');
        const methodField = document.getElementById('form-method');
        const modalTitle = document.getElementById('modal-title');
        const reviewIdField = document.getElementById('financial_review_id');
        const statusField = document.getElementById('status');
        const commentReviewGroup = document.getElementById('comment-review-group');

        const rejectModal = document.getElementById('rejectModal');
        let currentReviewId = null;
    
        // Open modal for creating new review
        window.openCreateFinancialReviewModal = function () {
            form.reset();
            form.action = '{{ route("finance.store") }}';
            methodField.value = 'POST';
            modalTitle.textContent = 'Create Financial Review';
            
            // Hide comment review field by default
            commentReviewGroup.style.display = 'none';
            
            modal.modal('show');
        };
    
        // Open modal for editing existing review
        window.openEditFinancialReviewModal = function (review) {
            form.reset();
            form.action = `/finance/${review.id}`;
            methodField.value = 'PUT';
            modalTitle.textContent = 'Edit Financial Review';
            reviewIdField.value = review.id;
    
            // Fill form with review data
            document.getElementById('neraca_keuangan').value = review.neraca_keuangan;
            document.getElementById('arus_kas').value = review.arus_kas;
            document.getElementById('profitabilitas').value = review.profitabilitas;
            document.getElementById('comment_input').value = review.comment_input;
            document.getElementById('comment_review').value = review.comment_review;
            document.getElementById('store_id').value = review.store_id;
    
            // Handle status field (for managers)
            if (statusField) {
                statusField.value = review.status;
                handleStatusChange(review.status);
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
    
        // For managers to approve
        window.approveReview = function(reviewId) {
            if (confirm('Are you sure you want to approve this review?')) {
                fetch(`/finance/${reviewId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        };
    
        // For managers to reject
        document.querySelectorAll('.reject-btn').forEach(button => {
          button.addEventListener('click', function() {
              currentReviewId = this.getAttribute('data-review-id');
              document.getElementById('rejectForm').action = `/finance/${currentReviewId}/reject`;
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
              alert('Error rejecting review');
          });
      });
    
        // Form validation
        form.addEventListener('submit', function(e) {
    // Get all required elements
    const statusField = document.getElementById('status');
    const commentReviewField = document.getElementById('comment_review');
    
    // Determine if validation is needed
    const isManager = @json(auth()->user()->role === 'Manager Business Development');
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

