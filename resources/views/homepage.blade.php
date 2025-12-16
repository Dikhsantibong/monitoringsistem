<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Informasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            max-width: 600px;
            text-align: center;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1a1a1a;
        }
        p {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .date {
            margin-top: 12px;
            font-weight: 600;
            color: #d9534f;
        }
        .footer {
            margin-top: 24px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Pemberitahuan Maintenance</h1>
        <p>Sistem saat ini sedang menjalani proses maintenance karena sedang beralih ke jaringan <strong>Intranet</strong>. Selama proses ini berlangsung, beberapa layanan mungkin tidak dapat diakses sementara.</p>
        <p class="date">Tanggal Maintenance: <strong>17 Desember 2025</strong></p>
        <p>Sistem dapat diakses kembali pada hari <strong>Jumat, 19 Desember 2025</strong>. Kami berupaya menyelesaikan proses ini secepat mungkin. Terima kasih atas pengertian dan kesabaran Anda.</p>
        <div class="footer">© {{ date('Y') }} — Tim IT UP KENDARI</div>
    </div>
</body>
</html>
