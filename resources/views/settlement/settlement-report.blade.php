@extends('layout')
@section('title', 'main')

@section('main')
<div class="container-fluid" style=" width: 100%; background: white; ">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                <div class="col-4 bg-info p-1 m-1">
                    <header class="card-header p-1 ">
                        <h5 class="card-title d-flex align-items-center">
                            <i class="ri-file-list-3-line "></i> Báo cáo quyết toán
                        </h5>
                    </header>
                    <table class="table table-nowrap">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Tháng</th>
                                <th scope="col">Thời gian</th>
                                <th scope="col">Tổng</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row"><a href="#" class="fw-semibold">#VZ2110</a></th>
                                <td>Bobby Davis</td>
                                <td>October 15, 2021</td>
                                <td>$2,300</td>
                                <td><a href="javascript:void(0);" class="link-success">View More <i class="ri-arrow-right-line align-middle"></i></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-4 bg-danger p-1 m-1">
                    <header class="card-header p-1 ">
                        <h5 class="card-title d-flex align-items-center">
                            <i class="ri-alert-line"></i> Alert Section
                        </h5>
                    </header>
                    <p class="text-white">This is a danger alert section.</p>
                </div>
            </div>
        </div>
    </div>
    @endsection