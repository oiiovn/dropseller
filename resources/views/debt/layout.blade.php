<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý nợ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --debt-primary: #0d6efd; --debt-sidebar: #1a1d29; --debt-card: #fff; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f0f2f5; min-height: 100vh; }
        .debt-nav { background: var(--debt-sidebar); color: #fff; }
        .debt-nav a { color: rgba(255,255,255,.85); text-decoration: none; padding: .5rem 1rem; display: block; }
        .debt-nav a:hover, .debt-nav a.active { color: #fff; background: rgba(255,255,255,.1); }
        .debt-card { background: var(--debt-card); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .debt-table th { font-weight: 600; color: #495057; }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-paid { background: #198754; color: #fff; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex min-vh-100">
        @hasSection('sidebar')
            <nav class="debt-nav flex-shrink-0" style="width: 220px;">
                <div class="p-3 border-bottom border-secondary">
                    <strong>Quản lý nợ</strong>
                </div>
                @yield('sidebar')
            </nav>
        @endif
        <main class="flex-grow-1 p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
