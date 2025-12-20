<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';

auth_required();
role_admin_only();

$pdo = db();

$stmt = $pdo->query("
    SELECT n.id, n.user_id, u.name, u.email,
           n.channel, n.title, n.status, n.created_at
    FROM notifications n
    JOIN users u ON u.id = n.user_id
    ORDER BY n.created_at DESC
    LIMIT 200
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Riwayat Notifikasi | TriHealth Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php $active = 'users'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h3 class="mb-1">Riwayat Notifikasi</h3>
            <p class="text-muted mb-0 small">
                Log pengiriman pengingat menu sehat harian ke seluruh user (maksimal 200 entri terbaru).
            </p>
        </div>
        <span class="badge bg-secondary-subtle text-secondary-emphasis border small mt-2 mt-md-0">
            <i class="bi bi-info-circle me-1"></i> Data hanya untuk monitoring admin
        </span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <?php if (empty($rows)): ?>
                <p class="p-4 mb-0 text-muted text-center">
                    Belum ada notifikasi yang tercatat.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                        <tr class="small text-uppercase text-muted">
                            <th style="width: 180px;">Waktu</th>
                            <th>User</th>
                            <th>Email</th>
                            <th style="width: 90px;">Channel</th>
                            <th>Judul</th>
                            <th style="width: 90px;">Status</th>
                        </tr>
                        </thead>
                        <tbody class="small">
                        <?php foreach ($rows as $n): ?>
                            <tr>
                                <td><?= htmlspecialchars($n['created_at']) ?></td>
                                <td>#<?= (int)$n['user_id'] ?> &mdash; <?= htmlspecialchars($n['name']) ?></td>
                                <td><?= htmlspecialchars($n['email']) ?></td>
                                <td class="text-uppercase">
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($n['channel']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($n['title']) ?></td>
                                <td>
                                    <?php if ($n['status'] === 'sent'): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i> sent
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i> failed
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

