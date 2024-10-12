<?php
session_start();
include_once '../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id']) && isset($_SESSION['user_id'])) {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT comments.user_id AS comment_user_id, posts.kullanici_id AS post_user_id 
            FROM comments 
            JOIN posts ON comments.post_id = posts.id 
            WHERE comments.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comment = $result->fetch_assoc();
    $stmt->close();

    if ($comment && ($comment['comment_user_id'] == $user_id || $comment['post_user_id'] == $user_id)) {
        $deleteSql = "DELETE FROM comments WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $comment_id);
        
        if ($deleteStmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Yorum silinemedi.']);
        }
        
        $deleteStmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Bu yorumu silme yetkiniz yok.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
}

$conn->close();
?>
