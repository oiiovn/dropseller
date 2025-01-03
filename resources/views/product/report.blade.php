@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product Report</h1>
    @if(isset($report['data']['product_report']))
        <table class="table">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['data']['product_report'] as $product)
                    <tr>
                        <td>{{ $product['product_id'] }}</td>
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ $product['quantity_sold'] }}</td>
                        <td>{{ $product['total_revenue'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No data available for the selected time period.</p>
    @endif
</div>
@endsection