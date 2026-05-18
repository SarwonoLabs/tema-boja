@extends('theme::layouts.full-content')
@include('theme::commons.asset_highcharts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/bagan.css') }}">
@endpush

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Struktur Organisasi</li>
        </ol>
    </nav>

    <div class="boja-page-header">
        <div class="boja-page-header-text">
            <h1 class="boja-page-title"><i class="fas fa-sitemap"></i> Struktur Organisasi dan Tata Kerja</h1>
            <p class="boja-page-subtitle">{{ ucwords(setting('sebutan_pemerintah_desa')) }} {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
    </div>

    <div class="sotk-chart-card" id="sotk-list">
        <div class="sotk-chart-loading" id="sotk-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat struktur organisasi...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            function loadHighcharts(strukturPemerintah, strukturSotk) {
                Highcharts.chart('container-sotk', {
                    chart: {
                        height: 900,
                        inverted: true,
                        backgroundColor: 'transparent'
                    },

                    title: {
                        text: ('Struktur Organisasi Pemerintah ' + setting.sebutan_desa).replace(/\b\w/g, function(c) { return c.toUpperCase(); }),
                        style: { fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '700', fontSize: '16px', color: '#1C4D35' }
                    },

                    accessibility: {
                        point: {
                            descriptionFormatter: function(point) {
                                var nodeName = point.toNode.name,
                                    nodeId = point.toNode.id,
                                    nodeDesc = nodeName === nodeId ? nodeName : nodeName + ', ' + nodeId,
                                    parentDesc = point.fromNode.id;
                                return point.index + '. ' + nodeDesc + ', reports to ' + parentDesc + '.';
                            }
                        }
                    },

                    series: [{
                        type: 'organization',
                        name: setting.sebutan_desa + ' ' + config.nama_desa,
                        keys: ['from', 'to'],
                        data: strukturSotk,
                        levels: [{
                            level: 0,
                            color: '#D4AF37',
                            dataLabels: { color: '#1a1a1a' },
                            height: 40
                        }, {
                            level: 1,
                            color: '#2F855A',
                            dataLabels: { color: 'white' },
                            height: 40
                        }, {
                            level: 2,
                            color: '#276749',
                            dataLabels: { color: 'white' },
                            height: 40
                        }, {
                            level: 4,
                            color: '#1C4D35',
                            dataLabels: { color: 'white' },
                            height: 40
                        }],

                        linkColor: "#c8d6e5",
                        linkLineWidth: 2,
                        linkRadius: 0,
                        nodes: strukturPemerintah,
                        colorByPoint: false,
                        color: '#2F855A',
                        dataLabels: {
                            useHTML: true,
                            color: 'white',
                            style: { fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '10px', textOverflow: 'none', whiteSpace: 'normal' },
                            nodeFormatter: function() {
                                var node = this.point;
                                var img = node.image
                                    ? '<img src="' + node.image + '" style="width:40px;height:40px;border-radius:50%;object-fit:cover;display:block;margin:0 auto 6px;border:2px solid rgba(255,255,255,0.5)">'
                                    : '';
                                var nameColor = (node.color === '#D4AF37') ? '#1a1a1a' : 'white';
                                var titleColor = (node.color === '#D4AF37') ? '#4a3a00' : 'rgba(255,255,255,0.85)';
                                return '<div style="text-align:center;padding:4px 6px;width:100%">'
                                    + img
                                    + '<div style="font-weight:700;font-size:10px;line-height:1.3;color:' + nameColor + ';word-break:break-word;white-space:normal">' + (node.name || '') + '</div>'
                                    + (node.title ? '<div style="font-size:9px;line-height:1.3;color:' + titleColor + ';margin-top:2px;word-break:break-word;white-space:normal">' + node.title + '</div>' : '')
                                    + '</div>';
                            }
                        },
                        shadow: {
                            color: 'rgba(0,0,0,0.08)',
                            width: 8,
                            offsetX: 0,
                            offsetY: 2
                        },
                        borderColor: 'rgba(255,255,255,0.3)',
                        borderWidth: 1,
                        nodeWidth: 160
                    }],
                    tooltip: {
                        outside: true,
                        style: { fontFamily: "'Nunito', sans-serif" }
                    },
                    exporting: {
                        allowHTML: true,
                        sourceWidth: 800,
                        sourceHeight: 600
                    }
                });
            }

            var strukturPemerintah = [];
            var strukturSotk = [];

            function loadSotk() {
                var apiPemerintah = '{{ route('api.pemerintah') }}';
                var $sotkList = $('#sotk-list');
                var $loading = $('#sotk-loading');

                $.get(apiPemerintah, function(response) {
                    var pemerintah = response.data;

                    if (!pemerintah.length) {
                        $loading.html('<i class="fas fa-sitemap" style="font-size:2rem;color:#d1d5db;margin-bottom:10px"></i><p>Tidak ada data struktur organisasi.</p>');
                        return;
                    }

                    var initialStructure = [
                        { id: 'BPD', color: '#D4AF37', column: 0, offset: '-150' },
                        { id: 'LPM', color: '#D4AF37', column: 0, dataLabels: { color: '#1a1a1a' }, offset: '150' }
                    ];

                    strukturPemerintah.push.apply(strukturPemerintah, initialStructure);
                    strukturSotk.push(['BPD', 'LPM']);

                    pemerintah.forEach(function(item) {
                        var data = {
                            id: parseInt(item.id),
                            title: item.attributes.nama_jabatan,
                            name: item.attributes.nama,
                            image: item.attributes.foto,
                            column: item.attributes.bagan_tingkat || undefined,
                            offset: item.attributes.bagan_offset || undefined,
                            layout: item.attributes.bagan_layout || undefined,
                            color: item.attributes.bagan_warna || undefined,
                        };

                        strukturPemerintah.push(data);

                        if (item.attributes.atasan) {
                            strukturSotk.push([parseInt(item.attributes.atasan), data.id]);
                        }
                    });

                    $loading.remove();
                    $sotkList.append('<div class="sotk-chart-inner"><figure class="highcharts-figure" style="margin:0;min-width:900px"><div id="container-sotk"></div></figure></div>');

                    loadHighcharts(strukturPemerintah, strukturSotk);
                });
            }

            loadSotk();
        });
    </script>
@endpush
