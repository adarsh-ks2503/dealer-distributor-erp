<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Dispatch document</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            font-size: 11px;
            /* Adjusted base font size */
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 5px;
        }

        /* --- UPDATED HEADER STYLES --- */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table .logo-cell {
            width: 25%;
            vertical-align: top;
        }

        .header-table .info-cell {
            width: 50%;
            text-align: center;
        }

        .header-table .meta-cell {
            width: 25%;
            text-align: right;
            vertical-align: top;
            font-size: 12px;
        }

        .company-main-name {
            font-size: 17px;
            font-weight: bold;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .company-unit {
            font-size: 16px;
            font-weight: bold;
            margin-top: 4px;
        }

        .company-address {
            font-size: 10px;
            margin-top: 4px;
            line-height: 1.3;
        }

        .document-type-header {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 0;
            display: inline-block;
            /* Allows border to wrap content */
        }

        /* --- END OF UPDATED HEADER STYLES --- */

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #003366;
            background-color: #f2f7ff;
            padding: 8px;
            border: 1px solid #ccc;
        }

        .details-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td,
        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .details-table th {
            width: 25%;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .items-table thead {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table tfoot td {
            font-weight: bold;
        }

        .font-dejavu {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .closing-section {
            margin-top: 40px;
        }

        .signatures {
            width: 100%;
            margin-top: 50px;
        }

        .signatures td {
            text-align: center;
            width: 33.33%;
            padding-top: 40px;
            border-top: 1px solid #999;
        }

        .page-footer {
            position: fixed;
            bottom: 50px;
            right: 20px;
            text-align: right;
        }

        .page-footer img {
            width: 150px;
            height: auto;
        }
    </style>

    <style>
        @page {
            /* Page ke margins kam karein taaki content ke liye zyada jagah mile */
            margin: 1cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            font-size: 10.5px;
            /* Font size thoda chota kiya */
            margin: 0;
            line-height: 1.3;
            /* Line spacing kam ki */
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 5px;
        }

        /* --- HEADER STYLES --- */
        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            /* Border patla kiya */
            padding-bottom: 8px;
            margin-bottom: 15px;
            /* Header ke neeche margin kam kiya */
        }

        .header-table .logo-cell {
            width: 20%;
            vertical-align: top;
        }

        .header-table .logo-cell img {
            width: 100px;
            /* Logo thoda chota kiya */
            height: auto;
        }

        .header-table .info-cell {
            width: 55%;
            text-align: center;
            vertical-align: top;
        }

        .header-table .meta-cell {
            width: 25%;
            text-align: right;
            vertical-align: top;
            font-size: 11px;
        }

        .company-main-name {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .company-address {
            font-size: 9px;
            margin-top: 4px;
            line-height: 1.3;
        }

        .document-type-header {
            display: inline-block;
            font-size: 13px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 4px 15px;
            margin-top: 8px;
        }

        /* --- GENERAL STYLES --- */
        .section {
            margin-bottom: 10px;
            /* Sections ke beech ka gap kam kiya */
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #003366;
            background-color: #f2f7ff;
            padding: 6px;
            /* Padding kam ki */
            border: 1px solid #ccc;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #ccc;
            padding: 6px;
            /* Table cell padding kam ki */
            text-align: left;
            vertical-align: top;
        }

        .details-table th {
            width: 20%;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .status {
            padding: 3px 8px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }

        .status-pending {
            background-color: #f0ad4e;
        }

        .status-completed {
            background-color: #5cb85c;
        }

        .status-cancelled {
            background-color: #d9534f;
        }

        .text-right {
            text-align: right;
        }

        /* --- SIGNATURE & FOOTER --- */
        .declaration-box {
            margin-top: 20px;
            border: 1px solid #333;
            padding: 10px;
            font-size: 9.5px;
            line-height: 1.3;
        }

        .declaration-box .signatory-line {
            margin-top: 25px;
            /* Signature ke liye space kam kiya */
            text-align: right;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            right: 30px;
            text-align: right;
        }

        .footer img {
            width: 120px;
            height: auto;
        }
    </style>

    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
            /* tighter margins to save space */
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
                /* prevent extra page spillover */
            }

            /* Make colors render accurately in print */
            body {
                font-size: 9px;
                /* reduce overall font-size */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                line-height: 1.35;
            }

            /* Scale content to fit a single page while keeping layout intact */
            .container {
                transform: scale(0.92);
                /* adjust 0.90–0.95 if needed */
                transform-origin: top left;
                width: calc(100% / 0.92);
                /* counteract scale so layout width remains aligned */
            }

            /* Reduce vertical rhythm */
            .section {
                margin-bottom: 10px;
            }

            /* Header compaction */
            .header-table {
                margin-bottom: 10px;
                padding-bottom: 6px;
                border-bottom-width: 1px;
            }

            .header-table .logo-cell img {
                width: 90px;
                height: auto;
                margin-bottom: 3px;
            }

            .company-main-name {
                font-size: 14px;
                /* tighter heading size in print */
            }

            .document-type-header {
                padding: 3px 8px;
                font-size: 12px;
                margin-top: 6px;
            }

            /* Tables: tighter padding + type sizes */
            .details-table th,
            .details-table td {
                padding: 4px;
                font-size: 9px;
                line-height: 1.25;
            }

            /* Declaration box: reduce spacing */
            .declaration-box {
                padding: 10px;
                font-size: 9px;
            }

            .declaration-box .signatory-line {
                margin-top: 24px;
                /* was 50px */
            }

            /* Footer: keep small, avoid overlapping too much */
            .footer {
                bottom: 10px;
                right: 15px;
            }

            .footer img {
                width: 90px;
                height: auto;
            }

            /* Try to keep rows together to avoid breaking important lines */
            table,
            tr,
            td,
            th {
                page-break-inside: avoid;
            }
        }
    </style>
    <link href="{{ $logoBase64 }}" rel="icon">
    <link href="{{ $logoBase64 }}" rel="apple-touch-icon">
</head>

<body>
    <div class="container">

        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if (!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Company Logo" style="width: 150px; height: auto;">
                    @endif
                    <div>ISO 9001:2015 Certified Company</div>
                </td>
                <td class="info-cell">
                    <div style="text-decoration: underline;">Subject to Raigarh Jurisdiction</div>
                    <div class="company-main-name"> {{ $company_settings->name ?? 'SINGHAL STEEL & POWER PVT. LTD.' }}
                    </div>
                    <div>(Formerly known as Singhal Enterprises Pvt Ltd)</div>
                    {{-- <div class="company-unit">(GERWANI UNIT)</div> --}}
                    <div class="company-address">
                        {!! $company_settings->address ??
                            '13 KM MILESTONE, AMBIKAPUR ROAD, VILLAGE: GERWANI, RAIGARH <br>
                                                                                                Regd Office : 303 CENTURY TOWER, 45 SHAKESPEARE SARANI, KOLKATA-700017 <br>' !!} <br>
                        {{-- Regd Office : 303 CENTURY TOWER, 45 SHAKESPEARE SARANI, KOLKATA-700017 <br> --}}
                        Phone No. : {{ $company_settings->phone_number ?? 'N/A' }} | Email :
                        {{ $company_settings->email ?? 'N/A' }}
                    </div>
                    <div class="document-type-header">DISPATCH DETAILS</div>
                </td>
                <td class="meta-cell">
                    {{-- <div><strong>Dispatch No:</strong> {{ $dispatch->dispatch_number }}</div>
                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($dispatch->dispatch_date)->format('d M, Y') }}
                    </div> --}}
                </td>
            </tr>
        </table>
        <div class="section">
            <div class="section-title">DISPATCH & TRANSPORT DETAILS</div>
            <table class="details-table">
                <tr>
                    <th>Dispatch No</th>
                    <td>{{ $dispatch->dispatch_number }}</td>
                    <th>Dispatch Date</th>
                    <td>{{ \Carbon\Carbon::parse($dispatch->dispatch_date)->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <th>Dispatched To</th>
                    <td>{{ $dispatch->consignee_name ?? 'N/A' }}</td>
                    <th>Vehicle Number</th>
                    <td>{{ $dispatch->vehicle_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Order Type</th>
                    <td style="text-transform: capitalize;">{{ $dispatch->type }}</td>
                    <th>Driver Name</th>
                    <td>{{ $dispatch->driver_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Warehouse</th>
                    <td>{{ $dispatch->warehouse?->name ?? 'N/A' }}</td>
                    <th>Driver Mob.</th>
                    <td style="text-transform: capitalize;">{{ $dispatch->driver_mobile_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Total Dispatched Qty</th>
                    <td>{{ $totalQty ?? '0' }} MT</td>
                    <th>Dispatch Time</th>
                    {{-- <td>{{ $dispatch->dispatch_out_time ?? 'N/A' }}</td> --}}
                    <td>
                        {{ $dispatch->dispatch_out_time ? date('h:i A', strtotime($dispatch->dispatch_out_time)) : 'N/A' }}
                    </td>
                </tr>

                <!-- 🆕 Add your custom data rows below (same format) -->
                <tr>
                    <th>Bill Number</th>
                    <td>{{ $dispatch->bill_number ?? 'N/A' }}</td>
                    <th>Transporter Name</th>
                    <td>{{ $dispatch->transporter_name ?? 'N/A' }}</td>
                </tr>
                {{-- <tr>
        <th>Contact Number</th>
        <td>{{ $dispatch->contact_number ?? 'N/A' }}</td>
        <th>Remarks</th>
        <td>{{ $dispatch->remarks ?? 'N/A' }}</td>
    </tr> --}}
                {{-- <tr>
        <th>Created By</th>
        <td>{{ $dispatch->createdBy?->name ?? 'System' }}</td>
        <th>Updated At</th>
        <td>{{ \Carbon\Carbon::parse($dispatch->updated_at)->format('d M, Y h:i A') }}</td>
    </tr> --}}
            </table>

        </div>


        {{-- <div class="section">
            <div class="section-title">ITEM LIST</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No</th>
                        <th>Item / Size</th>
                        <th class="text-right">Dispatch Qty (MT)</th>
                        <th class="text-right">Basic Price</th>
                        <th class="text-right">GST</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dispatch->dispatchItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->order?->order_number }}</td>
                            <td>{{ $item->item?->name ?? 'TMT Bar' }} / {{ $item->size?->size ?? 'N/A' }} mm</td>
                            <td class="text-right">{{ $item->dispatch_qty }}</td>
                            <td class="text-right"><span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->basic_price) }}
                            </td>
                            <td class="text-right">{{ $item->gst }}%</td>
                            <td class="text-right"><span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->total_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">No items in this dispatch.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="border: none;"><strong>Amount in Words:</strong>
                            {{ $amountInWords ?? '' }}</td>
                        <td class="text-right"><strong>Grand Total:</strong></td>
                        <td class="text-right"><span
                                class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($dispatch->dispatchItems->sum('total_amount')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div> --}}
        <div class="section">
            <div class="section-title">ITEM LIST</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order No</th>
                        <th>Item / Size / Length</th>
                        <th class="text-right">Dispatch Qty (MT)</th>
                        <th class="text-right">Basic Price (MT)</th>
                        <th class="text-right">Gauge Diff</th>
                        <th class="text-right">Final Price (MT)</th>
                        <th class="text-right">Loading Charge (MT)</th>
                        <th class="text-right">Insurance Charge (MT)</th>
                        <th class="text-right">GST</th>
                        <th class="text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dispatch->dispatchItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->order?->order_number }}</td>
                            <td>
                                {{ $item->item?->name ?? 'SINGHAL TMT' }} /
                                {{ $item->size?->size ?? 'N/A' }} mm /
                                {{ rtrim(rtrim($item->length, '0'), '.') }} Mtr
                            </td>
                            <td class="text-right">{{ rtrim(rtrim(number_format($item->dispatch_qty, 2), '0'), '.') }}
                            </td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->basic_price) }}
                            </td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->gauge_diff) }}
                            </td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->final_price) }}
                            </td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->loading_charge) }}
                            </td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->insurance) }}
                            </td>
                            <td class="text-right">{{ $item->gst }}%</td>
                            <td class="text-right">
                                <span
                                    class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($item->total_amount) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center;">No items in this dispatch.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        {{-- Updated colspan to match the new number of columns --}}
                        <td colspan="9" style="border: none;"><strong>Amount in Words:</strong>
                            {{ $amountInWords ?? '' }}
                        </td>
                        <td class="text-right"><strong>Grand Total:</strong></td>
                        <td class="text-right">
                            <span
                                class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($dispatch->dispatchItems->sum('total_amount')) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="closing-section">
            <div class="section-title">TERMS AND CONDITIONS</div>
            <table class="details-table">
                <tr>
                    <td>{!! $dispatch->terms_conditions ?? 'Standard terms and conditions apply.' !!}</td>
                </tr>
            </table>

            <table class="signatures">
                <tr>
                    <td>Receiver's Signature</td>
                    <td>Driver's Signature</td>
                    <td>Authorized Signatory</td>
                </tr>
            </table>
        </div>

    </div>
    <div class="page-footer">
        @if (!empty($sealBase64))
            <img src="{{ $sealBase64 }}" alt="Singhal Steel Seal">
        @else
            <p style="font-weight: bold; margin-top: 5px;">For Singhal Steel</p>
        @endif
    </div>
</body>

</html>
