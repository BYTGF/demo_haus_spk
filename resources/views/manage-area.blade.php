@extends('layouts.user_type.auth')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card mb-4 mx-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Area Management</h5>
                <button class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah Area</button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Area</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areas as $i => $area)
                            <tr>
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ $area->area_name }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm me-2" onclick="openEditModal({{ $area }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteArea({{ $area->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $areas->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h5 id="modalTitle">Tambah Area</h5>
          </div>
          <div class="card-body">
            <form id="areaForm" method="POST">
              @csrf
              <input type="hidden" name="_method" id="formMethod" value="POST">
              <div class="form-group">
                <label for="area_name">Nama Area</label>
                <input type="text" class="form-control" name="area_name" id="area_name" placeholder="Masukkan nama area" required>
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
    const modal = new bootstrap.Modal(document.getElementById('areaModal'));
    const form = document.getElementById('areaForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');
    const areaInput = document.getElementById('area_name');

    function openCreateModal() {
        form.reset();
        form.action = "{{ route('manage-area.store') }}";
        formMethod.value = "POST";
        modalTitle.textContent = "Tambah Area";
        modal.show();
    }

    function openEditModal(area) {
        form.action = `/admin/area/${area.id}`;
        formMethod.value = "PUT";
        areaInput.value = area.area_name;
        modalTitle.textContent = "Edit Area";
        modal.show();
    }

    function deleteArea(id) {
        if (confirm("Yakin mau hapus area ini?")) {
            fetch(`/admin/area/${id}`, {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (res.ok) {
                    location.reload();
                } else {
                    return res.json().then(data => {
                        alert(data.message || 'Gagal hapus area');
                    });
                }
            });
        }
    }
</script>
@endpush
