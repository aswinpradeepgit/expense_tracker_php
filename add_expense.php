<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $amount = $_POST['expense-amount'];
    $description = $_POST['expense-description'];
    $category = $_POST['expense-category'];
    $date = $_POST['expense-date'];

    include 'config.php';

    $stmt = $conn->prepare('INSERT INTO expenses (user_id, category_id, amount, description, date) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('iisss', $userId, $category, $amount, $description, $date);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
