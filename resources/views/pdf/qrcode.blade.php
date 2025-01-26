<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval QR Codes</title>
    <link href="{{ asset('assets/img/icon-kbi.png') }}" rel="icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 30px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 50px auto;
            background: linear-gradient(to right, #ffffff, #f8f9fa);
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 30px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .header p {
            font-size: 18px;
            color: #7f8c8d;
            margin: 5px 0 20px;
        }

        .logo {
            width: 200px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            font-size: 20px;
            text-transform: uppercase;
            font-weight: 600;
            border-radius: 5px;
        }

        td {
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #ecf0f1;
            text-align: center;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        td:hover {
            background-color: #dfe6e9;
        }

        img.qr-code {
            width: 180px;
            height: 180px;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        img.qr-code:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 18px;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
            font-size: 16px;
            color: #7f8c8d;
            font-weight: 400;
        }

        .doc-info {
            position: absolute;
            top: 20px;
            right: 30px;
            text-align: left;
            font-size: 14px;
            color: #2c3e50;
        }

        .doc-info p {
            margin: 5px 0;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Informasi dokumen di pojok kanan atas -->

        <div class="header">
            <img src="{{ asset('assets/img/kyoraku-baru.png') }}" alt="Company Logo" class="logo">
            <h1>Approval QR Codes</h1>
            <p>Pindai untuk memverifikasi persetujuan dokumen</p>
        </div>
        <div class="doc-info mb-4">
            <p>Dokumen : <span>{{ $submission->title }}</span></p>
            <p>No.Dokumen : <span>{{ $submission->no_transaksi }}</span></p>
        </div>

        <table>
            <tr>
                <th>Prepare</th>
                <th>Check-1</th>
                <th>Check-2</th>
                <th>Approved</th>
            </tr>
            <tr>
                <td>
                    <img class="qr-code" src="data:image/png;base64,{{ $qrCodes['prepare'] }}" alt="Prepare QR">
                    <p class="label">Prepared By:</p>
                    <p>{{ $approvals['prepare'] }}</p>
                    <p>{{ $approvalTimes['prepare'] }}</p>
                </td>
                <td>
                    <img class="qr-code" src="data:image/png;base64,{{ $qrCodes['check1'] }}" alt="Check-1 QR">
                    <p class="label">Checked By:</p>
                    <p>{{ $approvals['Check1'] }}</p>
                    <p>{{ $approvalTimes['Check1'] }}</p>
                </td>
                <td>
                    <img class="qr-code" src="data:image/png;base64,{{ $qrCodes['check2'] }}" alt="Check-2 QR">
                    <p class="label">Checked By:</p>
                    <p>{{ $approvals['Check2'] }}</p>
                    <p>{{ $approvalTimes['Check2'] }}</p>
                </td>
                <td>
                    <img class="qr-code" src="data:image/png;base64,{{ $qrCodes['approved'] }}" alt="Approved QR">
                    <p class="label">Approved By:</p>
                    <p>{{ $approvals['approved'] }}</p>
                    <p>{{ $approvalTimes['approved'] }}</p>
                </td>
            </tr>
        </table>

        <div class="footer">
            &copy; {{ date('Y') }} - HRGA System | All Rights Reserved.
        </div>
    </div>
</body>

</html>
