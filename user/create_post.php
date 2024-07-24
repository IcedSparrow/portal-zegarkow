<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    // Jeśli użytkownik nie jest zalogowany, przekieruj do strony logowania
    header('Location: ../user/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $imagePath = '';

    // Obsługa przesyłanego obrazka
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        // Sprawdzenie, czy plik jest w akceptowalnym formacie
        $validExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, $validExtensions)) {
            $uploadsDir = '../uploads/';
            $imageName = time() . '_' . basename($_FILES['post_image']['name']);
            $imagePath = $uploadsDir . $imageName;

            // Sprawdzenie, czy katalog uploads istnieje, jeśli nie - próba utworzenia
            if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0755, true) && !is_dir($uploadsDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadsDir));
            }

            if (!move_uploaded_file($_FILES['post_image']['tmp_name'], $imagePath)) {
                echo 'Nie udało się przesłać obrazka.';
                exit;
            }
        } else {
            echo 'Niedozwolony format pliku. Akceptowalne formaty to: jpg, jpeg, png.';
            exit;
        }
    }

    // Dodawanie posta do bazy danych
    if ($stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image_path) VALUES (?, ?, ?, ?)")) {
        $stmt->bind_param('isss', $user_id, $title, $content, $imagePath);
        if ($stmt->execute()) {
            header('Location: ../posts/index.php');
            exit();
        } else {
            echo 'Wystąpił błąd podczas dodawania posta.';
        }
        $stmt->close();
    } else {
        echo 'Wystąpił błąd podczas przygotowywania zapytania.';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Utwórz Post</title>
    <style>
/* Resetowanie stylów domyślnych */
body, h1, h2, h3, h4, h5, h6, p, ul, li, form, input, textarea, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-image: url('../img/creat-post.webp');
    background-size: cover;
    background-position: center;
    color: #333;
}

header {
    background-color: rgba(51, 51, 51, 0.8); /* Przezroczyste tło */
    color: #fff;
    padding: 20px;
}

header h1 {
    margin: 0;
    font-size: 24px;
    text-align: center;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    text-align: center;
}

nav ul li {
    display: inline-block;
    margin-right: 10px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
}

form {
    background-color: rgba(255, 255, 255, 0.5); /* Przezroczyste tło */
    padding: 20px;
    margin: 20px auto;
    max-width: 600px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form input[type="text"],
form textarea,
form input[type="file"],
form button {
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
}

form textarea {
    height: 150px;
}

form button {
    background-color: #333;
    color: #fff;
    border: none;
    cursor: pointer;
}

form button:hover {
    background-color: #555;
}


    </style>
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
                
            <?php else: ?>
                <li><a href="../user/login.php">Logowanie</a></li>
                <li><a href="../user/register.php">Rejestracja</a></li>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>
<form action="create_post.php" method="post" enctype="multipart/form-data">
    Tytuł: <input type="text" name="title" required><br>
    Treść: <textarea name="content" required></textarea><br>
    Obrazek: <input type="file" name="post_image"><br>
    <button type="submit">Utwórz post</button>
</form>
</body>
</html>
