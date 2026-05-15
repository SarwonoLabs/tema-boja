<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

{{-- Widget Jadwal Sholat — Sidebar (Boja) --}}
@if (theme_config('tampilkan_jadwal_sholat', '1') == '1')
<div class="wsholat-box" id="wsholat-grid">
    {{-- Header --}}
    <div class="wsholat-header">
        <div class="wsholat-header-icon">
            <i class="fas fa-mosque"></i>
        </div>
        <div class="wsholat-header-text">
            <h3>Jadwal Sholat</h3>
            <span class="wsholat-date" id="wsholat-tanggal">Memuat...</span>
        </div>
    </div>

    {{-- Prayer times list --}}
    <div class="wsholat-list">
        <div class="wsholat-item" data-index="0">
            <span class="wsholat-icon"><i class="fas fa-cloud-sun"></i></span>
            <span class="wsholat-name">Subuh</span>
            <span class="wsholat-time" data-waktu="subuh">--:--</span>
        </div>
        <div class="wsholat-item" data-index="1">
            <span class="wsholat-icon"><i class="fas fa-sun"></i></span>
            <span class="wsholat-name">Dzuhur</span>
            <span class="wsholat-time" data-waktu="dzuhur">--:--</span>
        </div>
        <div class="wsholat-item" data-index="2">
            <span class="wsholat-icon"><i class="fas fa-cloud-sun"></i></span>
            <span class="wsholat-name">Ashar</span>
            <span class="wsholat-time" data-waktu="ashar">--:--</span>
        </div>
        <div class="wsholat-item" data-index="3">
            <span class="wsholat-icon"><i class="fas fa-cloud-moon"></i></span>
            <span class="wsholat-name">Maghrib</span>
            <span class="wsholat-time" data-waktu="maghrib">--:--</span>
        </div>
        <div class="wsholat-item" data-index="4">
            <span class="wsholat-icon"><i class="fas fa-moon"></i></span>
            <span class="wsholat-name">Isya</span>
            <span class="wsholat-time" data-waktu="isya">--:--</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var grid = document.getElementById('wsholat-grid');
    if(!grid) return;

    var lat = '{{ $desa["lat"] ?? "" }}';
    var lng = '{{ $desa["lng"] ?? "" }}';
    if(!lat || !lng) return;

    var today = new Date();
    var dd = String(today.getDate()).padStart(2,'0');
    var mm = String(today.getMonth()+1).padStart(2,'0');
    var yyyy = today.getFullYear();
    var dateStr = dd+'-'+mm+'-'+yyyy;

    var tanggalEl = document.getElementById('wsholat-tanggal');
    var hariNama = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    var bulanNama = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    var hariIni = hariNama[today.getDay()] + ', ' + today.getDate() + ' ' + bulanNama[today.getMonth()] + ' ' + yyyy;
    if(tanggalEl) tanggalEl.textContent = hariIni;

    var apiUrl = 'https://api.aladhan.com/v1/timings/' + dateStr + '?latitude=' + lat + '&longitude=' + lng + '&method=20';

    fetch(apiUrl)
        .then(function(res){ return res.json(); })
        .then(function(data){
            if(data.code === 200 && data.data && data.data.timings){
                var t = data.data.timings;
                var mapping = { 'subuh': t.Fajr, 'dzuhur': t.Dhuhr, 'ashar': t.Asr, 'maghrib': t.Maghrib, 'isya': t.Isha };
                var nameMap = { 'subuh': 'Subuh', 'dzuhur': 'Dzuhur', 'ashar': 'Ashar', 'maghrib': 'Maghrib', 'isya': 'Isya' };

                Object.keys(mapping).forEach(function(key){
                    var el = grid.querySelector('[data-waktu="'+key+'"]');
                    if(el) el.textContent = mapping[key].replace(/\s*\(.*\)/, '');
                });

                var now = new Date();
                var nowMin = now.getHours() * 60 + now.getMinutes();
                var order = ['subuh','dzuhur','ashar','maghrib','isya'];
                var found = false;
                order.forEach(function(key){
                    var el = grid.querySelector('[data-waktu="'+key+'"]');
                    if(!el || found) return;
                    var item = el.closest('.wsholat-item');
                    if(!item) return;
                    var time = mapping[key].replace(/\s*\(.*\)/,'');
                    var parts = time.split(':');
                    var prayerMin = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                    if(prayerMin > nowMin){
                        item.classList.add('wsholat-next');
                        found = true;
                    }
                });

                order.forEach(function(key){
                    var el = grid.querySelector('[data-waktu="'+key+'"]');
                    if(!el) return;
                    var item = el.closest('.wsholat-item');
                    if(!item) return;
                    var time = mapping[key].replace(/\s*\(.*\)/,'');
                    var parts = time.split(':');
                    var prayerMin = parseInt(parts[0]) * 60 + parseInt(parts[1]);
                    if(prayerMin <= nowMin && !item.classList.contains('wsholat-next')) item.classList.add('wsholat-passed');
                });

                if(data.data.date && data.data.date.hijri && tanggalEl){
                    var h = data.data.date.hijri;
                    tanggalEl.textContent = hariIni + ' · ' + h.day + ' ' + h.month.en + ' ' + h.year + 'H';
                }

                grid.classList.add('wsholat-loaded');
            }
        })
        .catch(function(){
            grid.querySelectorAll('.wsholat-time').forEach(function(el){ el.textContent = '--:--'; });
        });
})();
</script>
@endpush
@endif
