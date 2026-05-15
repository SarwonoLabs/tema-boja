{{-- Widget Statistik Pengunjung — Elegant (boja) --}}
<div class="wvisitor-box">
    {{-- Header gradient --}}
    <div class="wvisitor-header">
        <div class="wvisitor-header-icon"><i class="fas fa-chart-line"></i></div>
        <div class="wvisitor-header-text">
            <h3>{{ $judul_widget }}</h3>
        </div>
    </div>

    {{-- Body — Stats row --}}
    <div class="wvisitor-body">
        <div class="wvisitor-stat-row">
            <div class="wvisitor-stat">
                <span class="wvisitor-num wvisitor-num-today" data-target="{{ $statistik_pengunjung['hari_ini'] ?? 0 }}">0</span>
                <span class="wvisitor-label">Hari Ini</span>
            </div>
            <div class="wvisitor-divider"></div>
            <div class="wvisitor-stat">
                <span class="wvisitor-num wvisitor-num-yesterday" data-target="{{ $statistik_pengunjung['kemarin'] ?? 0 }}">0</span>
                <span class="wvisitor-label">Kemarin</span>
            </div>
            <div class="wvisitor-divider"></div>
            <div class="wvisitor-stat">
                <span class="wvisitor-num wvisitor-num-total" data-target="{{ $statistik_pengunjung['total'] ?? 0 }}">0</span>
                <span class="wvisitor-label">Total</span>
            </div>
        </div>
        <div class="wvisitor-live">
            <span class="wvisitor-live-dot"></span>
            <span>Online sekarang</span>
        </div>
    </div>
</div>

{{-- Count-up animation --}}
<script>
(function(){
    var els = document.querySelectorAll('.wvisitor-num[data-target]');
    if (!els.length) return;
    var observer = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var target = parseInt(el.getAttribute('data-target')) || 0;
            var duration = 1200;
            var start = 0;
            var startTime = null;
            function animate(ts){
                if (!startTime) startTime = ts;
                var progress = Math.min((ts - startTime) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(animate);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(animate);
            observer.unobserve(el);
        });
    }, { threshold: 0.3 });
    els.forEach(function(el){ observer.observe(el); });
})();
</script>
