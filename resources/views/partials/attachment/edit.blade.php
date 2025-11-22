 @if ($selected_images->isNotEmpty())
     <div class="card mb-4">
         <div class="card-body">
             <h5 class="card-title">Attachment List</h5>
             <div class=" ">

                 <table class="col-md-4 col-sm-4 col-xl-12 table">
                     <thead>
                         <tr>

                             <th style="width: 50% !important">Attachment File</th>
                             <th style="width: 50% !important">Remarks</th>
                             <th class="table_heading_action">Action </th>
                         </tr>
                     </thead>
                     <tbody id="Attachment_File_table">
                         <tr></tr>
                     </tbody>
                     </tbody>

                 </table>

                 <style>
                     /* Correct class for setting borders to the table cells */
                     #Attachment_File_table td.for_border_in_td {
                         border: 1px solid rgb(199, 199, 199) !important;
                         padding: 4px !important;
                     }

                     #Attachment_File_table h6.border_in_h_tag {
                         margin: 10px !important;
                     }

                     #Attachment_File_table td.border_in_action_btn {
                         border: 1px solid rgb(199, 199, 199) !important;
                         padding: 4px !important;
                         text-align: center;
                     }
                 </style>

                 <script>
                     var attachment_lastItemId = 1; // Initial Item ID

                     function fetch_row_2(event) {
                         event.preventDefault();
                         var table = document.getElementById("Attachment_File_table");

                         @foreach ($selected_images as $images)
                             var newRow = table.insertRow(table.rows.length);

                             var cell1 = newRow.insertCell(0);
                             var cell2 = newRow.insertCell(1);
                             var cell3 = newRow.insertCell(2);

                             // Add the border class directly to the cell
                             cell1.classList.add('for_border_in_td');

                             // Add the content to cell1 with the required <h6> class
                             cell1.innerHTML = `
                                                    @if (!empty($images->file_path))
                                                       <a href="{{ asset('storage/' . $images->file_path) }}" target="_blank" rel="noopener noreferrer">
    <h6 class="border_in_h_tag">{{ $images->file_name }}</h6>
</a>
                                                    @else
                                                        <a>
                                                            <h6 class="border_in_h_tag">N/A</h6>
                                                        </a>
                                                    @endif
                                                    <div class="row" style="display:none">
                                                        <div>
                                                            <input type="hidden" id="image_link_${attachment_lastItemId}" name="file_path[]" value="{{ $images->file_path }}" class="form-control">
                                                            <input type="hidden" name="{{ $attachment_name }}[]" value="{{ $images->id }}" class="form-control">
                                                        </div>
                                                        <div class="col-lg-4"></div>
                                                    </div>
                                                    `;

                             // Add the border class directly to cell2
                             cell2.classList.add('for_border_in_td');
                             cell2.innerHTML = `<h6 class="border_in_h_tag">{{ $images->atch_remarks ?? 'N/A' }}</h6>`;


                             // Add the border and action button to cell3
                             cell3.classList.add('border_in_action_btn');
                             cell3.innerHTML =
                                 `<button class="btn btn-danger" onclick="deleteRow(this)"><i class="fas fa-minus-circle"></i></button>`;

                             attachment_lastItemId++;
                         @endforeach
                     }

                     function initializeTable_2() {
                         fetch_row_2(event);
                     }
                     document.addEventListener('DOMContentLoaded', initializeTable_2);
                 </script>

             </div>

         </div>
     </div>
 @endif
