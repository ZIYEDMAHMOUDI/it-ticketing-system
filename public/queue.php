<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';
requireRole(['technician', 'admin']);

// Which tickets to show, from the filter links at the top
$filter = $_GET['filter'] ?? 'open';
$allowedFilters = ['open', 'in_progress', 'solved', 'canceled', 'mine', 'all'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'open';
}

$sql = 'SELECT t.id, t.title, t.priority, t.status, t.assigned_to, t.created_at,
               c.name AS category, u.name AS employee
        FROM tickets t
        JOIN categories c ON c.id = t.category_id
        JOIN users u ON u.id = t.created_by';

$where  = [];
$params = [];

if ($filter === 'mine') {
    $where[]  = 't.assigned_to = ?';
    $params[] = $_SESSION['user_id'];
} elseif ($filter !== 'all') {
    $where[]  = 't.status = ?';
    $params[] = $filter;
}

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY t.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<h2>Ticket Queue</h2>

<p>
  Filter:
  <?php foreach (['open', 'in_progress', 'solved', 'canceled', 'mine', 'all'] as $f): ?>
    <a href="queue.php?filter=<?= $f ?>"><?= $f ?></a>
  <?php endforeach; ?>
</p>
<p><a href="dashboard.php">Dashboard</a> | <a href="logout.php">Log out</a></p>

<?php if (!$tickets): ?>
  <p>No tickets for this filter.</p>
<?php else: ?>
  <table border="1" cellpadding="6">
    <tr>
      <th>#</th><th>Title</th><th>Employee</th><th>Category</th>
      <th>Priority</th><th>Status</th><th>Opened</th><th>Action</th>
    </tr>
    <?php foreach ($tickets as $t): ?>
      <tr>
        <td><a href="ticket.php?id=<?= $t['id'] ?>"><?= $t['id'] ?></a></td>
        <td><?= htmlspecialchars($t['title']) ?></td>
        <td><?= htmlspecialchars($t['employee']) ?></td>
        <td><?= htmlspecialchars($t['category']) ?></td>
        <td><?= $t['priority'] ?></td>
        <td><?= $t['status'] ?></td>
        <td><?= $t['created_at'] ?></td>
        <td>
          <?php if ($t['status'] === 'open' && $t['assigned_to'] === null): ?>
            <form method="POST" action="ticket_action.php">
              <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
              <input type="hidden" name="action" value="take">
              <button type="submit">Take</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>