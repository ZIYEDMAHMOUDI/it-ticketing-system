<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT t.id, t.title, t.priority, t.status, t.created_at, c.name AS category
     FROM tickets t
     JOIN categories c ON c.id = t.category_id
     WHERE t.created_by = ?
     ORDER BY t.created_at DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<h2>My Tickets</h2>
<p><a href="new_ticket.php">+ New ticket</a> | <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Log out</a></p>

<?php if (!$tickets): ?>
  <p>You have no tickets yet.</p>
<?php else: ?>
  <table border="1" cellpadding="6">
    <tr>
      <th>#</th><th>Title</th><th>Category</th>
      <th>Priority</th><th>Status</th><th>Opened</th><th>Actions</th>
    </tr>
    <?php foreach ($tickets as $t): ?>
      <tr>
        <td><?= $t['id'] ?></td>
        <td><?= htmlspecialchars($t['title']) ?></td>
        <td><?= htmlspecialchars($t['category']) ?></td>
        <td><?= $t['priority'] ?></td>
        <td><?= $t['status'] ?></td>
        <td><?= $t['created_at'] ?></td>
        <td>
          <?php if ($t['status'] === 'open'): ?>
            <form method="POST" action="cancel_ticket.php"
                  onsubmit="return confirm('Cancel this ticket?');">
              <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
              <button type="submit">Cancel</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>