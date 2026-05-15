@if (session('notif'))
    <div class="alert alert-{{ session('notif.type', 'info') }}" role="alert">
        <i class="fas fa-{{ session('notif.type') === 'success' ? 'check-circle' : (session('notif.type') === 'danger' ? 'exclamation-circle' : 'info-circle') }}"></i>
        {{ session('notif.message', session('notif')) }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
