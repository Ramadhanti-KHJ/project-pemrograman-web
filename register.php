<?php
session_start();
require_once 'koneksi.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}
$error = '';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $divisi = mysqli_real_escape_string($koneksi, $_POST['divisi']);
    $password = $_POST['password'];
    $konfirmasi_pw = $_POST['konfirmasi_pw'];

    $cekusername = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");

    if ($password !== $konfirmasi_pw) {
        $error = "Passwords do not match!";
    } else if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long!";
    } else if (empty($username) || empty($divisi) || empty($password) || empty($konfirmasi_pw)) {
        $error = "All fields are required!";
    } else if (mysqli_num_rows($cekusername) > 0) {
        $error = "Username already used!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = mysqli_query($koneksi, "INSERT INTO users (username, password, divisi) VALUES ('$username', '$hashed_password', '$divisi')");

        if ($query) {
            header("Location: login.php?status=registered");
            exit;
        } else {
            $error = "Registration failed!";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="container py-4" style="max-width: 420px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="text-center mb-1">Employee Registration</h4>
                <p class="text-center text-muted small mb-4">Create an account to access HR services.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Division</label>
                        <select name="divisi" class="form-select" required>
                            <option value="" disabled selected>-- Choose Division --</option>
                            <option value="IT & Engineering">IT & Engineering</option>
                            <option value="Finance & Accounting">Finance & Accounting</option>
                            <option value="Marketing & Sales">Marketing & Sales</option>
                            <option value="Operations">Operations</option>
                            <option value="Product & Design">Product & Design</option>
                            <option value="General Affairs">General Affairs</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="konfirmasi_pw" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                </form>

                <p class="text-center mt-3 mb-0 small">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>