@extends('layout')
@section('title', 'main')

@section('main')
<div class="page-content">
    <div class="container-fluid">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#all-products" type="button" role="tab" aria-controls="all-products" aria-selected="true">Đăng sản phẩm</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-products" type="button" role="tab" aria-controls="pending-products" aria-selected="false">Sản phẩm kho Drop</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="all-products" role="tabpanel" aria-labelledby="home-tab">
                <div class="container-fluid">
                    <div class="row pt-3"> 
                        <div class="col-xl-12">
                            <div class="card" style="border: 1px rgb(236, 236, 236) solid">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h4>
                                </div>
                                <div class="card-body">
                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table align-middle table-nowrap table-striped-columns mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 46px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck">
                                                                <label class="form-check-label" for="cardtableCheck"></label>
                                                            </div>
                                                        </th>
                                                        <th scope="col">Ảnh</th>
                                                        <th scope="col">Tên sản phẩm</th>
                                                        <th scope="col">Đã đăng lên</th>
                                                        <th scope="col">Giá</th>
                                                        <th scope="col">Tồn kho</th>
                                                        <th scope="col">Trạng thái</th>
                                                        <th scope="col">Kho sỉ</th>
                                                        <th scope="col" style="width: 150px;">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>Ảnh</td>
                                                        <td>Tên</td>
                                                        <td>lovito</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>hoạt động</td>
                                                        <td>Beveda</td>
                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pending-products" role="tabpanel" aria-labelledby="pending-tab">
                <div class="container-fluid bg-white">
                    <div class="row bg-white pt-3"> 
                        <div class="col-xl-12 bg-white d-flex"> 
                            <div> 
                                <p>Các tab chức năng</p>
                            </div>
                        </div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-xl-12">
                            <div class="card" style="border: 1px rgb(236, 236, 236) solid">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h4>
                                </div>
                                <div class="card-body">
                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table align-middle table-nowrap table-striped-columns mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 46px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck">
                                                                <label class="form-check-label" for="cardtableCheck"></label>
                                                            </div>
                                                        </th>
                                                        <th scope="col">Ảnh</th>
                                                        <th scope="col">Tên sản phẩm</th>
                                                        <th scope="col">Đã đăng lên</th>
                                                        <th scope="col">Giá</th>
                                                        <th scope="col">Tồn kho</th>
                                                        <th scope="col">Trạng thái</th>
                                                        <th scope="col">Kho sỉ</th>
                                                        <th scope="col" style="width: 150px;">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>Ảnh</td>
                                                        <td>Tên</td>
                                                        <td>lovito</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>hoạt động</td>
                                                        <td>Beveda</td>
                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
