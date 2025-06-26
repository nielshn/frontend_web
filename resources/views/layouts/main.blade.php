<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset($web['web_logo']) }}" type="image/png">
    <title>{{ $web['web_nama'] }}</title>
    @stack('css')
    <!-- Responsive Scan Stylesheets-->
    <!-- Global stylesheets -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
    <link href="{{ asset('template/assets/fonts/inter/inter.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/icons/phosphor/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/ltr/all.min.css') }}" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- /global stylesheets -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/tippy.js@6/themes/light.css" rel="stylesheet" />


    <style>
        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            position: relative;
            width: 100%;
            height: 100%;
            background: #fff;
            border-radius: 4px;
        }

        .loader:before {
            content: '';
            position: absolute;
            width: 54%;
            height: 19%;
            left: 50%;
            top: 0;
            background-image:
                radial-gradient(ellipse at center, #0000 24%, #de3500 25%, #de3500 64%, #0000 65%),
                linear-gradient(to bottom, #0000 34%, #de3500 35%);
            background-size: 12%;
            background-repeat: no-repeat;
            background-position: center top;
            transform: translate(-50%, -65%);
            box-shadow: 0 -3px rgba(0, 0, 0, 0.25) inset;
        }

        .loader:after {
            content: '';
            position: absolute;
            left: 50%;
            top: 20%;
            transform: translateX(-50%);
            width: 66%;
            height: 60%;
            background: linear-gradient(to bottom, #f79577 30%, #0000 31%);
            background-size: 100% 15%;
            animation: writeDown 2s ease-out infinite;
        }

        @keyframes writeDown {
            0% {
                height: 0%;
                opacity: 0;
            }

            20% {
                height: 0%;
                opacity: 1;
            }

            80% {
                height: 65%;
                opacity: 1;
            }

            100% {
                height: 65%;
                opacity: 0;
            }
        }
    </style>

    <!-- Core JS files -->
    <script src="{{ asset('template/assets/demo/demo_configurator.js') }}"></script>
    <script src="{{ asset('template/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('template/assets/js/vendor/notifications/noty.min.js') }}"></script>
    {{-- <script src="{{asset('template/assets/demo/pages/extra_sweetalert.js')}}"></script> --}}
    <script src="{{ asset('template/assets/js/vendor/notifications/sweet_alert.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script src="{{ asset('template/assets/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/vendor/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/vendor/tables/datatables/extensions/buttons.min.js') }}"></script>
    <script src="{{ asset('template/assets/demo/pages/datatables_extension_buttons_html5.js') }}"></script>

    <link href="{{ asset('template/assets/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('template/assets/icons/material/styles.min.css') }}" rel="stylesheet" type="text/css">
    {{-- <script src="{{ asset('template/assets/js/vendor/visualization/d3/d3.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/assets/js/vendor/visualization/d3/d3_tooltip.js') }}"></script> --}}
    <script src="{{ asset('template/assets/js/vendor/ui/fullcalendar/main.min.js') }}"></script>
    <script src="{{ asset('template/assets/demo/pages/datatables_extension_key_table.js') }}"></script>
    <script src="{{ asset('template/assets/js/vendor/tables/datatables/extensions/key_table.min.js') }}"></script>
    <script src="{{ asset('template/assets/demo/pages/fullcalendar_styling.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.css" rel="stylesheet" />

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('template/assets/demo/pages/dashboard.js') }}"></script>
    <script src="{{ asset('template/assets/demo/charts/pages/dashboard/streamgraph.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/dashboard/sparklines.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/dashboard/lines.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/dashboard/areas.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/dashboard/donuts.js') }}"></script> --}}
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/deashboard/bars.js') }}"></script> --}}
    <script src="{{ asset('template/assets/demo/charts/pages/dashboard/progress.js') }}"></script>
    <script src="{{ asset('template/assets/demo/charts/pages/dashboard/heatmaps.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/demo/charts/pages/dashboard/pies.js') }}"></script> --}}
    <script src="{{ asset('template/assets/demo/data/dashboard/bullets.json') }}"></script>
    <!-- /theme JS files -->
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.3/dist/parsley.min.js"></script>

    {{-- font awesome  --}}
    <link href="{{ asset('template/assets/icons/fontawesome/styles.min.css') }}" rel="stylesheet" type="text/css">

    {{-- <script src="https://js.pusher.com/7.2/pusher.min.js"></script> --}}
    <script src=""></script>

    {{-- <script>
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'reverb',
            wsHost: '127.0.0.1',
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        window.Echo.channel('stock-channel')
            .listen('.stock.minimum', (e) => {
                alert(e.title + ": " + e.message);
            });
    </script> --}}
</head>

<body>
    <div id="page-loader"
        style="position: fixed; z-index: 9999; background-color: #010a26; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
        <div class="loader-container" style="width: 80px; height: 104px;">
            <span class="loader"></span>
        </div>
    </div>
    <!-- Main navbar -->
    @include('layouts.navbar')

    <!-- /main navbar -->


    <!-- Page content -->
    <div class="page-content">

        <!-- Main sidebar -->
        <div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

            <!-- Sidebar content -->
            @include('layouts.sidebar')

            <!-- /sidebar content -->

        </div>
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Inner content -->
            <div class="content-inner">

                <!-- Page header -->
                @include('components.page_header')
                <!-- /page header -->


                <!-- Content area -->
                <div class="content">
                    <!-- Dashboard content -->
                    @yield('content')
                    <!-- /dashboard content -->
                </div>
                <!-- /content area -->

                {{-- <!-- Tombol Scan -->
                <button id="scan-btn" class="btn btn-primary d-flex  btn-position btn-circle">
                    <i class="ph-scan ph-2x rounded"></i>
                </button>

                <!-- Container Scanner -->
                <div id="scanner-container" style="display: none;">
                    <button id="close-btn">✖</button>
                    <video id="preview"></video>
                    <input type="text" id="qrcode-result" class="form-control mt-2" readonly>
                </div> --}}

                <!-- Footer -->
                @include('layouts.footer')
                <!-- /footer -->

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

    @include('components.demo_config')

    <!-- Notifications -->
    @include('components.notifications')

    <!-- /notifications -->


    <!-- Demo config -->

    <!-- /demo config -->

</body>
{{--
<script>
    const swalCombineElement = document.querySelector('#sweet_combine');
    if (swalCombineElement) {
        swalCombineElement.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'No, cancel!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Kalau klik Yes, kirim form logout
                    document.getElementById('logoutForm').submit();
                }
                // Gak usah pakai else, biar kalau cancel yaudah selesai
            });
        });
    }
</script> --}}

<script>
    const swalCombineElement = document.querySelector('#sweet_combine');
    if (swalCombineElement) {
        swalCombineElement.addEventListener('click', function() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah anda yakin ingin keluar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ph-sign-out"></i> Ya , Keluar',
                cancelButtonText: '<i class="ph-x-circle"></i> Tidak Tetap Disini',
                buttonsStyling: false,
                reverseButtons: true,
                background: '#fdfdfd',
                color: '#333',
                iconColor: '#0d6efd',
                customClass: {
                    popup: 'rounded-4 shadow-lg border border-light-subtle',
                    title: 'fw-semibold fs-5',
                    confirmButton: 'btn btn-primary px-4 py-2 me-2',
                    cancelButton: 'btn btn-outline-secondary px-4 py-2'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
                // Cancel? Gak ngapa-ngapain. Biar smooth.
            });
        });
    }
</script>
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch("http://localhost:8090/api/auth/refresh-permission", {
                headers: {
                    'Authorization': 'Bearer {{ auth()->user()?->token() ?? '' }}',
                    'Accept': 'application/json'
                }
            })

            .then(response => response.json())
            .then(data => {
                console.log('Permission refreshed:', data);
            })
            .catch(error => {
                console.error('Failed to refresh permission:', error);
            });
    });
</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            // Hide loader when all resources are loaded
            window.addEventListener('load', function() {
                loader.style.transition = 'opacity 0.3s ease'; /* Reduced to 0.3s */
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300); // Matches transition duration (300ms)
            });

            // Fallback: Hide loader after 1 second (700ms + 300ms)
            setTimeout(() => {
                if (loader.style.display !== 'none') {
                    loader.style.transition = 'opacity 0.3s ease'; /* Reduced to 0.3s */
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 300);
                }
            }, 700); // 700ms + 300ms fade-out = 1000ms (1 second)
        }
    });
</script>

@stack('js')

</html>
