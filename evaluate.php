<?php
session_start();
include_once 'db/connect.php';
include_once 'db/yetki.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo "Bu sayfaya erişim izniniz yok!";
    exit();
}

$get_post_id = $_GET['post_id'];
$user_id = $_SESSION['user_id'];

if ($yetki != '0') {
    if (!checkReviewer($user_id, $get_post_id, $conn)) {
        echo "Bu sayfaya erişim izniniz yok!";
        exit();
    }
}

function checkReviewer($user_id, $get_post_id, $conn) {
    $sql = "SELECT * FROM post_reviewers WHERE reviewer_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $get_post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result->num_rows > 0);
}

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    $postSql = "SELECT * FROM posts WHERE id = ?";
    $postStmt = $conn->prepare($postSql);
    $postStmt->bind_param("i", $post_id);
    $postStmt->execute();
    $post = $postStmt->get_result()->fetch_assoc();
    $postStmt->close();

    $questionSql = "SELECT * FROM evaluation_questions WHERE post_id = ?";
    $questionStmt = $conn->prepare($questionSql);
    $questionStmt->bind_param("i", $post_id);
    $questionStmt->execute();
    $questions = $questionStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $questionStmt->close();

    $reviewerSql = "SELECT id, username AS name FROM users WHERE yetki = 1";
    $reviewerStmt = $conn->prepare($reviewerSql);
    $reviewerStmt->execute();
    $reviewers = $reviewerStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $reviewerStmt->close();

    $postReviewerSql = "SELECT pr.reviewer_id, u.username FROM post_reviewers pr JOIN users u ON pr.reviewer_id = u.id WHERE pr.post_id = ?";
    $postReviewerStmt = $conn->prepare($postReviewerSql);
    $postReviewerStmt->bind_param("i", $post_id);
    $postReviewerStmt->execute();
    $assigned_reviewers = $postReviewerStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $postReviewerStmt->close();

    $questionCreatorSql = "SELECT kullanici_id FROM posts WHERE id = ?";
    $questionCreatorStmt = $conn->prepare($questionCreatorSql);
    $questionCreatorStmt->bind_param("i", $post_id);
    $questionCreatorStmt->execute();
    $questionCreatorStmt->bind_result($questionCreatorId);
    $questionCreatorStmt->fetch();
    $questionCreatorStmt->close();

    $existingResponsesSql = "SELECT question_id, response FROM evaluations WHERE post_id = ? AND reviewer_id = ?";
    $existingResponsesStmt = $conn->prepare($existingResponsesSql);
    $existingResponsesStmt->bind_param("ii", $post_id, $user_id);
    $existingResponsesStmt->execute();
    $existingResponsesResult = $existingResponsesStmt->get_result();

    $existingResponses = [];
    while ($row = $existingResponsesResult->fetch_assoc()) {
        $existingResponses[$row['question_id']] = $row['response'];
    }
    $existingResponsesStmt->close();

} else {
    echo "Geçersiz makale ID!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['responses'])) {
        $responses = $_POST['responses'];
        $conn->begin_transaction(); 
    
        try {
            foreach ($responses as $question_id => $response) {
                $response = trim($response);
    
                if (empty($response)) {
                    continue; 
                }
    
                $checkSql = "SELECT * FROM evaluations WHERE post_id = ? AND question_id = ? AND reviewer_id = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("iii", $post_id, $question_id, $user_id);
                $checkStmt->execute();
                $existingEvaluation = $checkStmt->get_result()->fetch_assoc();
                $checkStmt->close();
    
                if ($existingEvaluation) {
                    $updateSql = "UPDATE evaluations SET response = ? WHERE post_id = ? AND question_id = ? AND reviewer_id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("siii", $response, $post_id, $question_id, $user_id);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $message = $_SESSION['username'] . "Değerlendirmesini güncelledi.";
                } else {
                    $insertSql = "INSERT INTO evaluations (post_id, question_id, reviewer_id, response) VALUES (?, ?, ?, ?)";
                    $insertStmt = $conn->prepare($insertSql);
                    $insertStmt->bind_param("iiis", $post_id, $question_id, $user_id, $response);
                    $insertStmt->execute();
                    $insertStmt->close();

                    $message = $_SESSION['username'] . "Değerlendirmesini tamamladı.";
                }
    
                $editor_id = $questionCreatorId;
                sendNotificationToEditor($conn, $editor_id, $post_id, $message);
            }
    
            $conn->commit();
            echo '<script>alert("Değerlendirme Kaydedildi!");</script>';
            header("Location: ./");
            exit();
                        
        } catch (Exception $e) {
            $conn->rollback();
        }
    
        exit();
    }
    
    if (isset($_POST['new_question']) && !empty($_POST['new_question'])) {
        $new_question = $_POST['new_question'];
        $insertQuestionSql = "INSERT INTO evaluation_questions (post_id, question) VALUES (?, ?)";
        $insertQuestionStmt = $conn->prepare($insertQuestionSql);
        $insertQuestionStmt->bind_param("is", $post_id, $new_question);
        $insertQuestionStmt->execute();
        $insertQuestionStmt->close();

        echo "Yeni soru eklendi!";
        header("Location: ".$_SERVER['PHP_SELF']."?post_id=".$post_id);
        exit();
    }

    if (isset($_POST['assign_reviewers'])) {
        $selected_reviewers = $_POST['reviewers'];
        foreach ($selected_reviewers as $reviewer_id) {
            $assignSql = "INSERT INTO post_reviewers (post_id, reviewer_id) VALUES (?, ?)";
            $assignStmt = $conn->prepare($assignSql);
            $assignStmt->bind_param("ii", $post_id, $reviewer_id);
            $assignStmt->execute();
            $assignStmt->close();

            $message = "Yeni bir makaleye hakem olarak atandınız.";
            sendNotificationToEditor($conn, $reviewer_id, $post_id, $message);

        }
        echo "Hakemler başarıyla atandı!";
        header("Location: ".$_SERVER['PHP_SELF']."?post_id=".$post_id);
        exit();
    }

    if (isset($_POST['remove_reviewer'])) {
        $reviewer_id = $_POST['remove_reviewer_id'];
        
        $deleteSql = "DELETE FROM post_reviewers WHERE post_id = ? AND reviewer_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $post_id, $reviewer_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    
        header("Location: ".$_SERVER['PHP_SELF']."?post_id=".$post_id."&filter=".$filter);
        exit();
    }
    
    if (isset($_POST['delete_question_id'])) {
        $question_id = $_POST['delete_question_id'];
        $deleteQuestionSql = "DELETE FROM evaluation_questions WHERE id = ?";
        $deleteQuestionStmt = $conn->prepare($deleteQuestionSql);
        $deleteQuestionStmt->bind_param("i", $question_id);
        $deleteQuestionStmt->execute();
        $deleteQuestionStmt->close();
    
        header("Location: ".$_SERVER['PHP_SELF']."?post_id=".$post_id);
        exit();
    }
}

function sendNotificationToEditor($conn, $editor_id, $post_id, $message) {
    $sql = "INSERT INTO notifications (editor_id, post_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $editor_id, $post_id, $message);
    $stmt->execute();
    $stmt->close();
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filtered_reviewers = [];

if ($filter == 'reviewed') {
    $reviewedSql = "SELECT DISTINCT r.id, r.username 
                FROM users r
                JOIN evaluations e ON r.id = e.reviewer_id
                WHERE e.post_id = ? AND r.yetki = 1";
    $stmt = $conn->prepare($reviewedSql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $filtered_reviewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} elseif ($filter == 'not_reviewed') {
    $notReviewedSql = "SELECT DISTINCT r.id, r.username 
                FROM users r
                JOIN post_reviewers pr ON r.id = pr.reviewer_id
                WHERE pr.post_id = ? 
                AND r.yetki = 1
                AND r.id NOT IN (
                    SELECT e.reviewer_id 
                    FROM evaluations e 
                    WHERE e.post_id = ?
                )";
    $stmt = $conn->prepare($notReviewedSql);
    $stmt->bind_param("ii", $post_id, $post_id);
    $stmt->execute();
    $filtered_reviewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $allReviewersSql = "SELECT DISTINCT r.id, r.username 
                    FROM users r
                    JOIN post_reviewers pr ON r.id = pr.reviewer_id
                    WHERE pr.post_id = ? AND r.yetki = 1";
    $stmt = $conn->prepare($allReviewersSql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $filtered_reviewers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Makale Değerlendirme</title>
</head>
<body>

<?php include 'inc/header.php'; ?>

<main class="flex flex-col gap-4 pt-6 max-w-2xl mx-auto ">
    
    <a href="makale?id=<?php echo $post['id']; ?>" class="p-4 w-full mt-6 border rounded-xl">
        <?php if (isset($post)): ?>
            <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($post['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
        <?php else: ?>
            <p>Makale bilgileri mevcut değil.</p>
        <?php endif; ?>
    </a>

    <?php if ($yetki == '0'): ?>
        <div class="p-4 border rounded-xl">
            <div class="flex w-full justify-between items-center">
                <h1 class="font-bold text-xl">Hakemler</h1>
                <button data-modal-trigger aria-controls="modal" class="bg-black text-white px-4 py-1.5 rounded-lg">Yeni Hakem Ekle</button>
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
                            <h1 class="modal-title">Hakem Seç</h1>
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
                            <input 
                                type="text" 
                                id="search-reviewer" 
                                onkeyup="searchReviewers()" 
                                placeholder="Hakem arayın..." 
                                class="border my-2 rounded px-2 py-1 w-full"
                            >

                            <form method="post" action="">
                                <ul class="my-4 list-style-none">
                                    <?php if (isset($reviewers) && !empty($reviewers)): ?>
                                        <?php foreach ($reviewers as $reviewer): ?>
                                            <?php if (!in_array($reviewer['id'], array_column($assigned_reviewers, 'reviewer_id'))): ?>
                                                <li class="reviewer-item p-2 list-style-none border-b">
                                                    <input type="checkbox" name="reviewers[]" value="<?php echo htmlspecialchars($reviewer['id']); ?>" id="reviewer-<?php echo htmlspecialchars($reviewer['id']); ?>">
                                                    <label for="reviewer-<?php echo htmlspecialchars($reviewer['id']); ?>"><?php echo htmlspecialchars($reviewer['name']); ?></label>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li>Hakemler mevcut değil.</li>
                                    <?php endif; ?>
                                </ul>

                                <button type="submit" name="assign_reviewers" class="bg-black ml-auto text-white px-4 py-1.5 rounded-lg">Hakemleri Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>   
            
            
            <input 
                type="text" 
                id="as-reviewer" 
                onkeyup="asReviewers()" 
                placeholder="Hakem arayın..." 
                class="border my-2 rounded px-2 py-1 w-full"
            >

            <div class="flex gap-2 my-2 *:text-sm">
                <a href="?post_id=<?php echo $post_id; ?>&filter=all" class="<?php echo $filter == 'all' ? 'bg-blue-500' : 'bg-zinc-500'; ?> text-white px-4 py-1.5 rounded-lg">Tümü</a>
                <a href="?post_id=<?php echo $post_id; ?>&filter=reviewed" class="<?php echo $filter == 'reviewed' ? 'bg-blue-500' : 'bg-zinc-500'; ?> text-white px-4 py-1.5 rounded-lg">Değerlendirenler</a>
                <a href="?post_id=<?php echo $post_id; ?>&filter=not_reviewed" class="<?php echo $filter == 'not_reviewed' ? 'bg-blue-500' : 'bg-zinc-500'; ?> text-white px-4 py-1.5 rounded-lg">Değerlendirmeyenler</a>
            </div>

                <ul id="reviewer-list" class="max-h-52 overflow-y-auto" >
                    <?php if (!empty($filtered_reviewers)): ?>
                        <?php foreach ($filtered_reviewers as $reviewer): ?>
                            <li class="as-item p-2 border-b flex justify-between items-center">
                                <button data-modal-trigger aria-controls="cevaplar" data-reviewer-id="<?php echo htmlspecialchars($reviewer['id']); ?>" data-post-id="<?php echo htmlspecialchars($post_id); ?>" class="cevaplar-button">
                                    <?php echo htmlspecialchars($reviewer['username']); ?>
                                </button>
                                <form method="post" action="">
                                    <input type="hidden" name="remove_reviewer_id" value="<?php echo htmlspecialchars($reviewer['id']); ?>">
                                    <button type="submit" name="remove_reviewer" class="text-red-600 px-2 py-1 rounded-lg">Sil</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Atanan hakemler mevcut değil.</li>
                    <?php endif; ?>
                </ul>
        </div>

    <?php endif; ?>

    <section id="cevaplar" data-modal-target class="hidden">
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
                class="modal-wrapper m-8 w-full max-w-screen-md"
            >
                <div class="modal-header">
                    <h1 class="modal-title">Cevaplar</h1>
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
                    <div id="modal-icerik">
                        <!-- Cevaplar burada yüklenecek -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="p-4 border rounded-xl">
        
        <?php if ($yetki == '0'): ?>
            <div class="mt-6 p-4 border rounded-xl">
                <h2 class="font-bold text-lg">Yeni Soru Ekle</h2>
                <form method="post" action="">
                    <textarea 
                        name="new_question" 
                        rows="4" 
                        class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" 
                        placeholder="Yeni soruyu buraya yazın..."
                    ></textarea>
                    <button type="submit" class="bg-green-500 text-white px-4 py-1.5 ml-auto rounded-lg mt-2">Soruyu Ekle</button>
                </form>
            </div>
        <?php endif; ?>

            <h1 class="font-bold text-xl mb-2 mt-6 ml-1">Sorular</h1>

            <?php if (isset($questions) && !empty($questions)): ?>
                <form method="post" action="">
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="mb-4 p-2">
                            <label class="block text-sm font-medium flex w-full items-center justify-between">
                                <?php echo ($index + 1) . ". " . htmlspecialchars($question['question']); ?>
                                <?php if ($user_id === $questionCreatorId): ?>
                                    <form method="post" action="" onsubmit="return confirm('Bu soruyu silmek istediğinize emin misiniz?');">
                                        <input type="hidden" name="delete_question_id" value="<?php echo htmlspecialchars($question['id']); ?>">
                                        <button type="submit" class="text-red-600 px-2 py-1 rounded-lg">Sil</button>
                                    </form>
                                <?php endif; ?>
                            </label>
                            <?php if ($user_id != $questionCreatorId): ?>
                                <textarea name="responses[<?php echo htmlspecialchars($question['id']); ?>]" rows="4" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm"><?php
                                    if (isset($existingResponses[$question['id']])) {
                                        echo htmlspecialchars($existingResponses[$question['id']]);
                                    }
                                ?></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($user_id != $questionCreatorId): ?>
                        <button type="submit" class="bg-blue-500 ml-auto text-white px-4 py-2 rounded-lg">Değerlendirmeyi Kaydet</button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <p>Sorular mevcut değil.</p>
            <?php endif; ?>

           

    </div>

</main>

<script>
    document.querySelectorAll('[data-modal-trigger]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(button.getAttribute('aria-controls')).classList.remove('hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById(button.closest('[data-modal-target]').id).classList.add('hidden');
        });
    });

    function searchReviewers() {
        const searchInput = document.getElementById('search-reviewer').value.toLowerCase();
        const reviewerItems = document.querySelectorAll('.reviewer-item');
        reviewerItems.forEach(item => {
            const name = item.textContent.toLowerCase();
            item.style.display = name.includes(searchInput) ? '' : 'none';
        });
    }

    function asReviewers() {
        const searchInput = document.getElementById('as-reviewer').value.toLowerCase();
        const reviewerItems = document.querySelectorAll('.as-item');
        reviewerItems.forEach(item => {
            const name = item.textContent.toLowerCase();
            item.style.display = name.includes(searchInput) ? '' : 'none';
        });
    }

    document.querySelectorAll('.cevaplar-button').forEach(button => {
        button.addEventListener('click', function () {
            const reviewerId = this.getAttribute('data-reviewer-id');
            const postId = this.getAttribute('data-post-id');
            const modalIcerik = document.getElementById('modal-icerik');

            fetch('form/get_evaluations.php?reviewer_id=' + reviewerId + '&post_id=' + postId)
                .then(response => response.text())
                .then(data => {
                    modalIcerik.innerHTML = data;
                    document.getElementById(this.getAttribute('aria-controls')).classList.remove('hidden');
                })
                .catch(error => console.error('Hata:', error));
        });
    });
</script>

</body>
</html>
