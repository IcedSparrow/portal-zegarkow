<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../user/login.php");
    exit;
}

// Logika przetwarzania formularza po jego wysłaniu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../includes/config.php'; // Plik konfiguracyjny bazy danych

    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Pobranie ID użytkownika z sesji

    // Wstawienie pytania do bazy danych
    $sql = "INSERT INTO questions (user_id, title, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $title, $content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: ../posts/index.php"); // Przekierowanie do listy pytań
        exit;
    } else {
        echo "Nie udało się dodać pytania.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zadaj pytanie</title>
    <style>
/* Resetowanie styli */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Podstawowe style */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-image: url('../img/ask.webp'); /* Dodaj ścieżkę do obrazu */
    color: #333;
    margin: 0;
    padding: 0;
}

header {
    background-color: #333;
    color: #fff;
    padding: 1rem;
    text-align: center;
}

header h1 {
    margin-bottom: 0.5rem;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

nav ul li {
    display: inline;
    margin-right: 1rem;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
}

form {
    background-color: #fff;
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

form input[type="text"],
form textarea {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

form textarea {
    height: 150px;
}

button[type="submit"] {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: red;
}

    </style>
</head>
<body>
    <header>
    <h1>Zadaj pytanie</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/question.php">Pytania </a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="../user/logout.php">Wyloguj</a></li>
            <?php else: ?>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>
    <form method="post" action="ask.php">
        Tytuł: <input type="text" name="title" required><br>
        Treść: <textarea name="content" required></textarea><br>
        <button type="submit">Zadaj pytanie</button>
    </form>
</body>
</html>
