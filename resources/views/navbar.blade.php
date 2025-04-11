<div class="app-menu navbar-menu ">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Light Logo -->
        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="https://img.icons8.com/?size=100&id=rfO6JiaCcd8a&format=png&color=000000" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="https://img.icons8.com/?size=100&id=rfO6JiaCcd8a&format=png&color=000000" alt="" height="58">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="assets/images/users/avatar-1.jpg" alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text">
                        <i class="ri ri-circle-fill fs-10 text-success align-baseline"></i>
                        <span class="align-middle">Online</span>
                    </span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">Welcome Anna!</h6>
            <a class="dropdown-item" href="pages-profile.html">
                <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Profile</span>
            </a>
            <a class="dropdown-item" href="apps-chat.html">
                <i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Messages</span>
            </a>
            <a class="dropdown-item" href="apps-tasks-kanban.html">
                <i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Taskboard</span>
            </a>
            <a class="dropdown-item" href="pages-faqs.html">
                <i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Help</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages-profile.html">
                <i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Balance: <b>$5971.67</b></span>
            </a>
            <a class="dropdown-item" href="pages-profile-settings.html">
                <span class="badge bg-success-subtle text-success mt-1 float-end">New</span>
                <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Settings</span>
            </a>
            <a class="dropdown-item" href="auth-lockscreen-basic.html">
                <i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle">Lock screen</span>
            </a>
            <a class="dropdown-item" href="auth-logout-basic.html">
                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
                <span class="align-middle" data-key="t-logout">Logout</span>
            </a>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title">
                    <span data-key="t-menu">Menu</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link ajax-link" href="{{route('dashboard')}}">
                        <i class="ri-dashboard-2-line"></i>
                        <span>Dashboards</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#donhang" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class=" ri-shopping-bag-3-line"></i>
                        <span data-key="t-apps">Quản Lý đơn hàng</span>
                    </a>
                    <div class="collapse menu-dropdown" id="donhang">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('order_si')}}" class="nav-link ajax-link" data-key="t-chat">Đơn hàng sỉ</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('quang_cao_shop')}}" class="nav-link ajax-link" data-key="t-chat">Quảng cáo</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#Push_sp" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-archive-fill"></i>
                        <span data-key="t-apps">Dịch Vụ DropShip</span>
                    </a>
                    <div class="collapse menu-dropdown" id="Push_sp">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('list_program')}}" class="nav-link ajax-link" data-key="t-chat">Đăng sản phẩm</a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#thanhtoan" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-money-dollar-circle-line"></i>
                        <span data-key="t-apps">Thanh toán</span>
                    </a>
                    <div class="collapse menu-dropdown" id="thanhtoan">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="nav-link ajax-link" id="openNapTienModal">Nạp</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('transaction') }}" class="nav-link ajax-link" data-key="t-chat">Lịch sử giao dịch</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('balance.history') }}" class="nav-link ajax-link" data-key="t-chat">Biến động số dư</a>
                            </li>

                            <!--
                            <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                        </ul>
                    </div>
                </li>
                @if(Auth::check() && Auth::user()->role == '2')
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                        <i class="ri-layout-3-line"></i>
                        <span data-key="t-layouts">Quản lý khách hàng</span>

                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{route('Get_all')}}" class="nav-link ajax-link" data-key="t-horizontal">Thông tin khách hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('naptien_khach_hang') }}">Nạp tiền cho khách hàng</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('transaction_all')}}" class="nav-link ajax-link" data-key="t-chat">Lịch sử nạp </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('Get_orders_all')}}" class="nav-link ajax-link" data-key="t-detached">Tất cả đơn hàng</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('get_all_transaction.list')}}" class="nav-link ajax-link" data-key="t-detached">Lịch sử giao dịch drop</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('Overdue_Order')}}" class="nav-link ajax-link" data-key="t-chat">Đơn hàng trễ thanh toán </a>

                            </li>
                            <li class="nav-item">
                                <a href="{{route('productsss')}}" class="nav-link ajax-link" data-key="t-chat">Lọc</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('shops')}}" class="nav-link ajax-link" data-key="t-chat">Shop</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-apps-2-line"></i>
                        <span data-key="t-apps">Quản lý dịch vụ Drops</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarApps">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#sidebarCalendar" class="nav-link " data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCalendar" data-key="t-calender">
                                    Copy sản phẩm
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarCalendar">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('program_view')}}" class="nav-link ajax-link" data-key="t-main-calender">Tạo gói sản phẩm</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{route('procerssing')}}" class="nav-link ajax-link">Thêm sản phẩm lên shop</a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a href="{{route('list_program')}}" class="nav-link" data-key="t-month-grid">Danh sách sản phẩm</a>
                                        </li> -->
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('quang-cao')}}" class="nav-link ajax-link" data-key="t-chat"> Thêm Quảng Cáo</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('quang_cao_all')}}" class="nav-link ajax-link" data-key="t-chat"> Tất Cả Quảng cáo</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{route('get_SI_transaction.list')}}" class="nav-link ajax-link" data-key="t-detached"> <i class="ri-layout-3-line"></i>Thanh toán hoá đơn Sỉ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.balance_issues.index') }}">
                        📉 Lỗi số dư AI
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#drop" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="drop">
                        <i class="ri-apps-2-line"></i>
                        <span data-key="t-apps">Doanh thu Web</span>
                    </a>
                    <div class="collapse menu-dropdown" id="drop">
                        <ul class="nav nav-sm flex-column">
                </li>
                <li class="nav-item">
                    <a href="{{route('view_total_bill')}}" class="nav-link ajax-link">Phí Drop & Gói sản phẩm</a>
                </li>

                <!-- <li class="nav-item">
                                <a href="#sidebarEmail" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarEmail" data-key="t-email">Email</a>
                                <div class="collapse menu-dropdown" id="sidebarEmail">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-mailbox.html" class="nav-link" data-key="t-mailbox">Mailbox</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebaremailTemplates" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebaremailTemplates" data-key="t-email-templates">Email Templates</a>
                                            <div class="collapse menu-dropdown" id="sidebaremailTemplates">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="apps-email-basic.html" class="nav-link" data-key="t-basic-action">Basic Action</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="apps-email-ecommerce.html" class="nav-link" data-key="t-ecommerce-action">Ecommerce Action</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li> -->
            </ul>
        </div>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link menu-link" href="#congcu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarLayouts">
                <i class="ri-layout-3-line"></i>
                <span data-key="t-layouts">Công cụ tiện ích</span>

            </a>
            <div class="collapse menu-dropdown" id="congcu">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="{{route('campaign')}}" class="nav-link ajax-link" data-key="t-horizontal">Tính phần trăm chiến dịch</a>
                    </li>
                    <!-- <li class="nav-item">
                                <a href="layouts-detached.html" target="_blank" class="nav-link" data-key="t-detached">Detached</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-two-column.html" target="_blank" class="nav-link" data-key="t-two-column">Two Column</a>
                            </li>
                            <li class="nav-item">
                                <a href="layouts-vertical-hovered.html" target="_blank" class="nav-link" data-key="t-hovered">Hovered</a>
                            </li> -->
                </ul>
            </div>
        </li>
        </ul>
    </div>

</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#openNapTienModal').click(function() {
            $.get('{{ route("naptien") }}', function(data) {
                // Thêm modal vào body nếu chưa tồn tại
                if ($('#napTienModal').length === 0) {
                    $('body').append(data);
                }

                // Hiển thị modal
                const napTienModal = new bootstrap.Modal(document.getElementById('napTienModal'));
                napTienModal.show();
            });
        });
    });
</script>