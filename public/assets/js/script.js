var csrfToken = "{{ csrf_token() }}";

// Hide sidebar on page load
// window.addEventListener('DOMContentLoaded', function () {
//     document.body.classList.add('toggle-sidebar');
// });

// for datatale column resize after toggle button clicked
$(document).ready(function () {
    $(".toggle-sidebar-btn").on("click", function () {
        setTimeout(function () {
            $.fn.dataTable.tables({ api: true }).columns.adjust();
        }, 200);
    });
});

$(document).ready(function () {
    $(".stock__nav_tab").on("click", function () {
        setTimeout(function () {
            $.fn.dataTable.tables({ api: true }).columns.adjust();
        }, 200);
    });
});

document.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-button")) {
        Swal.fire({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.closest("form").submit();
            }
        });
    }
});
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("Approve-button")) {
        Swal.fire({
            title: "Are you sure?",
            // text: "Once deactivate, this action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Approved it!",
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.closest("form").submit();
            }
        });
    }
});
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("activate-button")) {
        Swal.fire({
            title: "Are you sure?",
            // text: "Once deactivate, this action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, activate it!",
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.closest("form").submit();
            }
        });
    }
});
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("inactive-button")) {
        Swal.fire({
            title: "Are you sure?",
            // text: "Once deactivate, this action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, inactive it!",
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.closest("form").submit();
            }
        });
    }
});
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("rejected-button")) {
        Swal.fire({
            title: "Are you sure?",
            // text: "Once deactivate, this action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, rejected it!",
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.closest("form").submit();
            }
        });
    }
});
// ==================== for notification status UI ====================================
document.addEventListener("DOMContentLoaded", () => {
    const toast = document.querySelector(".tt");
    const closeIcon = document.querySelector(".close");
    const progress = document.querySelector(".pg");

    let timer1, timer2;

    if (toast) {
        // Display the toast message and progress bar
        toast.classList.add("active");
        progress.classList.add("active");

        // Set timeout to automatically close the toast message
        timer1 = setTimeout(() => {
            toast.classList.remove("active");
        }, 5000); // 1s = 1000 milliseconds

        // Set timeout to automatically remove the progress bar
        timer2 = setTimeout(() => {
            progress.classList.remove("active");
        }, 5300);

        // Add event listener to the close icon to manually close the toast message
        closeIcon.addEventListener("click", () => {
            toast.classList.remove("active");

            setTimeout(() => {
                progress.classList.remove("active");
            }, 300);

            clearTimeout(timer1);
            clearTimeout(timer2);
        });
    }
});
setTimeout(function () {
    var element = document.querySelector(".tt");
    if (element) {
        element.style.display = "none";
    }
}, 5100);

// ==================== for enquiry module ====================================
$(document).ready(function () {
    //click add row button
    $("#addRowBtn").click();
    $("#addRowBtn").hide();

    $("#addRowInternal_1").click();
    $("#addRowInternal_1").hide();

    $("#addRowBtn_for_img").click();
    $("#addRowBtn_for_img").hide();

    $("#addRowClient_1").click();
    $("#addRowClient_1").hide();
});

// ==================== for all select2 options ====================================
$(document).ready(function () {
    $("#item_name_input").select2();
    $("#invoice_state").select2();
    $("#delivery_state").select2();
    $("#invoice_city").select2();
    $("#delivery_city").select2();
    $("#lead_by").select2();
    $("#warehouse_input").select2();
    $("#item_size_input").select2();
    $("#stk_user_id").select2();

    $(".js-example-basic-multiple").select2();
});

// ==================== for required error ====================================

document.addEventListener("DOMContentLoaded", function () {
    const inputFields = document.querySelectorAll("input[required]");
    const submitButton = document.querySelector("[type=submit]");

    inputFields.forEach(function (input) {
        input.addEventListener("input", function () {
            validateField(input);
        });
        input.addEventListener("blur", function () {
            validateField(input);
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        let submitButton = document.getElementById("submitButton");
        // Ensure submitButton is not null
        if (submitButton) {
            submitButton.addEventListener("click", function (event) {
                let allValid = true;
                inputFields.forEach(function (input) {
                    validateField(input);
                    if (!input.value.trim()) {
                        allValid = false;
                    }
                });

                if (!allValid) {
                    event.preventDefault();
                }
            });
        }
    });

    function validateField(input) {
        let errorDiv = input.nextElementSibling;

        if (!input.value.trim()) {
            input.style.border = "1px solid red";

            if (!errorDiv || !errorDiv.classList.contains("error-message")) {
                errorDiv = document.createElement("div");
                errorDiv.classList.add("error-message");
                errorDiv.style.color = "red";
                errorDiv.textContent = "Required field";
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
            errorDiv.style.display = "block";
        } else {
            input.style.border = "";
            if (errorDiv && errorDiv.classList.contains("error-message")) {
                errorDiv.style.display = "none";
            }
        }
    }
});
