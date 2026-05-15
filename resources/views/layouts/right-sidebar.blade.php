@extends('theme::template')

@section('layout')
    <div class="container layout-boja">
        <div class="layout-main">
            {{-- Content --}}
            <main class="content-area box">
                @yield('content')
            </main>
            {{-- Sidebar --}}
            <aside class="sidebar-area">
                @include('theme::partials.sidebar')
            </aside>
        </div>
    </div>
@endsection
