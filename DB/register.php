<?php
// Включить отчет об ошибках
require 'connection.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'])); 
    $password = trim($_POST['password']); // Удалить пробелы

    // Validation for username input
    if (!preg_match('/^[A-Za-z]{1,25}$/', $username)) {
        $response['message'] = "Логин может содержать только английские буквы и до 25 символов.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // Проверка пароля
if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*()_+=-]).{8,32}$/', $password)) {
    $response['message'] = "Пароль должен содержать от 8 до 32 символов, одну заглавную букву, один специальный символ и цифры.";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}


    // Хеширование пароля
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $response['message'] = "Пользователь с таким логином уже существует.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        
        if ($stmt->execute()) {
            $response['message'] = "Регистрация успешна!";
        } else {
            $response['message'] = "Ошибка при регистрации. Попробуйте еще раз.";
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

?>
