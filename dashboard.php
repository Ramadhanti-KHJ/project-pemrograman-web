<?php
require_once 'cek_sesi.php';
require_once 'koneksi.php';

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$divisi = $_SESSION['divisi'];

$query = mysqli_query($koneksi, "SELECT * FROM aduan WHERE user_id = '$user_id' ORDER BY tanggal_kirim DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="landing.php">
                <span>HR Helpdesk & WBS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="respon.php">Complaints and Suggestions</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary px-3" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="card mb-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h4 class="mb-1">Halo, <?= htmlspecialchars($username) ?></h4>
                    <p class="text-muted mb-0">Divisi: <?= htmlspecialchars($divisi) ?></p>
                </div>
                <a href="aduan.php" class="btn btn-primary">+ Make Complaint or Suggestion</a>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h5 class="mb-0">Complaints and Suggestions Submitted</h5>
            <div class="small text-muted">
                Priority:
                <span class="badge border border-danger text-danger bg-white">Very Urgent</span>
                <span class="badge border border-warning text-warning-emphasis bg-white">Urgent</span>
                <span class="badge border border-secondary text-secondary bg-white">Not Urgent</span>
            </div>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="row g-3">
                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                    <?php
                    if ($data['prioritas'] === 'Very Urgent') {
                        $border_class = 'border-danger border-2';
                    } elseif ($data['prioritas'] === 'Urgent') {
                        $border_class = 'border-warning border-2';
                    } else {
                        $border_class = 'border-secondary border-2';
                    }

                    if ($data['status'] === 'Resolved' || !empty($data['respon_hr'])) {
                        $status_label = 'Resolved';
                        $status_badge = 'bg-success';
                    } elseif ($data['status'] === 'In Progress') {
                        $status_label = 'In Progress';
                        $status_badge = 'bg-info text-dark';
                    } else {
                        $status_label = 'Pending';
                        $status_badge = 'bg-secondary';
                    }

                    $cuplikan = strlen($data['detail_aduan']) > 120
                        ? substr($data['detail_aduan'], 0, 120) . '...'
                        : $data['detail_aduan'];
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 <?= $border_class ?>">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                    <?= htmlspecialchars($data['kategori']) ?>
                                </span>
                                <span class="badge <?= $status_badge ?>">
                                    <?= $status_label ?>
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2"><?= htmlspecialchars($data['judul']) ?></h6>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= nl2br(htmlspecialchars($cuplikan)) ?>
                                </p>
                                <div class="small text-muted mt-2">
                                    <?php if ($data['tanggal_kejadian']): ?>
                                        Incident Date: <?= date('d M Y', strtotime($data['tanggal_kejadian'])) ?>
                                    <?php endif; ?>
                                        <br>Sent: <?= date('d M Y, H:i', strtotime($data['tanggal_kirim'])) ?>
                                    <?php if ($data['is_anonim'] == 1): ?>
                                        <span class="badge bg-dark ms-1">Anonymous</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <small class="text-muted">Priority: <strong><?= htmlspecialchars($data['prioritas']) ?></strong></small>
                                <a href="respon.php#aduan-<?= $data['id'] ?>" class="btn btn-outline-primary btn-sm">See Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <p class="mb-3">No complaints or suggestions submitted.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>