@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        @include('components.flash-message')

        <div class="d-flex justify-content-between mb-3">
            <h4>Data Role</h4>
            <button type="button" class="btn btn-primary btn-labeled btn-labeled-start mb-2" data-bs-toggle="modal"
                data-bs-target="#createUserModal">
                <span class="btn-labeled-icon bg-black bg-opacity-20">
                    <i class="icon-database-add"></i>
                </span> Tambah User
            </button>
        </div>

        <!-- Tabel Users -->
        <div class="card">
            <div class="card-header">
                <h5>Data User</h5>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user['id'] }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['roles'][0] ?? '-' }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#detailUserModal{{ $user['id'] }}">
                                    <i class="ph-eye text-info"></i>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user['id'] }}">
                                    <i class="ph-pen text-primary ms-2"></i>
                                </a>
                                <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal{{ $user['id'] }}">
                                    <i class="ph-trash text-danger ms-2"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah User -->
    @include('frontend.user.create-modal')

    <!-- Modals per user -->
    @foreach ($users as $user)
        <!-- Modal Detail -->
        <div class="modal fade" id="detailUserModal{{ $user['id'] }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Detail User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nama:</strong> {{ $user['name'] }}</p>
                        <p><strong>Email:</strong> {{ $user['email'] }}</p>
                        <p><strong>Role:</strong> {{ $user['roles'][0] ?? '-' }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editUserModal{{ $user['id'] }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('users.update', $user['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <x-form.group label="Nama" name="name" value="{{ $user['name'] }}" required />
                            <x-form.group label="Password (kosongkan jika tidak diubah)" name="password" type="password" />
                            <x-form.group label="Konfirmasi Password" name="password_confirmation" type="password" />
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="roles" class="form-control" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role['name'] }}"
                                            {{ in_array($role['name'], old('roles', $user['roles'] ?? [])) ? 'selected' : '' }}>
                                            {{ $role['name'] }}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div class="modal fade" id="deleteUserModal{{ $user['id'] }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('users.destroy', $user['id']) }}" method="POST" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Hapus User</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus <strong>{{ $user['name'] }}</strong>?</p>
                        <p class="text-danger"><small>Data yang sudah dihapus tidak bisa dikembalikan.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
