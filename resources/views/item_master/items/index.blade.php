@extends('layouts.main')
@section('title', 'Items - Singhal')

@push('styles')
<style>
    .card {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-radius: 12px;
        border: none;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Allows items to wrap on smaller screens */
        gap: 1rem; /* Adds space between items */
    }

    .table-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .search-bar {
        max-width: 300px;
    }

    .search-bar .input-group-text {
        background-color: #0d6efd;
        border: none;
        color: white;
    }
    
    .search-bar .btn {
        padding: 0.55rem 1rem;
    }

    .table-responsive {
        padding: 0 1.5rem 1.5rem;
    }

    .data-table thead th {
        background-color: #2c3e50; /* Dark blue from your mockup */
        color: #ffffff;
        font-weight: 600;
        border-bottom-width: 0;
        text-align: left;
    }

    .data-table tbody tr:hover {
        background-color: #f6f9ff;
    }
    
    .data-table td, .data-table th {
        vertical-align: middle;
        padding: 1rem;
    }
    
    .status-badge {
        padding: 0.3em 0.75em;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    .status-active {
        background-color: #d1f7e5;
        color: #12804b;
    }
    
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .pagination-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 1.5rem 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #2c3e50;
        border-color: #2c3e50;
    }
    
    .btn-excel {
        background-color: #157347;
        color: white;
    }

</style>
@endpush

@section('content')
<main id="main" class="main">
    
    {{-- Session Messages (Keep your existing code) --}}
    
    <div class="dashboard-header pagetitle">
            <h1>Items</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Item Master</a></li>
                    <li class="breadcrumb-item">Items</li>
                </ol>
            </nav>
    </div><!-- End Page Title -->
        
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                   
                    
                    {{-- Data Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered display stripe row-border order-column"  id="item_size_table" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Size (mm)</th>
                                    <th scope="col">HSN Code</th>
                                    <th scope="col">UOM</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through your items. Here's dummy data for illustration. --}}
                                @forelse($itemSizes as $item)
                                <tr>
                                    <td>{{ $item->itemName->item_name }} 550D {{ $item->size }} mm</td>
                                    <td>{{ $item->size }} mm</td>
                                    <td>{{ $item->hsn_code }}</td>
                                    <td>MT</td>
                                    <td><span class="status-badge status-active">{{ $item->status }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No items found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>@endsection