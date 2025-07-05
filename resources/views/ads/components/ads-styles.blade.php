{{-- Component: Ads Styles --}}
<style>
    /* Ads Page Styles - Based on Order Page Styles */

    /* Enhanced Table Styling with Sticky Header & Footer */
    .modern-table-container {
        background: #fff;
        border-radius: 6px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e8eaed;
        position: relative;
        max-height: 85vh;
        overflow: visible;
    }

    .table-wrapper {
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        width: 100%;
      
        position: relative;
        border-radius: 6px;
    }

    /* Pagination Container - nằm ngoài table */
    .pagination-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e8eaed;
        border-top: none;
        padding: 0px 5px;
        border-radius: 0 0 6px 6px;
        position: relative;
        z-index: 1;
        width: 100%;
        margin-top: -1px;
        min-height: 40px;
        display: flex;
        align-items: center;
    }
    .pagination-container .row {
        width: 100%;
        margin: 0;
        align-items: center;
    }
    .pagination-container .col-4 {
        padding: 0px;
    }
    @media (max-width: 768px) {
        .pagination-container {
            display: none !important;
        }
    }

    .modern-table {
        margin: 0;
        border: none;
        font-size: 14px;
        position: relative;
    }
    .modern-table .table-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .modern-table .table-header th {
        padding: 8px 12px;
        font-weight: 600;
        color: #495057;
        border: none;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
    }
    .modern-table .table-body tr {
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.2s ease;
    }
    .modern-table .table-body tr:hover {
        background-color: #f8f9ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .modern-table .table-body td {
        padding: 14px 12px;
        vertical-align: middle;
        border: none;
        color: #777777;
    }
    /* Invoice Code Styling */
    .invoice-code-cell {
        position: relative;
    }
    .invoice-code-main {
        font-weight: 600;
        color: #5893e0;
        font-size: 15px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .invoice-code-date {
        font-size: 11px;
        color: #5f6368;
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
    }
    /* Shop Info */
    .shop-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .shop-platform-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
    }
    /* Status Pills */
    .status-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        letter-spacing: 0.5px;
        display: inline-block;
        min-width: 90px;
    }
    .status-paid {
        color: #009522;
    }
    .status-unpaid {
        color: #ff7777;
    }
    .status-reconciled {
        color: #009522;
    }
    .status-not-reconciled {
        color: #ff7777;
    }
    /* Amount Styling */
    .amount-cell {
        text-align: right;
        font-weight: 600;
        color: #1a73e8;
        font-size: 14px;
    }
    .quantity-cell {
        text-align: center;
        font-weight: 600;
        color: #34a853;
        background: #f0f8f0;
        border-radius: 6px;
        padding: 8px;
        font-size: 14px;
    }
    /* Copy functionality */
    .hienthicopy {
        display: flex;
        cursor: pointer;
        position: relative;
    }
    .hienthicopy .icon {
        display: block;
        cursor: pointer;
        opacity: 0;
        transition: all 0.3s ease;
        flex-shrink: 0;
        font-size: 14px;
        color: #5f6368;
        pointer-events: all;
    }
    .hienthicopy:hover .icon {
        display: flex;
        opacity: 1;
        color: #1a73e8;
    }
    /* Filter Section */
    .filter-section {
       padding: 8px;
    }
    .filter-section .form-control,
    .filter-section .form-select {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 2px 6px;
        transition: all 0.3s ease;
        font-size: 12px;
    }
    .filter-section .form-control:focus,
    .filter-section .form-select:focus {
        border-color: #3F87B1;
        box-shadow: 0 0 0 0.2rem rgba(63, 135, 177, 0.25);
    }
    .filter-section .btn {
        padding: 1px 8px;
        font-size: 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .filter-section .btn-primary {
        background: #3F87B1;
        border-color: #3F87B1;
    }
    .filter-section .btn-primary:hover {
        background: #2d6a8a;
        border-color: #2d6a8a;
    }
    /* No Data Message */
    .no-data-message {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px dashed #dee2e6;
    }
    .no-data-message:hover {
        border-color: #3F87B1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .no-data-message i {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 16px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .no-data-message .btn-outline-primary {
        border-color: #3F87B1;
        color: #3F87B1;
        margin-top: 16px;
    }
    .no-data-message .btn-outline-primary:hover {
        background: #3F87B1;
        border-color: #3F87B1;
        color: white;
    }
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .filter-section {
            display: none;
        }
        .modern-table-container{
            margin: 0;
        }
        .mobile-filter-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: #3F87B1;
            color: white;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .mobile-filter-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0,0,0,0.4);
        }
        .mobile-filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .mobile-filter-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .filter-section.mobile-show {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 20px;
            z-index: 1000;
            transform: translateY(100%);
            transition: transform 0.3s ease;
            max-height: 80vh;
            overflow-y: auto;
        }
        .filter-section.mobile-show.show {
            transform: translateY(0);
        }
        .mobile-filter-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: #6c757d;
            cursor: pointer;
        }
        .mobile-filter-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
            text-align: center;
        }
        body.modal-open {
            overflow: hidden;
        }
        .filter-section.mobile-show .row {
            margin: 0;
        }
        .filter-section.mobile-show .form-control,
        .filter-section.mobile-show .form-select {
            margin-bottom: 15px;
            font-size: 16px;
            padding: 12px;
        }
        .filter-section.mobile-show .btn {
            width: 100%;
            margin-bottom: 10px;
            padding: 12px;
            font-size: 16px;
        }
        .filter-section.mobile-show .row {
            flex-direction: column;
        }
        .filter-section.mobile-show .col-md-3,
        .filter-section.mobile-show .col-md-6 {
            width: 100%;
            margin-bottom: 15px;
        }
        .filter-section.mobile-show .d-flex {
            flex-direction: column;
            gap: 10px;
        }
        .filter-section.mobile-show .d-flex .btn {
            width: 100%;
        }
        .filter-section.mobile-show label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        .filter-section.mobile-show {
            animation: modalSlideIn 0.3s ease;
        }
        .filter-section.mobile-show input,
        .filter-section.mobile-show select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .filter-section.mobile-show input:focus,
        .filter-section.mobile-show select:focus {
            border-color: #3F87B1;
            box-shadow: 0 0 0 3px rgba(63, 135, 177, 0.1);
            outline: none;
        }
        @media (max-height: 600px) {
            .filter-section.mobile-show {
                max-height: 90vh;
            }
        }
        .filter-section.mobile-show button,
        .filter-section.mobile-show input,
        .filter-section.mobile-show select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .filter-section.mobile-show input::-webkit-input-placeholder,
        .filter-section.mobile-show select::-webkit-input-placeholder {
            color: #6c757d;
        }
        @keyframes modalSlideIn {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
        .hienthicopy {
            font-size: 14px;
            padding: 8px;
            border-radius: 6px;
            background: #f8f9fa;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .hienthicopy:hover,
        .hienthicopy:active {
            background: #e9ecef;
            color: #1a73e8;
        }
    }
    /* Mobile Card Container - giống order */
    .mobile-card-container {
        padding: 12px 8px;
    }
    .mobile-ads-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        margin-bottom: 16px;
        overflow: hidden;
        transition: all 0.2s ease;
        border: 1px solid #e8eaed;
    }
    .mobile-ads-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .mobile-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 8px 12px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .mobile-platform-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        margin-right: 10px;
    }
    .mobile-shop-name {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
        margin: 0;
    }
    .mobile-ads-info {
        padding: 8px 12px;
    }
    .mobile-invoice-code {
        font-weight: 590;
        color: #36B9E5;
        font-size: 13px;
       
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mobile-invoice-date {
        color: #6c757d;
        font-size: 14px;
    }
    .mobile-ads-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-bottom: 8px;

    }
    .mobile-ads-details > div:nth-child(odd) {
        text-align: left;
    }
    .mobile-ads-details > div:nth-child(even) {
        text-align: right;
    }

    .mobile-status-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mobile-status-pill {
      
        font-size: 12px;
        font-weight: 600;
        text-align: right;
       
    }
    .mobile-status-paid {
       
        color: #009522;
    }
    .mobile-status-unpaid {
        background: #ffe8e8;
        color: #ff7777;
    }
    .mobile-detail-btn {
        background: #3F87B1;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        width: 100%;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .mobile-detail-btn:hover {
        background: #2d6a8a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(63, 135, 177, 0.3);
        color: white;
    }
    .mobile-invoice-code:hover {
        color: #1a73e8;
    }
    /* Tab Styles */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-item {
        margin-bottom: -2px;
    }
    /* Custom Tabs Styling */
    .nav-tabs-custom {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
    }
    .nav-tabs-custom .nav-item {
        position: relative;
    }
    .nav-tabs-custom .nav-item .nav-link {
        border: none;
        font-weight: 500;
        color: #6c757d;
        padding: 12px 20px;
        transition: all 0.3s ease;
        position: relative;
    }
    .nav-tabs-custom .nav-item .nav-link::after {
        content: "";
        background: #3F87B1;
        height: 2px;
        position: absolute;
        width: 100%;
        left: 0;
        bottom: 0;
        transition: all 250ms ease 0s;
        transform: scale(0);
    }
    .nav-tabs-custom .nav-item .nav-link.active {
        color: #3F87B1;
        background-color: transparent;
    }
    .nav-tabs-custom .nav-item .nav-link.active::after {
        transform: scale(1);
    }
    .nav-tabs-custom .nav-item .nav-link:hover {
        color: #3F87B1;
        background-color: #f8f9fa;
    }
    /* Success variant for tabs */
    .nav-success.nav-tabs-custom .nav-link.active {
        color: #28a745;
        background-color: #f8f9fa;
    }
    .nav-success.nav-tabs-custom .nav-link.active::after {
        background-color: #28a745;
    }
    .nav-success.nav-tabs-custom .nav-link:hover {
        color: #28a745;
    }
    /* Filter Section Styling */
    .filters-container {
        padding: 8px;
    }
    .filters-container .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .filters-container .form-control,
    .filters-container .form-select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: none !important;
    }
    .filters-container .form-control:focus,
    .filters-container .form-select:focus {
        border-color: #3F87B1;
       
    }
    .filters-container .btn {
        padding: 4px 8px;
        font-size: 14px;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .filters-container .btn-primary {
        background: #3F87B1;
        border-color: #3F87B1;
    }
    .filters-container .btn-primary:hover {
        background: #2d6a8a;
        border-color: #2d6a8a;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .filters-container .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .filters-container .btn-outline-secondary:hover {
        background: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    .copy-notification:hover {
        transform: translateX(0);
    }
    @media (min-width: 769px) {
        .mobile-card-container {
            display: none;
        }
        .mobile-filter-toggle {
            display: none;
        }
    }
    .mobile-card-container {
        padding: 0px 8px 8px 8px;
    }
    @media (max-width: 768px) {
        .modal-fullscreen-mobile {
            padding: 0;
        }
        .modal-fullscreen-mobile .modal-content {
            border-radius: 0;
            min-height: 100vh;
        }
        .modal-fullscreen-mobile .modal-body {
            padding: 16px;
            overflow-y: auto;
        }
        .modal-fullscreen-mobile .modal-header {
            background: linear-gradient(135deg, #3F87B1 0%, #2d6a8a 100%);
            color: white;
            border-bottom: none;
        }
        .modal-fullscreen-mobile .table-responsive {
            margin: 0 -16px;
        }
        .modal-fullscreen-mobile table {
            font-size: 14px;
        }
        .modal-fullscreen-mobile th,
        .modal-fullscreen-mobile td {
            padding: 8px 12px;
        }
        .modal-fullscreen-mobile .table thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 10;
        }
        .modal-fullscreen-mobile img {
            max-width: 100%;
            height: auto;
        }
        .modal-fullscreen-mobile .row.mb-4 {
            margin-bottom: 16px !important;
        }
        .modal-fullscreen-mobile .col-md-8,
        .modal-fullscreen-mobile .col-md-4 {
            width: 100%;
            margin-bottom: 16px;
        }
    }
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    .copy-notification:hover {
        transform: translateX(0);
    }
    .mobile-created-at {
        font-size: 12px;
        color: #666;
        font-weight: 400;
        text-align: right;
    }
    .mobile-date-range {    
        font-size: 12px;
        color: #666;
        font-weight: 400;
        text-align: right;
    }
    .mobile-amount {    
        font-size: 11px;
        color: #666;
        font-weight: 400;
        text-align: right;
    }
    .mobile-vat {
        font-size: 12px;
        color: #666;
        font-weight: 400;
        text-align: right;
    }
    .mobile-total {
        font-size: 12px;
        color: #666;
        text-align: right;
    }
    /* Hide DataTables sort icons in table header */
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_asc_disabled:after,
    table.dataTable thead .sorting_asc_disabled:before,
    table.dataTable thead .sorting_desc_disabled:after,
    table.dataTable thead .sorting_desc_disabled:before {
        display: none !important;
    }
    /* DataTables info, lengthMenu, pagination ngang 1 hàng, căn giữa như order */
    .dataTables_wrapper .row:last-child {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-top: 8px;
        width: 100%;
        overflow: hidden;
    }
    .dataTables_wrapper .dataTables_info {
        margin: 0 16px 0 0;
        padding: 0;
        float: none !important;
    }
    .dataTables_wrapper .dataTables_length {
        margin: 0 16px 0 0;
        padding: 0;
        float: none !important;
    }
    .dataTables_wrapper .dataTables_paginate {
        margin: 0;
        padding: 0;
        float: none !important;
    }
    @media (max-width: 768px) {
        .dataTables_wrapper .row:last-child {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
    }
    .dataTables_wrapper .row {
        flex-wrap: wrap !important;
    }
    .dataTables_wrapper .row > .col-auto {
        min-width: 0;
    }
    .dataTables_wrapper .row.no-gutters {
        margin-right: 0 !important;
        margin-left: 0 !important;
        padding: 0px 10px;
    }
    .dataTables_wrapper .row.no-gutters > [class^="col-"],
    .dataTables_wrapper .row.no-gutters > [class*=" col-"] {
        padding-right: 0 !important;
        padding-left: 0 !important;
    }
    .dataTables_wrapper .row {
        flex-wrap: wrap !important;
        width: 100% !important;
        overflow-x: hidden !important;
    }
    .dataTables_wrapper .row > .col-auto {
        min-width: 0;
    }
    @media (max-width: 768px) {
        .dataTables_wrapper .row.justify-content-between {
            flex-direction: column;
            align-items: stretch;
            gap: 4px;
        }
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length {
            display: block !important;
            text-align: center;
            width: 100%;
            margin: 4px 0;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 14px;
            font-size: 16px;
            border-radius: 8px;
            margin: 0 2px;
        }
    }
</style>
