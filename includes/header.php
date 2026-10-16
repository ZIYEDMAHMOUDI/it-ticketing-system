<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedIn = isset($_SESSION['user_id']);
$role     = $_SESSION['role'] ?? '';
$current  = basename($_SERVER['PHP_SELF']);

// Menu items for each role: [page, label]
$menus = [
    'employee' => [
        ['my_tickets.php', 'My tickets'],
        ['new_ticket.php', 'New ticket'],
    ],
    'technician' => [
        ['queue.php?filter=open', 'Open queue'],
        ['queue.php?filter=mine', 'Assigned to me'],
    ],
    'admin' => [
        ['queue.php?filter=all', 'All tickets'],
        ['admin_users.php', 'Users'],
    ],
];

// Marks the menu item of the page currently open
function isActiveLink(string $href, string $current): bool
{
    $path = strtok($href, '?');
    parse_str((string)parse_url($href, PHP_URL_QUERY), $want);
    parse_str((string)($_SERVER['QUERY_STRING'] ?? ''), $have);

    return $current === $path
        && (($want['filter'] ?? null) === ($have['filter'] ?? null));
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IT Ticketing System</title>
  <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script>
    // Apply the saved theme before the page paints, so it doesn't flash
    (function () {
      var t = localStorage.getItem('theme');
      if (t) document.documentElement.setAttribute('data-theme', t);
    })();
  </script>
</head>
<body class="<?= $loggedIn ? 'app' : 'auth' ?>">
<div class="layout">

<?php if ($loggedIn): ?>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-row">
        <img src="../assets/images/logo.svg" alt="IT Ticketing logo" class="brand-logo">
        <span>IT Ticketing</span>
      </div>
      <small><?= htmlspecialchars($role) ?></small>
    </div>

    <nav class="nav">
      <?php foreach ($menus[$role] ?? [] as [$href, $label]): ?>
        <a href="<?= htmlspecialchars($href) ?>"
           class="<?= isActiveLink($href, $current) ? 'active' : '' ?>">
          <?= htmlspecialchars($label) ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
      <a class="btn btn-ghost" href="logout.php">Log out</a>
    </div>
  </aside>
<?php endif; ?>

  <div class="content">
    <div class="topbar">
      <span class="topbar-title">
        <?= $loggedIn ? htmlspecialchars($_SESSION['name']) : 'Sign in' ?>
      </span>
      <button type="button" id="theme-toggle" class="btn-ghost">Dark mode</button>
    </div>

    <main>