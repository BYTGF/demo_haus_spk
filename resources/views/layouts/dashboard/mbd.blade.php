<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Store Data Completion</h5>
        <div class="d-flex">
            <button id="refreshBtn" class="btn btn-sm btn-outline-secondary me-2">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            {{-- <div class="input-group input-group-sm" style="width: 200px;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search stores...">
            </div> --}}
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="completionTable" class="table table-hover table-striped" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>Store Name</th>
                        <th width="40%">Completion</th>
                        <th>Status</th>
                        {{-- <th width="5%">Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DataTables CSS -->

<!-- DataTables JS -->

@push("scripts")
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#completionTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.completion-data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'name', name: 'name' },
            { 
                data: 'progress', 
                name: 'progress',
                orderable: false,
                searchable: true
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data, type, row) {
                    if (data === '4/4') {
                        return '<span class="badge bg-success">Complete</span>';
                    } else if (data === '0/4') {
                        return '<span class="badge bg-danger">Not Started</span>';
                    }
                    return '<span class="badge bg-warning text-dark">In Progress</span>';
                }
            },
            // {
            //     data: null,
            //     render: function(data, type, row) {
            //         return `
            //         <div class="dropdown">
            //             <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
            //                     type="button" data-bs-toggle="dropdown">
            //                 <i class="fas fa-ellipsis-v"></i>
            //             </button>
            //             <ul class="dropdown-menu">
            //                 <li><a class="dropdown-item" href="/stores/${row.DT_RowId.replace('row_','')}">
            //                     <i class="fas fa-eye me-2"></i>View Details
            //                 </a></li>
            //                 <li><a class="dropdown-item" href="#">
            //                     <i class="fas fa-download me-2"></i>Export
            //                 </a></li>
            //             </ul>
            //         </div>`;
            //     },
            //     orderable: false,
            //     searchable: false
            // }
        ],
        order: [[0, 'asc']], // Default sort by store name
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search stores...",
            lengthMenu: "Show _MENU_ stores per page",
            zeroRecords: "No matching stores found",
            info: "Showing _START_ to _END_ of _TOTAL_ stores",
            infoEmpty: "No stores available",
            infoFiltered: "(filtered from _MAX_ total stores)"
        }
    });

    // Custom search input
    $('#searchInput').keyup(function(){
        table.search($(this).val()).draw();
    });

    // Refresh button
    $('#refreshBtn').click(function(){
        table.ajax.reload();
    });

    // Style progress bars after load
    table.on('draw', function() {
        $('.progress-bar').each(function() {
            const percent = $(this).attr('aria-valuenow');
            if (percent == 100) {
                $(this).addClass('bg-success');
            } else if (percent >= 50) {
                $(this).addClass('bg-primary');
            } else {
                $(this).addClass('bg-warning');
            }
        });
    });
});
</script>
@endpush