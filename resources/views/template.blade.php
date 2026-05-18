@php
    require_once base_path('desa/themes/' . basename(theme_path()) . '/resources/views/commons/theme_config.php');

    // Null-safety — pastikan variabel yang dipakai template selalu terdefinisi
    $themeVersion = $themeVersion ?? 'v0';
    $_tid         = $_tid         ?? null;
    $_tls         = $_tls         ?? [];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('theme::commons.meta')
    @include('theme::commons.source_css')
    @include('theme::commons.source_js')
    <title>@yield('title')</title>
    <style id="_bj-ctx">body{visibility:hidden;opacity:0}</style>
    @stack('styles')
</head>

<body>
    <div id="_bj" aria-hidden="true" style="display:none"></div>

    @include('theme::commons.loading_screen')

    {{-- Navbar top --}}
    @include('theme::commons.main_menu')
    @include('theme::commons.mobile_menu')

    {{-- Hero Header (hanya di halaman utama) --}}
    @include('theme::commons.header')

    @yield('layout')

    @include('theme::commons.footer')
    @include('theme::commons.back_to_top')

    {{-- Demo Banner — aktif jika file demo_banner.blade.php ada, skip otomatis jika tidak ada --}}
    @includeIf('theme::commons.demo_banner')

    <script src="{{ theme_asset('js/helper.js') }}&{{ $themeVersion }}"></script>
    <script type="text/javascript">
        var _0x = {
            a: "{{ $_tid }}",
            c: @json($_tls),
            d: typeof SITE_URL !== 'undefined' ? SITE_URL : '/'
        };
    </script>
    <script src="{{ theme_asset('js/script.js') }}&{{ $themeVersion }}"></script>
    <script type="text/javascript">
        function formatRupiah(angka, prefix = 'Rp ') {
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '') + ',00';
        }
    </script>
    @stack('scripts')
</body>

</html>
