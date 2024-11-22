<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn = new mysqli('localhost', 'root', '', 'expense_tracker');

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'Email already exists.';
    } else {
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $password);
        if ($stmt->execute()) {
            header('Location: login.html');
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
