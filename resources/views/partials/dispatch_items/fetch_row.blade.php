      function fetch_add_Row_items() {

      var table = document.getElementById("productTable");
      @foreach ($selected_items as $item)
          var newRow = table.insertRow(table.rows.length);
          newRow.setAttribute("data-row-id", lastItemId);

          var cell1 = newRow.insertCell(0);
          var cell2 = newRow.insertCell(1);
          var cell3 = newRow.insertCell(2);
          var cell4 = newRow.insertCell(3);
          var cell5 = newRow.insertCell(4);
          var cell6 = newRow.insertCell(5);
          var cell7 = newRow.insertCell(6);
          var cell8 = newRow.insertCell(7);
          var cell9 = newRow.insertCell(8);
          var cell10 = newRow.insertCell(9);
          var cell11 = newRow.insertCell(10);
          var cell12 = newRow.insertCell(11);
          var cell13 = newRow.insertCell(12);
          var cell14 = newRow.insertCell(13);
          var cell15 = newRow.insertCell(14);
          var cell16 = newRow.insertCell(15);
          var cell17 = newRow.insertCell(16);
          var cell18 = newRow.insertCell(17);
          var cell19 = newRow.insertCell(18);
          var cell20 = newRow.insertCell(19);
          var cell21 = newRow.insertCell(20);



          cell1.innerHTML = `
          <select class="sauda__id__select_input form-select" disabled>
              <option value="" disabled>Select Item Name</option>
              @foreach ($saudas as $sauda)
                  <option value="{{ $sauda->sauda_number }}" {{ $item->sauda_id == $sauda->id ? 'selected' : '' }}>
                      {{ $sauda->sauda_number }}
                  </option>
              @endforeach
          </select>
          <input type="hidden" name="sauda_id[]" value="{{ $item->sauda_id }}" id="sauda__id__${lastItemId}">
          <input type="hidden" name="group[]" value="{{ $item->group }}" class="form-control"
              id="store_group__${lastItemId}">

          `;
          $('.sauda__id__select_input').select2();

          cell2.innerHTML = `
          <select name="item_id[]" class="enq_item-select form-select" id="enq_item_id_${lastItemId}"
              onchange="get_size_data(this.value, ${lastItemId});get_group_name(this.value, ${lastItemId})" required>
              <option value="" disabled>Select Item Name</option>
              @foreach ($itemNames as $itemdata)
                  <option value="{{ $itemdata->id }}" {{ $item->item_id == $itemdata->id ? 'selected' : '' }}>
                      {{ $itemdata->name }}
                  </option>
              @endforeach
          </select>
          `;
          $('.enq_item-select').select2();

          cell3.innerHTML = `
          <select name="size_id[]" class="size_id_-select form-select" id="size_id_${lastItemId}"
              onchange="get__size_details(this.value, ${lastItemId}) " required>
              <option value="" disabled>Select Size</option>
              @foreach ($item->available_sizes as $size)
                  <option value="{{ $size->id }}" {{ $size->id == $item->size_id ? 'selected' : '' }}>
                      {{ $size->size }}
                  </option>
              @endforeach
          </select>
          `;
          $('.size_id_-select').select2();

          cell4.innerHTML = `
          <input type="text" name="length[]" value="{{ $item->length }}" class="form-control"
              onchange="check_same_data(${lastItemId}); get_stock_qty(this.value, ${lastItemId})"
              id="length__${lastItemId}" style="width: 100px!important" placeholder="0.00" required>
          `;

          cell5.innerHTML = `
          <input type="text" name="sauda_quantity[]" readonly value="{{ $item->sauda->sauda_quantity }}"
              class="form-control" id="sauda_quantity__${lastItemId}">
          `;

          cell6.innerHTML = `
          <input type="text" readonly value="{{ $item->sauda->remaining }}" class="form-control"
              id="remmaining_sauda_quantity__${lastItemId}">
          `;

          cell7.innerHTML = `
          <input type="text" name="sauda_item_quantity[]" value="{{ $item->quantity }}" class="form-control"
              id="sauda_item_quantity__${lastItemId}" style="width: 150px!important" placeholder="0.00"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{3}).*$/, '$1').replace(/^0+([^\\.])/, '$1');"
              readonly>
          `;

          cell8.innerHTML = `
          <input type="text" name="stock_quantity[]" value="{{ $item->stc_qty }}" class="form-control"
              id="stc_qty_quantity__${lastItemId}" style="width: 150px!important" placeholder="0.00" readonly>
          `;

          cell9.innerHTML = `
          <input type="text" name="dis_quantity[]" class="form-control" id="dis_quantity${lastItemId}"
              style="width: 150px!important" placeholder="0.000" required
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{3}).*$/, '$1').replace(/^0+([^\\.])/, '$1') ;calculat_price(${lastItemId});  check_all_dispatch_qty(this.value,${lastItemId})">
          `;



          cell10.innerHTML = `
          <input type="text" name="sauda_negotiate_price[]" class="form-control "
              id="sauda_negotiated_price_${lastItemId}" style="width: 150px!important" placeholder="0.000"
              value="{{ $item->negotiated_price ?? 0 }}" required
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{2}).*$/, '$1').replace(/^0+([^\\.])/, '$1'); calculat_price(${lastItemId})">

          `;


          cell11.innerHTML = `
          <input type="text" name="loading_charge[]" class="form-control" id="loading_charge_${lastItemId}"
              style="width: 150px!important" placeholder="0.00"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{2}).*$/, '$1').replace(/^0+([^\\.])/, '$1'); calculat_price(${lastItemId})">
          `;

          cell12.innerHTML = `
          <input type="text" name="insurance[]" class="form-control" id="insurance_${lastItemId}"
              style="width: 150px!important" placeholder="0.00"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{2}).*$/, '$1').replace(/^0+([^\\.])/, '$1'); calculat_price(${lastItemId})">
          `;

          cell13.innerHTML = `
          <input type="text" name="commission[]" class="form-control" id="commission_${lastItemId}"
              style="width: 150px!important" placeholder="0.00"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{2}).*$/, '$1').replace(/^0+([^\\.])/, '$1'); calculat_price(${lastItemId})">
          `;

          cell14.innerHTML = `
          <input type="text" name="over_billing[]" class="form-control" id="over_billing_${lastItemId}"
              style="width: 150px!important" placeholder="0.00"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\..{2}).*$/, '$1').replace(/^0+([^\\.])/, '$1'); calculat_price(${lastItemId})">
          `;

          cell15.innerHTML = `
          <input type="text" name="final_price[]" style="width: 150px!important" class="form-control"
              id="final_price__${lastItemId}" placeholder="0.00" readonly>
          `;

          cell16.innerHTML = `
          <input type="text" name="total_price[]" style="width: 150px!important" class="form-control"
              id="total_price${lastItemId}" placeholder="0.00" readonly>
          `;

          cell17.innerHTML = `
          <div>
              <select name="payment_type[]" class="form-select " id="sauda__payment_type${lastItemId}" required
                  onchange="sauda_item_change__payment_type(this.value, ${lastItemId})">
                  <option value="" disabled {{ !$item->sauda_item_payment_type ? 'selected' : '' }}>Select
                      Payment
                      Type</option>
                  <option value="Next Day" {{ $item->sauda_item_payment_type == 'Next Day' ? 'selected' : '' }}>Next
                      Day
                  </option>
                  <option value="Advanced" {{ $item->sauda_item_payment_type == 'Advanced' ? 'selected' : '' }}>
                      Advanced
                  </option>
                  <option value="Onloading" {{ $item->sauda_item_payment_type == 'Onloading' ? 'selected' : '' }}>
                      Onloading</option>
                  <option value="Other" {{ $item->sauda_item_payment_type == 'Other' ? 'selected' : '' }}>Other
                  </option>
              </select>
              <div id="sauda_item_other__type_input_${lastItemId}" class=" d-none ">
                  <input type="text" name="other_payment_type[]"
                      value="{{ $item->sauda_item_other_payment_type }}" class="form-control mt-1"
                      placeholder="Other Payment">
              </div>

          </div>

          `;

          cell18.innerHTML = `
          <input type="text" name="remark_item[]" style="width: 250px!important" class="form-control"
              id="remark_item_${lastItemId}" placeholder="Item Remark">
          `;

          cell19.innerHTML = `
          <button onclick="add_Row_items(event)" class="btn btn-success"><i class="fas fa-plus-circle"></i></button>
          `;


          cell20.innerHTML = lastItemId === 1
          ? `<button class="btn btn-secondary" onclick="resetRow_in_same_data(event, ${lastItemId})">
              <i class="fa-solid fa-rotate-right"></i>
          </button>`
          : `<button class="btn btn-danger" onclick="deleteRow_items(this, ${lastItemId})">
              <i class="fas fa-minus-circle"></i>
          </button>`;

          // Select2 focus fix
          $('.enq_item-select').on('select2:open', function() {
          document.querySelector('.select2-search__field').focus();
          });
          $('.size_id_-select').on('select2:open', function() {
          document.querySelector('.select2-search__field').focus();
          });

          lastItemId++;
      @endforeach
      }

      function initializeTable_fetch_add_Row_items() {
      fetch_add_Row_items();
      // calculat_price(lastItemId);
      }

      // Add event listener for DOMContentLoaded
      document.addEventListener('DOMContentLoaded', initializeTable_fetch_add_Row_items);
