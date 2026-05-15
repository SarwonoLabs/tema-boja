@php
    $alt_slug = PREMIUM ? 'artikel' : 'first';
@endphp

<div class="category-bar-boja">
    <div class="container">
        <div class="category-inner">
            <ul class="category-list">
                @foreach ($menu_kiri as $menu)
                    <li>
                        <a href="{{ site_url("{$alt_slug}/kategori/{$menu['slug']}") }}">
                            {{ $menu['kategori'] }}
                        </a>
                    </li>
                    @if (count($menu['submenu'] ?? []) > 0)
                        @foreach ($menu['submenu'] as $submenu)
                            <li>
                                <a href="{{ site_url("{$alt_slug}/kategori/{$submenu['slug']}") }}">
                                    {{ $submenu['kategori'] }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
            <div class="category-actions">
                @if (setting('layanan_mandiri') == 1)
                    <a href="{{ site_url('layanan-mandiri') }}" class="btn btn-sm btn-primary">
                        Layanan Mandiri <i class="fas fa-external-link-alt"></i>
                    </a>
                @endif
                <a href="{{ site_url('siteman') }}" class="btn btn-sm btn-secondary">
                    Login Admin <i class="fas fa-sign-in-alt"></i>
                </a>
            </div>
        </div>
    </div>
</div>
