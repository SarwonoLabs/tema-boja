@php
    $shareUrl = urlencode(current_url());
    $shareTitle = urlencode(isset($single_artikel) ? $single_artikel['judul'] : ($desa_title ?? ''));
@endphp

<div class="share-boja">
    <span class="share-label"><i class="fas fa-share-alt"></i> Bagikan:</span>
    <div class="share-buttons">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="share-btn facebook" title="Bagikan ke Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" class="share-btn twitter" title="Bagikan ke Twitter">
            <i class="fab fa-twitter"></i>
        </a>
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener" class="share-btn whatsapp" title="Bagikan ke WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" class="share-btn telegram" title="Bagikan ke Telegram">
            <i class="fab fa-telegram-plane"></i>
        </a>
    </div>
</div>
