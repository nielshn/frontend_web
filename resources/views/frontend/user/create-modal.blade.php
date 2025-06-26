<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <x-form.group label="Nama" name="name" required />
                <x-form.group label="Password" name="password" type="password" required />
                <x-form.group label="Konfirmasi Password" name="password_confirmation" type="password" required />
                <div class="mb-3">
                    <label>Role</label>
                    <select name="roles[]" class="form-control" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role['name'] }}"
                                {{ in_array($role['name'], $user['roles']) ? 'selected' : '' }}>
                                {{ $role['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
