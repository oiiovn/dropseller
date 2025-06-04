@extends('layout')

@section('title', 'Danh sách giới thiệu')

@section('main')
<div class="container-fluid py-3">
    <h3 class="text-muted fw-normal mb-3">Danh sách người được giới thiệu</h3>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Ngày đăng ký</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($referrals as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Chưa có người được giới thiệu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
