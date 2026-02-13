<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.use_strict_mode', 1);
session_start();
require_once 'lib/auth.php';
require_once 'lib/functions.php';
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid request. CSRF token mismatch.');
    }
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // jika tidak ada pilihan role maka role akan diisi dengan role: admin
    $role = $_POST['role'] ?? 'mahasiswa';
    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Validate password strength
        $passwordErrors = validatePassword($password, false); // ganti menjadi: false agar bebas membuat password
        if (!empty($passwordErrors)) {
            $error = implode('', $passwordErrors);
        } else {
            if (registerUser($username, $password, $role)) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Username already exists or registration failed.";
            }
        }
    }
}
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="assets/adminkit/img/icons/icon-48x48.png" />

    <title>Sign Up | Admin Panel</title>

    <link href="assets/adminkit/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="text-center mt-4">
                            <h1 class="h2">Buat Akun Baru</h1>
                            <p class="lead">
                                Mulai gunakan layanan kami
                            </p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <?php if ($success): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <input type="hidden" name="csrf_token"
                                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input class="form-control form-control-lg" type="text" name="username"
                                                placeholder="Masukkan username" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input class="form-control form-control-lg" type="password" name="password"
                                                placeholder="Masukkan password" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Role</label>
                                            <select class="form-select form-select-lg" name="role" required>
                                                <option value="pengaju">Pengaju</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <button type="submit" class="btn btn-lg btn-primary">Daftar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            Sudah punya akun? <a href="login.php">Log In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/adminkit/js/app.js"></script>

</body>

</html>