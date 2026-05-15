@php
    $shareUrl = urlencode(current_url());
    $shareTitle = urlencode(isset($single_artikel) ? $single_artikel['judul'] : ($desa_title ?? ''));
@endphp

<div class="sticky-share-boja">
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" title="Facebook"><i class="fab fa-facebook-f"></i></a>
    <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" title="Twitter"><i class="fab fa-twitter"></i></a>
    <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
    <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener" title="Telegram"><i class="fab fa-telegram-plane"></i></a>
</div>
