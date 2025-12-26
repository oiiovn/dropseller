@extends('layouts.auth')

@section('title', 'Đăng nhập | Dropships.vn')

@section('content')

<div class="layout-wrapper">
  <!-- LEFT SIDE -->
  @include('components.auth.login.login-logo-section')
  @include('components.auth.login.login-left-section')

  <!-- RIGHT SIDE -->
  @include('components.auth.login.login-right-section')
</div>

@endsection
