@extends('layout')
@section('title', 'main')

@section('main')
    <div class="container-fluid bg-white p-3 ">
        <!-- Các nav này chứa các nội dung bảng khác nhau -->
        <ul class="nav nav-pills mb-3 " id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-success active mx-2" id="pills-all-lich-su-gd-tab" data-bs-toggle="pill" data-bs-target="#pills-all-lich-su-gd" type="button" role="tab" aria-controls="pills-all-lich-su-gd" aria-selected="true">Tất cả giao dịch</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-danger mx-2" id="pills-bill-si-tab" data-bs-toggle="pill" data-bs-target="#pills-bill-si" type="button" role="tab" aria-controls="pills-bill-si" aria-selected="false">Giao dịch đơn sỉ</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-info mx-2" id="pills-nap-so-du-tab" data-bs-toggle="pill" data-bs-target="#pills-nap-so-du" type="button" role="tab" aria-controls="pills-nap-so-du" aria-selected="false">Nạp tiền</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-warning mx-2" id="pills-ads-tab" data-bs-toggle="pill" data-bs-target="#pills-ads" type="button" role="tab" aria-controls="pills-ads" aria-selected="false">Chi tiêu ADS</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-danger mx-2" id="pills-dich-vu-tab" data-bs-toggle="pill" data-bs-target="#pills-dich-vu" type="button" role="tab" aria-controls="pills-dich-vu" aria-selected="false">Hoá đơn dịch vụ</button>
            </li>
        </ul>
        <!-- kết thúc tab -->
        <div class="align-items-center m-1  justify-content-between row text-center text-sm-start">
            <div class="col-sm">
                <div class="text-muted">
                    Hiển thị <span class="fw-semibold">10</span> trên tổng <span class="fw-semibold">524</span> giao dịch
                </div>
            </div>
            <div class="col-sm-auto  mt-3 mt-sm-0">
                <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                    <li class="page-item disabled">
                        <a href="#" class="page-link">←</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link">1</a>
                    </li>
                    <li class="page-item active">
                        <a href="#" class="page-link">2</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link">3</a>
                    </li>
                    <li class="page-item">
                        <a href="#" class="page-link">→</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane fade show active mt-4 m-2" id="pills-all-lich-su-gd" role="tabpanel" aria-labelledby="pills-all-lich-su-gd-tab">
                <div class="table-responsive table-card bg-white ">
                    <div class="container-fluid bg-white">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID giao dịch</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-danger">Chờ thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-danger">Chờ thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">Coppy sản phẩm</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-danger">Chờ thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="tab-pane fade mt-4 m-2" id="pills-bill-si" role="tabpanel" aria-labelledby="pills-bill-si-tab">
                <div class="table-responsive table-card bg-white ">
                    <div class="container-fluid bg-white">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID giao dịch</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-primary">Nhập hàng</span></td>
                                    <td>LOVI120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="tab-pane fade mt-4 m-2" id="pills-nap-so-du" role="tabpanel" aria-labelledby="pills-nap-so-du-tab">
                <div class="table-responsive table-card bg-white ">
                    <div class="container-fluid bg-white">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID giao dịch</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-warning">Nạp web</span></td>
                                    <td>LOVINAP120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="tab-pane fade mt-4 m-2" id="pills-ads" role="tabpanel" aria-labelledby="pills-ads-tab">
                <div class="table-responsive table-card bg-white ">
                    <div class="container-fluid bg-white">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID giao dịch</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-danger">Chi ads</span></td>
                                    <td>LOVIADS120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        <div class="tab-pane fade mt-4 m-2" id="pills-dich-vu" role="tabpanel" aria-labelledby="pills-dich-vu-tab">
        <div class="table-responsive table-card bg-white ">
                    <div class="container-fluid bg-white">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">ID giao dịch</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Nội dung</th>
                                    <th scope="col">Ngày</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">Tối ưu shop</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">Tối ưu shop</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">Coppy sản phẩm</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>FT24342805875856</>
                                    </td>
                                    <td><span class="badge bg-success">GPT</span></td>
                                    <td>LOVIDV120719</td>
                                    <td>2024-12-07 07:04:19</td>
                                    <td>1,099,000 VNĐ</td>
                                    <td><span class="badge bg-success">Đã thanh toán</span></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info">Xem</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</div>


@endsection