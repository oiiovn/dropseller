@extends('layouts.auth')

@section('title', 'Đăng ký | Dropships.vn')

@section('content')

<div class="layout-wrapper">
  <!-- LEFT SIDE -->
  @include('components.auth.login.login-logo-section')
  @include('components.auth.login.login-left-section')

  <!-- RIGHT SIDE -->
  @include('components.auth.register.register-right-section')
</div>

@endsection