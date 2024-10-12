<?php
session_start();
include_once 'db/connect.php';

if (!isset($_GET['id'])) {
    echo "Kullanıcı ID'si belirtilmemiş.";
    exit;
}

$user_id = $_GET['id'];

$stmt = $conn->prepare("SELECT username, photo, gender, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $user_photo, $user_gender, $user_email);
$stmt->fetch();
$stmt->close();

$sql = "SELECT posts.*, users.username, users.photo, users.gender FROM posts JOIN users ON posts.kullanici_id = users.id WHERE users.id = ? ORDER BY posts.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();

$user_photo_path = !empty($user_photo) ? './assets/photos/' . $user_photo : 'https://i.pinimg.com/564x/1b/a2/e6/1ba2e6d1d4874546c70c91f1024e17fb.jpg';

?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include 'includes/head.php'; ?>
  <link rel="stylesheet" href="./styles/main.css">
  <script type="module" src="../scripts/modal.js"></script>
  <title><?php echo htmlspecialchars($user_name); ?>'in Makaleleri</title>
</head>
<body>
<?php include 'inc/header.php'; ?>

<main class="max-w-2xl mx-auto mt-6 px-4">
    <div class="my-4 items-center flex gap-2 w-full">
        <button data-modal-trigger aria-controls="modal">
            <img 
                id="profile-photo"
                class="w-14 h-14 rounded-xl border object-cover"
                src="<?php echo htmlspecialchars($user_photo_path); ?>"
                alt="Profil Fotoğrafı"
            />
        </button>
        <div>
            <h1 class="font-bold text-lg"><?php echo htmlspecialchars($user_name); ?></h1>
            <p><?php echo htmlspecialchars($user_email); ?></p>
        </div>
        <div x-data="{ isOpen: false, openedWithKeyboard: false }" class="relative ml-auto" @keydown.esc.window="isOpen = false, openedWithKeyboard = false">
            <button type="button" @click="isOpen = ! isOpen" class="inline-flex cursor-pointer ml-auto items-center gap-2" aria-haspopup="true" @keydown.space.prevent="openedWithKeyboard = true" @keydown.enter.prevent="openedWithKeyboard = true" @keydown.down.prevent="openedWithKeyboard = true" :class="isOpen || openedWithKeyboard ? 'text-neutral-900 dark:text-white' : 'text-neutral-600 dark:text-neutral-300'" :aria-expanded="isOpen || openedWithKeyboard">
                <i class="ri-more-fill text-2xl"></i>
            </button>

            <div x-cloak x-show="isOpen || openedWithKeyboard" x-transition x-trap="openedWithKeyboard" @click.outside="isOpen = false, openedWithKeyboard = false" @keydown.down.prevent="$focus.wrap().next()" @keydown.up.prevent="$focus.wrap().previous()" class="absolute top-1 right-0 max-lg:right-0 flex w-full min-w-[12rem] flex-col overflow-hidden rounded-md border border-neutral-300 bg-neutral-50 py-1.5 dark:border-neutral-700 dark:bg-neutral-900" role="menu">
                        <button onclick="copyLink('')" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm hover:bg-zinc-100" role="menuitem">
                            Profil Linkini Kopyala
                        </button>

                        <button data-modal-trigger aria-controls="modal" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm hover:bg-zinc-100" role="menuitem">
                            Profil Foroğrafı Değiştir
                        </button>

                        <button data-modal-trigger aria-controls="sifreDegis" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm hover:bg-zinc-100" role="menuitem">
                            Şifre Değiştir
                        </button>

                        <button class="bg-neutral-50 text-red-600 text-left w-full px-4 py-2 text-sm hover:bg-red-100" role="menuitem">
                            Profili Sil
                        </button>
            </div>
        </div>
    </div>

    <section id="modal" data-modal-target class="hidden">
        <div class="modal-container">
            <div
            data-modal-close
            data-modal-overlay
            tabindex="-1"
            data-class-in="opacity-50"
            data-class-out="opacity-0"
            class="modal-overlay"
            ></div>
            <div
            data-modal-wrapper
            data-class-in="opacity-100 translate-y-0"
            data-class-out="opacity-0 translate-y-5"
            class="modal-wrapper"
            >
            <div class="modal-header">
                <h1 class="modal-title">Fotoğrafını Değiştir</h1>
                <button
                data-modal-close
                aria-label="Close"
                type="button"
                class="modal-close"
                >
                    <i class="ri-close-fill text-2xl"></i>
                </button>
            </div>
                <div class="modal-body">
                    <form id="photo-upload-form" method="POST" enctype="multipart/form-data">
                        <div class="w-full py-4 bg-gray-50 rounded-2xl border border-gray-300 gap-3 grid border-dashed">
                            <div class="grid gap-1">
                                <h2 class="text-center text-gray-400 text-xs leading-4">PNG veya JPG</h2>
                            </div>
                            <div class="grid gap-2">
                                <h4 class="text-center text-gray-900 text-sm font-medium leading-snug">Dosya seç veya sürükle</h4>
                                <div class="flex items-center justify-center">
                                    <label>
                                        <input type="file" hidden name="photo" id="photo" accept=".png, .jpg, .jpeg" required />
                                        <div class="flex w-28 h-9 px-2 flex-col bg-black rounded-full shadow text-white text-xs font-semibold leading-4 items-center justify-center cursor-pointer focus:outline-none">
                                            Dosya Seç
                                        </div>
                                    </label>
                                </div>
                                <span id="file-name" class="text-center text-gray-500 text-sm"></span>
                            </div>
                        </div>

                        <button type="submit"  class="bg-black p-2 w-full mt-4 justify-center flex text-white rounded-lg flex items-center text-center">Güncelle</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
      
    <section id="sifreDegis" data-modal-target class="hidden">
        <div class="modal-container">
            <div
            data-modal-close
            data-modal-overlay
            tabindex="-1"
            data-class-in="opacity-50"
            data-class-out="opacity-0"
            class="modal-overlay"
            ></div>
            <div
            data-modal-wrapper
            data-class-in="opacity-100 translate-y-0"
            data-class-out="opacity-0 translate-y-5"
            class="modal-wrapper h-96"
            >
            <div class="modal-header">
                <h1 class="modal-title">Şifre Değiştir</h1>
                <button
                data-modal-close
                aria-label="Close"
                type="button"
                class="modal-close"
                >
                    <i class="ri-close-fill text-2xl"></i>
                </button>
            </div>
                <div class="modal-body">
                    <iframe src="./change_password.php" width="100%" height="100%" ></iframe>
                </div>
            </div>
        </div>
    </section>

    <div x-data="{ selectedTab: '<?php echo $posts_result->num_rows > 0 ? 'groups' : 'likes'; ?>' }" class="w-full">
	<div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex overflow-x-auto border-b border-neutral-300 dark:border-neutral-700" role="tablist" aria-label="tab options">
        <?php if ($posts_result->num_rows > 0): ?>
            <button @click="selectedTab = 'groups'" :aria-selected="selectedTab === 'groups'" :tabindex="selectedTab === 'groups' ? '0' : '-1'" :class="selectedTab === 'groups' ? 'font-bold text-black border-b-2 border-black dark:border-white dark:text-white' : 'text-neutral-600 font-medium dark:text-neutral-300 dark:hover:border-b-neutral-300 dark:hover:text-white hover:border-b-2 hover:border-b-neutral-800 hover:text-neutral-900'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelGroups" >
                <i class="ri-layout-grid-fill"></i>
                Makaleler
            </button>
        <?php endif; ?>
		<button @click="selectedTab = 'likes'" :aria-selected="selectedTab === 'likes'" :tabindex="selectedTab === 'likes' ? '0' : '-1'" :class="selectedTab === 'likes' ? 'font-bold text-black border-b-2 border-black dark:border-white dark:text-white' : 'text-neutral-600 font-medium dark:text-neutral-300 dark:hover:border-b-neutral-300 dark:hover:text-white hover:border-b-2 hover:border-b-neutral-800 hover:text-neutral-900'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelLikes" >
             <i class="ri-heart-fill"></i>
			Beğeniler
		</button>
		<button @click="selectedTab = 'comments'" :aria-selected="selectedTab === 'comments'" :tabindex="selectedTab === 'comments' ? '0' : '-1'" :class="selectedTab === 'comments' ? 'font-bold text-black border-b-2 border-black dark:border-white dark:text-white' : 'text-neutral-600 font-medium dark:text-neutral-300 dark:hover:border-b-neutral-300 dark:hover:text-white hover:border-b-2 hover:border-b-neutral-800 hover:text-neutral-900'" class="flex h-min items-center gap-2 px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelComments" >
            <i class="ri-discuss-fill"></i>
			Yorumlar
		</button>
	</div>
	<div class="px-2 py-4 text-neutral-600 dark:text-neutral-300">
		<div x-show="selectedTab === 'groups'" id="tabpanelGroups" role="tabpanel" aria-label="groups">
        <?php
        if ($posts_result->num_rows > 0) {
            while ($first_post = $posts_result->fetch_assoc()) {

                $isLiked = false;
                $like_count = 0;

                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];

                    $like_sorgu = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
                    $like_stmt = $conn->prepare($like_sorgu);
                    $like_stmt->bind_param("ii", $user_id, $first_post['id']);
                    $like_stmt->execute();
                    $like_result = $like_stmt->get_result();

                    if ($like_result->num_rows > 0) {
                        $isLiked = true;
                    }

                    $like_stmt->close();
                }

                $like_count_sorgu = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
                $like_count_stmt = $conn->prepare($like_count_sorgu);
                $like_count_stmt->bind_param("i", $first_post['id']);
                $like_count_stmt->execute();
                $like_count_result = $like_count_stmt->get_result();
                $like_count = $like_count_result->fetch_assoc()['like_count'];

                $like_count_stmt->close();

                $commentCountSql = "SELECT COUNT(*) as comment_count FROM comments WHERE post_id = ?";
                $commentCountStmt = $conn->prepare($commentCountSql);
                $commentCountStmt->bind_param("i", $first_post['id']);
                $commentCountStmt->execute();
                $commentCountResult = $commentCountStmt->get_result();
                $commentCount = $commentCountResult->fetch_assoc()['comment_count'];
                $commentCountStmt->close();

                ?>
                <div class="p-6 border rounded-xl my-4">
                    <div class="flex items-center gap-2">
                        <a href="./profile.php?id=<?php echo $first_post['kullanici_id']; ?>" class="flex items-center gap-2">
                            <img 
                                class="w-8 h-8 rounded-xl border object-cover"
                                src="<?php echo htmlspecialchars('./assets/photos/' . $first_post['photo']); ?>"
                                alt="Profil Fotoğrafı"
                            />
                            <b class="text-sm"><?php echo htmlspecialchars($first_post['username']); ?></b>
                        </a>
                        <p class="opacity-40 text-xs"><?php echo htmlspecialchars($first_post['createddate']); ?></p>
                        
                        <div x-data="{ isOpen: false, openedWithKeyboard: false }" class="relative ml-auto">
                            <button type="button" @click="isOpen = ! isOpen" type="button" class="inline-flex cursor-pointer ml-auto items-center gap-2 whitespace-nowrap rounded-md tracking-wide transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-800 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-300" aria-haspopup="true">
                                <i class="ri-more-fill text-2xl"></i>
                            </button>
    
                            <div x-cloak x-show="isOpen || openedWithKeyboard" x-transition x-trap="openedWithKeyboard" @click.outside="isOpen = false, openedWithKeyboard = false" @keydown.down.prevent="$focus.wrap().next()" @keydown.up.prevent="$focus.wrap().previous()" class="absolute top-11 right-0 flex w-full min-w-[12rem] flex-col overflow-hidden rounded-md border border-neutral-300 bg-neutral-50 py-1.5 dark:border-neutral-700 dark:bg-neutral-900" role="menu">
                                <button onclick="copylink()" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5" role="menuitem">
                                    Makale Linki Kopyala
                                </button>
                            
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == htmlspecialchars($first_post['kullanici_id'])): ?>
                                    <form action="form/delete_post.php" method="POST" onsubmit="return confirm('Bu makaleyi silmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($first_post['id']); ?>">
                                        <button type="submit" class="bg-neutral-50 text-left w-full px-4 py-2 text-sm text-neutral-600 hover:bg-neutral-900/5" role="menuitem">
                                            Sil
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <p class="my-2 font-medium"><?php echo htmlspecialchars($first_post['title']); ?></p>
                    <p class="my-2 text-sm"><?php echo htmlspecialchars($first_post['description']); ?></p>
                    
                    <div class="my-2 flex rounded-lg border items-center px-4 p-2">
                        <p><?php echo htmlspecialchars($first_post['file']); ?></p>
                        <a target="_blank" href="./uploads/<?php echo urlencode($first_post['file']); ?>" class="ml-auto p-2 rounded-lg transition hover:bg-zinc-200">
                            <i class="ri-download-2-line text-xl"></i>
                        </a>
                    </div>
                    
                    <div class="text-2xl flex *:p-2">
                        <button class="like-button items-center flex gap-1" data-post-id="<?php echo $first_post['id']; ?>">
                            <i class="<?php echo $isLiked ? 'ri-heart-fill' : 'ri-heart-line'; ?>"></i>
                            <span class="text-sm"><?php echo $like_count; ?></span>
                        </button>
    
                        <a href="makale?id=<?php echo $first_post['id']; ?>" class="items-center flex gap-1">
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
                                $post_id = $first_post['id'];

                                if ($yetki == '0' && $first_post['kullanici_id'] == $user_id) {
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
            echo "Makale Bulunamadı";
        }
        ?>
        </div>
		<div x-show="selectedTab === 'likes'" id="tabpanelLikes" role="tabpanel" aria-label="likes">
            <!-- Beğenilen gönderiler -->
            <?php
            $likes_sql = "SELECT posts.*, users.username FROM likes JOIN posts ON likes.post_id = posts.id JOIN users ON posts.kullanici_id = users.id WHERE likes.user_id = ?";
            $likes_stmt = $conn->prepare($likes_sql);
            $likes_stmt->bind_param("i", $user_id);
            $likes_stmt->execute();
            $likes_result = $likes_stmt->get_result();
            
            if ($likes_result->num_rows > 0) {
                while ($like_post = $likes_result->fetch_assoc()) {
                    ?>
                    <div class="p-6 border rounded-xl my-4">
                        <div class="flex items-center gap-2">
                            <a href="./profile.php?id=<?php echo $like_post['kullanici_id']; ?>" class="flex items-center gap-2">
                                <div class="w-8 h-8 flex items-center justify-center bg-blue-200 rounded-full">
                                    <?php echo strtoupper($like_post['username'][0]); ?>
                                </div>
                                <b class="text-sm"><?php echo htmlspecialchars($like_post['username']); ?></b>
                            </a>
                            <p class="opacity-40 text-xs"><?php echo htmlspecialchars($like_post['createddate']); ?></p>
                        </div>
                        <p class="my-2 font-medium"><?php echo htmlspecialchars($like_post['title']); ?></p>
                        <p class="my-2 text-sm"><?php echo htmlspecialchars($like_post['description']); ?></p>
                        
                        <div class="my-2 flex rounded-lg border items-center px-4 p-2">
                            <p><?php echo htmlspecialchars($like_post['file']); ?></p>
                            <a target="_blank" href="./uploads/<?php echo urlencode($like_post['file']); ?>" class="ml-auto p-2 rounded-lg transition hover:bg-zinc-200">
                                <i class="ri-download-2-line text-xl"></i>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Bu kullanıcı henüz bir makaleyi beğenmemiş.</p>";
            }
            $likes_stmt->close();
            ?>
        </div>
		<div x-show="selectedTab === 'comments'" id="tabpanelComments" role="tabpanel" aria-label="comments">
            <?php
            $comments_sql = "SELECT comments.*, posts.title, users.username FROM comments JOIN posts ON comments.post_id = posts.id JOIN users ON comments.user_id = users.id WHERE comments.user_id = ?";
            $comments_stmt = $conn->prepare($comments_sql);
            $comments_stmt->bind_param("i", $user_id);
            $comments_stmt->execute();
            $comments_result = $comments_stmt->get_result();
            
            if ($comments_result->num_rows > 0) {
                while ($comment = $comments_result->fetch_assoc()) {
                    ?>
                    <div class="p-6 border rounded-xl my-4">
                        <div class="flex items-center gap-2">
                            <a href="./profile.php?id=<?php echo $comment['user_id']; ?>" class="flex items-center gap-2">
                                <div class="w-8 h-8 flex items-center justify-center bg-blue-200 rounded-full">
                                    <?php echo strtoupper($comment['username'][0]); ?>
                                </div>
                                <b class="text-sm"><?php echo htmlspecialchars($comment['username']); ?></b>
                            </a>
                            <p class="opacity-40 text-xs"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                        </div>
                        <p class="my-2 font-medium"><?php echo htmlspecialchars($comment['title']); ?></p>
                        <p class="my-2 text-sm"><?php echo htmlspecialchars($comment['comment']); ?></p>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Bu kullanıcı henüz bir yorumu paylaşmamış.</p>";
            }
            $comments_stmt->close();
            ?>
        </div>
	</div>
</div>

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
  
    document.getElementById('photo').addEventListener('change', function() {
        const fileName = this.files[0].name;
        document.getElementById('file-name').textContent = fileName;
    });

    document.getElementById('photo-upload-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('form/update_photo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('profile-photo').src = './assets/photos/' + data.photo;
                alert(data.message);
                document.getElementById('modal').classList.add('hidden');
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

</script>

</body>
</html>
