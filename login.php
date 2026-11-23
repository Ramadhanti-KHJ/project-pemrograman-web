<?php
session_start();
require "koneksi.php";
$error = "";
$username = $_COOKIE['last_un'] ?? "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if (password_verify($password, $data['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['divisi'] = $data['divisi'];

            if (isset($_POST['remember'])) {
                setcookie("last_un", $username, time() + (7 * 24 * 60 * 60));
            }
            header("location: dashboard.php");
            exit();
        } else {
            $error = "Username or password is wrong!";
        }
    } else {
        $error = "Account is not registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="container" style="max-width: 420px;">
        <div class="card">
            <div class="card-body">
                <h4>Login — Employee HRIS Portal</h4>
                <p>HR Helpdesk and Whistleblowing</p>

                <?php if (isset($_GET['status']) && $_GET['status'] === 'registered'): ?>
                    <div class="alert alert-success">Registration successful, you can now login.</div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="text-center mt-3 mb-0">Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>

</html>