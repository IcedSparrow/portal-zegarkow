<?php
session_start();
require_once '../includes/config.php'; // Dostosuj ścieżkę do swojej konfiguracji

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;

    // Logika polubienia posta
    if ($user_id && isset($_POST['like_post_id'])) {
        $post_id_to_like = (int)$_POST['like_post_id'];
        $likeCheckStmt = $conn->prepare("SELECT like_id FROM likes WHERE post_id = ? AND user_id = ?");
        $likeCheckStmt->bind_param("ii", $post_id_to_like, $user_id);
        $likeCheckStmt->execute();
        if ($likeCheckStmt->get_result()->num_rows === 0) {
            $likeStmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
            $likeStmt->bind_param("ii", $post_id_to_like, $user_id);
            $likeStmt->execute();
        }
    }

    // Logika dodawania komentarza
    if ($user_id && isset($_POST['content'], $_POST['post_id_comment'])) {
        $content = trim($_POST['content']);
        $post_id_comment = (int)$_POST['post_id_comment'];
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id_comment, $user_id, $content);
        $stmt->execute();
    }

    header("Location: index.php");
    exit();
}

$posts = [];
$postQuery = "SELECT p.post_id, p.title, p.content, p.image_path, (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS likes_count FROM posts p ORDER BY p.created_at DESC";
if ($postResult = $conn->query($postQuery)) {
    while ($row = $postResult->fetch_assoc()) {
        $stmt = $conn->prepare("SELECT content, created_at, user_id FROM comments WHERE post_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $row['post_id']);
        $stmt->execute();
        $commentsResult = $stmt->get_result();
        $row['comments'] = $commentsResult->fetch_all(MYSQLI_ASSOC);
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Portal dla fanów zegarków</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../js/script.js">
    <script scr="../js/script.js"></script>
</head>
<body>
<header>
    <h1>Portal dla fanów zegarków</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/ask.php">Zadaj pytanie</a></li>
            <li><a href="../posts/view.php">Przeglądaj posty</a></li>
            <li><a href="../posts/question.php">Pytania </a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../user/create_post.php">Utwórz post</a></li>
                <li><a href="../user/logout.php">Wyloguj</a></li>
                <li><a href="../user/profile.php">Profil</a></li>

            <?php else: ?>
                <li><a href="../user/login.php">Logowanie</a></li>
                <li><a href="../user/register.php">Rejestracja</a></li>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>

<main>
    <?php foreach ($posts as $post): ?>
        <article>
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <?php if (!empty($post['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Obrazek posta" style="max-width: 100%; height: auto;">
            <?php endif; ?>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p>Liczba polubień: <?php echo $post['likes_count']; ?></p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="" method="post">
                    <input type="hidden" name="like_post_id" value="<?php echo $post['post_id']; ?>">
                    <button type="submit">Polub</button>
                </form>
            <?php endif; ?>
            <div class="comments">
                <?php foreach ($post['comments'] as $comment): ?>
                    <div class="comment">
                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
<small>Data: <?php echo $comment['created_at']; ?></small>
</div>
<?php endforeach; ?>
<?php if (isset($_SESSION['user_id'])): ?>
<!-- Formularz dodawania komentarza -->
<form method="post" action="">
<input type="hidden" name="post_id_comment" value="<?php echo $post['post_id']; ?>">
<textarea name="content" placeholder="Dodaj komentarz" required></textarea><br>
<button type="submit">Dodaj komentarz</button>
</form>
<?php endif; ?>
</div>
</article>
<?php endforeach; ?>

</main>
</body>
</html>
