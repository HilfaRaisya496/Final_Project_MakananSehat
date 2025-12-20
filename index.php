<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Trihealth - Rekomendasi Makanan Sehat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"><!-- [web:7][web:51] -->

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4fff6;
            color: #1b4332;
        }

        header {
            background: linear-gradient(135deg, #2d6a4f, #52b788);
            color: #fff;
            padding: 14px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .hero {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 50px 40px 40px;
        }

        .hero-text {
            max-width: 520px;
        }

        .hero-text h1 {
            font-size: 38px;
            margin-bottom: 16px;
            color: #1b4332;
        }

        .hero-text p {
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .btn-main {
            display: inline-block;
            padding: 12px 28px;
            background: #2d6a4f;
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s, transform 0.2s;
        }

        .btn-main:hover {
            background: #1b4332;
            transform: translateY(-2px);
        }

        .hero-logo {
            flex: 1;
            min-width: 260px;
            text-align: center;
        }

        .hero-logo img {
            width: 200px;     
            height: 200px;         
            border-radius: 50%; 
            object-fit: cover;   
            box-shadow: 0 12px 30px rgba(45, 106, 79, 0.35); 
            background: #f4fff6; 
        }

        .section-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 24px;
            font-size: 24px;
            color: #1b4332;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 0 40px 40px;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .card h3 {
            margin-bottom: 8px;
            color: #2d6a4f;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #d8f3dc;
            color: #1b4332;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .card span {
            font-size: 13px;
            color: #40916c;
            font-weight: 600;
        }

        footer {
            text-align: center;
            padding: 16px 0;
            background: #2d6a4f;
            color: #fff;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero {
                padding: 40px 20px;
            }

            .cards {
                padding: 0 20px 30px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo-text">Trihealth</div>
    <nav>
        <a href="public/index.php">Login</a>
    </nav>
</header>

<section class="hero">
    <div class="hero-text">
        <h1>Makan Sehat Itu Mudah dengan Trihealth</h1>
        <p>
            Temukan rekomendasi menu seimbang, penuh sayur dan buah, yang membantu menjaga energi dan kesehatan setiap hari.
        </p>

        <a href="public/index.php" class="btn-main">Start Now</a>
    </div>

    <div class="hero-logo">
        <img src="public/img/logo.jpg" alt="Logo Trihealth">
    </div>
</section>

<h2 id="tips" class="section-title">Tips Pola Makan Sehat</h2>
<section class="cards">
    <div class="card">
        <h3>Seimbangkan Piring</h3>
        <p>Setengah piring berisi sayur dan buah, seperempat protein, seperempat karbo kompleks.</p>
    </div>
    <div class="card">
        <h3>Kurangi Gula</h3>
        <p>Ganti minuman manis dengan air putih atau infused water.</p>
    </div>
    <div class="card">
        <h3>Cukup Serat</h3>
        <p>Pilih nasi merah, roti gandum, dan sayur hijau untuk kenyang lebih lama.</p>
    </div>
</section>

<footer id="kontak">
    &copy; <?php echo date("Y"); ?> Trihealth - Rekomendasi Makanan Sehat
</footer>

</body>
</html>
