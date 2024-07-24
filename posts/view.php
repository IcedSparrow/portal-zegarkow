<?php
session_start();
require_once '../includes/config.php'; // Adjust the path to your configuration file

$posts = [];
// Update your query to also select the image_path
$query = "SELECT post_id, title, content, image_path FROM posts ORDER BY created_at DESC";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Przeglądaj Posty</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
<h1>Przeglądaj Posty</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/ask.php">Zadaj pytanie</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../user/create_post.php">Utwórz post</a></li>
                <li><a href="../user/logout.php">Wyloguj</a></li>
            <?php else: ?>
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
                <!-- Display the image if it exists -->
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Obrazek posta" style="max-width: 100%; height: auto;">
            <?php endif; ?>
            <p><?php echo substr(htmlspecialchars($post['content']), 0, 200) . '...'; ?></p>
            <a href="view_post.php?post_id=<?php echo $post['post_id']; ?>">Czytaj więcej</a>
        </article>
    <?php endforeach; ?>
</main>
</body>
</html>
