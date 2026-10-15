<?php
session_start();

// If nobody is logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../includes/header.php';
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
<p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>

<?php if ($_SESSION['role'] === 'employee'): ?>
  <p>Employee dashboard: tickets come next.</p>
<?php elseif ($_SESSION['role'] === 'technician'): ?>
  <p>Technician dashboard: the queue comes next.</p>
<?php else: ?>
  <p>Admin dashboard: user management comes next.</p>
<?php endif; ?>

<p><a href="logout.php">Log out</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>