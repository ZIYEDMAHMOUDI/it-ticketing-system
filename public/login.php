<?php
session_start();
require __DIR__ . '/../config/database.php';

$error = '';

// Check the password first, before any HTML is printed
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

// Only now print the page
require __DIR__ . '/../includes/header.php';
?>

<h2>Login</h2>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="login.php">
  <label>Email
    <input type="email" name="email" required>
  </label>
  <label>Password
    <input type="password" name="password" required>
  </label>
  <button type="submit">Log in</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>