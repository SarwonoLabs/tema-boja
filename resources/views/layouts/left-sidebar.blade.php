@extends('theme::template')

@section('layout')
    <div class="container layout-boja">
        <div class="layout-main layout-reversed">
            {{-- Sidebar --}}
            <aside class="sidebar-area">
                @include('theme::partials.sidebar')
            </aside>
            {{-- Content --}}
            <main class="content-area box">
                @yield('content')
            </main>
        </div>
    </div>
@endsection
