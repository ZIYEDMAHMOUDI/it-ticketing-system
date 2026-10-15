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
  <p><a href="queue.php">Open the ticket queue</a></p>
<?php else: ?>
  <p><a href="queue.php">View all tickets</a></p>
<?php endif; ?>

<p><a href="logout.php">Log out</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>