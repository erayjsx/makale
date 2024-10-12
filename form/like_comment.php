<?php
session_start();
include '../db/connect.php';

if (isset($_POST['comment_id']) && isset($_SESSION['user_id'])) {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'];

    // Beğeni kontrolü
    $checkLike = $conn->prepare("SELECT * FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $checkLike->bind_param("ii", $comment_id, $user_id);
    $checkLike->execute();
    $result = $checkLike->get_result();

    if ($result->num_rows > 0) {
        // Beğeni sil
        $deleteLike = $conn->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $deleteLike->bind_param("ii", $comment_id, $user_id);
        $deleteLike->execute();
        $status = 'unliked';
    } else {
        // Beğeni ekle
        $insertLike = $conn->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
        $insertLike->bind_param("ii", $comment_id, $user_id);
        $insertLike->execute();
        $status = 'liked';
    }

    // Beğeni sayısını al
    $likeCount = $conn->prepare("SELECT COUNT(*) as like_count FROM comment_likes WHERE comment_id = ?");
    $likeCount->bind_param("i", $comment_id);
    $likeCount->execute();
    $countResult = $likeCount->get_result();
    $like_count = $countResult->fetch_assoc()['like_count'];

    echo json_encode(['status' => $status, 'like_count' => $like_count]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek']);
}
?>
