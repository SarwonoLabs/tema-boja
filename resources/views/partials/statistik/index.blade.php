@extends('theme::template')
@include('theme::commons.asset_highcharts')

@section('title', $judul ?? 'Data Statistik')

@section('layout')
    <div class="container layout-boja">
        <div class="stat-layout">
            {{-- Sidebar Navigation --}}
            <aside class="stat-sidebar">
                @include('theme::partials.statistik.sidenav')
            </aside>

            {{-- Main Content --}}
            <main class="stat-main">
                @include('theme::partials.statistik.default')
                <script>
                    const enable3d = {{ setting('statistik_chart_3d') ? 1 : 0 }};
                </script>
            </main>
        </div>
    </div>
@endsection
