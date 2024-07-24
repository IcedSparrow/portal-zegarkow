<?php
session_start();
require_once '../includes/config.php';

$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
$post = null;
$comments = [];
$likeCount = 0;

// Sprawdzenie, czy użytkownik zalogowany jest i czy kliknął "Lubię to!"
if (isset($_POST['like']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Dodaj rekord do tabeli likes, jeśli jeszcze nie istnieje
    if ($stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) SELECT ?, ? WHERE NOT EXISTS (SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?)")) {
        $stmt->bind_param("iiii", $post_id, $user_id, $post_id, $user_id);
        $stmt->execute();
    }
}

// Dodawanie komentarza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'] ?? null;
    if ($content && $user_id) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $content);
        $stmt->execute();
    }
    header("Location: view_post.php?post_id=$post_id"); 
    exit();
}

// Pobieranie posta
if ($post_id) {
    $stmt = $conn->prepare("SELECT title, content, image_path FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $post = $result->fetch_assoc();
    }
    
    // Pobieranie komentarzy dla posta
    $commentStmt = $conn->prepare("SELECT content, created_at FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    $commentStmt->bind_param("i", $post_id);
    $commentStmt->execute();
    $commentsResult = $commentStmt->get_result();
    while ($comment = $commentsResult->fetch_assoc()) {
        $comments[] = $comment;
    }

    // Pobieranie liczby polubień
    $likeStmt = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?");
    $likeStmt->bind_param("i", $post_id);
    $likeStmt->execute();
    $likeResult = $likeStmt->get_result()->fetch_assoc();
    $likeCount = $likeResult['like_count'];
}

if (!$post) {
    echo "Post nie istnieje.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Resetowanie stylów */
body, h1, h2, p, ul, li, form, textarea, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body i typografia */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

h1, h2 {
    margin-bottom: 20px;
}

h1 {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

h2 {
    font-size: 24px;
    font-weight: bold;
    color: #555;
}

p {
    margin-bottom: 10px;
    font-size: 16px;
}

/* Nagłówek */
header {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

/* Nawigacja */
nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

nav ul li {
    display: inline;
    margin-right: 10px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
}

/* Główny kontener */
main {
    max-width: 800px;
    margin: 20px auto;
    padding: 0 20px;
}

/* Artykuł */
article {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    padding: 20px;
}

article img {
    border-radius: 8px;
    display: block;
    margin: 0 auto 20px;
    max-width: 100%;
}

article p {
    margin-bottom: 20px;
}

article form {
    margin-top: 20px;
}

/* Komentarze */
.comments {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.comment {
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
    padding-bottom: 20px;
}

.comment p {
    margin-bottom: 10px;
}

.comment small {
    color: #888;
    display: block;
}

/* Formularz komentarzy */
.comments form {
    margin-top: 20px;
}

.comments textarea {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.comments button {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.comments button:hover {
    background-color: #555;
}

    </style>
</head>
<body>
<header>
<h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/view.php">Przeglądaj posty</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../user/logout.php">Wyloguj</a></li>
            <?php else: ?>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>

<main>
    <article>
        <?php if (!empty($post['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Obrazek posta" style="max-width: 100%; height: auto;">
        <?php endif; ?>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        <form method="post">
            <input type="submit" name="like" value="Lubię to!">
            <span><?php echo $likeCount; ?> polubień</span>
        </form>
    </article>

    <section class="comments">
        <h2>Komentarze</h2>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                <small>Opublikowano: <?php echo $comment['created_at']; ?></small>
            </div>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="" method="post">
                <textarea name="content" placeholder="Dodaj komentarz"></textarea>
                <button type="submit">Dodaj komentarz</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
