<?php 
    session_start();
    include_once 'db/connect.php';
    include_once 'db/yetki.php';

    $sql = "SELECT posts.*, users.username, users.photo FROM posts JOIN users ON posts.kullanici_id = users.id ORDER BY posts.id DESC";
    $posts = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include 'includes/head.php'; ?>
  <script src="./scripts/main.js"></script>
  <title>Makale Listesi</title>
</head>
<body>
<?php include 'inc/header.php'; ?>

<main class="max-w-2xl mx-auto px-4">

<?php
if ($posts->num_rows > 0) {
    while($row = $posts->fetch_assoc()) {

        $isLiked = false;
        $like_count = 0;

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            $like_sorgu = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
            $like_stmt = $conn->prepare($like_sorgu);
            $like_stmt->bind_param("ii", $user_id, $row['id']);
            $like_stmt->execute();
            $like_result = $like_stmt->get_result();

            if ($like_result->num_rows > 0) {
                $isLiked = true;
            }

            $like_stmt->close();
        }

        $like_count_sorgu = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
        $like_count_stmt = $conn->prepare($like_count_sorgu);
        $like_count_stmt->bind_param("i", $row['id']);
        $like_count_stmt->execute();
        $like_count_result = $like_count_stmt->get_result();
        $like_count = $like_count_result->fetch_assoc()['like_count'];

        $like_count_stmt->close();

        $commentCountSql = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?";
        $commentCountStmt = $conn->prepare($commentCountSql);
        $commentCountStmt->bind_param("i", $row['id']);
        $commentCountStmt->execute();
        $commentCountResult = $commentCountStmt->get_result();
        $commentCount = $commentCountResult->fetch_assoc()['comment_count'];
        $commentCountStmt->close();

        $user_photo_path = !empty($row['photo']) ? './assets/photos/' . $row['photo'] : 'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg';

        ?>
        <div class="p-6 border rounded-xl my-4">
            <div class="flex items-center gap-2">
                <a href="./profile.php?id=<?php echo $row['kullanici_id']; ?>" class="flex items-center gap-2">
                    <img 
                        class="w-8 h-8 rounded-xl border object-cover"
                        src="<?php echo htmlspecialchars($user_photo_path); ?>"
                        alt="Profil Fotoğrafı"
                    />
                    <b class="text-sm"><?php echo htmlspecialchars($row['username']); ?></b>
                </a>
                <p class="opacity-40 text-xs"><?php echo htmlspecialchars($row['createddate']); ?></p>
                
                <div x-data="{ isOpen: false, openedWithKeyboard: false }" class="relative ml-auto" @keydown.esc.window="isOpen = false, openedWithKeyboard = false">
                    <button type="button" @click="isOpen = ! isOpen" class="inline-flex cursor-pointer ml-auto items-center gap-2 whitespace-nowrap rounded-md tracking-wide transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-300" aria-haspopup="true" @keydown.space.prevent="openedWithKeyboard = true" @keydown.enter.prevent="openedWithKeyboard = true" @keydown.down.prevent="openedWithKeyboard = true" :class="isOpen || openedWithKeyboard ? 'text-neutral-900 dark:text-white' : 'text-neutral-600 dark:text-neutral-300'" :aria-expanded="isOpen || openedWithKeyboard">
                        <i class="ri-more-fill text-2xl"></i>
                    </button>

                    <div x-cloak x-show="isOpen || openedWithKeyboard" x-transition x-trap="openedWithKeyboard" @click.outside="isOpen = false, openedWithKeyboard = false" @keydown.down.prevent="$focus.wrap().next()" @keydown.up.prevent="$focus.wrap().previous()" class="absolute top-11 right-0 flex w-full min-w-[12rem] flex-col overflow-hidden rounded-md border border-neutral-300 bg-neutral-50 py-1.5 dark:border-neutral-700 dark:bg-neutral-900" role="menu">

                        <button onclick="copyLink('<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/makale?id=' . $row['id']; ?>')"  class="bg-neutral-50 text-left w-full px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5" role="menuitem">
                            Makale Linki Kopyala
                        </button>
                    
                         <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == htmlspecialchars($row['kullanici_id'])): ?>
                            <form action="form/delete_post.php" method="POST" onsubmit="return confirm('Bu makaleyi silmek istediğinize emin misiniz?');">
                                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5" role="menuitem">
                                        Sil
                                    </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <p class="my-2 font-medium"><?php echo htmlspecialchars($row['title']); ?></p>
            <p class="my-2 text-sm"><?php echo htmlspecialchars($row['description']); ?></p>
            
            <div class="my-2 flex rounded-lg border items-center px-4 p-2">
              <p><?php echo htmlspecialchars($row['file']); ?></p>
              
              <a target="_blank" href="./uploads/<?php echo urlencode($row['file']); ?>" class="ml-auto p-2 rounded-lg transition hover:bg-zinc-200">
                  <i class="ri-download-2-line text-xl"></i>
              </a>
              
          </div>
          
            <div class="text-2xl flex *:p-2">
                <button class="like-button items-center flex gap-1" data-post-id="<?php echo $row['id']; ?>">
                    <i class="<?php echo $isLiked ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
                    <span class="text-sm"><?php echo $like_count; ?></span>
                </button>

                <a href="makale?id=<?php echo $row['id']; ?>" class="items-center flex gap-1">
                  <i class="ri-discuss-line"></i>
                  <span class="text-sm"><?php echo $commentCount; ?></span>
                </a>

                <button onclick="sharePost('<?php echo $row['title']; ?>', '<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/makale?id=' . $row['id']; ?>')" class="rounded flex items-center gap-2">
                    <i class="ri-share-line"></i>
                    <p class="text-sm">Paylaş</p>
                </button>

                <?php
                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];
                        $post_id = $row['id'];

                        if ($yetki == '0' && $row['kullanici_id'] == $user_id) {
                            ?>
                            <a href="evaluate.php?post_id=<?php echo $post_id; ?>" class="items-center ml-auto flex gap-1">
                                <i class="ri-edit-line"></i>
                                <span class="text-sm">Değerlendirme Yönetimi</span>
                            </a>
                            <?php
                        } elseif ($yetki == '1') {
                            $checkReviewerSql = "SELECT * FROM post_reviewers WHERE post_id = ? AND reviewer_id = ?";
                            $checkReviewerStmt = $conn->prepare($checkReviewerSql);
                            $checkReviewerStmt->bind_param("ii", $post_id, $user_id);
                            $checkReviewerStmt->execute();
                            $result = $checkReviewerStmt->get_result();

                            if ($result->num_rows > 0) {
                                ?>
                                <a href="evaluate.php?post_id=<?php echo $post_id; ?>" class="items-center ml-auto flex gap-1">
                                    <i class="ri-edit-line"></i>
                                    <span class="text-sm">Değerlendir</span>
                                </a>
                                <?php
                            }

                            $checkReviewerStmt->close();
                        }
                    }
                ?>
            </div>
        </div>
      <?php
    }
} else {
    echo "Veri bulunamadı.";
}

$conn->close();
?>

</main>

<script>
  $(document).on('click', '.like-button', function() {
    var button = $(this);
    var postId = button.data('post-id');

    $.ajax({
        url: 'form/like_post.php',
        type: 'POST',
        data: { post_id: postId },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'liked') {
                button.find('i').removeClass('ri-heart-line').addClass('ri-heart-fill');
            } else if (data.status === 'unliked') {
                button.find('i').removeClass('ri-heart-fill').addClass('ri-heart-line');
            } else {
                alert(data.message);
            }
            // Beğeni sayısını güncelle
            button.find('span').text(data.like_count);
        },
        error: function() {
            alert('Bir hata oluştu.');
        }
    });
});
</script>

</body>
</html>
