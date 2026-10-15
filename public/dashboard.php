<?php
require __DIR__ . '/../includes/auth.php';
requireRole(['employee', 'technician', 'admin']);

require __DIR__ . '/../includes/header.php';
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
<p>Role: <?= htmlspecialchars($_SESSION['role']) ?></p>

<?php if ($_SESSION['role'] === 'employee'): ?>
  <p><a href="my_tickets.php">My tickets</a></p>
  <p><a href="new_ticket.php">Create a ticket</a></p>

<?php elseif ($_SESSION['role'] === 'technician'): ?>
  <p><a href="queue.php">Open the ticket queue</a></p>

<?php else: ?>
  <p><a href="queue.php">View all tickets</a></p>
  <p><a href="admin_users.php">Manage users</a></p>
<?php endif; ?>

<p><a href="logout.php">Log out</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>