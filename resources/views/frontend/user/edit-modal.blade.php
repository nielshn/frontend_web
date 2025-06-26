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
                        <select name="roles[]" multiple class="form-control">
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
