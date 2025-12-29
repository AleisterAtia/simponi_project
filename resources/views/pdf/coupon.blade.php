<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kupon Reward Mr. Wayoujiai</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            /* Warna latar belakang halaman saat preview */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        /* Container Utama Tiket - Dibuat agar muat di portrait */
        .ticket-container {
            display: flex;
            width: 100%;
            max-width: 600px;
            /* Lebar maksimal agar pas di halaman portrait */
            height: auto;
            min-height: 350px;
            background-color: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            margin: auto;
        }

        /* --- BAGIAN KIRI (STUB/BONGGOL) --- */
        .ticket-stub {
            width: 28%;
            background: linear-gradient(135deg, #c53030, #F25C05);
            /* Gradien Merah ke Orange */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 20px 10px;
            text-align: center;
        }

        /* Teks Vertikal di Kiri */
        .stub-text-vertical {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            font-weight: 800;
            letter-spacing: 3px;
            font-size: 24px;
            text-transform: uppercase;
            opacity: 0.9;
            white-space: nowrap;
        }

        .stub-branding {
            margin-top: auto;
            font-size: 12px;
            font-weight: 600;
            opacity: 0.8;
        }

        /* Efek Perforasi (Garis Putus-putus) di antara kiri dan kanan */
        .ticket-stub::after {
            content: '';
            position: absolute;
            right: -1px;
            top: 0;
            height: 100%;
            border-right: 4px dotted #ffffff;
            z-index: 2;
        }


        /* --- BAGIAN KANAN (KONTEN UTAMA) --- */
        .ticket-body {
            flex: 1;
            /* Mengambil sisa ruang */
            background-color: #ffffff;
            padding: 25px 30px;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Dekorasi Atas (Gelombang Merah) */
        .header-decoration {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 15px;
            background-color: #c53030;
            border-bottom: 3px solid #F25C05;
            /* Motif sederhana */
            background-image: repeating-linear-gradient(-45deg,
                    transparent,
                    transparent 5px,
                    rgba(255, 255, 255, 0.2) 5px,
                    rgba(255, 255, 255, 0.2) 10px);
        }

        /* Konten dalam Body */
        .body-content {
            margin-top: 20px;
            /* Memberi jarak dari dekorasi atas */
            text-align: center;
        }

        .main-title {
            font-size: 22px;
            font-weight: 800;
            color: #c53030;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.1;
        }

        .sub-title {
            font-size: 14px;
            color: #F25C05;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Nama Reward yang Besar */
        .reward-name-box {
            background-color: #fffbf7;
            border: 2px solid #F25C05;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .reward-name {
            font-size: 28px;
            font-weight: 800;
            color: #2d3748;
            margin: 0;
            line-height: 1.2;
        }

        .customer-name {
            font-size: 12px;
            color: #718096;
            margin-top: 5px;
        }

        /* Kotak Kode Kupon */
        .coupon-section {
            margin: 25px 0;
        }

        .code-label {
            font-size: 12px;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .code-box {
            background: #2d3748;
            color: #F25C05;
            font-family: 'Courier New', monospace;
            font-size: 32px;
            font-weight: 800;
            padding: 10px 25px;
            border-radius: 8px;
            display: inline-block;
            letter-spacing: 3px;
            border: 3px dashed #F25C05;
        }

        /* Info Kadaluarsa & Footer */
        .expiry-info {
            font-size: 13px;
            color: #c53030;
            font-weight: 600;
            margin-top: 10px;
        }

        .transaction-date {
            font-size: 10px;
            color: #a0aec0;
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        /* Placeholder untuk Ilustrasi (Opsional) */
        .illustration-placeholder {
            position: absolute;
            bottom: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 80px;
            color: #F25C05;
            /* Ganti ini dengan tag <img> jika ada gambar ilustrasi */
            content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-gift-fill" viewBox="0 0 16 16"><path d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3zm6 4v7.5a1.5 1.5 0 0 1-1.5 1.5H9V7h6zM2.5 16A1.5 1.5 0 0 1 1 14.5V7h6v9H2.5z"/></svg>');
            width: 80px;
            height: auto;
        }
    </style>
</head>

<body>

    <div class="ticket-container">
        <div class="ticket-stub">
            <div class="stub-text-vertical">KUPON HADIAH</div>
            <div class="stub-branding">Mr. Wayoujiai</div>
        </div>

        <div class="ticket-body">
            <div class="header-decoration"></div>

            <img class="illustration-placeholder" alt="gift icon">

            <div class="body-content">
                <h1 class="main-title">BUKTI PENUKARAN POIN</h1>
                <div class="sub-title">Mr. Wayoujiai Member</div>

                <div class="reward-name-box">
                    <h2 class="reward-name">{{ $redemption->reward->name }}</h2>
                    <div class="customer-name">Milik: {{ $redemption->customer->name }}</div>
                </div>

                <div class="coupon-section">
                    <div class="code-label">KODE PENUKARAN ANDA</div>
                    <div class="code-box">{{ $couponCode }}</div>
                </div>

                <div class="expiry-info">
                    BERLAKU SAMPAI: {{ $expiresAt->format('d/m/Y H:i') }} WIB
                    <div style="font-size: 11px; font-weight: normal; margin-top: 4px; color: #e53e3e;">
                        (Hangus otomatis dalam 24 Jam)
                    </div>
                </div>

                <div class="transaction-date">
                    Tunjukkan kode ini ke kasir. Pastikan terkoneksi internet.<br>
                    Dibuat pada: {{ $createdAt->format('d F Y, H:i') }} WIB
                </div>
            </div>
        </div>
    </div>

</body>

</html>
