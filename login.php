<?php
// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // 1 in production with HTTPS
ini_set('session.cookie_path', '/');
ini_set('session.use_strict_mode', 1);

session_start();

if (isset($_SESSION['user_id']) && empty($_SESSION['role'])) {
    session_destroy();
}

require_once 'lib/functions.php';
require_once 'lib/auth.php';

if (isset($_SESSION['user_id'])) {
    redirectBasedOnRole($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid request. CSRF token mismatch.');
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi.";
    } else {
        $role = login($username, $password);
        if ($role) {
            redirectBasedOnRole($role);
        } else {
            $error = "Username atau password salah.";
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

    <title>Sign In | Admin Panel</title>

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
                            <h1 class="h2">Selamat Datang Kembali!</h1>
                            <p class="lead">
                                Silakan login untuk melanjutkakn
                            </p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($error) ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST">
                                        <input type="hidden" name="csrf_token"
                                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input class="form-control form-control-lg" type="text" name="username"
                                                placeholder="Masukkan username Anda" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input class="form-control form-control-lg" type="password" name="password"
                                                placeholder="Masukkan password Anda" required />
                                        </div>
                                        <!-- 
                                        <div class="mb-3">
                                            <div class="form-check align-items-center">
                                                <input id="customControlInline" type="checkbox" class="form-check-input" value="remember-me" name="remember-me" checked>
                                                <label class="form-check-label text-small" for="customControlInline">Ingat saya</label>
                                            </div>
                                        </div> 
                                        -->
                                        <div class="d-grid gap-2 mt-3">
                                            <button type="submit" class="btn btn-lg btn-primary">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            Belum punya akun? <a href="register.php">Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/adminkit/js/app.js"></script>

</body>

</html>