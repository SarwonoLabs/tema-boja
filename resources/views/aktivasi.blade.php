<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Tema - Boja</title>
    @if (cek_koneksi_internet())
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @endif
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 50%, #10b981 100%);
            padding: 2rem;
        }
        .activation-card {
            background: #fff; border-radius: 16px; padding: 3rem 2.5rem;
            max-width: 480px; width: 100%; text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }
        .activation-logo {
            width: 72px; height: 72px; margin: 0 auto 1.5rem;
            display: flex; align-items: center; justify-content: center;
        }
        .activation-logo img {
            width: 100%; height: 100%; object-fit: contain;
        }
        h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; }
        p { color: #64748b; line-height: 1.6; margin-bottom: 1.5rem; }
        .btn-activate {
            display: inline-block; padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #1e40af, #0ea5e9);
            color: #fff; text-decoration: none; border-radius: 8px;
            font-weight: 600; font-size: 1rem;
            transition: opacity 0.3s; border: none; cursor: pointer;
        }
        .btn-activate:hover { opacity: 0.9; }
        .buy-license { margin-top: 1.25rem; font-size: 0.85rem; color: #64748b; }
        .buy-license a { color: #2563eb; text-decoration: none; }
        .buy-license a:hover { text-decoration: underline; }
        .notice-cache {
            margin-top: 1.25rem; padding: 0.875rem 1rem;
            background: #fefce8; border: 1px solid #fde68a; border-radius: 8px;
            font-size: 0.8125rem; color: #92400e; line-height: 1.5; text-align: left;
        }
        .notice-cache strong { display: block; margin-bottom: 0.25rem; color: #78350f; }
        .notice-cache a {
            color: #d97706; font-weight: 600; text-decoration: none;
        }
        .notice-cache a:hover { text-decoration: underline; }
        .footer-text { margin-top: 2rem; font-size: 0.8125rem; color: #94a3b8; }
        .footer-text a { color: #0ea5e9; text-decoration: none; }
    </style>
</head>
<body>
    <div class="activation-card">
        <div class="activation-logo">
            <img src="{{ theme_asset('images/desanta.png') }}" alt="OpenSID Logo">
        </div>
        <h1>Aktivasi Tema Boja</h1>
        <p>Tema ini memerlukan aktivasi lisensi sebelum dapat digunakan. Silakan lakukan aktivasi melalui panel admin atau hubungi Desanta untuk mendapatkan lisensi.</p>
        <a href="https://desanta.id" class="btn-activate" target="_blank">Dapatkan Lisensi</a>
        <div class="buy-license">
            <p>Sudah punya lisensi? <a href="{{ site_url() }}">Kembali ke Beranda</a></p>
        </div>
        <div class="notice-cache">
            <strong>⚠️ Sudah punya lisensi tapi halaman ini masih muncul?</strong>
            Lisensi mungkin tersimpan di cache lama. Hapus cache melalui menu
            <strong>Admin → Pengaturan → Cache</strong> atau jalankan perintah
            <code>php artisan cache:clear</code>, kemudian
            <a href="{{ site_url() }}">kembali ke Beranda</a>.
        </div>
        <div class="footer-text">
            <p>&copy; {{ date('Y') }} <a href="https://desanta.id" target="_blank">Desanta</a> &middot; -
                <a href="https://boja.desanta.id" target="_blank">Tema Boja {{ $themeVersion }}</a></p>
        </div>
    </div>
</body>
</html>
