<?php
session_start();
include_once 'db/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];

    $sorgu = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sorgu);
    $stmt->bind_param("s", $kullanici_adi);
    $stmt->execute();
    $sonuc = $stmt->get_result();

    if ($sonuc->num_rows == 1) {
        $kullanici = $sonuc->fetch_assoc();

        if (password_verify($sifre, $kullanici['password'])) {
            $_SESSION['user_id'] = $kullanici['id'];
            $_SESSION['username'] = $kullanici['username'];

            header('Location: ./');
            exit();
        } else {
            echo "Kullanıcı adı veya şifre hatalı!";
        }
    } else {
        echo "Kullanıcı adı veya şifre hatalı!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Giriş Yap</title>
</head>
<body>
    <?php include 'inc/header.php'; ?>
    
    <form method="POST" action="" class="max-w-sm p-4 m-4 border flex flex-col gap-2 *:flex *:flex-col *:gap-2 rounded-2xl mx-auto">
        <label>
            <p>Kullanıcı Adı</p>
            <input type="text" name="kullanici_adi" required placeholder="Kullanıcı Adı" class="px-3 p-2 border rounded-lg"/>
        </label>
        <label>
            <p>Şifre</p>
            <input name="sifre" type="password" placeholder="Şifre" required class="px-3 p-2 border rounded-lg"/>
        </label>
        <button type="submit" class="bg-black p-3 text-white rounded-lg flex items-center text-center">Giriş Yap</button>
        <a href="./register.php"  class="bg-zinc-200 p-3 rounded-lg flex items-center text-center">Kayıt Ol</a>
    </form>
</body>
</html>
