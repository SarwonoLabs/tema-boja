@extends('theme::layouts.full-content')
@include('theme::commons.asset_select2')
@include('theme::commons.asset_sweetalert')

@section('content')
    <nav class="breadcrumb-boja" aria-label="breadcrumb">
        <ol>
            <li><a href="{{ ci_route() }}"><i class="fas fa-home"></i> Beranda</a></li>
            <li>Pengaduan</li>
        </ol>
    </nav>

    {{-- ═══ Page Header ═══ --}}
    <div class="pengaduan-header">
        <div class="pengaduan-header-text">
            <h1 class="pengaduan-title"><i class="fas fa-bullhorn"></i> Pengaduan Masyarakat</h1>
            <p class="pengaduan-subtitle">Sampaikan keluhan atau aspirasi Anda untuk {{ ucwords(strtolower(setting('sebutan_desa') . ' ' . ($desa['nama_desa'] ?? ''))) }}</p>
        </div>
        <button type="button" class="pengaduan-cta-btn" data-bs-toggle="modal" data-bs-target="#newpengaduan">
            <i class="fas fa-pencil-alt"></i> Buat Pengaduan
        </button>
    </div>

    {{-- ═══ Search & Filter Bar ═══ --}}
    <div class="pengaduan-filter-bar">
        <div class="pengaduan-filter-group">
            <div class="pengaduan-status-tabs" id="pengaduan-status-tabs">
                <button type="button" class="pengaduan-tab active" data-status="">
                    <i class="fas fa-layer-group"></i> Semua
                </button>
                <button type="button" class="pengaduan-tab tab-waiting" data-status="1">
                    <i class="fas fa-clock"></i> Menunggu
                </button>
                <button type="button" class="pengaduan-tab tab-process" data-status="2">
                    <i class="fas fa-spinner"></i> Diproses
                </button>
                <button type="button" class="pengaduan-tab tab-success" data-status="3">
                    <i class="fas fa-check-circle"></i> Selesai
                </button>
            </div>
            <div class="pengaduan-search-wrap">
                <i class="fas fa-search pengaduan-search-icon"></i>
                <input type="text" name="cari-pengaduan" placeholder="Cari pengaduan..." class="form-control pengaduan-search-input">
                <button id="btn-search" type="button" class="pengaduan-search-btn">Cari</button>
            </div>
        </div>
    </div>

    @include('theme::commons.notifikasi')

    {{-- ═══ Pengaduan List ═══ --}}
    <div id="pengaduan-list" class="pengaduan-grid"></div>

    {{-- ═══ AJAX Pagination ═══ --}}
    <div id="pengaduan-pagination" class="pengaduan-pag-wrap" style="display:none;">
        <p class="pag-info" id="pengaduan-pag-info"></p>
        <ul class="pag-list" id="pengaduan-pag-list"></ul>
    </div>

    {{-- ═══ Detail Modal ═══ --}}
    <div class="modal" id="pengaduan-detail" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content pengaduan-modal-content">
                <div class="modal-header pengaduan-modal-header">
                    <div class="pengaduan-modal-title-wrap">
                        <span class="pengaduan-modal-icon"><i class="fas fa-folder-open"></i></span>
                        <h5 id="pengaduan-judul">Detail Pengaduan</h5>
                    </div>
                    <button type="button" class="pengaduan-modal-close" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body pengaduan-modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal"><i class="fas fa-times"></i> Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ New Pengaduan Modal ═══ --}}
    <div class="modal" id="newpengaduan" style="display:none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content pengaduan-modal-content">
                <div class="modal-header pengaduan-modal-header">
                    <div class="pengaduan-modal-title-wrap">
                        <span class="pengaduan-modal-icon"><i class="fas fa-pencil-alt"></i></span>
                        <h5>Buat Pengaduan Baru</h5>
                    </div>
                    <button type="button" class="pengaduan-modal-close" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ $form_action }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body pengaduan-modal-body">
                        @include('theme::commons.notifikasi')
                        @php $data = session('data', []) @endphp

                        <div class="pengaduan-form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> NIK</label>
                                <input type="text" name="nik" maxlength="16" class="form-control" placeholder="Nomor Induk Kependudukan" value="{{ $data['nik'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Nama <span class="text-required">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama Anda" required value="{{ $data['nama'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control" placeholder="alamat@email.com" value="{{ $data['email'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> No. Telepon</label>
                                <input type="text" name="telepon" class="form-control" placeholder="08xxxxxxxxxx" value="{{ $data['telepon'] ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-heading"></i> Judul Pengaduan <span class="text-required">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Judul singkat pengaduan Anda" required value="{{ $data['judul'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> Isi Pengaduan <span class="text-required">*</span></label>
                            <textarea name="isi" class="form-control" rows="5" placeholder="Jelaskan pengaduan Anda secara detail..." required>{{ $data['isi'] ?? '' }}</textarea>
                        </div>

                        {{-- Foto Upload --}}
                        <div class="form-group">
                            <label><i class="fas fa-camera"></i> Foto Lampiran</label>
                            <div class="pengaduan-upload-area" id="pengaduan-upload-area">
                                {{-- Hidden text input: populates $_POST['foto'] so controller's $this->request['foto'] check passes --}}
                                <input type="text" name="foto" id="pengaduan-foto-text" class="pengaduan-file-text-hidden" readonly>
                                <input type="file" name="foto" id="pengaduan-foto-input" class="pengaduan-file-hidden" accept="image/*">
                                <div class="pengaduan-upload-placeholder" id="pengaduan-upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Klik atau seret file ke sini</span>
                                    <small>.jpg, .jpeg, .png, .webp</small>
                                </div>
                                <img id="pengaduan-preview" class="pengaduan-upload-preview" src="#" alt="Preview" style="display:none;">
                            </div>
                        </div>

                        {{-- Captcha --}}
                        <div class="pengaduan-captcha-row">
                            <div class="pengaduan-captcha-img-wrap">
                                <img id="captcha2" src="{{ ci_route('captcha') }}" alt="CAPTCHA" class="pengaduan-captcha-img">
                                <button type="button" class="pengaduan-captcha-refresh" onclick="document.getElementById('captcha2').src='{{ ci_route('captcha') }}?'+Math.random();" title="Ganti Captcha">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <input type="text" name="captcha_code" class="form-control pengaduan-captcha-input" placeholder="Masukkan kode captcha" required maxlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Pengaduan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
    (function($){
        'use strict';

        var pageSize = {{ theme_config('jumlah_pengaduan_perhalaman') ?: 10 }};
        var apiUrl   = '{{ ci_route("internal_api.pengaduan") }}';
        var noImage  = '{{ theme_asset("images/no-image-article.svg") }}';

        // ── Helper: check if foto URL points to a real uploaded file ──
        function hasRealFoto(url){
            if(!url) return false;
            // Extract the 'path' param from the signed URL
            try {
                var u = new URL(url);
                var path = u.searchParams.get('path') || '';
                // If path ends with '/' or is empty after the folder, there's no real file
                if(!path || path.endsWith('/') || path.endsWith('%2F')) return false;
                return true;
            } catch(e) {
                return url.length > 0;
            }
        }

        // ── File Upload Preview ──
        var $uploadArea  = $('#pengaduan-upload-area');
        var $fileInput   = $('#pengaduan-foto-input');
        var $textInput   = $('#pengaduan-foto-text');
        var $preview     = $('#pengaduan-preview');
        var $placeholder = $('#pengaduan-upload-placeholder');

        // The file input overlays the entire upload area (opacity:0, z-index:2)
        // so direct clicks always hit the native file input. We also handle change:
        $fileInput.on('change', function(){
            if(this.files && this.files[0]){
                // Populate the hidden text input so $_POST['foto'] is truthy
                // (the controller checks $this->request['foto'] which reads POST data)
                $textInput.val(this.files[0].name);

                var reader = new FileReader();
                reader.onload = function(e){
                    $preview.attr('src', e.target.result).show();
                    $placeholder.hide();
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                $textInput.val('');
                $preview.hide();
                $placeholder.show();
            }
        });

        // Drag & drop support
        $uploadArea.on('dragover', function(e){ e.preventDefault(); $(this).addClass('drag-over'); });
        $uploadArea.on('dragleave drop', function(e){ e.preventDefault(); $(this).removeClass('drag-over'); });
        $uploadArea.on('drop', function(e){
            var files = e.originalEvent.dataTransfer.files;
            if(files.length){
                $fileInput[0].files = files;
                $fileInput.trigger('change');
            }
        });

        // ── Status helpers ──
        function statusBadge(s){
            if(s == 3) return '<span class="pengaduan-badge pengaduan-badge-success"><i class="fas fa-check-circle"></i> Selesai</span>';
            if(s == 2) return '<span class="pengaduan-badge pengaduan-badge-process"><i class="fas fa-spinner"></i> Diproses</span>';
            return '<span class="pengaduan-badge pengaduan-badge-waiting"><i class="fas fa-clock"></i> Menunggu</span>';
        }

        // ── Load Pengaduan ──
        function loadPengaduan(page, status, search){
            var params = 'sort=-created_at&page[number]='+(page||1)+'&page[size]='+pageSize;
            if(status) params += '&filter[status]='+status;
            if(search) params += '&filter[search]='+search;

            var $list = $('#pengaduan-list');
            $list.html(
                '<div class="pengaduan-loading">' +
                    '<div class="pengaduan-loading-spinner"></div>' +
                    '<span>Memuat data pengaduan...</span>' +
                '</div>'
            );

            $.get(apiUrl+'?'+params, function(data){
                renderList(data);
                renderPagination(data);
            }).fail(function(){
                $list.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal memuat data pengaduan.</div>');
            });
        }

        // ── Render List ──
        function renderList(resp){
            var $list = $('#pengaduan-list');
            $list.empty();

            if(!resp.data || !resp.data.length){
                $list.html(
                    '<div class="empty-state-boja">' +
                        '<img src="'+noImage+'" alt="Kosong">' +
                        '<h4>Belum Ada Pengaduan</h4>' +
                        '<p>Belum ada pengaduan yang dikirim oleh masyarakat.</p>' +
                    '</div>'
                );
                $('#pengaduan-pagination').hide();
                return;
            }

            resp.data.forEach(function(item, idx){
                var a = item.attributes;
                var fotoSrc = hasRealFoto(a.foto) ? a.foto : noImage;

                var card = $(
                    '<div class="pengaduan-card" style="animation-delay:'+(idx*0.07)+'s">' +
                        '<div class="pengaduan-card-img-wrap">' +
                            '<img src="'+fotoSrc+'" alt="Foto" class="pengaduan-card-img" onerror="this.src=\''+noImage+'\'">' +
                        '</div>' +
                        '<div class="pengaduan-card-body">' +
                            '<div class="pengaduan-card-top">' +
                                '<h3 class="pengaduan-card-title">'+a.judul+'</h3>' +
                                statusBadge(a.status) +
                            '</div>' +
                            '<p class="pengaduan-card-desc">'+a.isi+'</p>' +
                            '<div class="pengaduan-card-meta">' +
                                '<span><i class="fas fa-user"></i> '+a.nama+'</span>' +
                                '<span><i class="fas fa-calendar-alt"></i> '+a.created_at+'</span>' +
                                '<span class="pengaduan-card-comments"><i class="fas fa-comments"></i> '+(a.child_count||0)+' Tanggapan</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="pengaduan-card-arrow"><i class="fas fa-chevron-right"></i></div>' +
                    '</div>'
                );

                card.on('click', function(){ showDetail(a); });
                $list.append(card);
            });
        }

        // ── Render AJAX Pagination ──
        function renderPagination(resp){
            var $wrap = $('#pengaduan-pagination');
            var $info = $('#pengaduan-pag-info');
            var $ul   = $('#pengaduan-pag-list');

            if(!resp.meta || !resp.meta.pagination) { $wrap.hide(); return; }
            var p = resp.meta.pagination;
            if(p.total_pages <= 1){ $wrap.hide(); return; }

            $wrap.show();
            $info.html('Halaman <strong>'+p.current_page+'</strong> dari <strong>'+p.total_pages+'</strong>');
            $ul.empty();

            // Prev
            if(p.current_page > 1)
                $ul.append('<li><a href="#" class="pengaduan-page" data-page="'+(p.current_page-1)+'"><i class="fas fa-chevron-left"></i></a></li>');
            else
                $ul.append('<li class="pag-disabled"><span><i class="fas fa-chevron-left"></i></span></li>');

            // Pages (smart range)
            var start = Math.max(1, p.current_page-2), end = Math.min(p.total_pages, p.current_page+2);
            if(start > 1){ $ul.append('<li><a href="#" class="pengaduan-page" data-page="1">1</a></li>'); if(start>2) $ul.append('<li class="pag-dots"><span>&hellip;</span></li>'); }
            for(var i=start;i<=end;i++){
                $ul.append('<li class="'+(i==p.current_page?'pag-active':'')+'"><a href="#" class="pengaduan-page" data-page="'+i+'">'+i+'</a></li>');
            }
            if(end < p.total_pages){ if(end<p.total_pages-1) $ul.append('<li class="pag-dots"><span>&hellip;</span></li>'); $ul.append('<li><a href="#" class="pengaduan-page" data-page="'+p.total_pages+'">'+p.total_pages+'</a></li>'); }

            // Next
            if(p.current_page < p.total_pages)
                $ul.append('<li><a href="#" class="pengaduan-page" data-page="'+(p.current_page+1)+'"><i class="fas fa-chevron-right"></i></a></li>');
            else
                $ul.append('<li class="pag-disabled"><span><i class="fas fa-chevron-right"></i></span></li>');
        }

        // ── Show Detail Modal ──
        function showDetail(a){
            var fotoSrc = hasRealFoto(a.foto) ? a.foto : noImage;
            var html =
                '<div class="pengaduan-detail-header-row">' +
                    '<div class="pengaduan-detail-meta">' +
                        '<span><i class="fas fa-user"></i> '+a.nama+'</span>' +
                        '<span><i class="fas fa-calendar-alt"></i> '+a.created_at+'</span>' +
                        statusBadge(a.status) +
                    '</div>' +
                '</div>' +
                '<div class="pengaduan-detail-image">' +
                    '<img src="'+fotoSrc+'" alt="Foto Pengaduan" onerror="this.src=\''+noImage+'\'">' +
                '</div>' +
                '<div class="pengaduan-detail-isi">' +
                    '<p>'+a.isi+'</p>' +
                '</div>';

            if(a.child_count && a.child && a.child.length){
                html += '<div class="pengaduan-replies-section">' +
                    '<h4 class="pengaduan-replies-title"><i class="fas fa-comments"></i> Tanggapan ('+a.child_count+')</h4>';
                a.child.forEach(function(c){
                    html += '<div class="pengaduan-reply-card">' +
                        '<div class="pengaduan-reply-avatar"><i class="fas fa-user-tie"></i></div>' +
                        '<div class="pengaduan-reply-body">' +
                            '<div class="pengaduan-reply-meta"><strong>'+c.nama+'</strong> <span>'+c.created_at+'</span></div>' +
                            '<p>'+c.isi+'</p>' +
                        '</div>' +
                    '</div>';
                });
                html += '</div>';
            }

            $('#pengaduan-judul').text(a.judul);
            $('#pengaduan-detail .pengaduan-modal-body').html(html);
            $('#pengaduan-detail').addClass('show').css('display','block');
            $('body').append('<div class="modal-backdrop fade show"></div>');
        }

        // ── Events ──
        $(document).ready(function(){
            var activeStatus = '';

            // Auto-show form modal if session data exists (validation error)
            var hasData = {{ count(session('data') ?? []) }};
            if(hasData) {
                setTimeout(function(){
                    $('#newpengaduan').addClass('show').css('display','block');
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                }, 300);
            }

            // Status tabs
            $('#pengaduan-status-tabs').on('click', '.pengaduan-tab', function(){
                $(this).addClass('active').siblings().removeClass('active');
                activeStatus = $(this).data('status');
                loadPengaduan(1, activeStatus, $('[name=cari-pengaduan]').val());
            });

            // Search
            $('#btn-search').on('click', function(){
                loadPengaduan(1, activeStatus, $('[name=cari-pengaduan]').val());
            });
            $('[name=cari-pengaduan]').on('keydown', function(e){
                if(e.keyCode === 13){ e.preventDefault(); $('#btn-search').trigger('click'); }
            });

            // Pagination click
            $(document).on('click', '.pengaduan-page', function(e){
                e.preventDefault();
                var page = $(this).data('page');
                loadPengaduan(page, activeStatus, $('[name=cari-pengaduan]').val());
                $('html,body').animate({scrollTop: $('#pengaduan-list').offset().top - 100}, 300);
            });

            // Auto-dismiss flash notifications
            setTimeout(function(){
                $('.alert').not('.pengaduan-modal-body .alert').fadeOut(500, function(){ $(this).remove(); });
            }, 4000);

            loadPengaduan(1);
        });

    })(jQuery);
    </script>
@endpush
