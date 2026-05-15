{{-- Slider --}}
<section class="slider-boja">
    <div class="owl-carousel">
        @foreach ($slider_gambar['gambar'] as $data)
            @php
                $img = $slider_gambar['lokasi'] . 'sedang_' . $data['gambar'];
            @endphp
            @if (is_file($img))
                <figure class="slider-item">
                    <img src="{{ base_url($img) }}" alt="{{ $data['judul'] }}">
                    @if ($slider_gambar['sumber'] != 3)
                        <figcaption class="slider-caption">
                            <a href="{{ site_url('artikel/' . buat_slug($data)) }}">{{ $data['judul'] }}</a>
                        </figcaption>
                    @endif
                </figure>
            @endif
        @endforeach
    </div>
    <button class="slider-nav-boja prev" title="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
    <button class="slider-nav-boja next" title="Selanjutnya"><i class="fas fa-chevron-right"></i></button>
</section>
