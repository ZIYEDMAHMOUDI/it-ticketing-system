<?php
require __DIR__ . '/../includes/auth.php';
requireRole(['employee', 'technician', 'admin']);

require __DIR__ . '/../includes/header.php';

$role = $_SESSION['role'];

// Cards for each role: [page, title, description]
$cards = [
  'employee' => [
    ['my_tickets.php', 'My tickets', 'Track the status of tickets you opened.'],
    ['new_ticket.php', 'New ticket', 'Report a new IT problem.'],
  ],
  'technician' => [
    ['queue.php?filter=open', 'Open queue', 'Tickets waiting to be taken.'],
    ['queue.php?filter=mine', 'Assigned to me', 'Tickets you are working on.'],
  ],
  'admin' => [
    ['queue.php?filter=all', 'All tickets', 'Every ticket, every status.'],
    ['admin_users.php', 'Users', 'Create, activate, and deactivate accounts.'],
  ],
];
?>

<h2>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?></h2>
<p class="muted">Signed in as <?= htmlspecialchars($role) ?>.</p>

<div class="cards">
  <?php foreach ($cards[$role] as [$href, $title, $text]): ?>
    <a class="card" href="<?= htmlspecialchars($href) ?>">
      <h3><?= htmlspecialchars($title) ?></h3>
      <p><?= htmlspecialchars($text) ?></p>
    </a>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>