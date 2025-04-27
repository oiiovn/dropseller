Here are the contents for the file: /dropseller/dropseller/resources/views/tools/add-sku-name.blade.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add SKU Name</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>Add SKU Name</h1>
        <form action="{{ route('sku.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="sku_name" class="form-label">SKU Name</label>
                <input type="text" class="form-control" id="sku_name" name="sku_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add SKU</button>
        </form>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>