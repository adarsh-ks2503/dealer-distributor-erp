<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Order Document</title>
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
                <!-- LEFT: Company Logo -->
                <td class="logo-cell" style="text-align: left; vertical-align: top;">
                    @if (!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Singhal Steel Logo"
                            style="width: 100px; height: auto; margin-bottom: 5px;">
                    @endif
                    <div style="font-size: 9px; color: #555;">ISO 9001:2015 Certified Company</div>
                </td>

                <!-- CENTER: Company Info -->
                <td class="info-cell" style="text-align: center; line-height: 1.4;">
                    <div style="text-decoration: underline; font-size: 11px; margin-bottom: 4px;">
                        Subject to Raigarh Jurisdiction
                    </div>

                    <div class="company-main-name" style="font-size: 18px; font-weight: 800; color: #000;">
                        {{ $company_settings->name ?? 'SINGHAL STEEL & POWER PVT. LTD.' }}
                    </div>

                    <div style="font-size: 11px; font-weight: 500;">(Formerly known as Singhal Enterprises Pvt Ltd)
                    </div>

                    <div class="company-address" style="font-size: 10px; margin-top: 6px; line-height: 1.3;">
                        {!! $company_settings->address ??
                            '13 KM MILESTONE, AMBIKAPUR ROAD, VILLAGE: GERWANI, RAIGARH <br> Regd Office : 303 CENTURY TOWER, 45 SHAKESPEARE SARANI, KOLKATA-700017 <br>' !!}
                        <br>
                        Phone No. : {{ $company_settings->phone_number ?? 'N/A' }} |
                        Email : {{ $company_settings->email ?? 'N/A' }}
                    </div>

                    <div class="document-type-header"
                        style="margin-top: 10px; font-size: 13px; border: 1px solid #000; padding: 5px 10px; display: inline-block;">
                        ORDER DOCUMENT
                    </div>
                </td>

                <!-- RIGHT: Order Meta Info -->
                <td class="meta-cell" style="text-align: right; font-size: 12px; vertical-align: top;">
                    {{-- <div><strong>Order No:</strong> {{ $order->order_number }}</div>
                    <div><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</div> --}}
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">BASIC INFORMATION</div>
            <table class="details-table">
                <tr>
                    <th>Placed By</th>
                    <td>{{ $order->dealer?->name ?? ($order->distributor?->name ?? 'N/A') }}</td>
                    {{-- <th>Status</th> --}}
                    {{-- <td>
                        <span class="">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td> --}}
                    <th>Created By</th>
                    <td>{{ $order->created_by }}</td>
                </tr>
                <tr>
                    <th>Order No</th>
                    <td>{{ $order->order_number }}</td>
                    <th>Overall Remark</th>
                    <td>{{ $order->remarks }}</td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}</td>
                    {{-- <th>Order No</th>
                    <td>{{ $order->order_number }}</td> --}}
                    <th></th>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">CHARGES & PAYMENTS</div>
            <table class="details-table">
                <tr>
                    <th>Loading Charge (MT)</th>
                    <td><span class="font-dejavu"></span> {{ \App\Helpers\NumberHelper::formatIndianCurrency($order->loading_charge) }}</td>
                    <th>Insurance Charge (MT)</th>
                    <td><span class="font-dejavu"></span> {{ \App\Helpers\NumberHelper::formatIndianCurrency($order->insurance_charge) }}</td>
                </tr>
                <tr>
                    <th>Token Amount Received</th>
                    <td colspan="3"><span class="font-dejavu"></span>
                        {{ \App\Helpers\NumberHelper::formatIndianCurrency($order->allocations->sum('token_amount')) }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">ALLOCATIONS</div>
            <table class="details-table">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <td style="font-weight: bold;">#</td>
                        <td style="font-weight: bold;">Allocated To</td>
                        <td style="font-weight: bold;" class="text-right">Qty (MT)</td>
                        <td style="font-weight: bold;" class="text-right">Agreed Basic Price (MT)</td>
                        <td style="font-weight: bold;">Payment Term</td>
                        <td style="font-weight: bold;" class="text-right">Total</td>
                    </tr>
                </thead>
                <tbody>
    @php $subtotal = 0; @endphp @forelse($order->allocations as $allocation)
        @php
            // !! YEH RAHA CALCULATION FIX !!
            // Order-level charges ko price mein jodo
            $priceWithCharges = $allocation->agreed_basic_price + $order->loading_charge + $order->insurance_charge;
            // Ab line total calculate karo
            $lineTotal = $allocation->qty * $priceWithCharges;
            
            $subtotal += $lineTotal; // Hum $subtotal variable ko hi grand total ke liye use kar lenge
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $allocation->allocatedTo->name ?? 'N/A' }}</td>
            <td class="text-right">{{ $allocation->qty }}</td>
            <td class="text-right">
                <span class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($allocation->agreed_basic_price) }}
            </td>
            <td>{{ $allocation->payment_terms }}</td>
            <td class="text-right">
                <span class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($lineTotal) }}
            </td>
        </tr>
    @empty
        @endforelse
</tbody>
                <tfoot>
    {{-- 
    <tr>
        <td colspan="4" style="border: none;"></td>
        <td style="font-weight: bold; text-align: left;">Subtotal:</td>
        <td style="font-weight: bold; text-align: left;"><span
                class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($subtotal) }}</td>
    </tr> 
    --}}

    {{--
    <tr>
        <td colspan="4" style="border: none;"></td>
        <td style="text-align: left;">Loading & Insurance:</td>
        <td style="text-align: left;"><span
                class="font-dejavu"></span>{{ \App\Helpers\NumberHelper::formatIndianCurrency($order->loading_charge + $order->insurance_charge) }}
        </td>
    </tr>
    --}}

    <tr>
        <td colspan="4" style="border: none;"></td>
        <td style="font-weight: bold; text-align: left;">Grand Total:</td>
        <td style="font-weight: bold; text-align: left;">
            <span class="font-dejavu"></span>
            {{ \App\Helpers\NumberHelper::formatIndianCurrency($grandTotal) }}
        </td>
    </tr>
</tfoot>
            </table>
            <div class="footer-notes">
                <p>
                    <strong>Amount In Words:</strong> {{ $amountInWords ?? '' }}
                </p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">TERMS AND CONDITIONS</div>
            <table class="details-table">
                <tr>
                    <td>{!! $order->terms_conditions ?? 'Standard terms and conditions apply.' !!}</td>
                </tr>
            </table>
            <div class="declaration-box">
                <p>
                    Certified that the particulars given above are true and correct and the amount indicated represents
                    the price actually charged and that there is no flow additional consideration flowing directly or
                    indirectly from the buyer.
                </p>
                <div class="signatory-line">
                    Authorized Signatory
                </div>
            </div>
            <div class="footer">
                @if (!empty($sealBase64))
                    <img src="{{ $sealBase64 }}" alt="Singhal Steel Seal">
                @else
                    <p style="font-weight: bold; margin-top: 5px;">For Singhal Steel</p>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
