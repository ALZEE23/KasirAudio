<div class="print-area" style="padding: 10mm;">
    <!-- Header -->
    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="text-align: center;">
                <h2 style="margin: 0; font-size: 18px;">TOKO AUDIO MOBIL</h2>
                <p style="margin: 5px 0;">Jl. Example No. 123</p>
                <p style="margin: 5px 0;">Telp: 081234567890</p>
            </td>
        </tr>
    </table>

    <!-- Invoice Info -->
    <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 120px;">No. Invoice</td>
            <td>: INV-{{ date('Ymd') }}-{{ $cashier_id }}</td>
            <td style="width: 120px;">Nama Pelanggan</td>
            <td>: {{ $customer['customerName'] }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ date('d/m/Y H:i', strtotime($customer['transactionDate'])) }}</td>
            <td>Kendaraan</td>
            <td>: {{ $customer['carType'] }} - {{ $customer['carId'] }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>: {{ auth()->user()->name }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Items Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 8px; text-align: center; background-color: #f0f0f0;">No</th>
                <th style="border: 1px solid #000; padding: 8px; text-align: left; background-color: #f0f0f0;">Item</th>
                <th style="border: 1px solid #000; padding: 8px; text-align: center; background-color: #f0f0f0;">Qty
                </th>
                <th style="border: 1px solid #000; padding: 8px; text-align: right; background-color: #f0f0f0;">Harga
                </th>
                <th style="border: 1px solid #000; padding: 8px; text-align: right; background-color: #f0f0f0;">Total
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($customer['items'] as $index => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 8px;">{{ $item['name'] }}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $item['qty'] }}</td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: right;">
                        {{ number_format($item['sellPrice']) }}
                    </td>
                    <td style="border: 1px solid #000; padding: 8px; text-align: right;">{{ number_format($item['total']) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="border: 1px solid #000; padding: 8px; text-align: right;"><strong>Total
                        Item:</strong></td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center;">
                    <strong>{{ $customer['totalItems'] }}</strong>
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: right;"><strong>Rp
                        {{ number_format($customer['totalSell']) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 33%; text-align: center; vertical-align: top;">
                <p style="margin: 0;">Customer</p>
                <br><br><br>
                <p style="margin: 0;">({{ $customer['customerName'] }})</p>
            </td>
            <td style="width: 34%; text-align: center;">
                <p style="margin: 0;">Hormat Kami,</p>
                <br><br><br>
                <p style="margin: 0;">({{ auth()->user()->name }})</p>
            </td>
            <td style="width: 33%; text-align: center;">
                <p style="margin: 0;">Mekanik</p>
                <br><br><br>
                <p style="margin: 0;">(_____________)</p>
            </td>
        </tr>
    </table>
</div>

<style>
    @media print {
        @page {
            size: 148mm 210mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .print-area {
            width: 128mm;
            margin: 10mm;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>