let isMobile = window.matchMedia("(max-width: 768px)").matches;

// -------------------------- User Management --------------------------------------
// User_Management_table
$(document).ready(function () {
    let options = {
        responsive: true,
        fixedHeader: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 450,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( User Management Details ) ",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( User Management Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#User_Management_table", options);
});

// Role_Management_table
// -------------------------- Role Management --------------------------------------
$(document).ready(function () {
    let options = {
        responsive: true,
        // fixedColumns: {
        //     start: 1,
        //     end: 1,
        // },
        fixedHeader: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 450,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Role Management Details )",
                        exportOptions: {
                            columns: [0, 1],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Role Management Details )",
                    //     exportOptions: {
                    //         columns: [0, 1],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#Role_Management_table", options);
});

// customers table - customers_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Customer Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Customer Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#customers_table", options);
});

// enquiries_table -
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Enquiry Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Enquiry Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#enquiries_table", options);
});

// sales sauda table - sauda_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Sauda Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Sauda Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }
    // Initialize DataTables
    let dt = new DataTable("#sauda_table", options);
});

// invoice_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Invoice Details )",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,
                                14, 15,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Sauda Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#invoice_table", options);
});
// Payment_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Payment Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Sauda Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#payment_table", options);
});

// sales sauda items table - sauda_items_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Sauda Items Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Sauda Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#sauda_items_table", options);
});

// sales order table - sales_order_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Sales Order Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Sales Order Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#sales_order_table", options);
});


// stocks table - stocks_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 550,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Stock Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // If this is the reserved_qty column, extract just the numeric value
                                    if (column === 7) {
                                        // Extract numeric value from the button text
                                        const buttonText = $(node)
                                            .find("button")
                                            .text();
                                        const numericValue =
                                            parseFloat(buttonText);
                                        return isNaN(numericValue)
                                            ? "0.000"
                                            : numericValue.toFixed(3);
                                    }

                                    const span = $(node).find(".export-value");
                                    if (span.length) {
                                        return span.text().trim();
                                    }

                                    const button = $(node).find("button");
                                    if (button.length) {
                                        return button.text().trim();
                                    }

                                    // Fallback: plain cell text
                                    return $(node).text().trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Stock Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 6, 7],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#stocks_table", options);
});

// stocks adjustment table - stocks__adjustment_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Stocks Adjustment Details )",
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8, 9],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Stocks Adjustment Details )",
                    //     exportOptions: {
                    //         columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#stocks__adjustment_table", options);
});

// quotation table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Quotation Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Quotation Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#quotation_table", options);
});

// -------------------------- for Item Name table --------------------------------------

// item_name_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Item Name Details  )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Item Name Details  )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_name_table", options);
});

// warehouse_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( WareHouse Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( WareHouse Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#warehouse_table", options);
});

// gst_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( GST Settings Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#gst_table", options);
});

// item_name_price_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Item Price Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Item Price Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_name_price_table", options);
});

// -------------------------- for Item Master table --------------------------------------
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Item Category Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Item Group Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_gruop_table", options);
});

$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Item Size Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_master_table", options);
});

// for App User Table

$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( App User Mgmt Details  )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( App User Mgmt Details  )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#app_user_table", options);
});

// for rooling Program

$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Rolling  Program Request Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Rolling  Program Request Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#rolling_table", options);
});

// for dashboard_rolling_table Program

$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Rolling  Program Request Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "SunilSteel ( Rolling  Program Request Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#dashboard_rolling_table", options);
});

// Reports Table Datatables

// stock_transaction_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 430,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Stock Transaction Report Details)",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "SunilSteel ( Stock Transaction Report Details)",
                        orientation: "landscape",
                        // pageSize: "A3",  //for make extra large
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#stock_transaction_table", options);
});


// Modified by Md Raza - Starts

// Orders report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        // scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Orders Report Details)",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Orders Report Details)",
                        orientation: "landscape",
                        // pageSize: "A3",  //for make extra large
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }
    let dt = new DataTable("#orders_report_table", options);
});

// Dispatch report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        // scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Dispatch Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Dispatch Report Details)",
                        orientation: "landscape",
                        // pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }
    let dt = new DataTable("#dispatch_report_table", options);
});

// Dispatch report
// $(document).ready(function () {
//     // Initialize DataTables and store the instance in 'dt'
//     let options = {
//         responsive: true,
//         paging: true,
//         scrollCollapse: false,
//         scrollX: true,
//         scrollY: 500,
//         lengthMenu: [
//             [10, 25, 50, -1],
//             [10, 25, 50, "All"],
//         ],

//         layout: {
//             top1: {
//                 searchPanes: {
//                     viewTotal: true,
//                     columns: [0, 1],
//                     initCollapsed: true,
//                 },
//             },
//             topStart: {
//                 buttons: [
//                     {
//                         extend: "pageLength",
//                     },
//                     {
//                         extend: "excel",
//                         text: "Excel",
//                         title: "Singhal Steel ( Dispatch Report Details)",
//                         exportOptions: {
//                             columns: [
//                                 0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12,
//                             ],
//                             format: {
//                                 body: function (data, row, column, node) {
//                                     // Convert HTML → plain text
//                                     var text = $("<div>").html(data).text();

//                                     // Multiple spaces / line-breaks ko single space bana do
//                                     return text.replace(/\s+/g, " ").trim();
//                                 },
//                             },
//                         },
//                     },
//                     {
//                         extend: "pdfHtml5",
//                         text: "PDF",
//                         title: "Singhal Steel ( Dispatch Report Details)",
//                         orientation: "landscape",
//                         // pageSize: "A3",  //for make extra large
//                         exportOptions: {
//                             columns: [
//                                 0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12,
//                             ],
//                             format: {
//                                 body: function (data, row, column, node) {
//                                     var text = $("<div>").html(data).text();
//                                     return text.replace(/\s+/g, " ").trim();
//                                 },
//                             },
//                         },
//                         customize: function (doc) {
//                             // Decrease font size for compact view
//                             doc.defaultStyle.fontSize = 8;
//                             doc.styles.tableHeader.fontSize = 9;

//                             var table = doc.content[1].table;
//                             var columnCount = table.body[0].length;

//                             // Set all columns to auto → width adjusts to content/heading text
//                             table.widths = new Array(columnCount).fill("auto");

//                             // Make all header and body text left aligned
//                             doc.styles.tableHeader.alignment = "left";
//                             table.body.forEach(function (row, i) {
//                                 row.forEach(function (cell, j) {
//                                     cell.alignment = "left";
//                                 });
//                             });
//                         },
//                     },
//                 ],
//             },
//         },
//     };

//     // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
//     if (!isMobile) {
//         options.scrollX = true;
//         options.fixedColumns = {
//             end: 1,
//         };
//     }
//     let dt = new DataTable("#dispatch_report_table", options);
// });

// Item Price Report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Item Price Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Item Price Report Details)",
                        orientation: "landscape",
                        pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_price_report_table", options);
});

// Item Size Report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Item Sizes Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Item Sizes Report Details)",
                        orientation: "landscape",
                        pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_sizes_report_table", options);
});


// Distributor Team Report Details
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Distributor Team Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Distributor Team Report Details)",
                        orientation: "landscape",
                        pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#distributor_team_report_table", options);
});

// Dealers Report Details
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Dealers Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Dealers Report Details)",
                        orientation: "landscape",
                        pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#dealers_report_table", options);
});


// Distributors Report Details
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal Steel ( Distributors Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "Singhal Steel ( Distributors Report Details)",
                        orientation: "landscape",
                        pageSize: "A3", //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };

    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#distributors_report_table", options);
});


// Modified by Md Raza - Ends

// Invoice Report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Invoice Report Details)",
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,
                                14, 15,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "SunilSteel ( Invoice Report Details)",
                        orientation: "landscape",
                        // pageSize: "A3",  //for make extra large
                        exportOptions: {
                            columns: [
                                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13,
                                14, 15,
                            ],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#invoice_report_table", options);
});

// Payment Report
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Invoice Report Details)",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "SunilSteel ( Invoice Report Details)",
                        orientation: "landscape",
                        // pageSize: "A3",  //for make extra large
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#payment_report_table", options);
});

// Basic Price Report
// basic_price_report_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 500,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                searchPanes: {
                    viewTotal: true,
                    columns: [0, 1],
                    initCollapsed: true,
                },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "SunilSteel ( Basic Price Report Details)",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "pdfHtml5",
                        text: "PDF",
                        title: "SunilSteel ( Sauda Report Details)",
                        orientation: "A4",
                        // pageSize: "A3",  //for make extra large
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4],
                            format: {
                                body: function (data, row, column, node) {
                                    var text = $("<div>").html(data).text();
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                        customize: function (doc) {
                            // Decrease font size for compact view
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;

                            var table = doc.content[1].table;
                            var columnCount = table.body[0].length;

                            // Set all columns to auto → width adjusts to content/heading text
                            table.widths = new Array(columnCount).fill("auto");

                            // Make all header and body text left aligned
                            doc.styles.tableHeader.alignment = "left";
                            table.body.forEach(function (row, i) {
                                row.forEach(function (cell, j) {
                                    cell.alignment = "left";
                                });
                            });
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#basic_price_report_table", options);
});

// -------------------------- for filter buttons --------------------------------------

$(document).ready(function () {
    $(".dtsp-panesContainer").css("display", "none");
});

// -- singhal

// item_size_table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Item Size Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5,6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_size_table", options);
});

// Item Basic Price Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Item Basic Price Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_basic_price_table", options);
});

// Item Bundle Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Item Bundle Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_bundle_table", options);
});

// Distributors Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Distributors Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7 ,8 ,9],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#distributor_table", options);
});

// Dealers Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Dealers Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7 ,8, 9, 10],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#dealer_table", options);
});

// Distributor Teams Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Distributor's Teams Details )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7 ,8, 9],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( GST Settings Details )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#distributor_team_table", options);
});

// ordrs table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Orders )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7 ,8, 9, 10],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "print",
                        text: "Print",
                        title: "Singhal ( Orders )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#orders_table", options);
});

// warehouse table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Warehouses )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "print",
                        text: "Print",
                        title: "Singhal ( Warehouses )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#warehouses_table", options);
});

// Loading Points Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Loading Points )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "print",
                        text: "Print",
                        title: "Singhal ( Loading Points )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#loading_points_table", options);
});

// Dispatch Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Dispatch Table )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 11],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    {
                        extend: "print",
                        text: "Print",
                        title: "Singhal (Dispatch Table )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
                        },
                    },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        options.fixedColumns = {
            end: 1,
        };
    }

    // Initialize DataTables
    let dt = new DataTable("#dispatch_table", options);
});

// Dealer order limit Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Dealer Order Limit Requests )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( Loading Points )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#dlr_ol_req", options);
});

// Distributor order limit Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Distributor Order Limit Requests )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( Loading Points )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#dis_ol_req", options);
});

// Dealer approval request Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Distributor Order Limit Requests )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( Loading Points )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#dlr_approval_req", options);
});

// App User Table
$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( App Users )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( Loading Points )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#app-user-table", options);
});

$(document).ready(function () {
    // Initialize DataTables and store the instance in 'dt'
    let options = {
        responsive: true,
        paging: true,
        scrollCollapse: false,
        scrollX: true,
        scrollY: 530,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],

        layout: {
            top1: {
                // searchPanes: {
                //     viewTotal: true,
                //     columns: [0, 1],
                //     initCollapsed: true,
                // },
            },
            topStart: {
                buttons: [
                    {
                        extend: "pageLength",
                    },
                    {
                        extend: "excel",
                        text: "Excel",
                        title: "Singhal ( Item Basic Price Rejected Requests )",
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7],
                            format: {
                                body: function (data, row, column, node) {
                                    // Convert HTML → plain text
                                    var text = $("<div>").html(data).text();

                                    // Multiple spaces / line-breaks ko single space bana do
                                    return text.replace(/\s+/g, " ").trim();
                                },
                            },
                        },
                    },
                    // {
                    //     extend: "print",
                    //     text: "Print",
                    //     title: "Singhal ( Loading Points )",
                    //     exportOptions: {
                    //         columns: [0, 1, 2, 3, 4, 5],
                    //     },
                    // },
                ],
            },
        },
    };
    // Agar mobile nahi hai (desktop/tablet) tab hi fixedColumns add karo
    if (!isMobile) {
        options.scrollX = true;
        // options.fixedColumns = {
        //     end: 1,
        // };
    }

    // Initialize DataTables
    let dt = new DataTable("#item_basic_price_rejected_table", options);
});
