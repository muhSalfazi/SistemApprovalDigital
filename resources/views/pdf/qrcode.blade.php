<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval QR Codes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 30px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #3498db;
            color: #fff;
            padding: 15px;
            font-size: 18px;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #ecf0f1;
            text-align: center;
        }
        img {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            border: 5px solid #3498db;
            transition: transform 0.3s ease;
        }
        img:hover {
            transform: scale(1.1);
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #7f8c8d;
        }
        .label {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Approval QR Codes</h2>
        <table>
            <tr>
                <th>Prepare</th>
                <th>Check-1</th>
                <th>Check-2</th>
                <th>Approved</th>
            </tr>
            <tr>
                <td>
                    <img src="data:image/png;base64,{{ $qrCodes['prepare'] }}" alt="Prepare QR">
                    <p class="label">Prepared By:</p>
                    <p>{{ $approvals['prepare'] }}</p>
                    <p>{{ $approvalTimes['prepare'] }}</p>
                </td>
                <td>
                    <img src="data:image/png;base64,{{ $qrCodes['check1'] }}" alt="Check-1 QR">
                    <p class="label">Checked By:</p>
                    <p>{{ $approvals['Check1'] }}</p>
                    <p>{{ $approvalTimes['Check1'] }}</p>
                </td>
                <td>
                    <img src="data:image/png;base64,{{ $qrCodes['check2'] }}" alt="Check-2 QR">
                    <p class="label">Checked By:</p>
                    <p>{{ $approvals['Check2'] }}</p>
                    <p>{{ $approvalTimes['Check2'] }}</p>
                </td>
                <td>
                    <img src="data:image/png;base64,{{ $qrCodes['approved'] }}" alt="Approved QR">
                    <p class="label">Approved By:</p>
                    <p>{{ $approvals['approved'] }}</p>
                    <p>{{ $approvalTimes['approved'] }}</p>
                </td>
            </tr>
        </table>
        <div class="footer">
            &copy; {{ date('Y') }} - Approval QR System | All Rights Reserved.
        </div>
    </div>
</body>
</html>
