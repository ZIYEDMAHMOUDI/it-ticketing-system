<?php
session_start();
require __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND active = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid email or password.';
}

require __DIR__ . '/../includes/header.php';
?>

<div class="auth-card">
  <div class="auth-logo">
    <img src="../assets/images/logo.svg" alt="IT Ticketing logo" class="auth-logo-img">
  </div>

  <h2>Sign in</h2>
  <p class="auth-sub">Use the account your administrator created for you.</p>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="login.php" class="auth-form">
    <label>Email
      <span class="input-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
        <input type="email" name="email" placeholder="you@company.com" required>
      </span>
    </label>

    <label>Password
      <span class="input-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <input type="password" name="password" placeholder="Your password" required>
      </span>
    </label>

    <button type="submit">Sign in</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>