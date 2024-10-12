<?php 
session_start();
include_once 'db/connect.php';
include_once 'db/yetki.php';

if (isset($_GET['id'])) {
    $makale_id = $_GET['id'];

    $mklSql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.kullanici_id = users.id WHERE posts.id = ?";
    $mkl = $conn->prepare($mklSql);
    $mkl->bind_param("i", $makale_id);
    $mkl->execute();
    $results = $mkl->get_result();

    if ($results->num_rows > 0) {
        $makale = $results->fetch_assoc();
    } else {
        echo "Makale bulunamadı!";
        exit();
    }

    $mkl->close();
} else {
    echo "Geçersiz makale ID!";
    exit();
}

$isLiked = false;
$like_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $like_sorgu = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
    $like_stmt = $conn->prepare($like_sorgu);
    $like_stmt->bind_param("ii", $user_id, $makale['id']);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result();

    if ($like_result->num_rows > 0) {
        $isLiked = true;
    }

    $like_stmt->close();
}

$like_count_sorgu = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
$like_count_stmt = $conn->prepare($like_count_sorgu);
$like_count_stmt->bind_param("i", $makale['id']);
$like_count_stmt->execute();
$like_count_result = $like_count_stmt->get_result();
$like_count = $like_count_result->fetch_assoc()['like_count'];

$like_count_stmt->close();

$commentSql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ?";
$commentStmt = $conn->prepare($commentSql);
$commentStmt->bind_param("i", $makale['id']);
$commentStmt->execute();
$commentResults = $commentStmt->get_result();
$comments = $commentResults->fetch_all(MYSQLI_ASSOC);
$commentStmt->close();

$commentCountSql = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?";
$commentCountStmt = $conn->prepare($commentCountSql);
$commentCountStmt->bind_param("i", $makale['id']);
$commentCountStmt->execute();
$commentCountResult = $commentCountStmt->get_result();
$commentCount = $commentCountResult->fetch_assoc()['comment_count'];
$commentCountStmt->close();


?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title><?php echo htmlspecialchars($makale['title']); ?></title>
</head>
<body>

<?php include 'inc/header.php'; ?>

<main class="flex gap-6 w-full  justify-center">

    <div class="max-w-2xl w-full flex flex-col p-6 border mt-6 rounded-xl">
        <div class="w-full flex flex-col rounded-xl">
            
            <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($makale['title']); ?></h1>
            <div class="flex gap-2 mb-4">
                <b class="text-sm"><?php echo htmlspecialchars($makale['username']); ?></b>
                <p class="opacity-40 text-xs"><?php echo htmlspecialchars($makale['createddate']); ?></p>
            </div>

            <div class="mt-4">
                <?php if (!empty($makale['file'])): ?>
                    <div class="my-2 flex rounded-lg border items-center px-4 p-2">
                        <p><?php echo htmlspecialchars($makale['file']); ?></p>
                        <a target="_blank" href="./uploads/<?php echo htmlspecialchars($makale['file']); ?>" class="ml-auto">
                            <i class="ri-download-2-line text-xl"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-2xl flex w-full *:p-2 mt-4">
                <button class="like-post-button items-center flex gap-1" data-post-id="<?php echo $makale['id']; ?>">
                    <i class="<?php echo $isLiked ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
                    <span class="text-sm"><?php echo $like_count; ?></span>
                </button>

                <button class="">
                    <i class="ri-discuss-line"></i>
                    <span class="text-sm"><?php echo $commentCount; ?></span>
                </button>

                <button onclick="copyLink()" class="ml-2 p-2 rounded flex items-center gap-2">
                    <i class="ri-share-line"></i>
                    <p class="text-sm">Paylaş</p>
                </button>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $makale['kullanici_id']): ?>
                    <form action="./form/delete_post.php" method="POST" class="ml-auto" onsubmit="return confirm('Bu makaleyi silmek istediğinize emin misiniz?');">
                            <input type="hidden" name="post_id" value="<?php echo $makale['id']; ?>">
                            <button type="submit" class="delete-post items-center gap-2 justify-center flex ml-auto text-red-600">
                                <i class="ri-delete-bin-line"></i> 
                                <p class="text-sm">Sil</p>
                            </button>
                    </form>
                <?php endif; ?>
                
            </div>
        
        </div>

        <div class="border-t pt-2 mt-1">
            <div class="flex flex-col gap-3 py-3">
                <h2 class="">Yorum ekle</h2>
                <textarea name="comment" id="comment" cols="30" rows="3" placeholder="Yorumunuz" class="rounded-lg border p-2"></textarea>
                <button id="comment-submit" class="h-10 ml-auto w-24 rounded-xl bg-zinc-200 px-4 transition hover:bg-black hover:text-white">Gönder</button>
            </div>

            <h1 class="mb-4 text-2xl"><b>Yorumlar</b> (<?php echo $commentCount; ?>)</h1>

            <?php foreach ($comments as $comment): ?>
                <?php 
                    $commentIsLiked = false;
                    $commentLikeCount = 0;

                    if (isset($_SESSION['user_id'])) {
                        $user_id = $_SESSION['user_id'];

                        $comment_like_sorgu = "SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ?";
                        $comment_like_stmt = $conn->prepare($comment_like_sorgu);
                        $comment_like_stmt->bind_param("ii", $user_id, $comment['id']);
                        $comment_like_stmt->execute();
                        $comment_like_result = $comment_like_stmt->get_result();

                        if ($comment_like_result->num_rows > 0) {
                            $commentIsLiked = true;
                        }

                        $comment_like_stmt->close();
                    }

                    $comment_like_count_sorgu = "SELECT COUNT(*) as like_count FROM comment_likes WHERE comment_id = ?";
                    $comment_like_count_stmt = $conn->prepare($comment_like_count_sorgu);
                    $comment_like_count_stmt->bind_param("i", $comment['id']);
                    $comment_like_count_stmt->execute();
                    $comment_like_count_result = $comment_like_count_stmt->get_result();
                    $commentLikeCount = $comment_like_count_result->fetch_assoc()['like_count'];

                    $comment_like_count_stmt->close();
                ?>
                <div class="mb-2 flex border-b pb-4">
                    <div class="h-full">
                        <div class="flex items-center gap-2 mb-1">
                            <b class="text-sm"><?php echo htmlspecialchars($comment['username']); ?></b>
                            <p class="opacity-40 text-xs"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                        </div>
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    </div>

                    <div class="ml-auto flex flex-col justify-between items-start gap-4 h-full">
                            <button class="like-comment-button flex items-center mb-auto gap-1 mt-3 ml-2" data-comment-id="<?php echo $comment['id']; ?>">
                                <i class="<?php echo $commentIsLiked ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
                                <span><?php echo $commentLikeCount; ?></span>
                            </button>
                            
                            <?php if (isset($_SESSION['user_id']) && 
                                    ($_SESSION['user_id'] == $comment['user_id'] || 
                                    $_SESSION['user_id'] == $makale['kullanici_id'])): ?>
                                <button class="delete-comment mt-auto text-red-600" data-comment-id="<?php echo $comment['id']; ?>">
                                    <i class="ri-delete-bin-line text-base"></i> Sil
                                </button>
                            <?php endif; ?>
                            
                        </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

</main>

<script>
  
    $(document).on('click', '#comment-submit', function() {
        var comment = $('#comment').val();
        var postId = <?php echo $makale['id']; ?>;

        $.ajax({
                url: 'form/comment.php', 
                type: 'POST',
                data: { post_id: postId, comment: comment },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Bir hata oluştu.');
                }
            });
    });

    $(document).on('click', '.delete-comment', function() {
        var commentId = $(this).data('comment-id');

        if (confirm('Bu yorumu silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: 'form/delete_comment.php',
                type: 'POST',
                data: { comment_id: commentId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        location.reload(); 
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Bir hata oluştu.');
                }
            });
        }
    });

    $(document).on('click', '.like-post-button', function() {
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
            }
            button.find('span').text(data.like_count);
        },
        error: function() {
            alert('Bir hata oluştu.');
        }
    });
});

    $(document).on('click', '.like-comment-button', function() {
    var button = $(this);
    var commentId = button.data('comment-id');

    $.ajax({
        url: 'form/like_comment.php',
        type: 'POST',
        data: { comment_id: commentId },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'liked') {
                button.find('i').removeClass('ri-heart-line').addClass('ri-heart-fill');
            } else if (data.status === 'unliked') {
                button.find('i').removeClass('ri-heart-fill').addClass('ri-heart-line');
            }
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
