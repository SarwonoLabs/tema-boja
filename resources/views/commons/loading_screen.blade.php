{{-- Loading Screen — Elegant Boja --}}
<div class="ls-boja" id="loadingScreen">
    <div class="ls-inner">
        <div class="ls-logo-wrap">
            <img src="{{ gambar_desa($desa['logo'] ?? '') }}" alt="{{ $desa['nama_desa'] ?? '' }}" class="ls-logo">
            <div class="ls-ring ls-ring-1"></div>
            <div class="ls-ring ls-ring-2"></div>
        </div>
        <div class="ls-desa-name">{{ $desa['nama_desa'] ?? '' }}</div>
        <div class="ls-progress-wrap">
            <div class="ls-progress-bar"></div>
        </div>
        <div class="ls-tagline">Memuat halaman&hellip;</div>
    </div>
</div>
<style>
    .ls-boja{
        position:fixed;inset:0;z-index:99999;
        display:flex;align-items:center;justify-content:center;
        background:linear-gradient(145deg,#0F1A30 0%,#1E2D4E 60%,#162240 100%);
        transition:opacity .5s ease,visibility .5s ease;
    }
    /* Batik kawung background */
    .ls-boja::before{
        content:'';position:absolute;inset:0;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='0' r='11' fill='none' stroke='white' stroke-width='1' opacity='0.05'/%3E%3Ccircle cx='30' cy='60' r='11' fill='none' stroke='white' stroke-width='1' opacity='0.05'/%3E%3Ccircle cx='0' cy='30' r='11' fill='none' stroke='white' stroke-width='1' opacity='0.05'/%3E%3Ccircle cx='60' cy='30' r='11' fill='none' stroke='white' stroke-width='1' opacity='0.05'/%3E%3Ccircle cx='30' cy='30' r='14' fill='none' stroke='white' stroke-width='0.6' opacity='0.03'/%3E%3Ccircle cx='0' cy='0' r='6' fill='none' stroke='white' stroke-width='0.5' opacity='0.04'/%3E%3Ccircle cx='60' cy='0' r='6' fill='none' stroke='white' stroke-width='0.5' opacity='0.04'/%3E%3Ccircle cx='0' cy='60' r='6' fill='none' stroke='white' stroke-width='0.5' opacity='0.04'/%3E%3Ccircle cx='60' cy='60' r='6' fill='none' stroke='white' stroke-width='0.5' opacity='0.04'/%3E%3C/svg%3E");
        background-size:60px 60px;pointer-events:none;
    }
    .ls-boja.ls-done{opacity:0;visibility:hidden}
    .ls-inner{
        display:flex;flex-direction:column;align-items:center;gap:20px;
        position:relative;z-index:1;
        animation:ls-fade-in .5s ease forwards;
    }
    @keyframes ls-fade-in{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    /* Logo */
    .ls-logo-wrap{position:relative;width:80px;height:80px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ls-logo{width:64px;height:64px;object-fit:contain;border-radius:14px;position:relative;z-index:2;
        filter:drop-shadow(0 0 16px rgba(96,165,250,.35));
        animation:ls-logo-pulse 2s ease-in-out infinite;
    }
    @keyframes ls-logo-pulse{0%,100%{filter:drop-shadow(0 0 10px rgba(96,165,250,.25))}50%{filter:drop-shadow(0 0 22px rgba(96,165,250,.55))}}
    /* Rings */
    .ls-ring{position:absolute;border-radius:50%;border-style:solid;border-color:transparent}
    .ls-ring-1{
        width:86px;height:86px;
        border-width:2px;
        border-top-color:rgba(96,165,250,.5);
        border-right-color:rgba(96,165,250,.15);
        animation:ls-spin 1.4s linear infinite;
    }
    .ls-ring-2{
        width:100px;height:100px;
        border-width:1.5px;
        border-bottom-color:rgba(147,197,253,.3);
        border-left-color:rgba(147,197,253,.08);
        animation:ls-spin 2.2s linear infinite reverse;
    }
    @keyframes ls-spin{to{transform:rotate(360deg)}}
    /* Desa name */
    .ls-desa-name{
        font-family:'Plus Jakarta Sans',system-ui,sans-serif;
        font-size:1.05rem;font-weight:700;color:#fff;
        letter-spacing:.04em;text-align:center;
        text-shadow:0 2px 12px rgba(0,0,0,.4);
        max-width:260px;line-height:1.4;
    }
    /* Progress bar */
    .ls-progress-wrap{width:160px;height:3px;background:rgba(255,255,255,.1);border-radius:99px;overflow:hidden}
    .ls-progress-bar{
        height:100%;width:30%;border-radius:99px;
        background:linear-gradient(90deg,#3B82F6,#60A5FA,#93C5FD);
        animation:ls-progress 1.6s ease-in-out infinite;
    }
    @keyframes ls-progress{
        0%{left:-30%;width:30%;transform:translateX(-100%)}
        50%{width:60%}
        100%{transform:translateX(600%);width:30%}
    }
    .ls-progress-bar{position:relative}
    /* Tagline */
    .ls-tagline{
        font-size:.72rem;color:rgba(255,255,255,.4);
        font-family:'Plus Jakarta Sans',system-ui,sans-serif;
        letter-spacing:.08em;text-transform:uppercase;
        animation:ls-blink 1.8s ease-in-out infinite;
    }
    @keyframes ls-blink{0%,100%{opacity:.4}50%{opacity:.8}}
</style>
<script>
    window.addEventListener('load', function() {
        var el = document.getElementById('loadingScreen');
        if (el) {
            setTimeout(function() {
                el.classList.add('ls-done');
                setTimeout(function(){ el.style.display='none'; }, 550);
            }, 200);
        }
    });
</script>
