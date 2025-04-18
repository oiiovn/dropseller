@extends('layout')
@section('title', 'main')
@section('main')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Dropzone</h4>
            </div>
            <div class="card-body">
                <!-- Mô tả -->
                <p class="text-muted" id="dropzone-description">
                    DropzoneJS is an open source library that provides drag’n’drop file uploads with image previews.
                </p>

                <!-- Khu vực kéo thả file -->
                <div class="dropzone" id="dropzone-area">
                    <div class="fallback">
                        <input name="file" type="file" multiple="multiple">
                    </div>
                    <div class="dz-message needsclick">
                        <div class="mb-3">
                            <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                        </div>
                        <h4>Drop files here or click to upload.</h4>
                    </div>
                </div>

                <!-- Template preview (ẩn đi, Dropzone sẽ clone từ đây) -->
                <div id="dropzone-preview-list" style="display: none;">
                    <div class="border rounded mt-2">
                        <div class="d-flex p-2">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm bg-light rounded">
                                    <img data-dz-thumbnail class="img-fluid rounded d-block"
                                        src="assets/images/new-document.png" alt="Dropzone-Image" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="pt-1">
                                    <h5 class="fs-14 mb-1" data-dz-name>&nbsp;</h5>
                                    <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                    <strong class="error text-danger" data-dz-errormessage></strong>
                                </div>
                            </div>
                            <div class="flex-shrink-0 ms-4">
                                <button data-dz-remove class="btn btn-sm btn-info">Tải lên xử lý</button>
                                <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách file đã thêm -->
                <ul class="list-unstyled mb-0" id="dropzone-preview"></ul>
            </div>
        </div>
    </div>
</div>

<!-- Nút back to top -->
<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
</button>

<!-- Preloader -->
<div id="preloader">
    <div id="status">
        <div class="spinner-border text-primary avatar-sm" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Cài đặt giao diện -->
<div class="customizer-setting d-none d-md-block">
    <div class="btn-info rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas"
        data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
        <i class='mdi mdi-spin mdi-cog-outline fs-22'></i>
    </div>
</div>

<!-- JS -->
<script src="assets/libs/dropzone/dropzone-min.js"></script>
<script>
    Dropzone.autoDiscover = false;

    document.addEventListener("DOMContentLoaded", function () {
        const myDropzone = new Dropzone("#dropzone-area", {
            url: "/upload", // Cập nhật endpoint xử lý
            previewTemplate: document.querySelector("#dropzone-preview-list").innerHTML,
            previewsContainer: "#dropzone-preview",
            clickable: "#dropzone-area",
            init: function () {
                this.on("addedfile", function () {
                    document.getElementById("dropzone-area").style.display = "none";
                    document.getElementById("dropzone-description").style.display = "none";
                });

                this.on("removedfile", function () {
                    if (this.files.length === 0) {
                        document.getElementById("dropzone-area").style.display = "block";
                        document.getElementById("dropzone-description").style.display = "block";
                    }
                });
            }
        });
    });
</script>

@endsection
