<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Şifre Değiştirme</title>
    <style>
        .container {
            width: 300px;
            background-color: #ffffff;
            border-radius: 10px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: .4rem 0;
        }

        .message {
            margin: 10px 0;
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container flex flex-col gap-2">
    <h2>Şifre Değiştirme</h2>
    <form action="" method="POST">
        <input type="password" name="current_password" placeholder="Mevcut Şifre" required>
        <input type="password" name="new_password" placeholder="Yeni Şifre" required>
        <input type="password" name="confirm_password" placeholder="Yeni Şifreyi Onayla" required>
        <button type="submit" class="bg-green-600 p-2.5 w-full rounded-md text-white mt-4">Şifreyi Değiştir</button>
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    function validatePassword($password) {
    
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password);
    }

    $isValid = true;
    $messages = [];

    if ($new_password !== $confirm_password) {
        $isValid = false;
        $messages[] = 'Yeni şifreler eşleşmiyor!';
    }

    if (!validatePassword($new_password)) {
        $isValid = false;
        $messages[] = 'Yeni şifre en az 8 karakter olmalı ve hem harf hem de rakam içermelidir!';
    }

    if ($isValid) {
        
        echo '<p class="message">Şifre başarıyla değiştirildi.</p>';
    } else {
        foreach ($messages as $message) {
            echo '<p class="message">' . htmlspecialchars($message) . '</p>';
        }
    }
}
?>

</body>
</html>
