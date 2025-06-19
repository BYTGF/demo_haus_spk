@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <h6>Finance Input</h6>
                            @if (auth()->user()->role->role_name === 'Finance')
                                <button type="button" class="btn btn-primary" onclick="openCreateFinanceInputModal()">
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
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Pendapatan</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gross Profit</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Net Profit</th>
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
                                                <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($input->total_pendapatan) }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ number_format($input->gross_profit_margin) }}%</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs font-weight-bold mb-0">{{ number_format($input->net_profit_margin) }}%</p>
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
                                                @if (auth()->user()->role->role_name === 'Finance')
                                                    @if($input->status === 'Butuh Revisi')
                                                        <button class="btn btn-xs btn-warning px-3 py-2" 
                                                                onclick="openEditFinanceInputModal({{ json_encode($input) }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                                
                                                @if (auth()->user()->role->role_name === 'Manager Business Development')
                                                    @if($input->status === 'Sedang Direview')
                                                        <button class="btn btn-xs btn-success px-3 py-2" 
                                                                onclick="document.getElementById('approve-form-{{ $input->id }}').submit()">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <form id="approve-form-{{ $input->id }}" 
                                                              action="{{ route('finance.approve', $input) }}" 
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
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Store</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Pendapatan</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Laba Kotor</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gross Profit</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Laba Bersih</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Net Profit</th>                  </tr>
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
                            <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->total_pendapatan) }}</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->laba_kotor) }}</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs font-weight-bold mb-0">{{ number_format($done->gross_profit_margin) }}%</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs font-weight-bold mb-0">Rp. {{ number_format($done->laba_bersih) }}</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs font-weight-bold mb-0">{{ number_format($done->net_profit_margin) }}%</p>
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

    <!-- Finance Input Modal -->
    <div class="modal fade" id="finance-input-modal" tabindex="-1" role="dialog" aria-labelledby="finance-input-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h3 class="font-weight-bolder text-info text-gradient" id="modal-title">Finance Input</h3>
                        </div>
                        <div class="card-body">
                            <form id="finance-input-form" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="form-method" value="POST">
                                <input type="hidden" name="input_id" id="input_id">
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

                                <!-- Section 1: Revenue -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Pendapatan</h6>
                                        <small class="text-muted">Pendapatan dari penjualan dan sumber lainnya</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="penjualan">
                                                        Pendapatan Penjualan
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Total pendapatan dari penjualan produk"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="penjualan" id="penjualan" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pendapatan_lain">
                                                        Pendapatan Lain
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Pendapatan dari aktivitas non-penjualan (jasa, komisi, dll)"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="pendapatan_lain" id="pendapatan_lain" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="total_pendapatan">Total Pendapatan</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="total_pendapatan" id="total_pendapatan" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Section 2: Costs -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Biaya</h6>
                                        <small class="text-muted">Harga pokok penjualan dan biaya operasional</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="total_hpp">
                                                        Harga Pokok Penjualan (HPP)
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Biaya langsung yang berhubungan dengan produksi"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control" name="total_hpp" id="total_hpp" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="biaya_operasional">
                                                        Biaya Operasional
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Total biaya operasional (otomatis dari input operasional)"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="biaya_operasional" id="biaya_operasional" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="laba_kotor">Laba Kotor</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="laba_kotor" id="laba_kotor" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Section 3: Profit -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Laba & Margin</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="laba_sebelum_pajak">Laba Sebelum Pajak</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="laba_sebelum_pajak" id="laba_sebelum_pajak" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="laba_bersih">Laba Bersih</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number" class="form-control bg-light" name="laba_bersih" id="laba_bersih" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Kalau mau tambahkan lagi nanti bisa aktifkan ini --}}
                                        {{-- <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="period">
                                                        Periode
                                                        <i class="fas fa-info-circle text-primary ms-1" title="Pilih bulan/tahun untuk laporan ini"></i>
                                                    </label>
                                                    <input type="month" class="form-control" name="period" id="period" required>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gross_profit_margin">Gross Profit Margin (%)</label>
                                                    <input type="number" class="form-control bg-light" name="gross_profit_margin" id="gross_profit_margin" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="net_profit_margin">Net Profit Margin (%)</label>
                                                    <input type="number" class="form-control bg-light" name="net_profit_margin" id="net_profit_margin" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Section 4: Comments -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Komentar</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="comment_input">
                                                Komentar Input
                                                <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip" 
                                                   title="Add any notes about this financial report"></i>
                                            </label>
                                            <textarea class="form-control" name="comment_input" id="comment_input" rows="2"></textarea>
                                        </div>
                                        
                                        <div class="form-group" id="comment-review-group">
                                            <label for="comment_review">
                                                Komentar Review
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

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Finance Input</h5>
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
    // Fungsi untuk mengambil data operasional
    const fetchOperationalData = async (period) => {
        try {
            // Pastikan period tidak kosong
            if (!period) {
                return {
                    success: false,
                    message: 'Periode belum dipilih'
                };
            }

            // Dapatkan store_id dari user yang login
            const storeId = {{ auth()->user()->store_id }};
            
            // Debug: log nilai yang akan dikirim
            console.log('Mengambil data operasional untuk:', {
                period: period,
                store_id: storeId
            });

            const response = await fetch(`/finance/get-operational-data?period=${period}&store_id=${storeId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Debug: log response
            console.log('Response dari server:', response);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Gagal mengambil data operasional');
            }

            const data = await response.json();
            // Debug: log data yang diterima
            console.log('Data operasional:', data);
            return data;

        } catch (error) {
            console.error('Error dalam fetchOperationalData:', error);
            return {
                success: false,
                message: error.message
            };
        }
    };

    // Event listener untuk perubahan periode
    document.getElementById('period').addEventListener('change', async function() {
        const periodInput = this;
        const period = periodInput.value;
        const operationalField = document.getElementById('biaya_operasional');
        
        // Debug: log nilai period yang dipilih
        console.log('Period berubah:', period);

        // Reset nilai jika period kosong
        if (!period) {
            operationalField.value = 0;
            calculateFinanceMetrics();
            return;
        }

        // Tampilkan loading state
        operationalField.disabled = true;
        
        try {
            // Debug: sebelum fetch
            console.log('Mengambil data untuk periode:', period);
            
            const operationalData = await fetchOperationalData(period);
            
            // Debug: setelah fetch
            console.log('Hasil pengambilan data:', operationalData);

            if (operationalData.success) {
                operationalField.value = operationalData.data.biaya_operasional;
                // Debug: log nilai yang di-set
                console.log('Meng-set biaya operasional ke:', operationalData.data.biaya_operasional);
            } else {
                operationalField.value = 0;
                if (operationalData.message) {
                    alert(operationalData.message);
                }
            }
        } catch (error) {
            console.error('Error dalam event listener:', error);
            operationalField.value = 0;
            alert('Gagal memuat data operasional: ' + error.message);
        } finally {
            operationalField.disabled = false;
            calculateFinanceMetrics();
            // Debug: log setelah perhitungan
            console.log('Perhitungan terupdate dengan biaya operasional:', operationalField.value);
        }
    });

    // Fungsi utama untuk menghitung metrik keuangan
    const calculateFinanceMetrics = () => {
        // Revenue calculations
        const penjualan = parseFloat(document.getElementById('penjualan').value) || 0;
        const pendapatan_lain = parseFloat(document.getElementById('pendapatan_lain').value) || 0;
        const total_pendapatan = penjualan + pendapatan_lain;
        document.getElementById('total_pendapatan').value = total_pendapatan.toFixed(2);
        
        // Cost calculations
        const total_hpp = parseFloat(document.getElementById('total_hpp').value) || 0;
        const biaya_operasional = parseFloat(document.getElementById('biaya_operasional').value) || 0;
        
        // Profit calculations
        const laba_kotor = total_pendapatan - total_hpp;
        document.getElementById('laba_kotor').value = laba_kotor.toFixed(2);
        
        const laba_sebelum_pajak = laba_kotor - biaya_operasional;
        document.getElementById('laba_sebelum_pajak').value = laba_sebelum_pajak.toFixed(2);
        
        // Assuming tax is already considered in laba_bersih
        const laba_bersih = laba_sebelum_pajak;
        document.getElementById('laba_bersih').value = laba_bersih.toFixed(2);
        
        // Margin calculations
        const gross_profit_margin = penjualan > 0 ? (laba_kotor / penjualan) * 100 : 0;
        document.getElementById('gross_profit_margin').value = gross_profit_margin.toFixed(2);
        
        const net_profit_margin = penjualan > 0 ? (laba_bersih / penjualan) * 100 : 0;
        document.getElementById('net_profit_margin').value = net_profit_margin.toFixed(2);
    };

    // Event listener untuk perubahan periode
    document.getElementById('period').addEventListener('change', async function() {
        const period = this.value;
        if (!period) return;
        
        const operationalData = await fetchOperationalData(period);
        
        if (operationalData && operationalData.success) {
            document.getElementById('biaya_operasional').value = operationalData.data.biaya_operasional;
            calculateFinanceMetrics();
        } else {
            document.getElementById('biaya_operasional').value = 0;
            calculateFinanceMetrics();
            if (operationalData && operationalData.message) {
                alert(operationalData.message);
            }
        }
    });

    document.getElementById('total_hpp').addEventListener('change', async function() {
        const gprof = this.value;
        
        const operationalData = await fetchOperationalData(period);
        
        if (operationalData && operationalData.success) {
            document.getElementById('biaya_operasional').value = operationalData.data.biaya_operasional;
            calculateFinanceMetrics();
        } else {
            document.getElementById('biaya_operasional').value = 0;
            calculateFinanceMetrics();
            if (operationalData && operationalData.message) {
                alert(operationalData.message);
            }
        }
    });

    // Add event listeners to all input fields
    ['penjualan', 'pendapatan_lain', 'total_hpp', 'biaya_operasional'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateFinanceMetrics);
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Get form elements
    const form = document.getElementById('finance-input-form');
    const modal = $('#finance-input-modal');

    // Modal functions
    window.openCreateFinanceInputModal = function() {
        form.reset();
        form.action = '{{ route("finance.store") }}';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('modal-title').textContent = 'Create Finance Input';
        
        // Hide comment review field for operational users
        if (document.getElementById('comment-review-group')) {
            document.getElementById('comment-review-group').style.display = 'none';
        }
        
        // Reset biaya operasional
        document.getElementById('biaya_operasional').value = 0;
        calculateFinanceMetrics();
            
        modal.modal('show');
    };

    window.openEditFinanceInputModal = function(input) {
        form.reset();
        form.action = `/finance/${input.id}`;
        document.getElementById('form-method').value = 'PUT';
        document.getElementById('modal-title').textContent = 'Edit Finance Input';
        
        // Fill form with input data
        document.getElementById('penjualan').value = input.penjualan || '';
        document.getElementById('pendapatan_lain').value = input.pendapatan_lain || '';
        document.getElementById('total_hpp').value = input.total_hpp || '';
        document.getElementById('biaya_operasional').value = input.biaya_operasional || '';
        document.getElementById('comment_input').value = input.comment_input || '';
        document.getElementById('comment_review').value = input.comment_review || '';
        
        // Format period as YYYY-MM if needed
        if (input.period) {
            document.getElementById('period').value = input.period.substring(0, 7);
        }
        
        // Trigger calculations
        calculateFinanceMetrics();
        
        modal.modal('show');
    };

    // Reject functionality
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-input-id');
            document.getElementById('rejectForm').action = `/finance/${inputId}/reject`;
        });
    });

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

    // Initial calculation
    calculateFinanceMetrics();
    
    @if(session('error'))
        alert("{{ session('error') }}");
    @endif
});
</script>
@endpush