<?php
require __DIR__ . '/../app/core/bootstrap.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pendaftaran Berhasil | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #38b000, #70e000);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .success-card {
      max-width: 460px;
      width: 100%;
      border-radius: 20px;
      border: 0;
      box-shadow: 0 20px 55px rgba(0,0,0,.18);
      overflow: hidden;
      background: #fff;
      padding: 2.25rem 2rem;
      text-align: center;
    }
    .success-icon {
      width: 74px;
      height: 74px;
      border-radius: 50%;
      background: rgba(56,176,0,.1);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
      color: #2b9348;
      font-size: 2.2rem;
    }
    .success-card h1 {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: .25rem;
    }
    .success-card p.lead {
      font-size: .98rem;
      color: #6c757d;
      margin-bottom: 1.5rem;
    }
    .btn-success {
      background-color: #2b9348;
      border-color: #2b9348;
      font-weight: 600;
    }
    .btn-success:hover {
      background-color: #23863f;
      border-color: #23863f;
    }
    .small-note {
      font-size: .85rem;
      color: #6c757d;
      margin-top: 0.75rem;
    }
  </style>
</head>
<body>

<div class="success-card">
  <div class="success-icon">
    <i class="bi bi-check-lg"></i>
  </div>
  <h1>Pendaftaran Berhasil</h1>
  <p class="lead">
    Akun TriHealth kamu sudah aktif. Sekarang kamu bisa masuk dan mulai
    mencatat asupan kalori serta melihat rekomendasi menu sehat harian.
  </p>

  <a href="login.php" class="btn btn-success w-100 mb-2">
    Lanjut ke Halaman Login
  </a>

  <p class="small-note mb-0">
    Jika kamu merasa tidak mendaftar, abaikan saja pesan ini.
  </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
