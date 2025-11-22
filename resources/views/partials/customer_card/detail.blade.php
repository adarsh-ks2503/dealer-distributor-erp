<div class="card mb-4">

    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span>Customer Details</span>

    </div>


</div>


<div class="mb-2">
    <div class="row g-3">
        <!-- Basic & Contact Info Card -->
        <div class="col-md-4 d-flex">
            <div class="card w-100 d-flex flex-column"> <!-- Flex column -->
                <div class="card-header fw-bold">Basic & Contact Info</div>
                <div class="card-body flex-grow-1"> <!-- Fill available height -->
                    <p class="mb-1"><strong>Customer Name:</strong> {{ $customer->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Company Name:</strong>
                        {{ $customer->company_name ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>GST No:</strong> {{ $customer->gst_no ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>PAN No:</strong> {{ $customer->pan_no ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Mobile No:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Address Details Card -->
        <div class="col-md-4 d-flex">
            <div class="card w-100 d-flex flex-column">
                <div class="card-header fw-bold">Invoice Address Details</div>
                <div class="card-body flex-grow-1">
                    <p class="mb-1"><strong>Address:</strong>
                        {{ $customer->invoice_address ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>State:</strong> {{ $customer->invoice_state ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>City:</strong> {{ $customer->invoice_city ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Pincode:</strong>
                        {{ $customer->invoice_pincode ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Address Details Card -->
        <div class="col-md-4 d-flex">
            <div class="card w-100 d-flex flex-column">
                <div class="card-header fw-bold">Delivery Address Details</div>
                <div class="card-body flex-grow-1">
                    <p class="mb-1"><strong>Address:</strong>
                        {{ $customer->delivery_address ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>State:</strong> {{ $customer->delivery_state ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>City:</strong> {{ $customer->delivery_city ?? 'N/A' }}
                    </p>
                    <p class="mb-1"><strong>Pincode:</strong>
                        {{ $customer->delivery_pincode ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
