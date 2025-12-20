<?php
require_once __DIR__ . '/../../app/core/helpers.php';
auth_required();
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg,#1b4332,#2d6a4f);">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="user.php">
      <span class="me-2 rounded-3 bg-light bg-opacity-10 d-inline-flex align-items-center justify-content-center"
        style="width:34px;height:34px;">
        <i class="bi bi-egg-fried"></i>
      </span>
      <div class="d-flex flex-column lh-1">
        <span class="fw-semibold">TriHealth</span>
        <small class="text-white-50">Healthy Meal assistant</small>
      </div>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser"
      aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarUser">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <li class="nav-item">
          <a class="nav-link <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>" href="user.php">
            <i class="bi bi-grid-1x2 me-1"></i> Dashboardsssssss
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active ?? '') === 'rekomendasi' ? 'active' : '' ?>" href="rekomendasi.php">
            <i class="bi bi-stars me-1"></i> Rekomendasi
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active ?? '') === 'cari' ? 'active' : '' ?>" href="cari_makanan.php">
            <i class="bi bi-search me-1"></i> Cari makanan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active ?? '') === 'logs' ? 'active' : '' ?>" href="food_logs_index.php">
            <i class="bi bi-journal-text me-1"></i> Log makan
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active ?? '') === 'profile' ? 'active' : '' ?>" href="edit_profile.php">
            <i class="bi bi-person-circle me-1"></i> Profil
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <span class="navbar-text me-3 small text-white-50">
          <i class="bi bi-person-check me-1"></i>
          Logged in as <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>
        </span>
        <a href="<?= base_url('/auth/logout.php') ?>" class="btn btn-sm btn-outline-light">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>