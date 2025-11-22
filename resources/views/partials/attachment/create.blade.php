<div class="card mb-4">
    <div class="card-body">
        <div class="card-header mb-3 fw-bold">Attachment List</div>
        <table class="col-md-12 table">
            <thead>
                <tr>
                    <th style="width: 50%"><strong>Attachment </strong></th>
                    <th style="width: 50%"><strong>Remarks </strong></th>
                    <th class="table_heading_action"><strong>Action</strong></th>
                </tr>
            </thead>
            <tbody id="img_table">
                <tr></tr>
            </tbody>
        </table>
        <script>
            var attachment_lastItemId = 1; // Initial Item ID

            function attachment_add_Row_1(event) {
                event.preventDefault();
                var table = document.getElementById("img_table");
                var newRow = table.insertRow(table.rows.length);

                var cell1 = newRow.insertCell(0);
                var cell2 = newRow.insertCell(1);
                var cell3 = newRow.insertCell(2);
                var cell4 = newRow.insertCell(3);


                cell1.innerHTML =
                    `<input type="file" name="attachment[]" class="form-control">`;

                cell2.innerHTML =
                    `<textarea type="text" rows="1" name="atch_remarks[]" class="form-control" placeholder="Enter Remarks"></textarea>`;
                cell3.innerHTML =
                    `<button onclick="attachment_add_Row_1(event)" class="btn btn-success"><i class="fas fa-plus-circle"></i></button>`;
                if (attachment_lastItemId != 1) {
                    cell4.innerHTML =
                        `<button class="btn btn-danger" onclick="deleteRow(this)"><i class="fas fa-minus-circle"></i></button>`;
                }


                attachment_lastItemId++;

            }

            function initializeTable_2() {
                attachment_add_Row_1(event);
            }
            document.addEventListener('DOMContentLoaded', initializeTable_2);

            function deleteRow(button) {
                var row = button.parentNode.parentNode;
                var table = document.getElementById("myTable");
                var rowIndex = row.rowIndex;

                row.parentNode.removeChild(row);
                attachment_lastItemId--;
            }
        </script>

        <!-- Buttons -->
        @if ($cancelRoute != 'dispatch')
            <div class="text-end p-4">
                <a href="{{ $cancelRoute ?? '#' }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        @endif
    </div>

</div>
