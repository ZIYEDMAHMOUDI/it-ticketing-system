<?php
session_start();
require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Only accept the form submission, never a plain link visit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_tickets.php');
    exit;
}

$ticketId = (int)($_POST['ticket_id'] ?? 0);

// Cancel only if: the ticket belongs to this user AND it is still open
$stmt = $pdo->prepare(
    "UPDATE tickets
     SET status = 'canceled'
     WHERE id = ? AND created_by = ? AND status = 'open'"
);
$stmt->execute([$ticketId, $_SESSION['user_id']]);

// rowCount() tells us whether anything changed (0 = not allowed or not found)
header('Location: my_tickets.php');
exit;