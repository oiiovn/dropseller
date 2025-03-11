@extends('layout')
@section('title', 'main')

@section('main')


<div class="col-xl-12 " style="padding-top:-50px;">
    <div class="card  h-75">
        <div class="card-header">
            <h4 class="card-title mb-0">Tính <a class="text-danger"> % </a> giảm giá theo chiến dịch Tiktok Shop !</h4>
        </div><!-- end card header -->
        <div class="card-body">
            <form action="#" class="form-steps" autocomplete="off">
                <div class="text-center pt-3 pb-4 mb-1 d-flex justify-content-center">
                    <h1>CÔNG THỨC TÍNH %</h1>
                    <img src="assets/images/logo-light.png" class="card-logo card-logo-light" alt="logo light" height="17">
                </div>
                <div class="step-arrow-nav mb-4">

                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="steparrow-description-info-tab" data-bs-toggle="pill" data-bs-target="#steparrow-description-info" type="button" role="tab" aria-controls="steparrow-description-info" aria-selected="false">Toàn chiến dịch</button>
                        </li>
                        <!-- <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-experience-tab" data-bs-toggle="pill" data-bs-target="#pills-experience" type="button" role="tab" aria-controls="pills-experience" aria-selected="false">Theo sản phẩm</button>
                        </li> -->
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="steparrow-description-info" role="tabpanel" aria-labelledby="steparrow-description-info-tab">
                        <div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="mb-3">
                                        <label class="form-label" for="shop-discount">Shop</label>
                                        <input type="text" class="form-control" id="shop-discount" placeholder="% Hiện tại của chiết khấu sản phẩm" required oninput="tinhToanGiamGia()">
                                        <div class="invalid-feedback">Vui lòng nhập số % đã giảm trong chiết khấu shop</div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="mb-3">
                                        <label class="form-label" for="tiktok-discount">Tiktok</label>
                                        <input type="text" class="form-control" id="tiktok-discount" placeholder="Nhập % chiến dịch tiktok" required oninput="tinhToanGiamGia()">
                                        <div class="invalid-feedback">Vui lòng nhập % mà chiến dịch yêu cầu</div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="mb-3">
                                        <label class="form-label">Kết quả</label>
                                        <h3 class="" id="ket-qua-giam-gia">0%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end tab pane -->
                <!-- <div class="tab-pane fade" id="pills-experience" role="tabpanel">
                    <div>
                        <div class="row">
                            <div class="col-lg-1">
                                <div class="mb-3">
                                    <label class="form-label" for="steparrow-gen-info-email-input">Giá gốc</label>
                                    <input type="email" class="form-control" id="steparrow-gen-info-email-input" placeholder="Giá đăng của sản phẩm" required>
                                    <div class="invalid-feedback">Vui lòng nhập giá gốc của sản phẩm</div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="mb-3">
                                    <label class="form-label" for="steparrow-gen-info-email-input">Chiết khấu shop</label>
                                    <input type="email" class="form-control" id="steparrow-gen-info-email-input" placeholder="Chiết khấu sản phẩm" required>
                                    <div class="invalid-feedback">Please enter an email address</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3 ">
                                    <label class="form-label" for="steparrow-gen-info-username-input">User Name</label>
                                    <input type="text" class="form-control" id="steparrow-gen-info-username-input" placeholder="Enter user name" required>
                                    <div class="invalid-feedback">Please enter a user name</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3 mt-4">
                        <button type="button" class="btn btn-success btn-label right ms-auto nexttab nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Go to more info</button>
                    </div>
                </div> -->
                <!-- end tab pane -->
        </div>
        <!-- end tab content -->
        </form>
    </div>
    <!-- end card body -->
</div>
<!-- end card -->
</div>
<script>
    function tinhToanGiamGia() {
        let giaGoc = 100000; 
        let giamGiaShop = parseFloat(document.getElementById("shop-discount").value) || 0;
        let giamGiaTiktok = parseFloat(document.getElementById("tiktok-discount").value) || 0;
        let giaSauGiamShop = giaGoc * (1 - giamGiaShop / 100);
        let giaSauGiamChienDich = giaSauGiamShop + (giaSauGiamShop / 100 * giamGiaTiktok);
        let phanTramGiamDung = 100 - (giaSauGiamChienDich / giaGoc * 100) + 1;
        document.getElementById("ket-qua-giam-gia").innerText = phanTramGiamDung.toFixed(0) + "(%)";
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function handlePercentageInput(input) {
            input.addEventListener("focus", function() {
                input.value = input.value.replace(" (%)", "").trim();
            });
            input.addEventListener("input", function() {
                let value = input.value.replace(/[^0-9]/g, ""); 
                if (value !== "" && parseInt(value) > 50) {
                    value = "50";
                }
                input.value = value; 
            });
            input.addEventListener("blur", function() {
                if (input.value !== "") {
                    input.value = input.value + " (%)";
                }
            });
        }
        handlePercentageInput(document.getElementById("shop-discount"));
        handlePercentageInput(document.getElementById("tiktok-discount"));
    });
</script>

@endsection