<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Electrónica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .company-logo {
            width: 150px;
            float: left;
        }
        .company-info {
            float: left;
            margin-left: 20px;
            width: 300px;
        }
        .invoice-box {
            float: right;
            border: 1px solid #4caf50;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            width: 200px;
        }
        .invoice-box h3 {
            margin: 0;
            color: #4caf50;
            font-size: 14px;
        }
        .invoice-box h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .clear {
            clear: both;
        }
        .client-info {
            margin-top: 20px;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .client-info h3 {
            margin-top: 0;
            color: #4caf50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4caf50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin: 0;
        }
        .totals td {
            padding: 5px 10px;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .amount-words {
            margin-top: 20px;
            font-style: italic;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <!-- Using absolute path for dompdf -->
            <img src="{{ public_path('img/Logo_Oscuro.png') }}" alt="AgroMarket" style="width: 100%;">
        </div>
        <div class="company-info">
            <strong>{{ $data['company']['razonSocial'] }}</strong><br>
            RUC: {{ $data['company']['ruc'] }}<br>
            {{ config('app.address', 'Dirección de la Empresa') }}<br>
            Email: {{ config('mail.from.address') }}
        </div>
        <div class="invoice-box">
            <h3>R.U.C. {{ $data['company']['ruc'] }}</h3>
            <h2>FACTURA ELECTRÓNICA</h2>
            <h3>{{ $data['serie'] }} - {{ $data['correlativo'] }}</h3>
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <h3>Datos del Cliente</h3>
        <table style="margin: 0; width: 100%;">
            <tr>
                <td width="15%"><strong>Cliente:</strong></td>
                <td>{{ $data['client']['rznSocial'] }}</td>
                <td width="15%"><strong>Fecha:</strong></td>
                <td>{{ \Carbon\Carbon::parse($data['fechaEmision'])->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td><strong>{{ $data['client']['tipoDoc'] == '6' ? 'RUC' : 'DNI' }}:</strong></td>
                <td>{{ $data['client']['numDoc'] }}</td>
                <td><strong>Moneda:</strong></td>
                <td>{{ $data['tipoMoneda'] }}</td>
            </tr>
            <tr>
                <td><strong>Dirección:</strong></td>
                <td colspan="3">{{ $data['client']['address']['direccion'] }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cant.</th>
                <th>Descripción</th>
                <th class="text-right">P. Unit</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['details'] as $item)
            <tr>
                <td>{{ $item['cantidad'] }}</td>
                <td>{{ $item['descripcion'] }}</td>
                <td class="text-right">{{ number_format($item['mtoPrecioUnitario'], 2) }}</td>
                <td class="text-right">{{ number_format($item['mtoValorVenta'] + $item['igv'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="amount-words">
        <strong>SON:</strong> {{ $data['legends'][0]['value'] }}
    </div>

    <div class="totals">
        <table>
            <tr>
                <td class="text-right"><strong>Op. Gravada:</strong></td>
                <td class="text-right">{{ number_format($data['mtoOperGravadas'], 2) }}</td>
            </tr>
            <tr>
                <td class="text-right"><strong>I.G.V. (18%):</strong></td>
                <td class="text-right">{{ number_format($data['mtoIGV'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right">{{ number_format($data['mtoImpVenta'], 2) }}</td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <div class="footer">
        <p>Representación impresa de la Factura Electrónica.<br>
        Consulte su documento en {{ config('app.url') }}</p>
    </div>
</body>
</html>
