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

<form method="POST" action="{{ route('login') }}">
  @csrf

  <div class="mb-3">
    <label for="email" class="form-label fw-semibold">Nhập địa chỉ email của bạn</label>
    <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
  </div>

  <div class="mb-3 position-relative">
    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
    <input type="password" name="password" id="password" class="form-control" required>
    <i class="fa-solid fa-eye" id="togglePassword" style="position:absolute; top: 45px; right: 16px; cursor:pointer;"></i>
    @if ($errors->has('password'))
      <div class="text-danger mt-1">Mật khẩu không đúng hoặc không hợp lệ.</div>
    @endif
  </div>

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
      <label class="form-check-label" for="remember">Ghi nhớ</label>
    </div>
    <a href="{{ route('password.request') }}" class="text-small text-decoration-none fw-regular" style="color: #0089ED;">Quên mật khẩu?</a>
  </div>

  <button type="submit" class="btn btn-login w-100">Đăng nhập</button>
</form> 