@extends('layouts.main')
@section('title', 'Dashboard - Singhal Steel')
@section('content')
    <main id="main" class="main">
        @if ($message = Session::get('success'))
            <div class="tt active">
                <div class="tt-content">
                    <i class="fas fa-solid fa-check check"></i>
                    <div class="message">
                        <span class="text text-1">Success</span>
                        <span class="text text-2"> {{ $message }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-xmark close"></i>
                <div class="pg active"></div>
            </div>
        @endif
        <div class="dashboard-header pagetitle">
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <!-- Quick Stats Row -->
            <div class="row mb-4">
                <!-- Dealers -->
                @can('Dealers-Index')
                    <div class="col-xxl-4 col-md-4 col-sm-6">
                        <a href="{{ route('dealers.index') }}" class="text-decoration-none">
                            <div class="card info-card uniform-card enhanced-card quick-stat-card dealers-card">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <div class="card-icon-large rounded-circle d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 border border-primary-subtle mx-auto mb-2">
                                            <i class="fas fa-users fs-3 text-primary"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-1 fw-semibold text-dark">Total Dealers</h6>
                                    <h2 class="fw-bold text-primary mb-2 quick-stat-count">{{ number_format($dealerCount) }}</h2>
                                    <div class="bg-light rounded p-1">
                                        <small class="text-muted d-block">Manage your dealer network</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endcan
                <!-- Distributors -->
                @can('Distributors-Index')
                    <div class="col-xxl-4 col-md-4 col-sm-6">
                        <a href="{{ route('distributors.index') }}" class="text-decoration-none">
                            <div class="card info-card uniform-card enhanced-card quick-stat-card distributors-card">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <div class="card-icon-large rounded-circle d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 border border-success-subtle mx-auto mb-2">
                                            <i class="fas fa-truck fs-3 text-success"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-1 fw-semibold text-dark">Total Distributors</h6>
                                    <h2 class="fw-bold text-success mb-2 quick-stat-count">{{ number_format($distributorCount) }}</h2>
                                    <div class="bg-light rounded p-1">
                                        <small class="text-muted d-block">Track distribution channels</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endcan
                <!-- Internal Users -->
                @can('UserManagement-Index')
                    <div class="col-xxl-4 col-md-4 col-sm-6">
                        <a href="{{ route('users.index') }}" class="text-decoration-none">
                            <div class="card info-card uniform-card enhanced-card quick-stat-card users-card">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <div class="card-icon-large rounded-circle d-inline-flex align-items-center justify-content-center bg-info bg-opacity-10 border border-info-subtle mx-auto mb-2">
                                            <i class="fas fa-user-tie fs-3 text-info"></i>
                                        </div>
                                    </div>
                                    <h6 class="card-title mb-1 fw-semibold text-dark">Internal Users</h6>
                                    <h2 class="fw-bold text-info mb-2 quick-stat-count">{{ number_format($userCount) }}</h2>
                                    <div class="bg-light rounded p-1">
                                        <small class="text-muted d-block">System administrators & staff</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Order Details In MT -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="card-title mb-3 fw-semibold text-dark">Order Details (MT)</h5>
                </div>
                <div class="col-xxl-4 col-md-6 col-sm-12">
                    <a href="{{ route('order.report') }}?type=approved,partial dispatch,completed,closed with condition&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card mt-stat-card order-total-mt">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 border border-primary-subtle">
                                        <i class="fas fa-boxes fs-4 text-primary"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="card-subtitle mb-1 fw-bold text-primary title-focus">Total Orders Qty</h5>
                                    </div>
                                </div>
                                <span class="status-text d-block mb-2 text-muted small">Status: Approved, Partial, Completed, Closed</span>
                                <h2 class="fw-bold text-primary mb-1 mt-stat-count">{{ number_format($totalOrdersQtyMT, 2) }} MT</h2>
                                <small class="text-muted">({{ number_format($totalOrdersQtyNo) }} Orders)</small>
                                <div class="mt-3 pt-2 border-top border-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-4 col-md-6 col-sm-12">
                    <a href="{{ route('order.report') }}?type=approved,partial dispatch&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card mt-stat-card order-remaining-mt">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 border border-warning-subtle">
                                        <i class="fas fa-clock fs-4 text-warning"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="card-subtitle mb-1 fw-bold text-warning title-focus">Remaining Qty (Not Dispatched)</h5>
                                    </div>
                                </div>
                                <span class="status-text d-block mb-2 text-muted small">Status: Approved, Partial Dispatch</span>
                                <h2 class="fw-bold text-warning mb-1 mt-stat-count">{{ number_format($totalRemainingOrderQtyMT <= 0 ? 0 : $totalRemainingOrderQtyMT, 2) }} MT</h2>
                                <small class="text-muted">({{ number_format($totalRemainingOrderQtyNo) }} Orders)</small>
                                <div class="mt-3 pt-2 border-top border-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalOrdersQtyMT > 0 ? round(($totalRemainingOrderQtyMT / $totalOrdersQtyMT) * 100, 0) : 0 }}%" aria-valuenow="{{ $totalOrdersQtyMT > 0 ? round(($totalRemainingOrderQtyMT / $totalOrdersQtyMT) * 100, 0) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-4 col-md-6 col-sm-12">
                    <a href="{{ route('order.report') }}?type=completed,closed with condition&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card mt-stat-card order-completed-mt">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 border border-success-subtle">
                                        <i class="fas fa-check-circle fs-4 text-success"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="card-subtitle mb-1 fw-bold text-success title-focus">Completed Qty</h5>
                                    </div>
                                </div>
                                <span class="status-text d-block mb-2 text-muted small">Status: Completed, Closed</span>
                                <h2 class="fw-bold text-success mb-1 mt-stat-count">{{ number_format($completedOrderQtyMT, 2) }} MT</h2>
                                <small class="text-muted">({{ number_format($completedOrderQtyNo) }} Orders)</small>
                                <div class="mt-3 pt-2 border-top border-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalOrdersQtyMT > 0 ? round(($completedOrderQtyMT / $totalOrdersQtyMT) * 100, 0) : 0 }}%" aria-valuenow="{{ $totalOrdersQtyMT > 0 ? round(($completedOrderQtyMT / $totalOrdersQtyMT) * 100, 0) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Dispatch Details In MT -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="card-title mb-3 fw-semibold text-dark">Dispatch Details (MT)</h5>
                </div>
                <div class="col-xxl-6 col-md-6 col-sm-12">
                    <a href="{{ route('dispatch.report') }}?from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card mt-stat-card dispatch-total-mt">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 border border-info-subtle">
                                        <i class="fas fa-shipping-fast fs-4 text-info"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="card-subtitle mb-1 fw-bold text-info title-focus">Total Dispatch Qty</h5>
                                    </div>
                                </div>
                                <span class="status-text d-block mb-2 text-muted small">Status: Approved, Pending</span>
                                <h2 class="fw-bold text-info mb-1 mt-stat-count">{{ number_format($totalDispatchQtyMT, 2) }} MT</h2>
                                <small class="text-muted">({{ number_format($totalDispatchQtyNo) }} Dispatches)</small>
                                <div class="mt-3 pt-2 border-top border-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-6 col-md-6 col-sm-12">
                    <a href="{{ route('dispatch.report') }}?type=pending&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card mt-stat-card dispatch-pending-mt">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 border border-secondary-subtle">
                                        <i class="fas fa-hourglass-half fs-4 text-secondary"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h5 class="card-subtitle mb-1 fw-bold text-secondary title-focus">Pending Dispatch Qty</h5>
                                    </div>
                                </div>
                                <span class="status-text d-block mb-2 text-muted small">Status: Pending</span>
                                <h2 class="fw-bold text-secondary mb-1 mt-stat-count">{{ number_format($pendingDispatchQtyMT, 2) }} MT</h2>
                                <small class="text-muted">({{ number_format($pendingDispatchQtyNo) }} Dispatches)</small>
                                <div class="mt-3 pt-2 border-top border-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $totalDispatchQtyMT > 0 ? round(($pendingDispatchQtyMT / $totalDispatchQtyMT) * 100, 0) : 0 }}%" aria-valuenow="{{ $totalDispatchQtyMT > 0 ? round(($pendingDispatchQtyMT / $totalDispatchQtyMT) * 100, 0) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Order Details By Status (Count) -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="card-title mb-3 fw-semibold text-dark">Order Details by Status (Count)</h5>
                </div>
                @can('Order-Index')
                    <div class="col-xxl-3 col-md-4 col-sm-6">
                        <a href="{{ route('order.report') }}?from_dashboard=true" class="text-decoration-none">
                            <div class="card info-card uniform-card enhanced-card order-total-count">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-primary bg-opacity-10 border border-primary-subtle">
                                            <i class="fas fa-list fs-5 text-primary"></i>
                                        </div>
                                        <div class="text-end flex-grow-1">
                                            <h3 class="card-title mb-3 fw-bold text-primary">Total Orders</h3>
                                            <h2 class="fw-bold text-primary mb-0">{{ number_format($totalOrdersNo) }}</h2>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">All order records</small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endcan
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=pending&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-pending-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-warning bg-opacity-10 border border-warning-subtle">
                                        <i class="fas fa-exclamation-triangle fs-5 text-warning"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-warning">Pending Approvals</h3>
                                        <h2 class="fw-bold text-warning mb-0">{{ number_format($newOrdersApprovalsPendingNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Requires action</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=approved&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-approved-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-success bg-opacity-10 border border-success-subtle">
                                        <i class="fas fa-thumbs-up fs-5 text-success"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-success">Approved Orders</h3>
                                        <h2 class="fw-bold text-success mb-0">{{ number_format($approvedOrdersNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Ready for dispatch</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=completed,closed with condition&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-completed-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-success bg-opacity-10 border border-success-subtle">
                                        <i class="fas fa-check-double fs-5 text-success"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-success">Completed Orders</h3>
                                        <h2 class="fw-bold text-success mb-0">{{ number_format($ordersCompletedNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Successfully fulfilled</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=partial dispatch&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-partial-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-info bg-opacity-10 border border-info-subtle">
                                        <i class="fas fa-minus-circle fs-5 text-info"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-info">Partial Dispatched</h3>
                                        <h2 class="fw-bold text-info mb-0">{{ number_format($partialDispatchedOrdersNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">In progress</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=approved,partial dispatch&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-remaining-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-warning bg-opacity-10 border border-warning-subtle">
                                        <i class="fas fa-balance-scale fs-5 text-warning"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-warning">Remaining Orders</h3>
                                        <h2 class="fw-bold text-warning mb-0">{{ number_format($totalRemainingOrderNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Outstanding balance</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-md-4 col-sm-6">
                    <a href="{{ route('order.report') }}?type=rejected&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card order-rejected-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-danger bg-opacity-10 border border-danger-subtle">
                                        <i class="fas fa-times-circle fs-5 text-danger"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-danger">Rejected Orders</h3>
                                        <h2 class="fw-bold text-danger mb-0">{{ number_format($totalRejectedOrderNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Declined requests</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Dispatch Details By Status (Count) -->
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="card-title mb-3 fw-semibold text-dark">Dispatch Details by Status (Count)</h5>
                </div>
                <div class="col-xxl-4 col-md-4 col-sm-6">
                    <a href="{{ route('dispatch.report') }}?from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card dispatch-total-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-info bg-opacity-10 border border-info-subtle">
                                        <i class="fas fa-list-alt fs-5 text-info"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-info">Total Dispatches</h3>
                                        <h2 class="fw-bold text-info mb-0">{{ number_format($totalDispatchesNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">All dispatch records</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-4 col-md-4 col-sm-6">
                    <a href="{{ route('dispatch.report') }}?type=pending&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card dispatch-pending-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-warning bg-opacity-10 border border-warning-subtle">
                                        <i class="fas fa-exclamation-triangle fs-5 text-warning"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-warning">Pending Approvals</h3>
                                        <h2 class="fw-bold text-warning mb-0">{{ number_format($newDispatchApprovalsPendingNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Requires review</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-4 col-md-4 col-sm-6">
                    <a href="{{ route('dispatch.report') }}?type=approved&from_dashboard=true" class="text-decoration-none">
                        <div class="card info-card uniform-card enhanced-card dispatch-approved-count">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center me-3 bg-success bg-opacity-10 border border-success-subtle">
                                        <i class="fas fa-thumbs-up fs-5 text-success"></i>
                                    </div>
                                    <div class="text-end flex-grow-1">
                                        <h3 class="card-title mb-3 fw-bold text-success">Approved Dispatches</h3>
                                        <h2 class="fw-bold text-success mb-0">{{ number_format($totalApprovedDispatchNo) }}</h2>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Cleared for shipment</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

    @push('styles')
    <style>
        .enhanced-card {
            transition: all 0.3s ease-in-out;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.75rem;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        .enhanced-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .enhanced-card .card-body {
            padding: 1.75rem;
        }
        .enhanced-card .card-icon {
            width: 55px;
            height: 55px;
            border: 2px solid rgba(0, 0, 0, 0.05);
        }
        .enhanced-card .card-icon-large {
            width: 60px;
            height: 60px;
            border: 2px solid rgba(0, 0, 0, 0.05);
        }
        .quick-stat-card .card-body {
            padding: 1.5rem 1rem;
        }
        .mt-stat-card .card-body {
            padding: 1.75rem;
        }
        .mt-stat-card .card-icon {
            width: 60px;
            height: 60px;
        }
        .quick-stat-count, .mt-stat-count {
            font-size: 2.5rem;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .enhanced-card h3.card-title {
            font-size: 1.25rem;
            margin-bottom: 0;
            font-weight: 700;
        }
        .enhanced-card h2 {
            font-size: 2.5rem;
            margin-bottom: 0;
        }
        .enhanced-card h5.card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .enhanced-card h6.card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .enhanced-card h6.card-subtitle {
            font-size: 0.8rem;
        }
        .enhanced-card .title-focus {
            font-size: 1rem;
            color: inherit;
            font-weight: 600;
            text-transform: none;
        }
        .enhanced-card .status-text {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }
        .enhanced-card small {
            font-size: 0.8rem;
        }
        .progress {
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }
        .section-title {
            position: relative;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #007bff, #0056b3);
            border-radius: 2px;
        }
    </style>
    @endpush
@endsection
