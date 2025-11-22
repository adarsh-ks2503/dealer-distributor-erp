@if ($selected_dis_items->isNotEmpty())
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 mt-4 table-responsive" style="max-height: 600px;">
                    <h3 class="mb-4">Dispatch Items List Details</h3>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S. No</th>
                                <th>Sauda No </th>
                                <th>Item </th>
                                <th>Size </th>

                                <th>Length</th>
                                <th>Sauda <br> Qty(MT)</th>
                                <th>Sauda <br> Item Qty(MT)</th>

                                <th>Dispatched <br> Qty(MT)</th>
                                <th>Negotiated <br> Price (MT)</th>
                                <th>Laoding <br> Charge </th>

                                <th>Insurance</th>
                                <th>Commission</th>
                                <th>OverBilling</th>
                                <th>Amount</th>

                                <th>Total Amount</th>
                                <th>Payment Term</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($selected_dis_items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->sauda->sauda_number ?? 'N/A' }}</td>
                                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                                    <td>{{ $item->size->size ?? 'N/A' }} </td>
                                    <td>{{ $item->length ?? 'N/A' }}</td>
                                    <td>{{ $item->sauda_quantity ?? 'N/A' }}</td>
                                    <td>{{ $item->sauda_item_quantity ?? 'N/A' }}</td>
                                    <td>{{ $item->dis_quantity ?? 'N/A' }}</td>
                                    <td>{{ $item->sauda_negotiate_price ?? 'N/A' }}</td>
                                    <td>{{ $item->loading_charge ?? 'N/A' }}</td>
                                    <td>{{ $item->insurance ?? 'N/A' }} </td>
                                    <td>{{ $item->commission ?? 'N/A' }} </td>
                                    <td>{{ $item->over_billing ?? 'N/A' }} </td>
                                    <td>{{ $item->final_price ?? 'N/A' }} </td>
                                    <td>{{ $item->total_price ?? 'N/A' }} </td>
                                    <td>{{ $item->payment_type ?? 'N/A' }}
                                        <br>
                                        @if ($item->payment_type == 'Other')
                                            ({{ $item->other_payment_type ?? 'N/A' }})
                                        @endif
                                    </td>
                                    <td>{{ $item->remark_item ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="text-center">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>


                    </table>


                </div>

            </div>



        </div>
    </div>
@endif
