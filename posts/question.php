<?php
session_start();
require_once '../includes/config.php'; // Adjust the path to your configuration file

// Sprawdzenie, czy formularz został wysłany
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'], $_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $question_id = $_POST['question_id'];
    $content = $_POST['content'];

    // Dodanie komentarza do bazy danych
    $stmt = $conn->prepare("INSERT INTO comments_question (question_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $question_id, $user_id, $content);
    $stmt->execute();

    // Przekierowanie, aby uniknąć ponownego wysłania formularza
    header("Location: question.php");
    exit();
}

// Pobranie pytań wraz z komentarzami z bazy danych
$questions = [];
$query = "SELECT q.question_id, q.title, q.content, q.created_at FROM questions q ORDER BY q.created_at DESC";
if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $question_id = $row['question_id'];

        // Pobieranie komentarzy dla pytania
        $commentsQuery = $conn->prepare("SELECT c.content, c.created_at FROM comments_question c WHERE c.question_id = ? ORDER BY c.created_at DESC");
        $commentsQuery->bind_param("i", $question_id);
        $commentsQuery->execute();
        $commentsResult = $commentsQuery->get_result();
        $comments = [];
        while ($comment = $commentsResult->fetch_assoc()) {
            $comments[] = $comment;
        }

        // Dodanie komentarzy do pytania
        $row['comments'] = $comments;
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Pytania</title>
    <style>
        /* Resetowanie domyślnych stylów */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-image: url('../img/question.webp'); /* Dodaj ścieżkę do obrazu */
    color: #333;
}

header {
    background-color: #333;
    color: #fff;
    padding: 20px 0;
    text-align: center;
}

header h1 {
    font-size: 2em;
    margin-bottom: 10px;
}

nav ul {
    list-style-type: none;
}

nav ul li {
    display: inline;
    margin-right: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
}

nav ul li a:hover {
    text-decoration: underline;
}

main {
    max-width: 800px;
    margin: 20px auto;
    padding: 0 20px;
}

.question {
    background-color: #fff;
    border: 1px solid #ccc;
    margin-bottom: 20px;
    padding: 20px;
}

.question h2 {
    margin-bottom: 10px;
}

.question p {
    margin-bottom: 15px;
}

.question small {
    color: #777;
}

.comments {
    margin-top: 20px;
}

.comments h3 {
    margin-bottom: 10px;
}

.comment {
    border-top: 1px solid #ccc;
    padding-top: 10px;
    margin-top: 10px;
}

textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
}

button:hover {
    background-color: #555;
}

    </style>
</head>
<body>
<header>
    <h1>Pytania</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/ask.php">Zadaj pytanie</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../user/logout.php">Wyloguj</a></li>
                
            <?php else: ?>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>

<main>
    <?php foreach ($questions as $question): ?>
        <div class="question">
            <h2><?php echo htmlspecialchars($question['title']); ?></h2>
            <p><?php echo nl2br(htmlspecialchars($question['content'])); ?></p>
            <small>Zadane: <?php echo $question['created_at']; ?></small>
            
            <!-- Wyświetlanie komentarzy -->
            <?php if (!empty($question['comments'])): ?>
                <div class="comments">
                    <h3>Komentarze</h3>
                    <?php foreach ($question['comments'] as $comment): ?>
                        <div class="comment">
                            <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                            <small>Opublikowano: <?php echo $comment['created_at']; ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Formularz dodawania komentarza -->
                <form action="question.php" method="post">
                    <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                    <textarea name="content" placeholder="Dodaj swój komentarz" required></textarea>
                    <button type="submit" name="submit_comment">Dodaj komentarz</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</main>
</body>
</html>
