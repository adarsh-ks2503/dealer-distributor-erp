 

function getMaker(id, csrf, uniqueId, type) {
    console.log(uniqueId);
    $.ajax({
        url: "get_makers",
        method: "POST",
        data: {
            item_id: id,
            _token: csrf,
        },
        success: function (res) {
            let htmldata = '<option value="">Select</option>';
            res.forEach((item) => {
                htmldata += `<option value="${item.maker_id}">${item.maker_title}</option>`;
            });
            if (type === 'client') {
                $(`#client_maker_${uniqueId}`).empty().html(htmldata);
            } else {
                $(`#internal_maker_${uniqueId}`).empty().html(htmldata);
            }
        },
        error: function (xhr, status, error) {
            if (type === 'client') {
                $(`#client_maker_${uniqueId}`).empty();
            }
            else {
                $(`#internal_maker_${uniqueId}`).empty();
            }


            console.error("An error occurred:", error);
        },
    });
}





// ==================== for UOM module ====================================
function check_uom_name(csrf) {
    let new_title = $("#check_uom_title").val();
    // console.log(name);
    $.ajax({
        url: "check-uom-name",
        method: "POST",
        data: {
            name: new_title,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_title = res.uom_title;
            if (old_title.toLowerCase() === new_title.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Dupplicate entry found.",
                }).then(() => {
                    ResetRowin_uoms();
                });
            }
        },
    });
}

function check_uom_name_in_edit(id, csrf) {
    let new_title = $("#check_uom_title").val();
    $.ajax({
        url: "edit-uom-name",
        method: "POST",
        data: {
            id: id,
            new_title: new_title,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_title = res.uom_title;
            if (old_title.toLowerCase() === new_title.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_uoms();
                });
            }
        },
    });
}

function ResetRowin_uoms() {
    $("#check_uom_title").val("").trigger("change");
}

// ==================== for maker module ====================================
function check_maker_name(csrf) {
    let new_title = $("#check_maker_title").val();
    $.ajax({
        url: "check-maker-name",
        method: "POST",
        data: {
            new_title: new_title,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_title = res.maker_title;
            if (old_title.toLowerCase() === new_title.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_makers();
                });
            }
        },
    });
}
function check_edit_maker_name(id, csrf) {
    let new_title = $("#check_maker_title").val();
    $.ajax({
        url: "edit-check-maker-name",
        method: "POST",
        data: {
            id: id,
            new_title: new_title,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_title = res.maker_title;
            if (old_title.toLowerCase() === new_title.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_makers();
                });
            }
        },
    });
}
function ResetRowin_makers() {
    $("#check_maker_title").val("").trigger("change");
}

// ==================== for item master module ====================================
function check_item_master_name(csrf) {
    let new_title = $("#itemdescription").val();
    let new_item_uoms = $("#item_uoms").val();
    $.ajax({
        url: "check-item-master-name",
        method: "POST",
        data: {
            new_title: new_title,
            new_item_uoms: new_item_uoms,
            _token: csrf,
        },
        success: function (res) {
            console.log(res);
            let old_title = res.itemdescription;
            let old_uom = res.uom;
            if (
                old_title.toLowerCase() === new_title.toLowerCase() &&
                old_uom.toLowerCase() == new_item_uoms.toLowerCase()
            ) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_itemdescription();
                });
            }
        },
    });
}
function check_edit_item_master_name(id, csrf) {
    let new_title = $("#itemdescription").val();
    let new_item_uoms = $("#item_uoms").val();

    $.ajax({
        url: "edit-check-item-master-name",
        method: "POST",
        data: {
            id: id,
            new_title: new_title,
            new_item_uoms: new_item_uoms,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_title = res.itemdescription;
            let old_uom = res.uom;

            if (
                old_title.toLowerCase() === new_title.toLowerCase() &&
                old_uom.toLowerCase() == new_item_uoms.toLowerCase()
            ) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_itemdescription();
                });
            }
        },
    });
}
function ResetRowin_itemdescription() {
    $("#itemdescription").val("").trigger("change");
    $("#item_uoms").val("").trigger("change");
}

// ==================== for client vendor module ====================================

function check_client_vendor_name(csrf) {
    let new_c_v_name = $("#client_vendor_name").val();
    $.ajax({
        url: "check-client-vendor-and-prefix",
        method: "POST",
        data: {
            new_c_v_name: new_c_v_name,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_name = res.name_data.company_name;
            if (new_c_v_name.toLowerCase() == old_name.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_vendor_name();
                });
            }
        },
    });
}
function check_client_vendor_name_in_edit(id, csrf) {
    let new_c_v_name = $("#client_vendor_name").val();
    let c_id = id;
    let url = "/client-vendor-name-edit";

    $.ajax({
        url: url,
        method: "POST",
        data: {
            new_c_v_name: new_c_v_name,
            c_id: c_id,
            _token: csrf,
        },
        success: function (res) {
            let old_name = res.name_data.company_name;
            if (new_c_v_name.toLowerCase() === old_name.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Oops!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_vendor_name();
                });
            }
        },
    });
}

function ResetRowin_vendor_name() {
    $("#client_vendor_name").val("").trigger("change");
}

function check_prefix_name(csrf) {
    let new_prefix = $("#prefix_name").val();

    $.ajax({
        url: "check-prefix-name",
        method: "POST",
        data: {
            new_prefix: new_prefix,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_prefix = res.prefix_data.prefix;
            if (new_prefix.toLowerCase() == old_prefix.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_new_prefix();
                });
            }
        },
    });
}
function check_prefix_name_in_edit(id, csrf) {
    let new_prefix = $("#prefix_name").val();
    let c_id = id;
    let url = "/check-prefix-name-edit";

    $.ajax({
        url: url,
        method: "POST",
        data: {
            new_prefix: new_prefix,
            c_id: c_id,
            _token: csrf,
        },
        success: function (res) {
            // console.log(res);
            let old_prefix = res.prefix_data.prefix;
            if (new_prefix.toLowerCase() == old_prefix.toLowerCase()) {
                Swal.fire({
                    icon: "error",
                    title: "Opps!",
                    text: "Duplicate entry found.",
                }).then(() => {
                    ResetRowin_new_prefix();
                });
            }
        },
    });
}
function ResetRowin_new_prefix() {
    $("#prefix_name").val("").trigger("change");
}


// ==================== Follow Up module ====================================

function get_client_data(id, csrf) {
    let client_id = id;
    console.log(client_id);
    $.ajax({
        url: "get_data_client",
        method: "POST",
        data: {
            client_id: client_id,
            _token: csrf,
        },
        success: function (res) {
            console.log(res);
            document.getElementById('client_name').value = res.company.contact_person;
            document.getElementById('mobile').value = res.company.mobile;
            document.getElementById('email').value = res.company.email;

            let data = res.enquiry;
            if (data) {
                let htmldata = '<option value="">Select Enquiry</option>';
                for (let item of data) {
                    htmldata += `<option value="${item.id}">${item.enq_number}</option>`;
                }
                $("#enquiry_id").html(htmldata);
            }
            let data_2 = res.offer;

            if (data_2) {
                let htmldata = '<option value="">Select Offer</option>';
                for (let item of data_2) {
                    htmldata += `<option value="${item.id}">${item.ofr_number}</option>`;
                }
                $("#offer_id").html(htmldata);
            }
        },
    });
}


