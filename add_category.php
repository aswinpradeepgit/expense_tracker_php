<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $categoryName = $_POST['new-category'];

    include 'config.php';

    $stmt = $conn->prepare('INSERT INTO categories (user_id, name) VALUES (?, ?)');
    $stmt->bind_param('is', $userId, $categoryName);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
