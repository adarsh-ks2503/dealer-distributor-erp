 <div class="mb-1">
     <div class="row g-3">


         <div class="col-md-4 d-flex">
             <div class="card w-100 d-flex flex-column"> <!-- Flex column -->
                 <div class="card-header fw-bold">Customer Details</div>
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


         <!-- Basic & Contact Info Card -->
         <div class="col-md-4 d-flex">
             <div class="card w-100 d-flex flex-column"> <!-- Flex column -->
                 <div class="card-header fw-bold">Dispatch Basic Details</div>
                 <div class="card-body flex-grow-1 p-4"> <!-- Fill available height -->
                     <p class="mb-1"><strong>Dispatch No :</strong> {{ $dispatch->dispatch_number ?? 'N/A' }}</p>
                     <p class="mb-1"><strong>Dispatch Date :</strong>
                         {{ date('d-M-Y', strtotime($dispatch->date ?? 'N/A')) }}</p>
                     <p class="mb-1"><strong>Dispatch Qty :</strong> {{ $dispatch->total__dis_quantity ?? 'N/A' }}</p>
                     <p class="mb-1"><strong>Amount :</strong> {{ $dispatch->grand_total_amount ?? 'N/A' }}</p>
                     <p class="mb-1"><strong>Bill To :</strong> {{ $dispatch->bill_to ?? $dispatch->customer->name ?? 'N/A' }}</p>
                 </div>
             </div>
         </div>

         <!-- Address Details Card -->
         <div class="col-md-4 d-flex">
             <div class="card w-100 d-flex flex-column">
                 <div class="card-header fw-bold">Dispatch Basic Details</div>
                 <div class="card-body flex-grow-1 p-4">
                     <p class="mb-1"><strong>WareHouse :</strong> {{ $dispatch->warehouse->prefix ?? 'N/A' }}</p>
                     <p class="mb-1">
                         <strong>Sauda No :</strong>
                         {{ !empty($sauda_numbers) ? implode(', ', $sauda_numbers) : 'N/A' }}
                     </p>

                     <p class="mb-1"><strong>Transport Name :</strong> {{ $dispatch->transporter_name ?? 'N/A' }}</p>
                     <p class="mb-1"><strong>Vehicle No :</strong> {{ $dispatch->vehicle ?? 'N/A' }}</p>
                 </div>
             </div>
         </div>


     </div>
 </div>
