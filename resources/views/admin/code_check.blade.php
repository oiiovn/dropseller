@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Form kiểm tra mã truy cập admin -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Kiểm tra mã truy cập Admin</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.verify_access_code') }}" method="post" class="form-inline">
                        @csrf
                        <div class="form-group mr-2">
                            <label for="access_code" class="mr-2">Mã truy cập:</label>
                            <input type="text" name="access_code" id="access_code" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác thực</button>
                    </form>
                    
                    @if(session('access_code_status'))
                        <div class="alert alert-{{ session('access_code_status') == 'success' ? 'success' : 'danger' }} mt-3">
                            {{ session('access_code_message') }}
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <small class="text-muted">Mã truy cập được sử dụng để xác thực quyền quản trị cao cấp.</small>
                    </div>
                </div>
            </div>
            
            <!-- Nội dung kiểm tra mã giới thiệu (hiện tại) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kiểm tra mã giới thiệu</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.fix_invalid_codes') }}" class="btn btn-primary btn-sm" 
                           onclick="return confirm('Bạn có chắc chắn muốn sửa tất cả mã không hợp lệ?');">
                            Sửa mã không hợp lệ
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <div class="info-box-content">
                                    <span class="info-box-text">Tổng số mã</span>
                                    <span class="info-box-number">{{ $results['stats']['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <div class="info-box-content">
                                    <span class="info-box-text">Số mã duy nhất</span>
                                    <span class="info-box-number">{{ $results['stats']['unique'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <div class="info-box-content">
                                    <span class="info-box-text">Số mã trùng lặp</span>
                                    <span class="info-box-number">{{ $results['stats']['duplicates'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($results['duplicate_codes']) > 0)
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Mã trùng lặp</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Số lần xuất hiện</th>
                                        <th>Người dùng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['duplicate_codes'] as $code => $count)
                                    <tr>
                                        <td>{{ $code }}</td>
                                        <td>{{ $count }}</td>
                                        <td>
                                            @php
                                                $usersWithCode = $users->where('referral_code', $code);
                                                foreach($usersWithCode as $user) {
                                                    echo $user->id . ": " . $user->name . " (" . $user->email . ")<br>";
                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if(count($results['invalid_format']) > 0)
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Mã không đúng định dạng</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Mã</th>
                                        <th>Vấn đề</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['invalid_format'] as $item)
                                    <tr>
                                        <td>{{ $item['user_id'] }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['email'] }}</td>
                                        <td>{{ $item['code'] }}</td>
                                        <td>{{ $item['issue'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if(!empty($results['transaction_conflicts']))
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Xung đột với mã giao dịch</h3>
                        </div>
                        <div class="card-body">
                            <ul>
                                @foreach($results['transaction_conflicts'] as $code)
                                <li>{{ $code }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
