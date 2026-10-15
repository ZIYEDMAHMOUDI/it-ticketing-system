<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/database.php';
requireRole(['technician', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: queue.php');
    exit;
}

$ticketId = (int)($_POST['ticket_id'] ?? 0);
$action   = $_POST['action'] ?? '';
$me       = (int)$_SESSION['user_id'];

if ($action === 'take') {
    // Only an open, unassigned ticket can be taken
    $stmt = $pdo->prepare(
        "UPDATE tickets
         SET assigned_to = ?, status = 'in_progress'
         WHERE id = ? AND assigned_to IS NULL AND status = 'open'"
    );
    $stmt->execute([$me, $ticketId]);
}

if ($action === 'update') {
    $newStatus  = $_POST['status'] ?? '';
    $resolution = trim($_POST['resolution'] ?? '');
    $resolution = ($resolution === '') ? null : $resolution;

    if (in_array($newStatus, ['in_progress', 'solved'], true)) {
        // Only the assigned technician can update, and never a canceled ticket
        $stmt = $pdo->prepare(
            "UPDATE tickets
             SET status = ?, resolution = ?
             WHERE id = ? AND assigned_to = ? AND status <> 'canceled'"
        );
        $stmt->execute([$newStatus, $resolution, $ticketId, $me]);
    }
}

header('Location: ticket.php?id=' . $ticketId);
exit;