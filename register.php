<?php
include_once 'db/connect.php'; 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $yetki = $_POST['yetki'];
    $profile_photo = '';

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $error = "Şifre en az bir harf, en az bir rakam içermeli ve en az 8 karakter uzunluğunda olmalıdır.";
    } else {
        $checkUsernameStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkUsernameStmt->bind_param("s", $username);
        $checkUsernameStmt->execute();
        $checkUsernameStmt->store_result();

        if ($checkUsernameStmt->num_rows > 0) {
            $error = "Bu kullanıcı adı zaten alınmış.";
        } else {
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photoTmpName = $_FILES['photo']['tmp_name'];
                $photoName = $_FILES['photo']['name'];
                $photoExtension = pathinfo($photoName, PATHINFO_EXTENSION);
                $allowedExtensions = ['png', 'jpg', 'jpeg'];

                if (in_array($photoExtension, $allowedExtensions)) {
                    $uploadDir = './assets/photos/';
                    $newPhotoName = uniqid() . '.' . $photoExtension;
                    $uploadFile = $uploadDir . $newPhotoName;

                    if (move_uploaded_file($photoTmpName, $uploadFile)) {
                        $profile_photo = $newPhotoName; 
                    } else {
                        $error = "Profil fotoğrafı yüklenirken bir hata oluştu.";
                    }
                } else {
                    $error = "Yalnızca PNG, JPG ve JPEG dosyalarına izin verilmektedir.";
                }
            } else {
                $error = "Profil fotoğrafı yüklenmedi. Hata kodu: " . $_FILES['photo']['error'];
            }

            if (empty($error)) {
                $stmt = $conn->prepare("INSERT INTO users (username, fullname, gender, email, password, yetki, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $username, $fullname, $gender, $email, $passwordHash, $yetki, $profile_photo);

                if ($stmt->execute()) {
                    echo "Kayıt başarılı!";
                    header('Location: ./login.php');
                    exit();
                } else {
                    $error = "Kayıt eklenirken bir hata oluştu: " . $stmt->error;
                }

                $stmt->close();
            }
        }

        $checkUsernameStmt->close();
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Kayıt Ol</title>
</head>
<body>
    <?php include 'inc/header.php'; ?>

    <form method="post" action="" enctype="multipart/form-data" class="max-w-xl p-4 m-4 border flex flex-col gap-4 rounded-2xl mx-auto">
        <label for="username">
            <p>Kullanıcı Adı*</p>
            <input type="text" name="username" required class="border rounded-lg w-full p-2">
            
        </label>
        
        <label for="fullname">
            <p>Ad & Soyad*</p>
            <input type="text" name="fullname" required class="border rounded-lg w-full p-2">
        </label>

        <label for="gender">
            <p>Cinsiyet*</p>
            <select name="gender" id="gender" required class="border rounded-lg w-full p-2">
                <option value="0">Erkek</option>
                <option value="1">Kadın</option>
            </select>
        </label>

        <label for="email">
            <p>E-posta*</p>
            <input type="email" name="email" required class="border rounded-lg w-full p-2">
        </label>

        <label for="password">
            <p>Şifre*</p>
            <input type="password" name="password" required class="border rounded-lg w-full p-2">
            <?php if (!empty($error)): ?>
                <p class="text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
        </label>

        <label for="password-2">
            <p>Şifre Tekrar*</p>
            <input type="password" name="password-2" required class="border rounded-lg w-full p-2">
        </label>

        <label for="yetki">
            <p>Yetki*</p>
            <select name="yetki" id="yetki" required class="border rounded-lg w-full p-2">
                <option value="0">Editör</option>
                <option value="1">Hakem</option>
            </select>
        </label>

        <p>Profil Fotoğrafı</p>
        <div class="w-full py-4 bg-gray-50 rounded-2xl border border-gray-300 gap-3 grid border-dashed">
            <div class="grid gap-1">
                <h2 class="text-center text-gray-400 text-xs leading-4">PNG veya JPG</h2>
            </div>
            <div class="grid gap-2">
                <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Dosya seç veya sürükle</h4>
                <div class="flex items-center justify-center">
                    <label>
                        <input type="file" hidden name="photo" id="photo" accept=".png, .jpg, .jpeg" required />
                        <div class="flex w-28 h-9 px-2 flex-col bg-black rounded-full shadow text-white text-xs font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">
                            Dosya Seç
                        </div>
                    </label>
                </div>
                <span id="file-name" class="text-center text-gray-500 text-sm"></span>
            </div>
        </div>

        <button type="submit" class="bg-black p-3 mt-2 justify-center flex text-white rounded-lg flex items-center text-center">Kayıt Ol</button>
    </form>

    <script>
        document.getElementById('photo').addEventListener('change', function() {
            const fileName = this.files[0].name;
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>
