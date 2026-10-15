<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];

// Load categories for the dropdown
$categories = $pdo->query('SELECT id, name FROM categories')->fetchAll();
$categoryIds = array_column($categories, 'id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId  = (int)($_POST['category_id'] ?? 0);
    $priority    = $_POST['priority'] ?? 'medium';

    if ($title === '')       $errors[] = 'Title is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if (!in_array($categoryId, $categoryIds, true)) $errors[] = 'Choose a valid category.';
    if (!in_array($priority, ['low', 'medium', 'high'], true)) $errors[] = 'Invalid priority.';

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO tickets (title, description, priority, category_id, created_by)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$title, $description, $priority, $categoryId, $_SESSION['user_id']]);

        header('Location: my_tickets.php');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>

<h2>New Ticket</h2>

<?php foreach ($errors as $error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<form method="POST" action="new_ticket.php">
  <label>Title
    <input type="text" name="title" maxlength="200" required>
  </label><br>

  <label>Description
    <textarea name="description" rows="5" required></textarea>
  </label><br>

  <label>Category
    <select name="category_id" required>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Priority
    <select name="priority">
      <option value="low">Low</option>
      <option value="medium" selected>Medium</option>
      <option value="high">High</option>
    </select>
  </label><br>

  <button type="submit">Submit ticket</button>
</form>

<p><a href="my_tickets.php">Back to my tickets</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>