@extends('layouts.main')

@section('content')
    @include('components.flash-message')

    <h2 class="mb-4 fw-bold">Permissions untuk Role: <span class="text-primary">{{ ucfirst($role) }}</span></h2>

    @php
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $parts = explode('_', $permission['name'], 2);
            $group = $parts[1] ?? $parts[0];
            $groupedPermissions[$group][] = $permission;
        }
    @endphp

    <div class="accordion" id="permissionsAccordion">
        @foreach ($groupedPermissions as $group => $groupPermissions)
            <div class="accordion-item border-0 mb-4 rounded shadow-sm">
                <h2 class="accordion-header" id="heading-{{ $group }}">
                    <button class="accordion-button bg-light fw-semibold" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $group }}" aria-expanded="false"
                        aria-controls="collapse-{{ $group }}">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>
                        {{ ucfirst($group) }} Permissions
                    </button>
                </h2>
                <div id="collapse-{{ $group }}" class="accordion-collapse collapse"
                    aria-labelledby="heading-{{ $group }}" data-bs-parent="#permissionsAccordion">
                    <div class="accordion-body bg-white rounded-bottom">
                        <div class="row g-3">
                            @foreach ($groupPermissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm bg-light-subtle">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <form action="{{ route('permissions.toggle') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="role" value="{{ $role }}">
                                                <input type="hidden" name="permission" value="{{ $permission['name'] }}">
                                                <input type="hidden" name="status" value="0">

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <label class="form-label mb-1 fw-bold text-dark"
                                                            for="permission_{{ $permission['id'] }}">
                                                            <i class="bi bi-key me-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $permission['name'])) }}
                                                        </label>
                                                        <div>
                                                            <span
                                                                class="badge rounded-pill {{ $permission['status'] ? 'bg-success' : 'bg-secondary' }} fw-normal">
                                                                {{ $permission['status'] ? 'Aktif' : 'Tidak Aktif' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch ms-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="permission_{{ $permission['id'] }}" name="status"
                                                            value="1" {{ $permission['status'] ? 'checked' : '' }}
                                                            onchange="this.form.submit()">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
