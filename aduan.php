<?php
require_once 'cek_sesi.php';
require_once 'koneksi.php';
$error = '';

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$divisi = $_SESSION['divisi'];

if (isset($_POST['kirim_aduan'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $prioritas = mysqli_real_escape_string($koneksi, $_POST['prioritas']);
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $detail_aduan = mysqli_real_escape_string($koneksi, trim($_POST['detail_aduan']));
    $tanggal_kejadian = !empty($_POST['tanggal_kejadian'])? "'" . mysqli_real_escape_string($koneksi, $_POST['tanggal_kejadian']) . "'" : "NULL";
    $is_anonim = isset($_POST['is_anonim']) ? 1 : 0;
    $nama_file_baru = NULL;

    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
            $nama_asli = $_FILES['lampiran']['name'];
            $ukuran = $_FILES['lampiran']['size'];
            $tmp_name = $_FILES['lampiran']['tmp_name'];

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
                $nama_file_baru = uniqid() . '.' . $ekstensi;
                $tujuan = "lampiran/" . $nama_file_baru;

                if (!move_uploaded_file($tmp_name, $tujuan)) {
                    $error = "Failed to upload the file.";
                }
            }
        } else {
            $error = "An error occurred while uploading the file.";
        }
    }

    if ($error === "") {
        $lampiran_sql = $nama_file_baru ? "'$nama_file_baru'" : "NULL";
        $query = "INSERT INTO aduan (user_id, kategori, prioritas, judul, detail_aduan, tanggal_kejadian, lampiran, is_anonim, status) 
                  VALUES ('$user_id', '$kategori', '$prioritas', '$judul', '$detail_aduan', $tanggal_kejadian, $lampiran_sql, '$is_anonim', 'Pending')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Report submitted!'); window.location='respon.php';</script>";
            exit;
        } else {
            $error = "Failed to submit the report: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk and Whistleblowing</title>
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

    <div class="container py-4" style="max-width: 720px;">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Helpdesk and Whistleblowing Form</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($username) ?> (<?= htmlspecialchars($divisi) ?>)" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service Category</label>
                            <select name="kategori" class="form-select" required>
                                <option value="" disabled selected>-- Select Category --</option>
                                <option value="HR Helpdesk">HR Helpdesk</option>
                                <option value="Feedback & Complaints">Feedback & Complaints</option>
                                <option value="Whistleblowing">Whistleblowing</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Priority Level</label>
                            <select name="prioritas" class="form-select" required>
                                <option value="Not Urgent">Not Urgent</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Very Urgent">Very Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="incident_date_group">
                            <label class="form-label">Incident Date <span class="text-muted small">(Optional — ignored for Feedback & Complaints)</span></label>
                            <input type="date" name="tanggal_kejadian" id="tanggal_kejadian" class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Evidence Attachment (Optional)</label>
                            <input type="file" name="lampiran" class="form-control" accept=".jpg,.jpeg,.png,.mp4,.mp3,.wav,.m4a">
                            <div class="form-text">Format: JPG, PNG, MP4, MP3, WAV (Max. 15MB)</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="judul" class="form-control" placeholder="Example: Issues with this month's salary slip" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="detail_aduan" class="form-control" rows="5" placeholder="Write your complaint or question in detail..." required></textarea>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_anonim" id="is_anonim" value="1">
                        <label class="form-check-label" for="is_anonim">Submit as <strong>Anonymous</strong> (Hide my name and division from HR)</label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="kirim_aduan" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>