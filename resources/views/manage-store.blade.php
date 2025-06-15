@extends('layouts.user_type.auth')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card mb-4 mx-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Store Management</h5>
                <button class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah Store</button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Store</th>
                                <th>Area</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $i => $store)
                            <tr>
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ $store->store_name }}</td>
                                <td>{{ $store->area->area_name }}</td>
                                <td>{{ $store->status }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm me-2" onclick="openEditModal({{ $store }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteStore({{ $store->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $stores->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h5 id="modalTitle">Tambah Store</h5>
          </div>
          <div class="card-body">
            <form id="storeForm" method="POST">
              @csrf
              <input type="hidden" name="_method" id="formMethod" value="POST">
              <div class="form-group">
                <label for="store_name">Nama Store</label>
                <input type="text" class="form-control" name="store_name" id="store_name" required>
              </div>
              <div class="form-group mt-2">
                <label for="area_id">Area</label>
                <select class="form-control" name="area_id" id="area_id" required>
                  @foreach ($areas as $area)
                      <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group mt-2">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-sm mt-3">Simpan</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js')
<script>
    const modal = new bootstrap.Modal(document.getElementById('storeModal'));
    const form = document.getElementById('storeForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');

    function openCreateModal() {
        form.reset();
        form.action = "{{ route('store.store') }}";
        formMethod.value = "POST";
        modalTitle.textContent = "Tambah Store";
        modal.show();
    }

    function openEditModal(store) {
        form.action = `/admin/store/${store.id}`;
        formMethod.value = "PUT";
        document.getElementById('store_name').value = store.store_name;
        document.getElementById('area_id').value = store.area_id;
        document.getElementById('status').value = store.status;
        modalTitle.textContent = "Edit Store";
        modal.show();
    }

    function deleteStore(id) {
        if (confirm("Yakin mau hapus store ini?")) {
            fetch(`/admin/store/${id}`, {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => location.reload());
        }
    }
</script>
@endpush
