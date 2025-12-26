<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Forms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 300px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .container form {
            display: flex;
            flex-direction: column;
        }
        .container form input {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .container form button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .container form button:hover {
            background-color: #0056b3;
        }
        .container .switch {
            text-align: center;
            margin-top: 10px;
        }
        .container .switch a {
            color: #007BFF;
            text-decoration: none;
        }
        .alert {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
    </style>
</head>
<body>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    <div class="container" id="register-form">
        <h2>Register</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <input type="text" name="referral_code" placeholder="Referral Code (Optional)">
            
            <!-- Form đăng ký -->
<div class="form-group mb-3">
    <label for="captcha-register">CAPTCHA <span class="text-danger">*</span></label>
    <div class="captcha-container">
        <span>{!! captcha_img('math') !!}</span>
        <button type="button" class="btn btn-danger refresh-captcha" id="refresh-captcha-register">
            <i class="fa fa-refresh"></i>
        </button>
    </div>
    <input id="captcha-register" type="text" class="form-control @error('captcha') is-invalid @enderror" name="captcha" placeholder="Nhập kết quả toán học">
    @error('captcha')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

            <button type="submit">Register</button>
        </form>
        
        <div class="switch">
            <p>Already have an account? <a href="#" onclick="showLoginForm()">Login</a></p>
        </div>
    </div>

    <div class="container" id="login-form" style="display: none;">
        <h2>Login</h2>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <!-- Form đăng nhập -->
<div class="form-group mb-3">
    <label for="captcha">CAPTCHA <span class="text-danger">*</span></label>
    <div class="captcha-container">
        <span>{!! captcha_img('math') !!}</span>
        <button type="button" class="btn btn-danger refresh-captcha" id="refresh-captcha-login">
            <i class="fa fa-refresh"></i>
        </button>
    </div>
    <input id="captcha" type="text" class="form-control @error('captcha') is-invalid @enderror" name="captcha" placeholder="Nhập kết quả toán học">
    @error('captcha')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

            <button type="submit">Login</button>
        </form>
        <div class="switch">
            <p>Don't have an account? <a href="#" onclick="showRegisterForm()">Register</a></p>
        </div>
    </div>

    <div class="container" id="logout-form" style="display: none;">
        <h2>Logout</h2>
        <form action="/api/logout" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>

    <script>
        function showRegisterForm() {
            document.getElementById('register-form').style.display = 'block';
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('logout-form').style.display = 'none';
        }

        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('logout-form').style.display = 'none';
        }

        function showLogoutForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('logout-form').style.display = 'block';
        }
    </script>
    @section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#refresh-captcha-login, #refresh-captcha-register').click(function() {
            $.ajax({
                type: 'GET',
                url: '{{ route("auth.refresh.captcha") }}',
                success: function(data) {
                    $(this).closest('.captcha-container').find('span').html(data.captcha);
                }
            });
        });
    });
</script>
@endsection
</body>
</html>
