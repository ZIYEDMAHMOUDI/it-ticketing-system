<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';
requireRole(['admin']);

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Create a new account
    if ($action === 'create') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $role     = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email.';
        }
        if (!in_array($role, ['employee', 'technician', 'admin'], true)) {
            $errors[] = 'Choose a valid role.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if (!$errors) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password, role, active)
                     VALUES (?, ?, ?, ?, 1)'
                );
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
                $success = 'Account created.';
            } catch (PDOException $e) {
                // The email column is UNIQUE, so a duplicate lands here
                $errors[] = 'That email is already in use.';
            }
        }
    }

    // Activate or deactivate an account
    if ($action === 'toggle') {
        $id = (int)($_POST['user_id'] ?? 0);

        // An admin cannot deactivate their own account
        if ($id !== (int)$_SESSION['user_id']) {
            $stmt = $pdo->prepare('UPDATE users SET active = 1 - active WHERE id = ?');
            $stmt->execute([$id]);
        }

        header('Location: admin_users.php');
        exit;
    }
}

$users = $pdo->query('SELECT id, name, email, role, active FROM users ORDER BY id')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<h2>Manage Users</h2>
<p><a href="dashboard.php">Dashboard</a> | <a href="logout.php">Log out</a></p>

<?php foreach ($errors as $error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
  <p><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<h3>Create account</h3>
<form method="POST" action="admin_users.php">
  <input type="hidden" name="action" value="create">

  <label>Name <input type="text" name="name" required></label><br>
  <label>Email <input type="email" name="email" required></label><br>
  <label>Role
    <select name="role">
      <option value="employee">Employee</option>
      <option value="technician">Technician</option>
      <option value="admin">Admin</option>
    </select>
  </label><br>
  <label>Temporary password (min 8 characters)
    <input type="password" name="password" required minlength="8">
  </label><br>

  <button type="submit">Create</button>
</form>

<h3>All users</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th>
  </tr>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= $u['role'] ?></td>
      <td><?= $u['active'] ? 'Active' : 'Inactive' ?></td>
      <td>
        <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
          <form method="POST" action="admin_users.php">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
            <button type="submit"><?= $u['active'] ? 'Deactivate' : 'Activate' ?></button>
          </form>
        <?php else: ?>
          (you)
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>