<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';
requireRole(['technician', 'admin']);

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT t.*, c.name AS category, u.name AS employee, a.name AS technician
     FROM tickets t
     JOIN categories c ON c.id = t.category_id
     JOIN users u ON u.id = t.created_by
     LEFT JOIN users a ON a.id = t.assigned_to
     WHERE t.id = ?'
);
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header('Location: queue.php');
    exit;
}

$isMine = (int)$ticket['assigned_to'] === (int)$_SESSION['user_id'];

require __DIR__ . '/../includes/header.php';
?>

<h2>Ticket #<?= $ticket['id'] ?></h2>
<p class="muted"><?= htmlspecialchars($ticket['title']) ?></p>

<table>
  <tr><th>Description</th><td><?= nl2br(htmlspecialchars($ticket['description'])) ?></td></tr>
  <tr><th>Employee</th><td><?= htmlspecialchars($ticket['employee']) ?></td></tr>
  <tr><th>Category</th><td><?= htmlspecialchars($ticket['category']) ?></td></tr>
  <tr><th>Priority</th><td><?= $ticket['priority'] ?></td></tr>
  <tr><th>Status</th><td><span class="badge badge-<?= $ticket['status'] ?>"><?= str_replace('_', ' ', $ticket['status']) ?></span></td></tr>
  <tr><th>Technician</th><td><?= htmlspecialchars($ticket['technician'] ?? 'Not assigned') ?></td></tr>
  <tr><th>Resolution</th><td><?= nl2br(htmlspecialchars($ticket['resolution'] ?? '')) ?></td></tr>
</table>

<?php if ($isMine && $ticket['status'] !== 'canceled'): ?>
  <h3 style="margin-top:28px">Update ticket</h3>
  <form method="POST" action="ticket_action.php">
    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
    <input type="hidden" name="action" value="update">

    <label>Status
      <select name="status">
        <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In progress</option>
        <option value="solved" <?= $ticket['status'] === 'solved' ? 'selected' : '' ?>>Solved</option>
      </select>
    </label>

    <label>Resolution (what you did)
      <textarea name="resolution" rows="4"><?= htmlspecialchars($ticket['resolution'] ?? '') ?></textarea>
    </label>

    <button type="submit">Save</button>
  </form>
<?php endif; ?>

<p><a class="btn btn-ghost" href="queue.php">Back to queue</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>