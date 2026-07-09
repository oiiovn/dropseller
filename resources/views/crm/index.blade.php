<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Himawari CRM</title>
    @vite(['resources/css/crm.css', 'resources/js/crm/main.jsx'])
</head>
<body>
    <div id="crm-root"></div>
</body>
</html>
