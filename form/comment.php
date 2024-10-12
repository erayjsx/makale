<?php
session_start();
include_once '../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_POST['post_id']) && isset($_POST['comment'])) {
        $user_id = $_SESSION['user_id'];
        $post_id = $_POST['post_id'];
        $comment = trim($_POST['comment']);

        if (!empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $post_id, $user_id, $comment);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Yorum eklenirken bir hata oluştu.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Yorum boş olamaz.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Yalnızca POST isteklerine izin verilir.']);
}

$conn->close();
?>
