<?php
session_start();
include_once '../db/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Bu sayfaya erişim izniniz yok!";
    exit();
}

if (!isset($_GET['reviewer_id']) || !isset($_GET['post_id'])) {
    echo "Geçersiz istek!";
    exit();
}

$reviewer_id = $_GET['reviewer_id'];
$post_id = $_GET['post_id'];

$evaluationsSql = "
    SELECT eq.question, e.response 
    FROM evaluations e
    JOIN evaluation_questions eq ON e.question_id = eq.id
    WHERE e.reviewer_id = ? AND e.post_id = ?";
$stmt = $conn->prepare($evaluationsSql);
$stmt->bind_param("ii", $reviewer_id, $post_id);
$stmt->execute();
$evaluations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!empty($evaluations)) {
    foreach ($evaluations as $evaluation) {
        echo "<div class='p-2 my-2 border-b'>";
        echo "<h2 class='font-bold'>" . htmlspecialchars($evaluation['question']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($evaluation['response'])) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>Bu hakem için cevap bulunamadı.</p>";
}

$conn->close();
?>
