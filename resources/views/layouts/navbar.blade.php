@php
    $webService = app(\App\Services\WebService::class);
    $response = $webService->getById(session('token'), 1);
    $web = (array) ($response->data ?? $response);
@endphp
<!-- Main navbar -->
<div class="navbar navbar-dark navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
    <div class="container-fluid">
        <div class="d-flex d-lg-none me-2">
            <button type="button" class="navbar-toggler sidebar-mobile-main-toggle rounded-pill">
                <i class="ph-list"></i>
            </button>
        </div>

        <div class="navbar-brand flex-1 flex-lg-0">
            <a href="{{ route('dashboard') }}" class="d-inline-flex align-items-center">
                <img src="{{ $web['web_logo'] }}" alt="" style="height: 24px;">
                <h6 class="ms-2 mb-0 text-light ultra-regular" style="line-height: 1;">{{ $web['web_nama'] }}</h6>
            </a>
        </div>


        {{-- <ul class="nav flex-row">
            <li class="nav-item d-lg-none">
                <a href="#navbar_search" class="navbar-nav-link navbar-nav-link-icon rounded-pill"
                    data-bs-toggle="collapse">
                    <i class="ph-magnifying-glass"></i>
                </a>
            </li>
        </ul> --}}
        <ul class="nav flex-row justify-content-end order-1 order-lg-2">
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <script>
                window.laravelToken = "{{ session('token') }}";
            </script>
            <div id="app"></div>

            <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                    <div class="status-indicator-container">
                        <img src="{{ !empty($user['avatar']) ? $user['avatar'] : Avatar::create($user['name'] ?? 'User')->toBase64() }}"
                            class="w-32px h-32px rounded-pill" alt="">
                        <span class="status-indicator bg-success"></span>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2"> {{ $user['name'] }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('profile.user_profile') }}" class="dropdown-item">
                        <i class="ph-user-circle me-2"></i>
                        My profile
                    </a>

                    <button class="dropdown-item" id="sweet_combine">
                        <i class="ph-sign-out me-2"></i>
                        Logout
                    </button>
                    <form action="{{ route('auth.logout') }}" method="POST" id="logoutForm" style="display: none;">
                        @csrf
                        <button type="submit" style="display: none;"></button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- /main navbar -->
