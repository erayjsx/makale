<?php
session_start();
include_once '../db/connect.php';

if (isset($_POST['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $deleteCommentsSql = "DELETE FROM comments WHERE post_id = ?";
    $deleteCommentsStmt = $conn->prepare($deleteCommentsSql);
    $deleteCommentsStmt->bind_param("i", $post_id);
    $deleteCommentsStmt->execute();
    $deleteCommentsStmt->close();

    $notifCommentsSql = "DELETE FROM notifications WHERE post_id = ?";
    $notifCommentsStmt = $conn->prepare($notifCommentsSql);
    $notifCommentsStmt->bind_param("i", $post_id);
    $notifCommentsStmt->execute();
    $notifCommentsStmt->close();

    $mklSql = "SELECT * FROM posts WHERE id = ? AND kullanici_id = ?";
    $stmt = $conn->prepare($mklSql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $deleteSql = "DELETE FROM posts WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $post_id);

        if ($deleteStmt->execute()) {
            header("Location: ../");
            exit();
        } else {
            echo "Makale silinirken bir hata oluştu: " . $conn->error;
        }
        $deleteStmt->close();
    } else {
        echo "Bu makaleyi silmeye yetkiniz yok!";
    }

    $stmt->close();
} else {
    echo "Geçersiz istek!";
}


?>
