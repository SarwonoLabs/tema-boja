@php
    $comments = [];
    if (is_array($komentar) && $single_artikel['boleh_komentar']) {
        $comments = [];
        foreach ($komentar as $comment) {
            if ($comment['is_archived'] != 1) {
                $comments[] = $comment;
            }
        }
        $comments = array_reverse($comments);
        $forms = [
            'owner' => 'Nama',
            'email' => 'Alamat Email',
            'no_hp' => 'No. HP',
        ];
    }
    $notif = session('notif');
@endphp

@if (count($comments) > 0)
    <div class="comments-boja" style="margin-top: 2rem;">
        <h4 class="box-header"><i class="fas fa-comments"></i> Komentar ({{ count($comments) }})</h4>
        <div class="comments-list">
            @foreach ($comments as $comment)
                <div class="comment-item">
                    <div class="comment-avatar"><i class="fa fa-user"></i></div>
                    <div class="comment-body">
                        <blockquote>"{{ $comment['komentar'] }}"</blockquote>
                        <div class="comment-meta">
                            <span><i class="fa fa-user"></i> {{ $comment['pengguna']['nama'] }}</span>
                            <span><i class="fa fa-calendar-alt"></i> {{ tgl_indo($comment['tgl_upload']) }}</span>
                        </div>
                    </div>
                </div>
                @if (count($comment['children']) > 0)
                    @foreach ($comment['children'] as $children)
                        <div class="comment-item comment-reply">
                            <div class="comment-avatar"><i class="fa fa-reply"></i></div>
                            <div class="comment-body">
                                <blockquote>"{{ $children['komentar'] }}"</blockquote>
                                <div class="comment-meta">
                                    <span><i class="fa fa-user"></i> {{ $children['pengguna']['nama'] }} <code>({{ $children['pengguna']['level'] }})</code></span>
                                    <span><i class="fa fa-calendar-alt"></i> {{ tgl_indo($children['tgl_upload']) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
@endif

@if ($single_artikel['boleh_komentar'] == 1)
    <form action="{{ site_url('/add_comment/' . $single_artikel['id']) }}" method="POST" class="comment-form-boja" id="kolom-komentar">
        <h4 class="box-header"><i class="fas fa-pen"></i> Beri Komentar</h4>
        <div class="alert alert-info"><i class="fas fa-info-circle"></i> Komentar baru terbit setelah disetujui oleh admin</div>

        @php $alert = ($notif['status'] == -1) ? 'danger' : 'success'; @endphp
        @if ($flash_message = $notif['pesan'])
            <div class="alert alert-{{ $alert }}">{{ $flash_message }}</div>
        @endif

        <div class="form-group">
            <label for="komentar">Komentar <span style="color:red">*</span></label>
            <textarea class="form-control" name="komentar" id="komentar" rows="4" required>{{ $notif['data']['komentar'] }}</textarea>
        </div>
        <div class="form-grid-3">
            @foreach ($forms as $name => $label)
                <div class="form-group">
                    <label for="{{ $name }}">{{ $label }} @if ($name !== 'email') <span style="color:red">*</span> @endif</label>
                    <input type="text" class="form-control" id="{{ $name }}" name="{{ $name }}" value="{{ $name === 'owner' && !empty($notif['data']['nama']) ? $notif['data']['nama'] : $notif['data'][$name] }}" {{ $name !== 'email' ? 'required' : '' }}>
                </div>
            @endforeach
        </div>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <img id="captcha" src="{{ site_url('captcha') }}" alt="CAPTCHA" style="max-width: 100%; height: auto;">
                <button type="button" style="font-size: 0.75rem; color: var(--primary); background: none; border: none; cursor: pointer; padding: 0.25rem 0;" onclick="document.getElementById('captcha').src = '{{ ci_route('captcha') }}?' + Math.random();">[Ganti Gambar]</button>
            </div>
            <input type="text" name="captcha_code" class="form-control" placeholder="Tulis kode di samping" style="max-width: 200px;">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Komentar</button>
    </form>
@endif
