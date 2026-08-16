<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Village Payment Statement - {{ $statement['village']['name'] }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #1e293b;
            padding: 30px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #005BAC;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 800;
            color: #005BAC;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-top: 4px;
        }

        .meta-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 18px;
            border-radius: 6px;
        }

        .meta-box p {
            font-size: 12px;
            margin-bottom: 4px;
        }

        .meta-box p strong {
            color: #005BAC;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 11px;
        }

        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td.text-right, th.text-right {
            text-align: right;
        }

        td.text-center, th.text-center {
            text-align: center;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .summary-box {
            margin-top: 20px;
            width: 320px;
            margin-left: auto;
            border: 1.5px solid #005BAC;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .summary-row:last-child {
            border-bottom: none;
            background-color: #005BAC;
            color: #ffffff;
            font-weight: 800;
            font-size: 12px;
        }

        .summary-row span.label {
            font-weight: 600;
        }

        .summary-row span.val {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
            font-size: 11px;
            color: #64748b;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background-color: #005BAC; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px;">
            🖨️ Print Statement
        </button>
    </div>

    <div class="header">
        <h1>{{ setting('business_name', 'DHOAT DAIRY FARM & PROCESSING CENTER') }}</h1>
        <h2>VILLAGE MILK PAYMENT STATEMENT</h2>
    </div>

    <div class="meta-container">
        <div class="meta-box">
            <p><strong>VILLAGE:</strong> {{ strtoupper($statement['village']['name']) }} (CODE: {{ $statement['village']['code'] }})</p>
        </div>
        <div class="meta-box" style="text-align: right;">
            <p><strong>PERIOD:</strong> {{ $statement['period']['start_date_formatted'] }} TO {{ $statement['period']['end_date_formatted'] }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Date</th>
                <th>Particular</th>
                <th class="text-right">Milk Qty (L)</th>
                <th class="text-right">Fat (%)</th>
                <th class="text-right">SNF (%)</th>
                <th class="text-right">Price (₹)</th>
                <th class="text-right">Payment (₹)</th>
                <th class="text-right">Balance (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($statement['entries'] as $entry)
                <tr>
                    <td class="text-center font-mono">{{ $entry['date_formatted'] }}</td>
                    <td>{{ $entry['particular'] }}</td>
                    <td class="text-right font-mono">{{ number_format($entry['milk_quantity'], 2) }}</td>
                    <td class="text-right font-mono">{{ number_format($entry['fat'], 2) }}</td>
                    <td class="text-right font-mono">{{ number_format($entry['snf'], 2) }}</td>
                    <td class="text-right font-mono">₹ {{ number_format($entry['price'], 2) }}</td>
                    <td class="text-right font-mono">₹ {{ number_format($entry['payment'], 2) }}</td>
                    <td class="text-right font-mono" style="font-weight: 700;">₹ {{ number_format($entry['running_balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" style="text-transform: uppercase;">Total / Average</td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['total_milk'], 2) }} L</td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['average_fat'], 2) }}%</td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['average_snf'], 2) }}%</td>
                <td class="text-right">-</td>
                <td class="text-right font-mono">₹ {{ number_format($statement['summary']['total_payment'], 2) }}</td>
                <td class="text-right font-mono">₹ {{ number_format($statement['summary']['closing_balance'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <div class="summary-row">
            <span class="label">Total Milk Quantity:</span>
            <span class="val">{{ number_format($statement['summary']['total_milk'], 2) }} L</span>
        </div>
        <div class="summary-row">
            <span class="label">Average Fat:</span>
            <span class="val">{{ number_format($statement['summary']['average_fat'], 2) }}%</span>
        </div>
        <div class="summary-row">
            <span class="label">Average SNF:</span>
            <span class="val">{{ number_format($statement['summary']['average_snf'], 2) }}%</span>
        </div>
        <div class="summary-row">
            <span class="label">Total Payment:</span>
            <span class="val">₹ {{ number_format($statement['summary']['total_payment'], 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="label">Closing Balance:</span>
            <span class="val">₹ {{ number_format($statement['summary']['closing_balance'], 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <div>Generated on: {{ date('d-m-Y H:i') }}</div>
        <div>Authorized Signatory</div>
    </div>

    <script>
        window.onload = function() {
            // Auto trigger print when loaded via print route
            if (window.location.search.includes('autoprint=1')) {
                window.print();
            }
        };
    </script>
</body>
</html>
