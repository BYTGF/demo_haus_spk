@extends('layouts.user_type.auth')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card mb-4 mx-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Criteria Weight</h5>
                <button class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah Bobot</button>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Kriteria</th>
                                <th>Bobot</th>
                                <th>Periode</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($weights as $i => $w)
                            <tr>
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ ucfirst($w->criteria) }}</td>
                                <td>{{ $w->weight }}</td>
                                <td>{{ $w->updated_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm me-2" onclick="openEditModal({{ $w }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteWeight({{ $w->id }})">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $weights->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="weightModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card card-plain">
          <div class="card-header pb-0 text-left">
            <h5 id="modalTitle">Tambah Bobot</h5>
          </div>
          <div class="card-body">
            <form id="weightForm" method="POST">
              @csrf
              <input type="hidden" name="_method" id="formMethod" value="POST">
              <div class="form-group">
                <label>Divisi</label>
                <select name="criteria" id="criteria" class="form-control" required>
                    <option value="finance">Finance</option>
                    <option value="operational">Operational</option>
                    <option value="bd">Business Dev</option>
                    <option value="store">Store</option>
                </select>
              </div>
              <div class="form-group mt-2">
                <label>Bobot</label>
                <input type="number" name="weight" id="weight" class="form-control" required step="0.01">
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
    const modal = new bootstrap.Modal(document.getElementById('weightModal'));
    const form = document.getElementById('weightForm');
    const formMethod = document.getElementById('formMethod');
    const modalTitle = document.getElementById('modalTitle');

    function openCreateModal() {
        form.reset();
        form.action = "{{ route('manage-cw.store') }}";
        formMethod.value = "POST";
        modalTitle.textContent = "Tambah Bobot";
        modal.show();
    }

    function openEditModal(weight) {
        form.action = `/manage-cw/${weight.id}`;
        formMethod.value = "PUT";
        document.getElementById('criteria').value = weight.criteria;
        document.getElementById('weight').value = weight.weight;
        modalTitle.textContent = "Edit Bobot";
        modal.show();
    }

    function deleteWeight(id) {
        if (confirm("Yakin mau hapus data ini?")) {
            fetch(`/manage-cw/${id}`, {
                method: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            }).then(res => location.reload());
        }
    }
</script>
@endpush
