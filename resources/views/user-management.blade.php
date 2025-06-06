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
                        <button type="button" class="btn btn-block btn-default bg-gradient-primary btn-sm mb-0" onclick="window.openCreateModal()">+&nbsp; New User</button>
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
                                            <button 
                                                class="mx-3 edit-user-btn" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-original-title="Edit user" 
                                                data-user='@json($user)' 
                                                onclick="window.editUser(this)">
                                                <i class="fa-solid fa-user-pen" style="--fa-primary-color: #ffee00; --fa-secondary-color: #0c0066; --fa-secondary-opacity: 1;"></i>
                                            </button>

                                            <button 
                                                class="mx-3 delete-user-btn" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-original-title="Delete user" 
                                                data-id="{{ $user->id }}" 
                                                onclick="deleteUser(this)">
                                                <i class="fa-solid fa-user-slash" style="--fa-primary-color: #ff0000; --fa-secondary-color: #0c0066; --fa-secondary-opacity: 1;"></i>
                                            </button>

                                            
                                        </td>
                                    </tr>
                                
                                    @php
                                        $i++; // Increment the counter after the loop
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $userManagement->links('pagination::bootstrap-5') }}
                        </div>
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
                            <label for="username">Username</label>
                            <input class="form-control" type="text" name="username" id="username" placeholder="Masukkan Username">
                        </div>
                    
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select class="form-control" id="role_id" name="role_id">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <label for="area_id_display">Area</label>
                            <select class="form-control" id="area_id_display" disabled>
                                <option value="">Pilih Area</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="area_id" id="area_id">
                        </div>
                    
                        <div class="form-group">
                            <label for="store_id_display">Store</label>
                            <select class="form-control" id="store_id_display" disabled>
                                <option value="">Pilih Store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}|{{ $store->area_id }}">{{ $store->store_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="store_id" id="store_id">
                        </div>
                    
                        <button type="submit" class="btn btn-primary btn-block btn-sm">Submit</button>
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

    let allStoreOptions = [];
    document.addEventListener('DOMContentLoaded', function () {
        const modal = $('#modal-form');
        const form = document.getElementById('user-form');
        const methodField = document.getElementById('form-method');
        const modalTitle = document.querySelector('#modal-form .card-header h3');

        const usernameInput = document.getElementById('username');
        const roleSelect = document.getElementById('role_id');
        const areaDisplay = document.getElementById('area_id_display');
        const areaInput = document.getElementById('area_id');
        const storeDisplay = document.getElementById('store_id_display');
        const storeInput = document.getElementById('store_id');
        const originalAreaOptions = areaDisplay.innerHTML;
        const originalStoreOptions = storeDisplay.innerHTML;


        allStoreOptions = Array.from(document.getElementById('store_id_display').options);

        // BUTTON: Tambah User
        window.openCreateModal = function () {
            form.reset();
            form.action = '{{ route("user-management.store") }}';
            methodField.value = 'POST';
            modalTitle.textContent = 'Tambah User';

            areaDisplay.disabled = true;
            storeDisplay.disabled = true;

            modal.modal('show');
        };

        // BUTTON: Edit User
        window.openEditModal = function (user) {
            form.reset();
            form.action = '/user-management/' + user.id;
            methodField.value = 'PUT';
            modalTitle.textContent = 'Edit User';

            usernameInput.value = user.username;
            roleSelect.value = user.role_id;
            areaInput.value = user.area_id;
            storeInput.value = user.store_id;

            areaDisplay.value = user.area_id;
            storeDisplay.value = user.store_id + '|' + user.area_id;

            applyRoleBehavior(user.role_id);
            modal.modal('show');
        };

        window.editUser = function(button) {
            const userData = JSON.parse(button.dataset.user);
            window.openEditModal(userData);
        };

        // HANDLE: Delete User
        document.addEventListener('click', function(event) {
            if (event.target.closest('.delete-user-btn')) {
                event.preventDefault();
                const userId = event.target.closest('.delete-user-btn').dataset.id;
                if (confirm('Yakin mau hapus user ini?')) {
                    deleteUser(userId);
                }
            }

            if (event.target.closest('.edit-user-btn')) {
                const userData = JSON.parse(event.target.closest('.edit-user-btn').dataset.user);
                openEditModal(userData);
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
            .then(res => {
                if (res.ok) {
                    alert('User berhasil dihapus!');
                    location.reload();
                } else {
                    return res.json().then(data => {
                        throw new Error(data.message || 'Gagal menghapus user.');
                    });
                }
            })
            .catch(err => alert(err.message));
        }

        // HANDLE: Role Change
        roleSelect.addEventListener('change', function () {
            applyRoleBehavior(this.value);
        });

        function applyRoleBehavior(roleId) {
        if (roleId == 7) {
            // Area Manager
            areaDisplay.innerHTML = originalAreaOptions; // restore opsinya
            areaDisplay.removeAttribute('disabled');
            areaInput.value = 1;

            storeDisplay.innerHTML = '<option>Manager</option>';
            storeDisplay.disabled = true;
        } else if (roleId == 8 || roleId == 5 || roleId == 6) {
            // Store Manager
            areaDisplay.innerHTML = originalAreaOptions;
            areaDisplay.removeAttribute('disabled');
            storeDisplay.disabled = false;
            areaDisplay.innerHTML = originalAreaOptions;
            areaDisplay.value = areaDisplay.options[0]?.value ?? ''; // set ke value pertama yg tersedia
            areaInput.value = areaDisplay.value;
            updateStoreOptions(areaDisplay.value);

        } else {
            // HO
            areaDisplay.innerHTML = '<option selected>Head Office</option>';
            areaDisplay.disabled = true;
            areaInput.value = 1;

            storeDisplay.innerHTML = '<option selected>Manager</option>';
            storeDisplay.disabled = true;
            storeInput.value = 1;
        }
    }


        areaDisplay.addEventListener('change', function () {
            areaInput.value = this.value;
            updateStoreOptions(this.value);
        });

        storeDisplay.addEventListener('change', function () {
            const storeId = this.value.split('|')[0];
            storeInput.value = storeId;
        });

        function updateStoreOptions(selectedAreaId) {
            storeDisplay.innerHTML = '';

            const filtered = allStoreOptions.filter(opt => {
                const [storeId, areaId] = opt.value.split('|');
                return areaId === selectedAreaId;
            });

            if (filtered.length === 0) {
                const empty = document.createElement('option');
                empty.text = 'No Store Available';
                empty.disabled = true;
                storeDisplay.appendChild(empty);
            } else {
                filtered.forEach(opt => {
                    storeDisplay.appendChild(opt.cloneNode(true));
                });
            }
        }

        // HANDLE: Form Submission Validation
        form.addEventListener('submit', function (e) {
            const roleName = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();

            if (roleName.includes('store')) {
                if (!areaInput.value) {
                    e.preventDefault();
                    alert('Pilih Area terlebih dahulu.');
                    return;
                }
                if (!storeInput.value) {
                    e.preventDefault();
                    alert('Pilih Store terlebih dahulu.');
                    return;
                }
            } else if (roleName.includes('area')) {
                if (!areaInput.value) {
                    e.preventDefault();
                    alert('Pilih Area terlebih dahulu.');
                    return;
                }
            }
        });

        // RESET on modal close
        $('#modal-form').on('hidden.bs.modal', function () {
            form.reset();
            areaDisplay.disabled = true;
            storeDisplay.disabled = true;
        });
    });
    </script>
@endpush
