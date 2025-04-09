<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="index.html" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-dark.png" alt="" height="17">
                        </span>
                    </a>
                    <a href="index.html" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-light.png" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <!-- <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Tìm kiếm..." autocomplete="off" id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div> -->
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                            </div>

                            <div class="dropdown-item bg-transparent text-wrap">
                                <a href="index.html" class="btn btn-soft-secondary btn-sm rounded-pill">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a href="index.html" class="btn btn-soft-secondary btn-sm rounded-pill">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>

                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                            </div>

                            <div class="notification-list">
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-2.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="fs-11 mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a href="javascript:void(0);" class="dropdown-item notify-item py-2">
                                    <div class="d-flex">
                                        <img src="assets/images/users/avatar-5.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="fs-11 mb-0 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-3 pb-1">
                            <a href="pages-search-results.html" class="btn btn-primary btn-sm">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex align-items-center">
                <div>
                    <a class="align-items-center " href="{{route('balance.history')}}"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Số dư : <b class="text-success">{{ number_format($totalAmount, 0, ',', '.'); }} đ</b></span></a>
                    <i class=" text-body-tertiary ">(Chờ đối soát: {{ number_format($balace, 0, ',', '.'); }} đ )</i>
                </div>
                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img id="header-lang-img" src="assets/images/flags/vn.svg" alt="Header Language" height="20" class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item language py-2" data-lang="en" title="English">
                            <img src="assets/images/flags/vn.svg" alt="user-image" class="me-2 rounded" height="18">
                            <span class="align-middle">Tiếng Việt</span>
                        </a>
                    </div>
                </div>
                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        @if($unreadNotificationsCount > 0)
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">{{$unreadNotificationsCount ?? 0}}<span class="visually-hidden">unread messages</span></span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-white rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-black"> Thông báo </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge bg-light text-body fs-13"> {{$unreadNotificationsCount ?? 0}} Mới</span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 pt-2">

                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Tất Cả ({{$NotificationsCount ?? 0}})</button>
                                        <button id="markReadButton" class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                                            Thông báo mới ({{$unreadNotificationsCount ?? 0}})
                                        </button>
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                        <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                                @if($Notifications && $Notifications->count() > 0)
                                                @foreach($Notifications as $notification)
                                                <div class="text-reset notification-item d-block dropdown-item position-relative" id="notification-{{ $notification->id }}" data-id="{{ $notification->id }}">
                                                    <div class="d-flex">
                                                        <div class="avatar-xs me-3 flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                                @if(isset($notification->image))
                                                                <img src="{{ $notification->image }}" class="rounded-circle mt-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                                                @else
                                                                <i class="bx bx-badge-check"></i>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <a href="#!" class="stretched-link">
                                                                <h6 class="mt-0 mb-2 lh-base"> {{$notification->shop->shop_name ?? ''}} <b></b><span class="text-secondary">{{$notification->message}}</span></h6>
                                                            </a>
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                @php
                                                                $created_at = $notification->created_at;
                                                                $now = \Carbon\Carbon::now();
                                                                @endphp

                                                                <span>
                                                                    <i class="mdi mdi-clock-outline"></i>
                                                                    @if ($created_at->diffInDays($now) > 3)
                                                                    {{ $created_at->translatedFormat('d/m/Y H:i') }}
                                                                    @else
                                                                    {{ $created_at->diffForHumans() }}
                                                                    @endif
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach

                                                @else
                                                <p>Không có thông báo nào</p>
                                                @endif



                                                <!-- <div class="my-3 text-center view-all">
                                        <button type="button" class="btn btn-soft-success waves-effect waves-light">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></button>
                                    </div> -->
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                            <div data-simplebar style="max-height: 300px;" class="pe-2">
                                                @if($unreadNotifications && $unreadNotifications->count() > 0)
                                                @foreach($unreadNotifications as $notification )
                                                <div class="text-reset notification-item d-block dropdown-item position-relative">


                                                    <div class="d-flex">

                                                        <div class="avatar-xs me-3 flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                                @if(isset($notification->image))
                                                                <img src="{{ $notification->image }}" class="rounded-circle mt-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ddd;">
                                                                @else
                                                                <i class="bx bx-badge-check"></i>
                                                                @endif

                                                            </span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <a href="#!" class="stretched-link">
                                                                <h6 class="mt-0 mb-2 lh-base"> {{$notification->shop->shop_name ?? ''}} <b>


                                                                    </b><span class="text-secondary">{{$notification->message}}</span>
                                                                </h6>
                                                            </a>
                                                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                                @php
                                                                $created_at = $notification->created_at;
                                                                $now = \Carbon\Carbon::now();
                                                                @endphp

                                                                <span>
                                                                    <i class="mdi mdi-clock-outline"></i>
                                                                    @if ($created_at->diffInDays($now) > 3)
                                                                    {{ $created_at->translatedFormat('d/m/Y H:i') }} <!-- Hiển thị ngày cụ thể sau 3 ngày -->
                                                                    @else
                                                                    {{ $created_at->diffForHumans() }} <!-- Hiển thị "X phút trước", "X giờ trước" -->
                                                                    @endif
                                                                </span>


                                                            </p>
                                                        </div>


                                                    </div>

                                                </div>
                                                @endforeach
                                                @else
                                                <p class="text-muted">Không có thông báo nào</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>



                        </div>


                    </div>
                </div>

                <div class="dropdown ms-sm-3 header-item topbar-user bg-white">
                    <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src=" 
                            @if (Auth::check() && Auth::user()->image)
                                    {{ Auth::user()->image }}
                                    @else
                                    https://img.icons8.com/ios-filled/100/user-male-circle.png
                                    @endif
                                    " alt="Header Avatar" style="width: 40px; height: 40px; object-fit: cover;">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    @if (Auth::check())
                                    {{ Auth::user()->name }}
                                    @else
                                    Welcome, Guest
                                    @endif
                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text border rounded p-1" style="background-color: #faa887; color: black;">Nhà Bán Mới</span>

                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->

                        <a class="dropdown-item" href="{{route('portfolio')}}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Hồ Sơ</span></a>
                        <a class="dropdown-item" href="{{route('balance.history')}}"> <span class="align-middle">  💰    Biến động số dư</span></a>
                        @if(Auth::check() && Auth::user()->role == '2') 
                        <a class="dropdown-item" href="{{route('shop')}}"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Shop</span></a>
                        @endif
                        <!-- <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Cài đặt</span></a>
                        <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{route('transaction')}}"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Số dư : <b class="text-success ">856,847 đ</b></span></a>
                        <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
                        <a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a> -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle" data-key="t-logout">Đăng xuất</span>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</header>