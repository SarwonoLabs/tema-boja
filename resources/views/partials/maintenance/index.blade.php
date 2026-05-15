<!DOCTYPE html>
<html lang="id">
<head>
    <title>Maintenance - {{ ucwords(setting('sebutan_desa') . ' ' . $desa['nama_desa']) }}</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="{{ favico_desa() }}">
    @if (cek_koneksi_internet())
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @endif
    <link rel="stylesheet" href="{{ theme_asset('css/style-mt.css') }}">
</head>
<body>
    {{-- Animated background particles --}}
    <div class="mt-particles">
        <div class="mt-particle"></div>
        <div class="mt-particle"></div>
        <div class="mt-particle"></div>
        <div class="mt-particle"></div>
        <div class="mt-particle"></div>
        <div class="mt-particle"></div>
    </div>

    <div class="mt-wrapper">
        {{-- Logo --}}
        <div class="mt-logo-wrap">
            <img src="{{ gambar_desa($desa['logo']) }}" alt="Logo {{ $desa['nama_desa'] }}">
        </div>

        {{-- Main Card --}}
        <div class="mt-card">
            {{-- Animated Gear Icon --}}
            <div class="mt-icon-wrap">
                <div class="mt-icon-ring">
                    <i class="fas fa-cog mt-gear-spin"></i>
                </div>
            </div>

            <h1 class="mt-title">Sedang Dalam Perbaikan</h1>
            <p class="mt-subtitle">Website {{ ucwords(setting('sebutan_desa') . ' ' . $desa['nama_desa']) }} sedang dalam tahap pemeliharaan untuk meningkatkan layanan kami.</p>

            {{-- Decorative divider --}}
            <div class="mt-divider">
                <span></span>
                <i class="fas fa-leaf"></i>
                <span></span>
            </div>

            <p class="mt-message">Kami mohon maaf atas ketidaknyamanan ini. Silakan kunjungi kembali dalam beberapa saat. Terima kasih atas pengertian Anda.</p>

            {{-- Contact info --}}
            <div class="mt-contact">
                <h3 class="mt-contact-title"><i class="fas fa-headset"></i> Hubungi Kami</h3>
                <div class="mt-contact-grid">
                    @if ($desa['alamat_kantor'])
                        <div class="mt-contact-item">
                            <div class="mt-contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <span class="mt-contact-label">Alamat</span>
                                <span class="mt-contact-value">{{ $desa['alamat_kantor'] }}</span>
                            </div>
                        </div>
                    @endif
                    @if ($desa['email_desa'])
                        <div class="mt-contact-item">
                            <div class="mt-contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <span class="mt-contact-label">Email</span>
                                <a href="mailto:{{ $desa['email_desa'] }}" class="mt-contact-value mt-link">{{ $desa['email_desa'] }}</a>
                            </div>
                        </div>
                    @endif
                    @if ($desa['telepon'])
                        <div class="mt-contact-item">
                            <div class="mt-contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <span class="mt-contact-label">Telepon</span>
                                <a href="tel:{{ $desa['telepon'] }}" class="mt-contact-value mt-link">{{ $desa['telepon'] }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Signature --}}
            <div class="mt-signature">
                <p class="mt-sig-title">{{ ucwords(kades()->nama) }} {{ $desa['nama_desa'] }}</p>
                <div class="mt-sig-line"></div>
                <p class="mt-sig-name">{{ $desa['nama_kepala_desa'] }}</p>
                @if ($desa['nip_kepala_desa'])
                    <p class="mt-sig-nip">NIP. {{ $desa['nip_kepala_desa'] }}</p>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-footer">
            <p>&copy; {{ date('Y') }} {{ ucwords(setting('sebutan_desa') . ' ' . $desa['nama_desa']) }}. Dikelola dengan <i class="fas fa-heart" style="color:#e74c3c;font-size:.7rem"></i> menggunakan <a href="https://opendesa.id" target="_blank" rel="noopener">OpenSID</a></p>
        </div>
    </div>
</body>
</html>
