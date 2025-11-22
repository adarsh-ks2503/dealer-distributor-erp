<!--  Sauda Items List -->
<style>
    .note-text {
        position: absolute;
        top: -12px;
        left: 0;
        font-size: 10px;
        color: red;
    }

    .th-wrapper {
        position: relative;
    }
</style>
<div class="card mb-4">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        Dispatch Items List

        <button type="button" class="btn btn-success" onclick="add_Row_items(event)">Add
            Row</button>
    </div>
    <div>
        <p style="color: red" class="ps-3  m-0">Note : Dispatch cannot exceed total Sauda QTY + Threshold %
    </div>
    <div class="card-body table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sauda No <span class="text-danger">*</span></th>
                    <th>Item <span class="text-danger">*</span></th>

                    <th>Size <span class="text-danger">*</span></th>
                    <th>Length (ft) <span class="text-danger">*</span></th>
                    <th>Sauda Quantity</th>
                    <th>Remaining <br> Sauda Qty(MT)</th>
                    <th>Sauda <br> Item Qty(MT) </th>
                    <th>Stock <br> Qty(MT)</th>
                    <th>Dispatch <br> Qty (MT) <span class="text-danger">*</span></th>

                    {{-- <th>Item Remaining Qty</th> --}}
                    {{-- <th>Item Rate(Rs)</th>
                    <th>Sauda Basic Price
                        (/MT) </th> --}}
                    <th class="th-wrapper">
                        Negotiated <br> Price (MT) <span class="text-danger">*</span>
                        <span class="note-text">Note: GST Exclusive</span>
                    </th>

                    <th>Loading Charge </th>
                    <th>Insurance </th>
                    <th>Commission </th>

                    <th>Overbilling </th>
                    <th>Amount (₹) </th>
                    <th>Total Amount (₹) </th>
                    <th>Payment Term <span class="text-danger">*</span></th>

                    <th>Remark </th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="productTable">

            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td class="text-end"><b>Total</b></td>
                    <td><input type="number" name="total__dis_quantity" class="form-control" id="total__dis_quantity"
                            readonly></td>
                    <td></td>
                    <td> </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <input type="number" name="total_amount" class="form-control" id="total__basic_amount"
                            readonly>
                    </td>

                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                </tr>
            </tfoot>
        </table>

        <style>
            #productTable td:first-child select,
            #productTable td:first-child input {
                width: 150px !important;
            }

            #productTable td:nth-child(2) select,
            #productTable td:nth-child(2) input {
                width: 150px !important;
            }

            #productTable td:nth-child(3) select,
            #productTable td:nth-child(3) input {
                width: 150px !important;
            }

            #productTable td:nth-child(5) select,
            #productTable td:nth-child(5) input {
                width: 150px !important;
            }

            #productTable td:nth-child(6) select,
            #productTable td:nth-child(6) input {
                width: 150px !important;
            }

            /* Mobile (max 768px) */
            @media (max-width: 768px) {

                #productTable td:first-child select,
                #productTable td:first-child input {
                    width: 100px !important;
                }

                #productTable td:nth-child(2) select,
                #productTable td:nth-child(2) input {
                    width: 100px !important;
                }

                #productTable td:nth-child(3) select,
                #productTable td:nth-child(3) input {
                    width: 100px !important;
                }

                #productTable td:nth-child(5) select,
                #productTable td:nth-child(5) input {
                    width: 110px !important;
                }

                #productTable td:nth-child(6) select,
                #productTable td:nth-child(6) input {
                    width: 110px !important;
                }

            }
        </style>


        <script>
            var lastItemId = 1; // Initial Item ID

            @include('partials.dispatch_items.fetch_row')

            @include('partials.dispatch_items.add_row')

            @if ($selected_items->count() == 0)
                function initializeTable___add_Row_items() {
                    add_Row_items(event);
                }
                // Add event listener for DOMContentLoaded
                document.addEventListener('DOMContentLoaded', initializeTable___add_Row_items);
            @endif

            function resetLastItemId() {
                lastItemId = 1; // Reset to initial Item ID
                // Clear all rows inside the tbody
                document.getElementById("productTable").innerHTML = "";

                document.getElementById('total__quantity').value = 0.000;
                document.getElementById('total__basic_price').value = 0.00;
                document.getElementById('total__rate').value = 0.00;
                document.getElementById('total__basic_amount').value = 0.00;

            }

            function deleteRow_items(button, lastItemId) {
                var row = button.parentNode.parentNode;
                var table = document.getElementById("productTable");
                var rowIndex = row.rowIndex;

                row.parentNode.removeChild(row);

                calculat_price(lastItemId);

                lastItemId--;

            }
        </script>


    </div>

</div>

<!-- End  Sauda Items List -->


<script>
    //  function for get_ group_ name
    function get_group_name(val, lastItemId) {
        $.ajax({
            url: "{{ route('group_data') }}", 
            method: "POST",
            data: {
                item_id: val,
                "_token": "{{ csrf_token() }}",
            },
            success: function(res) {
                
                $(`#store_group__${lastItemId}`).val(res.group);
            },
            error: function(err) {
                console.error("Error fetching item data:", err);
            }
        });
    }

    //  function for check dupllicate item row
    function get_suada_qty_data(val, lastItemId) {

        $.ajax({
            url: "{{ route('sauda_data') }}",
            method: "POST",
            data: {
                sauda_id: val,
                "_token": "{{ csrf_token() }}",
            },
            success: function(res) {
                $(`#length__${lastItemId}`).val('');

                $(`#sauda_quantity__${lastItemId}`).val(res.quantity);
                $(`#remmaining_sauda_quantity__${lastItemId}`).val(res.remaining);
            },
            error: function(err) {
                console.error("Error fetching item data:", err);
            }
        });
    }


    // Function for check all items qty
    function check_all_dispatch_qty(val, lastItemId) {
        let saudaId = $(`#sauda__id__${lastItemId}`).val(); // hidden input sauda_id for this row
        let maxQty = parseFloat($(`#remmaining_sauda_quantity__${lastItemId}`).val()) ||
            0; // allowed qty stored in hidden group field
        let threshold_val = parseFloat($('#company__threshold').val()) || 0;

        // Calculate extra as percentage
        const extraQty = (maxQty * threshold_val) / 100;

        let total__qty = maxQty + extraQty;
        // Sum all rows that have same sauda_id
        let totalQty = 0;
        $(`input[name='sauda_id[]'], select.sauda__id__select`).each(function() {
            let currentSaudaId = $(this).val(); // works for both input and select
            if (currentSaudaId == saudaId) {

                let rowIndex = $(this).attr("id").split("__")[1]; // extract row number
                let qty = parseFloat($(`#dis_quantity${rowIndex}`).val()) || 0;

                totalQty += qty;
            }
        });


        // Check if total exceeds allowed
        if (totalQty > total__qty) {
            Swal.fire({
                icon: 'warning',
                title: 'Quantity Exceeded',
                text: `Quantity exceeded. Allowed limit Quantity + Threshold: ${total__qty}`,
                confirmButtonText: 'Ok',
                reverseButtons: true
            });
            $(`#dis_quantity${lastItemId}`).val(''); // clear current input
            updateFooterTotals();
            calculat_price(lastItemId);

        }
    }



    // Function to check length
    function check__length(val, lastItemId) {
        let g_value = $(`#store_group__${lastItemId}`).val();

        // Define the allowed lengths in JS
        const allowedLengths = ['R', '18', '20', '22', 'SL', 'FL'];

        if (g_value === "FSR") {
            if (!allowedLengths.includes(val)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: `Item length not matched. Allowed lengths: ${allowedLengths.join(', ')}`,
                    confirmButtonText: 'Ok',
                    reverseButtons: true
                });
                $(`#length__${lastItemId}`).val('');
            }
        }
    }

    $(document).ready(function() {
        $('[id^="sauda__payment_type"]').each(function() {
            let id = $(this).attr('id').replace('sauda__payment_type', '');
            let val = $(this).val();

            // Manually trigger the function
            sauda_item_change__payment_type(val, id);
        });
    });

    function sauda_item_change__payment_type(val, id) {
        const wrapper = document.getElementById(`sauda_item_other__type_input_${id}`);
        const input = wrapper.querySelector('input');

        if (val === 'Other') {
            wrapper.classList.remove('d-none');
        } else {
            wrapper.classList.add('d-none');
            input.value = '';
        }
    }



    // Function to check if total quantity for a group is exceeded
    function check_group_qty(lastItemId) {
        let selectedGroup = $(`#group__${lastItemId}`).val();
        if (!selectedGroup) return;

        // Step 1: Get quantity from item_group__input (Group section)
        let group_Qty = 0;
        $('[id^="item_group__input"]').each(function() {
            const thisId = $(this).attr('id');
            const rowId = thisId.replace('item_group__input', '');
            const groupVal = $(this).val();

            if (groupVal === selectedGroup) {
                group_Qty = parseFloat($(`#sauda__quantity_${rowId}`).val()) || 0;
            }
        });

        // Step 2: Get total quantity from all matching group__ rows (Item section)
        let item_totalGroupQty = 0;
        $('[id^="group__"]').each(function() {
            const thisId = $(this).attr('id');
            const rowId = thisId.replace('group__', '');
            const groupVal = $(this).val();

            if (groupVal === selectedGroup) {
                const item_qtyVal = parseFloat($(`#quantity_${rowId}`).val()) || 0;
                item_totalGroupQty += item_qtyVal;
            }
        });

        // Step 3: Get threshold value
        let threshold_val = parseFloat($('#company__threshold').val()) || 0;

        // Calculate extra as percentage
        const extraQty = (group_Qty * threshold_val) / 100;




        const maxQtyAllowed = group_Qty + extraQty;

        // Step 4: Check if exceeded
        if (item_totalGroupQty > maxQtyAllowed) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: `Quantity exceeded. Allowed limit Quantity + Threshold: ${maxQtyAllowed}`,
            }).then(() => {
                qty_resetRow_in_data(lastItemId);
                updateFooterTotals();
                calculat_price(lastItemId);

            });
        }
    }



    function qty_resetRow_in_data(lastItemId) {
        // Reset specific input fields in the row
        $(`#quantity_${lastItemId}`).val('');
    }


    $(document).ready(function() {

        $('#customer_input').select2();

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

    });

    //  function for check dupllicate item row
    function get_stock_qty(val, lastItemId) {

        let war_id = $('#war_id').val();
        let sauda_id = $(`#sauda__id__${lastItemId}`).val();
        let item_id = $(`#enq_item_id_${lastItemId}`).val();
        let size_id = $(`#size_id_${lastItemId}`).val();
        let group = $(`#store_group__${lastItemId}`).val();

        $(`#sauda_item_quantity__${lastItemId}`).val('');

        $.ajax({
            url: "{{ route('stock_data') }}",
            method: "POST",
            data: {
                war_id: war_id,
                sauda_id: sauda_id,
                item_id: item_id,
                size_id: size_id,
                group: group,
                length: val,
                "_token": "{{ csrf_token() }}",
            },
            success: function(res) {
                $(`#sauda_item_quantity__${lastItemId}`).val(res.sauda_item_qty);
                $(`#stc_qty_quantity__${lastItemId}`).val(res.stock_qty);
                $(`#sauda_negotiated_price_${lastItemId}`).val((res.negotiate_price));

                calculat_price(lastItemId);
            },
            error: function(err) {
                console.error("Error fetching item data:", err);
            }
        });
    }


    //  function for check dupllicate item row
    function check_same_data(lastItemId) {

        const currentsaudaId = document.getElementById(`sauda__id__${lastItemId}`);
        const currentItemId = document.getElementById(`enq_item_id_${lastItemId}`);
        const currentSize = document.getElementById(`size_id_${lastItemId}`);
        const currentLength = document.getElementById(`length__${lastItemId}`);

        const value_currentSaudaId = currentsaudaId?.value;
        const value_currentItemId = currentItemId?.value;
        const value_currentSize = currentSize?.value;
        const value_currentLength = currentLength?.value?.replace(/\s+/g, '').toLowerCase();


        if (!value_currentSaudaId || !value_currentItemId || !value_currentSize || !value_currentLength) return;

        let isDuplicate = false;

        // Check all rows except the current one
        $('[id^="enq_item_id_"]').each(function() {
            const thisId = $(this).attr('id'); // enq_item_id_1, enq_item_id_2, etc.
            const rowId = thisId.replace('enq_item_id_', '');

            if (parseInt(rowId) === parseInt(lastItemId)) return; // skip current row

            const saudaVal = $(`#sauda__id__${rowId}`).val();
            const itemVal = $(`#enq_item_id_${rowId}`).val();
            const sizeVal = $(`#size_id_${rowId}`).val();
            const lengthVal = $(`#length__${rowId}`).val()?.replace(/\s+/g, '').toLowerCase();

            if (
                saudaVal && itemVal && sizeVal && lengthVal &&
                saudaVal === value_currentSaudaId &&
                itemVal === value_currentItemId &&
                sizeVal === value_currentSize &&
                lengthVal === value_currentLength
            ) {
                isDuplicate = true;
                return false; // stop loop
            }
        });

        if (isDuplicate) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Duplicate entry found.'
            }).then(() => {
                resetRow_in_same_data(event, lastItemId);
            });
        }
    }


    function resetRow_in_same_data(event, lastItemId) {
        event.preventDefault();

        // Reset specific input fields in the row
        //  $(`#enq_item_id_${lastItemId}`).val('').trigger('change');
        //  $(`#size_id_${lastItemId}`).val('').trigger('change');
        // const $sauda__ = $(`#sauda__id__${lastItemId}`);
        const $item = $(`#enq_item_id_${lastItemId}`);
        const $size = $(`#size_id_${lastItemId}`);

        // Reset Select2 only if value is not already empty
        // if ($sauda__.val()) {
        //     $sauda__.val(null).trigger('change.select2');
        // }
        if ($item.val()) {
            $item.val(null).trigger('change.select2');
        }
        if ($size.val()) {
            $size.val(null).trigger('change.select2');
        }

        $(`#store_group__${lastItemId}`).val('').trigger('change');
        $(`#length__${lastItemId}`).val('');
        // $(`#sauda_quantity__${lastItemId}`).val('');
        // $(`#sauda_item_quantity__${lastItemId}`).val('');
        $(`#stc_qty_quantity__${lastItemId}`).val('');
        $(`#delivered_quantity${lastItemId}`).val('');
        $(`#dis_quantity${lastItemId}`).val('');
        $(`#sauda_negotiated_price_${lastItemId}`).val('');
        $(`#loading_charge_${lastItemId}`).val('');
        $(`#insurance_${lastItemId}`).val('');
        $(`#commission_${lastItemId}`).val('');
        $(`#over_billing_${lastItemId}`).val('');
        $(`#final_price__${lastItemId}`).val('');
        $(`#total_price${lastItemId}`).val('');
        $(`#sauda__payment_type${lastItemId}`).val('');
        $(`#remark_item_${lastItemId}`).val('');
    }

    function get_size_data(id, lastItemId) {
        $.ajax({
            url: "{{ route('item_data') }}",
            method: "POST",
            data: {
                item_id: id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(res) {
                //  console.log(res);
                //  $(`#basic_price${lastItemId}`).val(res.item_data.price);
                $(`#group__${lastItemId}`).val(res.item_data.item_group);
                $(`#size_id_${lastItemId}`).empty();

                let currentGroup = res.item_data.item_group;


                // Add the default "Select Item" option
                $(`#size_id_${lastItemId}`).append(
                    '<option value="" selected disabled>Select Size</option>');

                // Check if grade_data exists and is an array
                if (Array.isArray(res.size_data)) {
                    // Loop through the grade_data and append each option
                    res.size_data.forEach(function(item) {
                        $(`#size_id_${lastItemId}`).append(
                            '<option value="' + item.id + '" >' + item.size + '</option>'
                        );
                    });
                } else {
                    $(`#size_id_${lastItemId}`).append(
                        '<option value="" selected disabled>Select Size</option>');
                }



                // After loop completes
                if (!res.size_data || res.size_data.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops!',
                        text: `Item size not categorized`,
                        confirmButtonText: 'Ok',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            resetRow_in_same_data(event, lastItemId);
                            //  resetRow_in_same_data(event, lastItemId);
                            updateFooterTotals(); // update totals

                        }
                    });
                }




                check_same_data(lastItemId);
                // Reset the total price and quantity fields
                resetRow__in_Selecting_time(lastItemId);

                calculat_price(lastItemId);
            },
            error: function(err) {
                console.error("Error fetching item data:", err);
            }
        });
    }

    function get__size_details(id, lastItemId) {
        let sauda_id = $(`#sauda__id__${lastItemId}`).val();
        let item_id = $(`#enq_item_id_${lastItemId}`).val();
        let group = $(`#store_group__${lastItemId}`).val();
        $.ajax({
            url: "{{ route('item_size.check') }}",
            method: "POST",
            data: {
                sauda_id: sauda_id,
                item_id: item_id,
                size_id: id,
                group: group,
                "_token": "{{ csrf_token() }}",
            },
            success: function(res) {
                // console.log(res);
                if (res.size_data) {
                    $(`#rate_${lastItemId}`).val(res.size_data.price);
                } else {
                    $(`#rate_${lastItemId}`).val('');
                }

                calculat_price(lastItemId);
                resetRow__in_Selecting_time(lastItemId);

            },
            error: function(err) {
                console.error("Error fetching item data:", err);
            }
        });
    }


    function resetRow__in_Selecting_time(lastItemId) {
        // Reset specific input fields in the row
        $(`#length__${lastItemId}`).val('');
        // $(`#sauda_item_quantity__${lastItemId}`).val('');
        $(`#stc_qty_quantity__${lastItemId}`).val('');
        $(`#delivered_quantity${lastItemId}`).val('');
        $(`#dis_quantity${lastItemId}`).val('');
        $(`#sauda_negotiated_price_${lastItemId}`).val('');
        $(`#loading_charge_${lastItemId}`).val('');
        $(`#insurance_${lastItemId}`).val('');
        $(`#commission_${lastItemId}`).val('');
        $(`#over_billing_${lastItemId}`).val('');
        $(`#final_price__${lastItemId}`).val('');
        $(`#total_price${lastItemId}`).val('');
        $(`#sauda__payment_type${lastItemId}`).val('');
        $(`#remark_item_${lastItemId}`).val('');
    }

    function calculat_price(lastItemId) {
        const quantity = parseFloat($(`#dis_quantity${lastItemId}`).val()) || 0;

        const sauda_negotiated_price_ = parseFloat($(`#sauda_negotiated_price_${lastItemId}`).val()) || 0;
        const loading_charge = parseFloat($(`#loading_charge_${lastItemId}`).val()) || 0;
        const insurance = parseFloat($(`#insurance_${lastItemId}`).val()) || 0;
        const commission = parseFloat($(`#commission_${lastItemId}`).val()) || 0;
        const over_billing = parseFloat($(`#over_billing_${lastItemId}`).val()) || 0;

        // Per MT total
        const amount = sauda_negotiated_price_ + loading_charge + insurance + commission + over_billing;



        // Total for quantity
        const totalPrice = quantity * amount;

        $(`#final_price__${lastItemId}`).val(amount.toFixed(2)); // per MT price
        $(`#total_price${lastItemId}`).val(totalPrice.toFixed(2)); // total price

        let gst_type_selected = $('#gst_type_select').val();
        check_gst_type(gst_type_selected);

        updateFooterTotals();

        $('[name="gst_type"]').val('');
        $('[name="percent"]').val('');
        $('[name="i_gst"]').val('');
        $('[name="s_gst"]').val('');
        $('[name="c_gst"]').val('');
        $('#grand_total_amount__input').val('');
    }


    function updateFooterTotals() {
        let totalQuantity = 0;
        let totalRate = 0;
        let totalBasicPrice = 0;
        let totalPrice__mt = 0;
        let totalPrice = 0;


        $('#productTable tr').each(function() {
            const rowId = $(this).data('row-id'); // Get dynamic row ID

            const quantity = parseFloat($(`#dis_quantity${rowId}`).val()) || 0;

            const total_price = parseFloat($(`#total_price${rowId}`).val()) || 0;


            totalQuantity += quantity;

            totalPrice += total_price;

        });


        $('#total__dis_quantity').val(totalQuantity.toFixed(3));

        $('#total__basic_amount').val(totalPrice.toFixed(2));

    }
</script>
