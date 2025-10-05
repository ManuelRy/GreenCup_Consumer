@extends('master')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Receipt Management</h1>
                    <p class="text-muted mb-0">Approve or reject claimed receipts</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Receipts Table -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Claimed Receipts Pending Review</h5>
                </div>
                <div class="card-body p-0">
                    @if($receipts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt Code</th>
                                        <th>Consumer</th>
                                        <th>Seller</th>
                                        <th>Points</th>
                                        <th>Claimed At</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipts as $receipt)
                                        <tr>
                                            <td>
                                                <code>{{ $receipt->receipt_code }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $receipt->consumer->full_name ?? 'Unknown' }}</strong>
                                                    <small class="text-muted d-block">ID: {{ $receipt->claimed_by_consumer_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $receipt->seller->business_name }}</strong>
                                                    <small class="text-muted d-block">{{ $receipt->seller->address }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">{{ $receipt->total_points }} pts</span>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $receipt->claimed_at ? $receipt->claimed_at->format('M d, Y') : 'Not claimed' }}
                                                    <small class="text-muted d-block">{{ $receipt->claimed_at ? $receipt->claimed_at->format('g:i A') : '' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ ucfirst($receipt->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ url('/test-approve/' . $receipt->receipt_code) }}"
                                                       class="btn btn-success btn-sm"
                                                       onclick="return confirm('Approve this receipt? The consumer will be notified.')">
                                                        <i class="bi bi-check-circle me-1"></i>Approve
                                                    </a>
                                                    <a href="{{ url('/test-reject/' . $receipt->receipt_code) }}"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Reject this receipt? Points will be deducted and consumer will be notified.')">
                                                        <i class="bi bi-x-circle me-1"></i>Reject
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No claimed receipts found</h5>
                            <p class="text-muted mb-0">No receipts are currently waiting for approval or rejection.</p>
                            <a href="{{ url('/test-receipt') }}" class="btn btn-primary mt-3">
                                Create Test Receipt
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Instructions -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="bi bi-info-circle me-2"></i>How it works
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Approve
                            </h6>
                            <ul class="small text-muted">
                                <li>Confirms the receipt is valid</li>
                                <li>Points remain in consumer's account</li>
                                <li>Approval logged in transaction history</li>
                                <li>Receipt status changes to "approved"</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </h6>
                            <ul class="small text-muted">
                                <li>Marks receipt as invalid</li>
                                <li>Points are deducted from consumer</li>
                                <li>Rejection logged in transaction history</li>
                                <li>Receipt status changes to "rejected"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
