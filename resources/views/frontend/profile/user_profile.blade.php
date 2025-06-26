{{-- filepath: resources/views/frontend/profile/user_profile.blade.php --}}
@extends('frontend.profile.layout.template')
@section('contentprofile')
    <!-- Card Profil -->
    <div class="col-md-12">
        {{-- ALERT SUKSES/ERROR DI LUAR MODAL --}}
        @if (session('success') && !session('from_edit_profile'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->has('name') || $errors->has('phone') || $errors->has('message'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error!</strong>
                @if ($errors->has('name')) {{ $errors->first('name') }}<br>@endif
                @if ($errors->has('phone')) {{ $errors->first('phone') }}<br>@endif
                @if ($errors->has('message')) {{ $errors->first('message') }}<br>@endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm bordere-0 rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-muted form-label mb-0">
                    <i class="bi bi-person-circle me-2 text-muted text-primary form-label"></i> Profil Pengguna
                </h5>
            </div>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label text-muted">Nama Lengkap</label>
                    <div class="form-control bg-light border-0 shadow-sm">{{ $user['name'] }}</div>
                </div>
                <div class="col-md-12">
                    <label class="form-label text-muted">Email</label>
                    <div class="form-control bg-light border-0 shadow-sm">{{ $user['email'] }}</div>
                    @if(isset($user['pending_email']) && $user['pending_email'])
                        <small class="text-warning mt-1 d-block">
                            <i class="bi bi-clock me-1"></i>
                            Menunggu verifikasi: {{ $user['pending_email'] }}
                        </small>
                    @endif
                    <small class="text-muted mt-1 d-block">Klik tombol di bawah untuk mengganti email.</small>
                </div>
                <div class="col-md-12">
                    <label class="form-label text-muted">Nomor Telepon</label>
                    <div class="form-control bg-light border-0 shadow-sm">{{ $user['phone'] ?? '-' }}</div>
                </div>
            </div>
            <div class="text-end mt-4">
                <button class="btn btn-primary rounded-pill px-4 me-2" data-bs-toggle="modal"
                    data-bs-target="#editInfoModal">
                    <i class="bi bi-pencil me-1"></i> Edit Profil
                </button>
                <button class="btn btn-outline-dark rounded-pill px-4" data-bs-toggle="modal"
                    data-bs-target="#changeEmailModal">
                    <i class="bi bi-envelope me-1"></i> Ubah Email
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div class="modal fade" id="editInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form method="POST" action="{{ route('profile.update-user') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header text-muted text-primary form-label border-0 form-label">
                        <h5 class="modal-title text-muted "><i class="bi bi-pencil-square me-1 text-primary"></i> Edit Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text"
                                    class="form-control bg-light border-0 shadow-sm @error('name') is-invalid @enderror"
                                    name="name"
                                    value="{{ $user['name'] }}">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text"
                                    class="form-control bg-light border-0 shadow-sm @error('phone') is-invalid @enderror"
                                    name="phone"
                                    value="{{ $user['phone'] }}">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Change Email -->
    <div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form method="POST" action="{{ route('profile.update-email') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header text-muted text-primary form-label border-0">
                        <h5 class="modal-title"><i class="bi bi-envelope-at me-1 text-warning"></i> Ubah Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (session('success') && session('from_edit_profile') !== true)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <strong>Berhasil!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->has('new_email'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Error!</strong> {{ $errors->first('new_email') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(isset($user['pending_email']) && $user['pending_email'])
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Email pending verifikasi:</strong> {{ $user['pending_email'] }}
                                <br><small>Cek inbox email Anda untuk link verifikasi.</small>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <label class="form-label">Email Baru</label>
                        <input type="email"
                            id="new_email_input"
                            class="form-control bg-light border-0 shadow-sm @error('new_email') is-invalid @enderror"
                            name="new_email"
                            placeholder="nama@email.com"
                            value="{{ old('new_email') }}"
                            required>
                        @error('new_email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted mt-1 d-block">Kami akan mengirimkan link verifikasi ke email baru.</small>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4">
                            <i class="bi bi-send me-1"></i> Kirim Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal auto-show hanya jika error pada form terkait --}}
    @if ($errors->has('new_email'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var changeEmailModal = new bootstrap.Modal(document.getElementById('changeEmailModal'));
                changeEmailModal.show();
            });
        </script>
    @endif

    @if ($errors->has('name') || $errors->has('phone') || $errors->has('message') || session('from_edit_profile'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var editInfoModal = new bootstrap.Modal(document.getElementById('editInfoModal'));
                editInfoModal.show();
            });
        </script>
    @endif
@endsection
