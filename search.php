<?php
session_start();
include_once 'db/connect.php';
include_once 'db/yetki.php';

if (isset($_GET['query'])) {
    $search_query = "%" . $_GET['query'] . "%";

    $searchSqlPosts = "SELECT posts.*, users.username FROM posts JOIN users ON posts.kullanici_id = users.id WHERE posts.title LIKE ? OR posts.description LIKE ?";
    $srcPosts = $conn->prepare($searchSqlPosts);
    
    $srcPosts->bind_param("ss", $search_query, $search_query);
    $srcPosts->execute();
    $searchResultsPosts = $srcPosts->get_result();
    $srcPosts->close();

    $searchSqlUsers = "SELECT * FROM users WHERE username LIKE ? OR email LIKE ?";
    $srcUsers = $conn->prepare($searchSqlUsers);

    $srcUsers->bind_param("ss", $search_query, $search_query);
    $srcUsers->execute();
    $searchResultsUsers = $srcUsers->get_result();
    $srcUsers->close();
}
?>

<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="./styles/main.css" >
    <title>Arama Sonuçları</title>
</head>
<body>
<?php include 'inc/header.php'; ?>

<main class="search-container max-w-2xl">

    <form action="" method="GET" class="search-form">
        <input type="text" name="query" placeholder="Ara..." required class="rounded-l-lg">
        <button type="submit" class="rounded-r-lg">Ara</button>
    </form>

    <h1 class="text-2xl font-bold mb-4">Arama Sonuçları</h1>

        <?php if (isset($searchResultsUsers)): ?>
            <h2 class="my-4">Profil Sonuçları</h2>
            <?php if ($searchResultsUsers->num_rows > 0): ?>
                <div class="flex gap-2 flex-col">
                    <?php while ($row = $searchResultsUsers->fetch_assoc()): ?>
                        <a href="./profile.php?id=<?php echo $row['id']; ?>" class="profile border p-2 gap-6 rounded-lg  items-center  flex w-full">
                            <img src="<?php echo htmlspecialchars('./assets/photos/' . $row['photo']); ?>" class="w-8 h-8 rounded-lg"></i>
                            <div>
                                <h3><?php echo htmlspecialchars($row['username']); ?></h3>
                                <p><?php echo htmlspecialchars($row['email']); ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Hiçbir profil sonucu bulunamadı.</p>
            <?php endif; ?>
        <?php endif; ?>
    
    <?php if (isset($searchResultsPosts)): ?>
        <h2 class="my-4">Makale Sonuçları</h2>
        <?php if ($searchResultsPosts->num_rows > 0): ?>
            <?php while ($row = $searchResultsPosts->fetch_assoc()): ?>
                <div class="post">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>Yazar: <?php echo htmlspecialchars($row['username']); ?></p>
                    <a href="./makale.php?id=<?php echo $row['id']; ?>">Makale'ye Git</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Hiçbir makale sonucu bulunamadı.</p>
        <?php endif; ?>
    <?php endif; ?>

</main>

</body>
</html>
