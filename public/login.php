<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/header.php';

$error = '';

// Only process the form when it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND active = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store only what we need, never the password
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['name']    = $user['name'];

        header('Location: dashboard.php');
        exit;
    }

    // Same message for wrong email or wrong password: don't reveal which one
    $error = 'Invalid email or password.';
}
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