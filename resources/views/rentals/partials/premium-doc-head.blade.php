@php
    $docTitle = $docTitle ?? 'SewaJas Document';
    $outputMode = $outputMode ?? 'web';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $docTitle }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            height: auto;
            font-family: Segoe UI, Tahoma, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111827;
            background: #ffffff;
        }

        /* Colors */
        .text-primary { color: #1E40AF; }
        .text-muted { color: #64748B; }
        .text-success { color: #15803D; }
        .text-warning { color: #A16207; }
        .text-danger { color: #B91C1C; }

        .bg-white { background-color: #ffffff; }
        .bg-muted { background-color: #F8FAFC; }
        .bg-light { background-color: #F1F5F9; }

        /* Utils */
        .w-full { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }
        .uppercase { text-transform: uppercase; }
        .leading-tight { line-height: 1.25; }

        /* Page wrapper */
        .page {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* Table base */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px 10px;
            vertical-align: top;
        }

        th {
            background: #F8FAFC;
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #E5E7EB;
        }

        td {
            font-size: 11px;
            color: #111827;
            border-bottom: 1px solid #F1F5F9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Header */
        .header-section {
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .header-table td {
            padding: 0;
            vertical-align: top;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            background: linear-gradient(135deg, #1E40AF, #3B82F6);
            color: #ffffff;
            font-weight: 800;
            font-size: 20px;
            text-align: center;
            line-height: 54px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
        }

        .brand-name {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            margin-top: 10px;
            letter-spacing: -0.01em;
        }

        .brand-detail {
            font-size: 10px;
            color: #64748B;
            margin-top: 3px;
            line-height: 1.6;
        }

        .invoice-title {
            font-size: 36px;
            font-weight: 800;
            color: #1E40AF;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .invoice-meta {
            margin-top: 10px;
        }

        .doc-meta-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .doc-meta-value {
            font-size: 11px;
            font-weight: 700;
            color: #111827;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            vertical-align: middle;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            line-height: 1.4;
        }

        .status-paid { background: #DCFCE7; color: #15803D; }
        .status-unpaid { background: #FEE2E2; color: #B91C1C; }
        .status-partial { background: #FEF3C7; color: #A16207; }
        .status-overdue { background: #FEE2E2; color: #B91C1C; }

        /* QR Card */
        .qr-card {
            border: 1px solid #E5E7EB;
            background: #ffffff;
            padding: 10px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: inline-block;
        }

        .qr-image {
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto;
        }

        .qr-caption {
            font-size: 9px;
            color: #64748B;
            margin-top: 6px;
        }


        /* Info Card */
        .info-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .info-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 6px;
        }

        .info-row {
            padding: 4px 0;
        }

        .info-label {
            font-size: 10px;
            color: #64748B;
            display: inline-block;
            width: 100px;
            vertical-align: middle;  
        }

        .info-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
            vertical-align: middle;   
        }
        /* Payment Card */
        .payment-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .payment-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .payment-row {
            padding: 4px 0;
        }

        .payment-label {
            font-size: 10px;
            color: #64748B;
            display: inline-block;
            width: 100px;
        }

        .payment-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* Transaction Card */
        .transaction-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }

        .transaction-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .transaction-row {
            padding: 4px 0;
        }

        .transaction-label {
            font-size: 10px;
            color: #64748B;
            display: inline-block;
            width: 100px;
        }

        .transaction-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* Audit Trail Card */
        .audit-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 14px;
        }

        .audit-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .audit-row {
            padding: 4px 0;
        }

        .audit-label {
            font-size: 10px;
            color: #64748B;
            display: inline-block;
            width: 110px;
        }

        .audit-value {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
        }

        /* Items Table */
        .items-table th {
            background: #F8FAFC;
            font-size: 10px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 12px;
            border-bottom: 1px solid #E5E7EB;
        }

        .items-table td {
            padding: 12px 12px;
            font-size: 11px;
            border-bottom: 1px solid #F1F5F9;
        }

        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }

        /* Summary */
        .summary-card {
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
        }

        .summary-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E40AF;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #E5E7EB;
        }

        .summary-table td {
            padding: 6px 0;
            font-size: 11px;
        }

        .summary-table .summary-label {
            color: #64748B;
            font-weight: 600;
            text-align: left;
        }

        .summary-table .summary-value {
            font-weight: 700;
            text-align: right;
        }

        .summary-total td {
            padding: 10px 0 6px;
            font-size: 22px;
            font-weight: 800;
            color: #1E40AF;
            border-top: 2px solid #E5E7EB;
        }

        /* Notes */
        .notes-box {
            background: #F8FAFC;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 14px;
            font-size: 10px;
            color: #64748B;
            line-height: 1.6;
        }

        /* Footer */
        .doc-footer {
            border-top: 1px solid #E5E7EB;
            padding-top: 14px;
            margin-top: 18px;
            font-size: 10px;
            color: #64748B;
            text-align: center;
            line-height: 1.6;
        }

        /* Buttons */
        .doc-btn {
            display: inline-block;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .doc-btn-primary {
            background: #1E40AF;
            color: #ffffff;
            border-color: #1E40AF;
        }

        .doc-btn-secondary {
            background: #F1F5F9;
            color: #374151;
            border-color: #E5E7EB;
        }

        .no-print { display: none !important; }

        /* Print & PDF */
        @media print {
            @page { size: A4; margin: 8mm; }
            body { background: #ffffff !important; }
            .no-print { display: none !important; }
            .page { padding: 0 !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .header-section { break-inside: avoid; }
            .info-card { break-inside: avoid; }
            .items-table tr { break-inside: avoid; }
            .summary-table { break-inside: avoid; }
            .doc-footer { break-inside: avoid; }
            .notes-box { break-inside: avoid; }
        }

        .mode-pdf .page {
            padding: 0;
        }

        .mode-pdf .header-section {
            break-inside: avoid;
        }

        .mode-pdf .info-card {
            break-inside: avoid;
        }

        .mode-pdf .items-table tr {
            break-inside: avoid;
        }

        .mode-pdf .summary-table {
            break-inside: avoid;
        }

        .mode-pdf .notes-box {
            break-inside: avoid;
        }

        .mode-pdf .doc-footer {
            break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="page">
