<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Himawari Admin</title>
    @vite(['resources/css/admin.css', 'resources/js/admin/main.jsx'])
</head>
<body>
    <div id="admin-root"></div>
</body>
</html>
