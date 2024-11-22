<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $newLimit = $_POST['new-limit'];
    $month = date('m');
    $year = date('Y');

    include 'config.php';

    $stmt = $conn->prepare('INSERT INTO monthly_limits (user_id, month, year, limit_amount) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE limit_amount=?');
    $stmt->bind_param('iiidi', $userId, $month, $year, $newLimit, $newLimit);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
