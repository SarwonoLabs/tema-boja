{{-- Widget Kehadiran Perangkat Desa --}}
<div class="box kehadiran-box">
    {{-- Header dengan link login kehadiran --}}
    <div class="box-header kehadiran-header">
        <h3 class="box-title">
            <i class="fas fa-clipboard-check mr-1"></i>Kehadiran Perangkat
        </h3>
        @if (setting('tampilkan_kehadiran') != '0')
            <a href="{{ ci_route('kehadiran') }}" class="kehadiran-login-btn" title="Login Kehadiran" target="_blank">
                <i class="fas fa-sign-in-alt"></i>
            </a>
        @endif
    </div>

    <div class="box-body kehadiran-body">
        @php
            $perangkat = $aparatur_desa['daftar_perangkat'] ?? [];
        @endphp

        @if (count($perangkat) > 0)
            <div class="kehadiran-list">
                @foreach ($perangkat as $i => $data)
                    @php
                        // Tentukan status
                        if (!$tampilkan_status_kehadiran) {
                            $statusClass = 'libur';
                            $statusLabel = 'Libur';
                            $statusIcon  = 'fas fa-moon';
                        } elseif ($data['kehadiran'] == 1) {
                            if ($data['status_kehadiran'] == 'hadir') {
                                $statusClass = 'hadir';
                                $statusLabel = 'Hadir';
                                $statusIcon  = 'fas fa-check-circle';
                            } elseif ($data['tanggal'] == date('Y-m-d') && $data['status_kehadiran'] != 'hadir') {
                                $statusClass = 'izin';
                                $statusLabel = ucwords($data['status_kehadiran']);
                                $statusIcon  = 'fas fa-info-circle';
                            } else {
                                $statusClass = 'belum';
                                $statusLabel = 'Belum Rekam';
                                $statusIcon  = 'fas fa-clock';
                            }
                        } else {
                            $statusClass = 'belum';
                            $statusLabel = 'Belum Rekam';
                            $statusIcon  = 'fas fa-clock';
                        }
                    @endphp

                    <div class="kehadiran-item" data-index="{{ $i }}">
                        {{-- Avatar dengan status indicator --}}
                        <div class="kehadiran-avatar-wrap">
                            <img src="{{ $data['foto'] }}" alt="{{ $data['nama'] }}" class="kehadiran-avatar" loading="lazy">
                            <span class="kehadiran-dot status-{{ $statusClass }}" title="{{ $statusLabel }}"></span>
                        </div>

                        {{-- Info --}}
                        <div class="kehadiran-info">
                            <span class="kehadiran-nama">{{ $data['nama'] }}</span>
                            <span class="kehadiran-jabatan">{{ $data['jabatan'] }}</span>
                        </div>

                        {{-- Badge status --}}
                        <span class="kehadiran-badge badge-{{ $statusClass }}" title="{{ $statusLabel }}">
                            <i class="{{ $statusIcon }}"></i>
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-4" style="font-size:13px;">Belum ada data perangkat</p>
        @endif
    </div>
</div>
