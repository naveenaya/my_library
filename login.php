<?php
session_start();
require 'config.php'; // must create $conn (mysqli)

// friendly error
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_user = trim($_POST['username'] ?? '');
    $input_pass = $_POST['password'] ?? '';

    if ($input_user === '' || $input_pass === '') {
        $error = 'Enter username and password.';
    } else {
        // fetch user record case-insensitively
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1");
        $stmt->bind_param('s', $input_user);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $row = $res->fetch_assoc()) {
            $stored = (string)$row['password'];
            $ok = false;

            // 1) If DB used password_hash()
            if (function_exists('password_verify') && password_verify($input_pass, $stored)) {
                $ok = true;
            }

            // 2) If DB used MD5
            if (!$ok && $stored === md5($input_pass)) {
                $ok = true;
            }

            // 3) If DB stored plaintext (not recommended)
            if (!$ok && $stored === $input_pass) {
                $ok = true;
            }

            if ($ok) {
                // Login success: store DB username (original case) and role
                $_SESSION['user_id']  = (int)$row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = (!empty($row['role']) ? $row['role'] : 'user');

                // redirect to dashboard or posts page
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login • My Library</title>
  <link rel="stylesheet" href="file.css">
  <style>
    /* Small fallback styling if file.css missing */
    .box{max-width:380px;margin:50px auto;background:#fff;padding:18px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.06);}
    input{width:100%;padding:8px;margin:8px 0;border:1px solid #ddd;border-radius:6px}
    button{width:100%;padding:10px;background:#007bff;color:#fff;border:none;border-radius:6px}
    .error{color:#c00;margin-bottom:10px}
  </style>
</head>
<body>
  <div class="box">
    <h2>Login</h2>
    <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post" action="">
      <label>Username</label>
      <input type="text" name="username" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>