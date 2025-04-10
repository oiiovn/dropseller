@extends('layout')
@section('title', 'Tất cả giao dịch theo mã giới thiệu')

@section('main')
<style>
    .nav-tabs .nav-link {
        border: none;
        background-color: #f1f1f1;
        margin-right: 4px;
        border-radius: 6px 6px 0 0;
        color: #444;
        padding: 10px 16px;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-bottom-color: transparent;
        color: #0d6efd;
        font-weight: bold;
    }

    .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        padding: 20px;
        border-radius: 0 0 6px 6px;
        background-color: #fff;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .table th {
        font-weight: 600;
        color: #333;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f2f2f2;
    }

    h5.mb-3 {
        font-weight: bold;
        color: #333;
    }

    .text-muted {
        font-style: italic;
    }
</style>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="referralTab" role="tablist">
                @foreach($dd_si as $code => $data)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if($loop->first) active @endif" id="tab-{{ Str::slug($code) }}" data-bs-toggle="tab" href="#content-{{ Str::slug($code) }}" role="tab">
                            {{ $code }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content pt-3" id="referralTabContent">
                @foreach($dd_si as $code => $data)
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="content-{{ Str::slug($code) }}" role="tabpanel">
                        <h5 class="mb-3">Mã giới thiệu: {{ $code }}</h5>
                        @if($data['transactions']->isEmpty())
                            <p class="text-muted">Không có giao dịch nào.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Mô tả</th>
                                            <th>Số tiền</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['transactions'] as $transaction)
                                            <tr>
                                                <td>{{ $transaction->id }}</td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>{{ number_format($transaction->amount, 0, ',', '.') }} đ</td>
                                                <td>{{ $transaction->created_at }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection