<?php auth_required(); ?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg,#1b4332,#2d6a4f);">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="/tes2/dashboard/admin/admin.php">
      <span class="me-2 rounded-3 bg-light bg-opacity-10 d-inline-flex align-items-center justify-content-center"
            style="width:34px;height:34px;">
        <i class="bi bi-speedometer2"></i>
      </span>
      <div class="d-flex flex-column lh-1">
        <span class="fw-semibold">TriHealth Admin</span>
        <small class="text-white-50">Panel pengelola</small>
      </div>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarAdmin">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
        <li class="nav-item">
          <a class="nav-link <?= $active ?? '' === 'dashboard' ? 'active' : '' ?>" href="admin.php">
            <i class="bi bi-grid-1x2 me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $active ?? '' === 'users' ? 'active' : '' ?>" href="users.php">
            <i class="bi bi-people me-1"></i> Users
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $active ?? '' === 'notifications' ? 'active' : '' ?>" href="notifications.php">
            <i class="bi bi-bell me-1"></i> Notifikasi
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <span class="navbar-text me-3 small text-white-50">
          <i class="bi bi-person-badge me-1"></i> Administrator
        </span>
        <a href="/tes2/auth/logout.php" class="btn btn-sm btn-outline-light">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>
