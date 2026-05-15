{{-- Sidebar — Ringan, hanya widget teks/link --}}
<aside class="sidebar-boja">

    @php
        // Widget berat yang dipindahkan ke section full-width + media_sosial sudah di footer
        // Kehadiran sudah ada di section info-desa (full-width)
        // Komentar sudah dipindahkan ke bawah pagination di halaman utama
        // Statistik Pengunjung dirender manual setelah arsip_artikel
        // Sinergi Program dirender manual setelah statistik_pengunjung
        // Jam Kerja sudah ada di section info-desa (di bawah kehadiran)
        // Agenda sudah dipindahkan ke section info-desa (di bawah lokasi kantor)
        // Galeri sudah dipindahkan ke section full-width sendiri (di atas info-desa)
        // Jadwal Sholat sudah dipindahkan ke widget sidebar khusus
        $widgetBerat = ['aparatur_desa', 'peta_wilayah_desa', 'peta_lokasi_kantor', 'keuangan', 'media_sosial', 'statistik', 'statistik_pengunjung', 'komentar', 'sinergi_program', 'jam_kerja', 'agenda', 'galeri'];

        // Cari widget khusus untuk ambil judulnya
        $sinergiWidget = null;
        $statPengunjungWidget = null;
        if ($widgetAktif) {
            foreach ($widgetAktif as $w) {
                if ($w['isi'] === 'sinergi_program') $sinergiWidget = $w;
                if ($w['isi'] === 'statistik_pengunjung') $statPengunjungWidget = $w;
            }
        }
    @endphp

    {{-- Jadwal Sholat Widget (di atas Menu Kategori) --}}
    @includeIf("theme::widgets.jadwal_sholat")

    @if ($widgetAktif)
        @foreach ($widgetAktif as $widget)
            @if (in_array($widget['isi'], $widgetBerat))
                @continue
            @endif
            @php
                $judul_widget = [
                    'judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($widget['judul'])),
                ];
            @endphp
            @includeIf("theme::widgets.{$widget['isi']}", $judul_widget)

            {{-- Render statistik_pengunjung + sinergi_program tepat setelah arsip_artikel --}}
            @if ($widget['isi'] === 'arsip_artikel')
                @if ($statPengunjungWidget)
                    @includeIf("theme::widgets.statistik_pengunjung", [
                        'judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($statPengunjungWidget['judul'])),
                    ])
                @endif
                @if ($sinergiWidget)
                    @includeIf("theme::widgets.sinergi_program", [
                        'judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($sinergiWidget['judul'])),
                    ])
                @endif
            @endif
        @endforeach

        {{-- Fallback: jika arsip_artikel tidak ada, render di akhir --}}
        @if (!collect($widgetAktif)->where('isi', 'arsip_artikel')->count())
            @if ($statPengunjungWidget)
                @includeIf("theme::widgets.statistik_pengunjung", [
                    'judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($statPengunjungWidget['judul'])),
                ])
            @endif
            @if ($sinergiWidget)
                @includeIf("theme::widgets.sinergi_program", [
                    'judul_widget' => str_replace('Desa', ucwords(setting('sebutan_desa')), strip_tags($sinergiWidget['judul'])),
                ])
            @endif
        @endif
    @endif
</aside>
