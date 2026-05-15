<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

{{-- Widget Agenda — Elegant (boja) --}}
@php
    $allAgenda = array_merge($hari_ini ?? [], $yad ?? [], $lama ?? []);
    // Tentukan tab default aktif
    $defaultTab = 'hari-ini';
    if (count($hari_ini ?? []) === 0 && count($yad ?? []) > 0) $defaultTab = 'yad';
    elseif (count($hari_ini ?? []) === 0 && count($yad ?? []) === 0 && count($lama ?? []) > 0) $defaultTab = 'lama';
@endphp

<div class="wagenda-box">
    {{-- Header gradient (konsisten dengan wcat & warsip) --}}
    <div class="wagenda-header">
        <div class="wagenda-header-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="wagenda-header-text">
            <h3>{{ $judul_widget }}</h3>
            <p>Kegiatan &amp; acara {{ setting('sebutan_desa') }}</p>
        </div>
    </div>

    @if (count($allAgenda) > 0)
        {{-- Tabs --}}
        <div class="wagenda-tabs">
            @if (count($hari_ini ?? []) > 0)
                <button class="wagenda-tab {{ $defaultTab === 'hari-ini' ? 'is-active' : '' }}" data-target="wagenda-hari-ini">
                    <i class="fas fa-sun"></i> Hari Ini
                    <span class="wagenda-tab-count">{{ count($hari_ini) }}</span>
                </button>
            @endif
            @if (count($yad ?? []) > 0)
                <button class="wagenda-tab {{ $defaultTab === 'yad' ? 'is-active' : '' }}" data-target="wagenda-yad">
                    <i class="fas fa-hourglass-half"></i> Mendatang
                    <span class="wagenda-tab-count">{{ count($yad) }}</span>
                </button>
            @endif
            @if (count($lama ?? []) > 0)
                <button class="wagenda-tab {{ $defaultTab === 'lama' ? 'is-active' : '' }}" data-target="wagenda-lama">
                    <i class="fas fa-history"></i> Lalu
                    <span class="wagenda-tab-count">{{ count($lama) }}</span>
                </button>
            @endif
        </div>

        {{-- Content panels --}}
        <div class="wagenda-body">
            {{-- Panel: Hari Ini --}}
            @if (count($hari_ini ?? []) > 0)
                <div class="wagenda-panel {{ $defaultTab === 'hari-ini' ? 'is-active' : '' }}" id="wagenda-hari-ini">
                    <div class="wagenda-scroll-wrap">
                        <div class="wagenda-scroll-track">
                            <div class="wagenda-list">
                                @foreach ($hari_ini as $idx => $agenda)
                                    @include('theme::widgets._agenda_card', ['agenda' => $agenda, 'idx' => $idx, 'badge' => 'Hari Ini', 'badgeClass' => 'wagenda-badge-today'])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Panel: Yang Akan Datang --}}
            @if (count($yad ?? []) > 0)
                <div class="wagenda-panel {{ $defaultTab === 'yad' ? 'is-active' : '' }}" id="wagenda-yad">
                    <div class="wagenda-scroll-wrap">
                        <div class="wagenda-scroll-track">
                            <div class="wagenda-list">
                                @foreach ($yad as $idx => $agenda)
                                    @include('theme::widgets._agenda_card', ['agenda' => $agenda, 'idx' => $idx, 'badge' => 'Mendatang', 'badgeClass' => 'wagenda-badge-upcoming'])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Panel: Lama --}}
            @if (count($lama ?? []) > 0)
                <div class="wagenda-panel {{ $defaultTab === 'lama' ? 'is-active' : '' }}" id="wagenda-lama">
                    <div class="wagenda-scroll-wrap">
                        <div class="wagenda-scroll-track">
                            <div class="wagenda-list">
                                @foreach ($lama as $idx => $agenda)
                                    @include('theme::widgets._agenda_card', ['agenda' => $agenda, 'idx' => $idx, 'badge' => 'Selesai', 'badgeClass' => 'wagenda-badge-past'])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="wagenda-empty">
            <i class="fas fa-calendar-times"></i>
            <p>Belum ada agenda kegiatan</p>
        </div>
    @endif
</div>

{{-- Tab switching + vertical auto-scroll (jQuery) --}}
<script>
jQuery(function($){
    // Tab switching
    $('.wagenda-tab').on('click', function(){
        var target = $(this).data('target');
        $(this).addClass('is-active').siblings().removeClass('is-active');
        var box = $(this).closest('.wagenda-box');
        box.find('.wagenda-panel').removeClass('is-active');
        box.find('#' + target).addClass('is-active');
        // Re-init scroll for newly active panel
        initAgendaScroll(box.find('#' + target).find('.wagenda-scroll-wrap'));
    });

    // Vertical auto-scroll from bottom to top
    function initAgendaScroll($wrap) {
        if (!$wrap.length) return;
        var $track = $wrap.find('.wagenda-scroll-track');
        var $list = $track.find('.wagenda-list').first();
        var listH = $list.outerHeight(true);
        var wrapH = $wrap.height();

        // Only scroll if content taller than container
        if (listH <= wrapH) {
            $track.css({ animation: 'none', transform: 'none' });
            // Remove duplicate if exists
            $track.find('.wagenda-list-clone').remove();
            return;
        }

        // Duplicate content for seamless loop (only if not already cloned)
        if (!$track.find('.wagenda-list-clone').length) {
            var $clone = $list.clone().addClass('wagenda-list-clone').attr('aria-hidden','true');
            $track.append($clone);
        }

        // Calculate duration: ~30px per second for smooth scroll
        var totalH = listH;
        var duration = totalH / 28;
        $track.css({
            'animation': 'wagendaScrollUp ' + duration + 's linear infinite'
        });

        // Pause on hover
        $wrap.off('mouseenter mouseleave').on('mouseenter', function(){
            $track.css('animation-play-state', 'paused');
        }).on('mouseleave', function(){
            $track.css('animation-play-state', 'running');
        });
    }

    // Init all visible panels
    $('.wagenda-panel.is-active .wagenda-scroll-wrap').each(function(){
        initAgendaScroll($(this));
    });
});
</script>
