<?php
session_start();
include_once 'db/connect.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM notifications WHERE editor_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (isset($_POST['delete_all_notifications'])) {
    $deleteSql = "DELETE FROM notifications WHERE editor_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $user_id);
    $deleteStmt->execute();
    $deleteStmt->close();

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Bildirimler</title>
</head>
<body>

<?php include 'inc/header.php'; ?>


<main class="max-w-2xl mx-auto p-4 border m-4 rounded-lg">
    <div class="flex items-center justify-between">
        <h1 class="font-bold text-2xl">Bildirimler</h1>
        <?php if (!empty($notifications)): ?>
            <form method="post" action="">
                <button type="submit" name="delete_all_notifications" class="bg-black text-white px-4 py-1.5 rounded-lg">Tüm Bildirimleri Sil</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="flex flex-col gap-4 my-4">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <a href="evaluate?post_id=<?php echo $notification['post_id']; ?>" class="p-4 transition hover:bg-zinc-200 border rounded-lg">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo htmlspecialchars($notification['created_at']); ?></small>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Bildirim bulunamadı.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
