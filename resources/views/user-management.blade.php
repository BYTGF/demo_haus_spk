@extends('layouts.user_type.auth')

@section('content')

<div>
    {{-- <div class="alert alert-secondary mx-4" role="alert">
        <span class="text-white">
            <strong>Add, Edit, Delete features are not functional!</strong> This is a
            <strong>PRO</strong> feature! Click <strong>
            <a href="https://www.creative-tim.com/live/soft-ui-dashboard-pro-laravel" target="_blank" class="text-white">here</a></strong>
            to see the PRO product!
        </span>
    </div> --}}

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">All Users</h5>
                        </div>
                        <button type="button" class="btn btn-block btn-default bg-gradient-primary btn-sm mb-0" onclick="openCreateModal()" >+&nbsp; New User</button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Username
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Role
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Area
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Store
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Creation Date
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1; // Initialize the counter before the loop
                                @endphp
                                
                                @foreach ($userManagement as $user)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $i }}</p>
                                        </td>
                                        <td>
                                            <div>
                                                <p class="text-xs font-weight-bold mb-0">{{ $user->username }}</p>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->role->role_name }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->area->area_name }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->store->store_name }}</p>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="#" 
                                                class="mx-3 edit-user-btn" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-original-title="Edit user" 
                                                data-user='@json($user)'>
                                                <i class="fa-solid fa-user-pen" style="--fa-primary-color: #ffee00; --fa-secondary-color: #0c0066; --fa-secondary-opacity: 1;"></i>
                                            </a>
                                            <a href="#" 
                                                class="mx-3 delete-user-btn" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-original-title="Delete user" 
                                                data-id="{{ $user->id }}">
                                                <i class="fa-solid fa-user-slash" style="--fa-primary-color: #ff0000; --fa-secondary-color: #0c0066; --fa-secondary-opacity: 1;"></i>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                
                                    @php
                                        $i++; // Increment the counter after the loop
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-body p-0">
              <div class="card card-plain">
                <div class="card-header pb-0 text-left">
                  <h3 class="font-weight-bolder text-info text-gradient">User Detail</h3>
                </div>
                <div class="card-body">
                    <form id="user-form" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="form-method" value="POST">
                        <div class="form-group">
                            <label for="example-text-input" class="form-control-label" >Username</label>
                            <input class="form-control" type="text" name="username" id="example-text-input">
                        </div>
            
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" id="role_create" name="role_id">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                            </div>
                        <div class="form-group">
                            <label for="area_create">Area</label>
                            <select class="form-control" id="area_create" name="area_id" readonly>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="store_create">Store</label>
                            <select class="form-control" id="store_create" name="store_id" readonly>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }} || {{ $area->id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select> 
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-default bg-gradient-primary btn-sm mb-0">Submit</button>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.addEventListener('click', function(event) {
                // Handle tombol edit
                if (event.target.closest('.edit-user-btn')) {
                    const button = event.target.closest('.edit-user-btn');
                    const userData = JSON.parse(button.dataset.user);
                    openEditModal(userData);
                }

                // Handle tombol delete
                if (event.target.closest('.delete-user-btn')) {
                    event.preventDefault(); // supaya ga reload page
                    const button = event.target.closest('.delete-user-btn');
                    const userId = button.dataset.id;
                    
                    if (confirm('Yakin mau hapus user ini?')) {
                        deleteUser(userId);
                    }
                }
            });

            function deleteUser(id) {
                fetch(`/user-management/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        alert('User berhasil dihapus!');
                        location.reload(); // refresh page setelah hapus
                    } else {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Gagal hapus user.');
                        });
                    }
                })
                .catch(error => {
                    alert(error.message);
                });
            }


            //Check for Create or Update Request
            const modal = $('#modal-form');
            const form = document.getElementById('user-form');
            const methodField = document.getElementById('form-method');
            const modalTitle = document.querySelector('#modal-form .card-header h3');

             // Buka Modal Buat Tambah
            window.openCreateModal = function() {
                form.reset();
                form.action = '{{ route("user-management.store") }}'; // ganti sesuai route resource kamu
                methodField.value = 'POST';
                modalTitle.textContent = 'Tambah User';
                $('#store_create').html('<option value="">Pilih Store</option>'); // reset store option
                $('#area_create').prop('disabled', false);
                $('#store_create').prop('disabled', false);
                modal.modal('show');
            };

            // Buka Modal Buat Edit
            window.openEditModal = function(user) {
                form.reset();
                form.action = '/user-management/' + user.id;
                methodField.value = 'PUT';
                modalTitle.textContent = 'Edit User';

                // Set input value
                document.getElementById('example-text-input').value = user.username;
                document.getElementById('role_create').value = user.role_id;
                document.getElementById('area_create').value = user.area_id;
                document.getElementById('store_create').value = user.store_id;
                document.getElementById('area_create').readonly = true;
                document.getElementById('store_create').readonly = true;
                // Optional: kalau role settingan disable perlu disesuaikan juga
                // contoh kayak script validasi role tadi.

                modal.modal('show');
            };

            //User interaction in Form

            const roleSelect = document.getElementById('role_create');
            const areaSelect = document.getElementById('area_create');
            const storeSelect = document.getElementById('store_create');

            // Simpan semua option store
            const allStores = Array.from(storeSelect.options);

            //Update Area and Store Field on Role Change
            roleSelect.addEventListener('change', function () {
                //const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
                
                if (roleSelect.value == 7) {
                    // Role Area: Area bisa pilih, store default 1
                    areaSelect.readonly = false;
                    storeSelect.innerHTML = '<option value="1">Manager</option>';
                    storeSelect.value = 1
                    storeSelect.readonly = true;
                } else if (roleSelect.value == 8) {
                    // Role Store: Area bebas pilih, Store harus sesuai area
                    areaSelect.readonly = false;
                    storeSelect.readonly = false;

                    // Kalau user ganti area, update store
                    areaSelect.addEventListener('change', updateStoresForArea);
                    updateStoresForArea();
                } else {
                    areaSelect.innerHTML = '<option value="1">HO</option>';
                     storeSelect.innerHTML = '<option value="1">Manager</option>';
                    areaSelect.value = 1;
                    storeSelect.value = 1;
                    areaSelect.readonly = true;
                    storeSelect.readonly = true;
                }
            });

            //update Store Selection on Area Change
            function updateStoresForArea() {
                const selectedAreaId = areaSelect.value;

                // Clear store options
                storeSelect.innerHTML = '';

                // Filtering store berdasarkan area_id
                const filteredStores = allStores.filter(option => {
                    // Misal di value store disimpan kayak "1|AreaId"
                    const [store_id, area_id] = option.value.split('|');
                    return area_id === selectedAreaId;
                });

                // Kalau ketemu store, render ulang option
                filteredStores.forEach(option => {
                    storeSelect.appendChild(option);
                });

                if (filteredStores.length === 0) {
                    const emptyOption = document.createElement('option');
                    emptyOption.text = 'No Store Available';
                    emptyOption.disabled = true;
                    storeSelect.appendChild(emptyOption);
                }
            }

            // Validate Form
            form.addEventListener('submit', function (e) {
                const roleSelect = document.getElementById('role_create');
                const areaSelect = document.getElementById('area_create');
                const storeSelect = document.getElementById('store_create');

                const selectedRole = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

                if (selectedRole === 'store') {
                    if (!areaSelect.value) {
                        e.preventDefault();
                        alert('Area harus dipilih untuk role Store.');
                        return;
                    }
                    if (!storeSelect.value) {
                        e.preventDefault();
                        alert('Store harus dipilih untuk role Store.');
                        return;
                    }
                } else if (selectedRole === 'area') {
                    if (!areaSelect.value) {
                        e.preventDefault();
                        alert('Area harus dipilih untuk role Area.');
                        return;
                    }
                }

                // Role HO ga perlu dicek karena udah auto set id 1

                // reset data on modal open
                $('#modal-form').on('hidden.bs.modal', function () {
                    form.reset(); // Reset semua input
                    document.getElementById('area_create').disabled = false;
                    document.getElementById('store_create').disabled = false;
                    $('#store_create').html('<option value="">Pilih Store</option>'); // Reset store options
                });
            }); 
        });
    </script>

@endpush