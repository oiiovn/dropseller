@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('register') }}">
  @csrf

  <div class="mb-3">
    <input type="text" name="name" placeholder="Họ và tên" id="name" class="form-control" required value="{{ old('name') }}">
  </div>

  <div class="mb-3">
    <input type="email" name="email" placeholder="Nhập địa chỉ email của bạn" id="email" class="form-control" required value="{{ old('email') }}">
  </div>

  <div class="row mb-3">
    <div class="col-md-6 position-relative mb-3">
      <input type="password" name="password" placeholder="Mật khẩu" id="password" class="form-control" required>
      <i class="fa-solid fa-eye" id="togglePassword" style="position:absolute; top: 15px; right: 25px; cursor:pointer;"></i>
    </div>
    <div class="col-md-6 position-relative">
      <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" id="password_confirmation" class="form-control" required>
      <i class="fa-solid fa-eye" id="togglePasswordConfirm" style="position:absolute; top: 15px; right: 25px; cursor:pointer;"></i>
    </div>
  </div>

  <!-- CAPTCHA -->
  <div class="mb-3">
    <label for="captcha" class="form-label fw-semibold">Mã xác thực <span class="text-danger">*</span></label>
    <div class="captcha-container mb-2">
      <span>{!! Captcha::img('default') !!}</span>
      <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary ms-2">
        <i class="fa fa-refresh"></i> Làm mới mã
      </a>
    </div>
    <input id="captcha" type="text" class="form-control @error('captcha') is-invalid @enderror"
        name="captcha" placeholder="Nhập mã xác thực">
    @error('captcha')
    <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
  </div>

  <button type="submit" class="btn btn-login w-100">Đăng ký</button>
</form> 