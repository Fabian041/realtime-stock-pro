<!DOCTYPE html>

<html lang="en" class="light-style  layout-menu-fixed   " dir="ltr" data-theme="theme-default"
    data-assets-path="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/"
    data-base-url="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1"
    data-framework="laravel" data-template="vertical-menu-theme-default-light">


<!-- Mirrored from demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 16 Feb 2023 02:03:20 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Realtime Stock</title>
    <meta name="description"
        content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="TRJBlEhmYek8o5uYJpjNfDyqTIKILSWlz9hcVRpS">
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://themeselection.com/item/sneat-bootstrap-html-laravel-admin-template/">
    <!-- Include the toastify CSS file -->
    <link rel="stylesheet" href="{{ asset('css/toastify.css') }}">

    <!-- Include the toastify JS file -->
    <script src="{{ asset('js/toastify.js') }}"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/favicon/favicon.ico" />


    <!-- Include Styles -->
    <!-- BEGIN: Theme CSS-->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href={{ asset('vendor/fonts/boxiconsc4a7.css?id=87122b3a3900320673311cebdeb618da') }} />
    <link rel="stylesheet" href={{ asset('vendor/fonts/fontawesome5cae.css?id=89157e39c524ff7f679d3aebf872b7b9') }} />
    <link rel="stylesheet" href={{ asset('vendor/fonts/flag-icons5883.css?id=403b97c176f3cdf56a3cbf09107ee240') }} />

    <!-- Core CSS -->
    <link rel="stylesheet" href={{ asset('vendor/css/rtl/coref43c.css?id=f1cefeba0c68d327230d2f6538f276fa') }}
        class="template-customizer-core-css" />
    <link rel="stylesheet" href={{ asset('vendor/css/rtl/theme-default56b8.css?id=cc3d4ef91c8c858754d8ed20c78a3a3c') }}
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href={{ asset('css/democb2e.css?id=24b55ca26d6f2bafc5a21ff5a4bcdfb3') }} />


    <link rel="stylesheet"
        href={{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbarb440.css?id=d9fa6469688548dca3b7e6bd32cb0dc6') }} />
    <link rel="stylesheet"
        href={{ asset('vendor/libs/typeahead-js/typeahead3881.css?id=8fc311b79b2aeabf94b343b6337656cf') }} />

    <!-- Vendor Styles -->
    <link rel="stylesheet" href={{ asset('vendor/libs/apex-charts/apex-charts.css') }}>
    <!-- Page Styles -->
    <script src={{ asset('vendor/js/helpers.js') }}></script>
    <script src={{ asset('vendor/js/template-customizer.js') }}></script>
    <!-- Vendor Styles -->
    <link rel="stylesheet" href={{ asset('vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}>
    <link rel="stylesheet" href={{ asset('vendor/libs/select2/select2.css') }}>
    <link rel="stylesheet" href={{ asset('vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}>

    <link rel="stylesheet" href={{ asset('vendor/libs/toastr/toastr.css') }} />
    <link rel="stylesheet" href={{ asset('vendor/libs/animate-css/animate.css') }} />

    <script src={{ asset('js/config.js') }}></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- beautify ignore:end -->

</head>

<body>

    <!-- Layout Content -->
    <div class="layout-wrapper layout-content-navbar ">
        <div class="layout-container">

            {{-- sidenav --}}
            <x-sidenav>
                {{-- content inside sidenav --}}
            </x-sidenav>
            {{-- end sidenav --}}

            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <x-navbar>
                    {{-- content inside navbar --}}
                </x-navbar>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Content -->
                        @yield('content')
                        <!-- / Content -->
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
                <!--/ Content wrapper -->

                <!-- / Layout page -->
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->
    <!--/ Layout Content -->

    <!-- Include Scripts -->
    <!-- BEGIN: Vendor JS-->
    <script src={{ asset('vendor/libs/jquery/jquery8853.js?id=08d304be7f95879ae643fabf15fb255a') }}></script>
    <script src={{ asset('vendor/libs/popper/popper5751.js?id=70485ad9be8ba3e426172708feb35181') }}></script>
    <script src={{ asset('vendor/js/bootstrape305.js?id=3cb2c653a33d885b40641d15713e3994') }}></script>
    {{-- <script src={{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar6188.js?id=44b8e955848dc0c56597c09f6aebf89a') }}>
    </script>
    <script src={{ asset('vendor/libs/hammer/hammera90c.js?id=5c0a4017d0ce861e87a50c0c1401eb81') }}></script>
    <script src={{ asset('vendor/libs/i18n/i18nbcd7.js?id=74a027f4696264ade8796f88b3d49c14') }}></script> --}}
    <script src={{ asset('vendor/libs/typeahead-js/typeahead60e7.js?id=f6bda588c16867a6cc4158cb4ed37ec6') }}></script>
    <script src={{ asset('vendor/js/menuf635.js?id=03d9787739b295480194ce0a121ae550') }}></script>
    <script src={{ asset('vendor/libs/apex-charts/apexcharts.js') }}></script>
    <script src={{ asset('vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}></script>

    {{-- <script src={{ asset('vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}></script>
    <script src={{ asset('vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}></script>
    <script src={{ asset('vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}></script> --}}

    <!-- END: Page Vendor JS-->
    <!-- BEGIN: Theme JS-->
    <script src={{ asset('js/maincf4d.js?id=e0aeed34a47c1efb009b120245cce82e') }}></script>

    <!-- BEGIN: Page JS-->
    {{-- <script src={{ asset('js/dashboards-analytics.js') }}></script> --}}
    <script src={{ asset('vendor/libs/toastr/toastr.js') }}></script>
    <script src={{ asset('js/ui-toasts.js') }}></script>


    <!-- END: Page JS-->

</body>


<!-- Mirrored from demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 16 Feb 2023 02:04:01 GMT -->

</html>
