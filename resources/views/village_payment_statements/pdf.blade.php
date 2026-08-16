<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Village Payment Statement - {{ $statement['village']['name'] }}</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #005BAC;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #005BAC;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .header h2 {
            font-size: 13px;
            color: #334155;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
        }
        .meta-table td {
            font-size: 11px;
            padding: 4px;
            border: none;
        }
        .meta-table td strong {
            color: #005BAC;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .summary-wrapper {
            float: right;
            width: 280px;
            margin-top: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #005BAC;
        }
        .summary-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .summary-table tr.total-row {
            background-color: #005BAC;
            color: #ffffff;
            font-weight: bold;
        }
        .summary-table tr.total-row td {
            color: #ffffff;
        }
        .clear {
            clear: both;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('business_name', 'DHOAT DAIRY FARM & PROCESSING CENTER') }}</h1>
        <h2>VILLAGE MILK PAYMENT STATEMENT</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>VILLAGE:</strong> {{ strtoupper($statement['village']['name']) }} ({{ $statement['village']['code'] }})</td>
            <td class="text-right"><strong>PERIOD:</strong> {{ $statement['period']['start_date_formatted'] }} TO {{ $statement['period']['end_date_formatted'] }}</td>
        </tr>
    </table>

    <table class="data-table">
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
                    <td class="text-right font-mono" style="font-weight: bold;">₹ {{ number_format($entry['running_balance'], 2) }}</td>
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

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td><strong>Total Milk Quantity:</strong></td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['total_milk'], 2) }} L</td>
            </tr>
            <tr>
                <td><strong>Average Fat:</strong></td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['average_fat'], 2) }}%</td>
            </tr>
            <tr>
                <td><strong>Average SNF:</strong></td>
                <td class="text-right font-mono">{{ number_format($statement['summary']['average_snf'], 2) }}%</td>
            </tr>
            <tr>
                <td><strong>Total Payment:</strong></td>
                <td class="text-right font-mono">₹ {{ number_format($statement['summary']['total_payment'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Closing Balance:</strong></td>
                <td class="text-right font-mono">₹ {{ number_format($statement['summary']['closing_balance'], 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        <div>Generated on: {{ date('d-m-Y H:i') }}</div>
        <div>Page 1 of 1</div>
    </div>
</body>
</html>
