<?php
session_start();
include_once '../db/connect.php';

$response = [];

if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'Beğenmek için giriş yapmalısınız.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];

    $sorgu = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sorgu);
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sorgu = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $delete_stmt = $conn->prepare($delete_sorgu);
        $delete_stmt->bind_param("ii", $user_id, $post_id);
        if ($delete_stmt->execute()) {
            $response['status'] = 'unliked';
            $response['message'] = 'Beğeni geri alındı.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Beğeni geri alınırken bir hata oluştu.';
        }
        $delete_stmt->close();
    } else {
        $insert_sorgu = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sorgu);
        $insert_stmt->bind_param("ii", $user_id, $post_id);
        if ($insert_stmt->execute()) {
            $response['status'] = 'liked';
            $response['message'] = 'Beğenildi.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Beğeni eklenirken bir hata oluştu.';
        }
        $insert_stmt->close();
    }

    $like_count_sorgu = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
    $like_count_stmt = $conn->prepare($like_count_sorgu);
    $like_count_stmt->bind_param("i", $post_id);
    $like_count_stmt->execute();
    $like_count_result = $like_count_stmt->get_result();
    $like_count = $like_count_result->fetch_assoc()['like_count'];

    $response['like_count'] = $like_count;

    $like_count_stmt->close();
    $stmt->close();
}

$conn->close();

echo json_encode($response);
