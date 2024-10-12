<?php
session_start();
include_once '../db/connect.php'; 

$response = array('status' => 'error', 'message' => 'Bir hata oluştu.');

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $user_id = $_SESSION['user_id'];
    $photoTmpName = $_FILES['photo']['tmp_name'];
    $photoName = $_FILES['photo']['name'];
    $photoExtension = pathinfo($photoName, PATHINFO_EXTENSION);
    $allowedExtensions = ['png', 'jpg', 'jpeg'];

    if (in_array($photoExtension, $allowedExtensions)) {
        $uploadDir = '../assets/photos/';
        $newPhotoName = uniqid() . '.' . $photoExtension; 
        $uploadFile = $uploadDir . $newPhotoName;

        $stmt = $conn->prepare("SELECT photo FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($oldPhoto);
        $stmt->fetch();
        $stmt->close();

        if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
            unlink($uploadDir . $oldPhoto);
        }

        if (move_uploaded_file($photoTmpName, $uploadFile)) {
            $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
            $stmt->bind_param("si", $newPhotoName, $user_id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Profil fotoğrafı başarıyla güncellendi!';
                $response['photo'] = $newPhotoName;
            } else {
                $response['message'] = 'Veritabanı güncellemesi sırasında bir hata oluştu.';
            }
            $stmt->close();
        } else {
            $response['message'] = 'Dosya yüklenirken bir hata oluştu.';
        }
    } else {
        $response['message'] = 'Yalnızca PNG, JPG ve JPEG dosyalarına izin verilmektedir.';
    }
} else {
    $response['message'] = 'Geçerli bir dosya seçilmedi.';
}

$conn->close();
echo json_encode($response);
