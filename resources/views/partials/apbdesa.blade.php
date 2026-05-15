@php defined('BASEPATH') OR exit('No direct script access allowed'); @endphp

{{-- Transparansi Anggaran Desa — Elegant Design --}}
<section class="apbd-wrap">

    {{-- Page Header --}}
    <div class="apbd-page-header">
        <div class="apbd-page-header-inner">
            <div class="apbd-page-header-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <div>
                <h2 class="apbd-page-header-title">Transparansi Anggaran {{ ucfirst(setting('sebutan_desa')) }}</h2>
                <p class="apbd-page-header-sub">Data Anggaran Pendapatan &amp; Belanja {{ ucfirst(setting('sebutan_desa')) }}</p>
            </div>
        </div>
        <div class="apbd-page-header-badge">
            <i class="fas fa-shield-alt mr-1"></i>Data Resmi
        </div>
    </div>

    {{-- Groups --}}
    <div class="apbd-groups">
        @foreach ($data_widget as $subdata_name => $subdatas)
            @php
                $rows = collect($subdatas)->filter(fn($v, $k) => is_array($v) && !empty($v['judul']) && $k !== 'laporan' && ($v['realisasi'] != 0 || $v['anggaran'] != 0));
                $totalAnggaran = $rows->sum('anggaran');
                $totalRealisasi = $rows->sum('realisasi');
                $avgPersen = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 2) : 0;
                $headerClass = $avgPersen >= 75 ? 'good' : ($avgPersen >= 50 ? 'medium' : 'low');
                $judulGrup = \Illuminate\Support\Str::of($subdatas['laporan'])->when(setting('sebutan_desa') != 'desa', function (\Illuminate\Support\Stringable $string) {
                    return $string->replace('Des', \Illuminate\Support\Str::of(setting('sebutan_desa'))->substr(0, 1)->ucfirst());
                });
            @endphp
            <div class="apbd-group">
                {{-- Group Header --}}
                <div class="apbd-group-header">
                    <div class="apbd-group-header-left">
                        <div class="apbd-group-header-icon">
                            @if(str_contains(strtolower($subdatas['laporan']), 'pelaksanaan'))
                                <i class="fas fa-chart-pie"></i>
                            @elseif(str_contains(strtolower($subdatas['laporan']), 'pendapatan'))
                                <i class="fas fa-arrow-circle-down"></i>
                            @elseif(str_contains(strtolower($subdatas['laporan']), 'belanja'))
                                <i class="fas fa-arrow-circle-up"></i>
                            @else
                                <i class="fas fa-coins"></i>
                            @endif
                        </div>
                        <div>
                            <div class="apbd-group-title">{{ $judulGrup }}</div>
                            <div class="apbd-group-meta">{{ $rows->count() }} uraian &bull; realisasi {{ $avgPersen }}%</div>
                        </div>
                    </div>
                    <div class="apbd-group-summary">
                        <div class="apbd-group-summary-item">
                            <span class="apbd-gs-label">Anggaran</span>
                            <span class="apbd-gs-value">{{ rupiah24($totalAnggaran) }}</span>
                        </div>
                        <div class="apbd-group-summary-item">
                            <span class="apbd-gs-label">Realisasi</span>
                            <span class="apbd-gs-value">{{ rupiah24($totalRealisasi) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Progress bar total --}}
                @php $barWidth = min($avgPersen, 100); @endphp
                <div class="apbd-group-progress-track">
                    <div class="apbd-group-progress-bar apbd-group-progress-bar--{{ $headerClass }}" data-w="{{ $barWidth }}"></div>
                </div>

                {{-- Table --}}
                <div class="apbd-table-wrap">
                    <table class="apbd-table">
                        <thead>
                            <tr>
                                <th class="apbd-th-uraian">Uraian</th>
                                <th class="apbd-th-money">Anggaran</th>
                                <th class="apbd-th-money">Realisasi</th>
                                <th class="apbd-th-pct">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subdatas as $key => $subdata)
                                @continue(!is_array($subdata))
                                @if ($subdata['judul'] != null && $key != 'laporan' && ($subdata['realisasi'] != 0 || $subdata['anggaran'] != 0))
                                    @php
                                        $pct = $subdata['persen'];
                                        $pctBar = min($pct, 100);
                                        if ($pct >= 75) {
                                            $cls = 'good';
                                        } elseif ($pct >= 50) {
                                            $cls = 'medium';
                                        } elseif ($pct < 0) {
                                            $cls = 'negative';
                                        } else {
                                            $cls = 'low';
                                        }
                                        $judul = \Illuminate\Support\Str::of($subdata['judul'])->title()->whenEndsWith('Desa', function (\Illuminate\Support\Stringable $string) {
                                            if (!in_array($string, ['Dana Desa'])) {
                                                return $string->replace('Desa', setting('sebutan_desa'));
                                            }
                                        })->title();
                                    @endphp
                                    <tr class="apbd-tr">
                                        <td class="apbd-td-uraian">
                                            <div class="apbd-uraian-inner">
                                                <span class="apbd-uraian-dot apbd-uraian-dot--{{ $cls }}"></span>
                                                {{ $judul }}
                                            </div>
                                        </td>
                                        <td class="apbd-td-money">{{ rupiah24($subdata['anggaran']) }}</td>
                                        <td class="apbd-td-money">{{ rupiah24($subdata['realisasi']) }}</td>
                                        <td class="apbd-td-pct">
                                            <div class="apbd-pct-wrap">
                                                <div class="apbd-mini-bar-track">
                                                    <div class="apbd-mini-bar apbd-mini-bar--{{ $cls }}" data-w="{{ max($pctBar,0) }}"></div>
                                                </div>
                                                <span class="apbd-pct-badge apbd-pct-badge--{{ $cls }}">{{ $pct }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</section>

<script>
(function(){
    // Animasi progress bar saat elemen masuk layar
    var bars = document.querySelectorAll('.apbd-mini-bar[data-w], .apbd-group-progress-bar[data-w]');
    function animateBars(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                el.style.width = el.getAttribute('data-w') + '%';
                observer.unobserve(el);
            }
        });
    }
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(animateBars, { threshold: 0.1 });
        bars.forEach(function(b){ io.observe(b); });
    } else {
        bars.forEach(function(b){ b.style.width = b.getAttribute('data-w') + '%'; });
    }
})();
</script>
