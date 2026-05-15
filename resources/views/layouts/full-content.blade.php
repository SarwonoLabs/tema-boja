@extends('theme::template')

@section('layout')
    <div class="container layout-boja">
        <main class="content-area box" style="width: 100%;">
            @yield('content')
        </main>
    </div>
@endsection
