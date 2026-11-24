<?php
require_once 'cek_sesi.php';
require_once 'koneksi.php';

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$divisi = $_SESSION['divisi'];
$error = '';

if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];

    $cek = mysqli_query($koneksi, "SELECT lampiran FROM aduan WHERE id = '$id_hapus' AND user_id = '$user_id'");
    if (mysqli_num_rows($cek) > 0) {
        $data_hapus = mysqli_fetch_assoc($cek);

        if (!empty($data_hapus['lampiran']) && file_exists("lampiran/" . $data_hapus['lampiran'])) {
            unlink("lampiran/" . $data_hapus['lampiran']);
        }

        mysqli_query($koneksi, "DELETE FROM aduan WHERE id = '$id_hapus' AND user_id = '$user_id'");
        echo "<script>('Report has been deleted.'); window.location='respon.php';</script>";
        exit;
    } else {
        $error = "Report cannot be deleted (already processed or not found)";
    }
}

if (isset($_POST['update_report'])) {
    $id_edit = (int) $_POST['id_aduan'];
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $prioritas = mysqli_real_escape_string($koneksi, $_POST['prioritas']);
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $detail_aduan = mysqli_real_escape_string($koneksi, trim($_POST['detail_aduan']));
    $tanggal_kejadian = ($kategori !== 'Feedback & Complaints' && !empty($_POST['tanggal_kejadian'])) ? "'" . mysqli_real_escape_string($koneksi, $_POST['tanggal_kejadian']) . "'" : "NULL";
    $is_anonim        = isset($_POST['is_anonim']) ? 1 : 0;

    $cek = mysqli_query($koneksi, "SELECT lampiran FROM aduan WHERE id = '$id_edit' AND user_id = '$user_id' AND status = 'Pending'");
    if (mysqli_num_rows($cek) > 0) {
        $dataOld = mysqli_fetch_assoc($cek);
        $nama_lampiran = $dataOld['lampiran'];
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
                $nama_asli = $_FILES['lampiran']['name'];
                $ukuran = $_FILES['lampiran']['size'];
                $tmp_name  = $_FILES['lampiran']['tmp_name'];

                $tipe = ['jpg', 'jpeg', 'png', 'mp4', 'mp3', 'wav', 'm4a'];
                $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));

                if (!in_array($ekstensi, $tipe)) {
                    $error = "Unsupported file format! Please use JPG, PNG, MP4, MP3, or WAV.";
                } elseif ($ukuran > 15000000) {
                    $error = "File size too large (max 15MB).";
                } else {
                    if (!is_dir('lampiran')) {
                        mkdir('lampiran', 0777, true);
                    }
                    $nama_file_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_asli);
                    if (move_uploaded_file($tmp_name, "lampiran/" . $nama_file_baru)) {
                        if (!empty($nama_lampiran) && file_exists("lampiran/" . $nama_lampiran)) {
                            unlink("lampiran/" . $nama_lampiran);
                        }
                        $nama_lampiran = $nama_file_baru;
                    } else {
                        $error = "Failed to upload new file.";
                    }
                }
            } else {
                $error = "An error occurred while uploading the attachment.";
            }
        }

        if ($error === "") {
            $lampiran_sql = !empty($nama_lampiran) ? "'$nama_lampiran'" : "NULL";
            $sql_update   = "UPDATE aduan SET kategori = '$kategori', prioritas = '$prioritas', judul = '$judul', detail_aduan = '$detail_aduan', tanggal_kejadian = $tanggal_kejadian, lampiran = $lampiran_sql, is_anonim = '$is_anonim' WHERE id = '$id_edit' AND user_id = '$user_id'";
            if (mysqli_query($koneksi, $sql_update)) {
                echo "<script>('Report updated successfully.'); window.location='respon.php';</script>";
                exit;
            } else {
                $error = "Failed to update report: " . mysqli_error($koneksi);
            }
        }
    } else {
        $error = "Report cannot be edited (already processed or not found).";
    }
}

$query = mysqli_query($koneksi, "SELECT * FROM aduan WHERE user_id = '$user_id' ORDER BY tanggal_kirim DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responses</title>
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

    <div class="container my-4 content-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Reports & HR Responses</h4>
            <a href="aduan.php" class="btn btn-primary btn-sm">+ Submit New Report</a>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
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
                        $status_badge = 'bg-success';
                        $status_text  = 'Resolved';
                    } elseif ($data['status'] === 'In Progress') {
                        $status_badge = 'bg-info text-dark';
                        $status_text  = 'In Progress';
                    } else {
                        $status_badge = 'bg-secondary';
                        $status_text  = 'Pending';
                    }
                ?>

                <div class="card mb-4 report-card <?= $border_class ?>" id="aduan-<?= $data['id'] ?>">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="badge bg-primary"><?= htmlspecialchars($data['kategori']) ?></span>
                            <span class="badge bg-light text-dark border">Priority: <?= htmlspecialchars($data['prioritas']) ?></span>
                            <?php if ($data['is_anonim'] == 1): ?>
                                <span class="badge bg-dark">Anonymous</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border">Sender: <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($divisi) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <span class="badge <?= $status_badge ?>"><?= $status_text ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($data['judul']) ?></h5>
                        <div class="small text-muted mb-3">
                            Submitted: <?= date('d M Y, H:i', strtotime($data['tanggal_kirim'])) ?>
                            <?php if (!empty($data['tanggal_kejadian'])): ?>
                                &bull; Incident Date: <strong><?= date('d M Y', strtotime($data['tanggal_kejadian'])) ?></strong>
                            <?php endif; ?>
                        </div>
                        <p class="card-text"><?= nl2br(htmlspecialchars($data['detail_aduan'])) ?></p>

                        <?php if (!empty($data['lampiran'])): ?>
                            <?php $jenis = strtolower(pathinfo($data['lampiran'], PATHINFO_EXTENSION)); ?>
                            <div class="mt-3">
                                <div class="small text-muted mb-1">Attachment:</div>
                                    <?php if (in_array($jenis, ['jpg', 'jpeg', 'png'])): ?>
                                        <img src="lampiran/<?= htmlspecialchars($data['lampiran']) ?>" alt="Attachment" width="120" height="120" class="rounded border" style="object-fit: cover;">
                                    <?php elseif ($jenis === 'mp4'): ?>
                                        <video src="lampiran/<?= htmlspecialchars($data['lampiran']) ?>" width="220" height="130" class="rounded border" style="object-fit: cover;"></video>
                                    <?php else: ?>
                                        <audio src="lampiran/<?= htmlspecialchars($data['lampiran']) ?>" controls></audio>
                                    <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <hr>
                        <?php if (!empty($data['respon_hr'])): ?>
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>HR Response<?= !empty($data['nama_pic_hr']) ? ' (' . htmlspecialchars($data['nama_pic_hr']) . ')' : '' ?>:</strong>
                                    <?php if (!empty($data['tanggal_respon'])): ?>
                                        <small><?= date('d M Y, H:i', strtotime($data['tanggal_respon'])) ?></small>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($data['respon_hr'])) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="text-muted small fst-italic">
                                No response from HR yet. You can still edit this report while its status is "Pending".
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-end gap-2">
                    <?php if ($data['status'] === 'Pending'): ?>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $data['id'] ?>">Edit</button>
                    <?php endif; ?>
                        <a href="respon.php?hapus=<?= $data['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this report?')">Delete</a>
                    </div>
                </div>

                <?php if ($data['status'] === 'Pending'): ?>
                    <div class="modal fade" id="editModal<?= $data['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Report</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id_aduan" value="<?= $data['id'] ?>">
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <label class="form-label">Service Category</label>
                                                <select name="kategori" class="form-select" required>
                                                    <option value="HR Helpdesk" <?= $data['kategori'] === 'HR Helpdesk' ? 'selected' : '' ?>>HR Helpdesk</option>
                                                    <option value="Feedback & Complaints" <?= $data['kategori'] === 'Feedback & Complaints' ? 'selected' : '' ?>>Feedback & Complaints</option>
                                                    <option value="Whistleblowing" <?= $data['kategori'] === 'Whistleblowing' ? 'selected' : '' ?>>Whistleblowing</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Priority Level</label>
                                                <select name="prioritas" class="form-select" required>
                                                    <option value="Not Urgent" <?= $data['prioritas'] === 'Not Urgent' ? 'selected' : '' ?>>Not Urgent</option>
                                                    <option value="Urgent" <?= $data['prioritas'] === 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                                                    <option value="Very Urgent" <?= $data['prioritas'] === 'Very Urgent' ? 'selected' : '' ?>>Very Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Incident Date <span class="text-muted small">(Optional — ignored for Feedback & Complaints)</span></label>
                                            <input type="date" name="tanggal_kejadian" class="form-control" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($data['tanggal_kejadian'] ?? '') ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Report Details</label>
                                            <textarea name="detail_aduan" class="form-control" rows="4" required><?= htmlspecialchars($data['detail_aduan']) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Replace Attachment <span class="text-muted small">(Optional)</span></label>
                                            <input type="file" name="lampiran" class="form-control" accept=".jpg,.jpeg,.png,.mp4,.mp3,.wav,.m4a">
                                            <?php if (!empty($data['lampiran'])): ?>
                                                <div class="form-text">Current file: <?= htmlspecialchars($data['lampiran']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_anonim" id="anonEdit<?= $data['id'] ?>" value="1" <?= $data['is_anonim'] == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="anonEdit<?= $data['id'] ?>">Submit as <strong>Anonymous</strong></label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_report" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5 text-muted">
                    <p class="mb-3">No complaints or suggestions submitted.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="py-2 border-top text-center">
        <div class="container">
            &copy; 2026 HR Helpdesk & WBS &mdash; Internal Employee Consultation & Whistleblowing Portal
        </div>
    </footer>
</body>
</html>