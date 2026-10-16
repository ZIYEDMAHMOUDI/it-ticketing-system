<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';
requireRole(['employee', 'technician', 'admin']);

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
<p class="muted">Tickets you have opened.</p>
<p><a class="btn" href="new_ticket.php">+ New ticket</a></p>

<?php if (!$tickets): ?>
  <p>You have no tickets yet.</p>
<?php else: ?>
  <table>
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
        <td><span class="badge badge-<?= $t['status'] ?>"><?= str_replace('_', ' ', $t['status']) ?></span></td>
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