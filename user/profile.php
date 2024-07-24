<?php
session_start();
require_once '../includes/config.php'; // Dostosuj ścieżkę do swojej konfiguracji

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pobranie informacji o użytkowniku z bazy danych
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Zmiana hasła
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        $update_stmt->execute();
        // Jeśli aktualizacja hasła zakończyła się sukcesem, przekieruj użytkownika do profilu
        header("Location: profile.php");
        exit();
    } else {
        $password_error = "Hasła nie pasują do siebie.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twój Profil</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('../img/profile.webp'); /* Dodaj ścieżkę do obrazu */
}

header {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}

nav ul li {
    display: inline;
    margin-right: 20px;
}

nav ul li a {
    color: #fff;
    text-decoration: none;
}

main {
    max-width: 800px;
    margin: 20px auto;
    background-color: rgba(255, 255, 255, 0.9); /* Dodaj kolor tła i ustal jego przezroczystość */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    margin-top: 0;
}

form {
    margin-bottom: 20px;
    opacity: 0.5; /* Dodaj przezroczystość do formularza */
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button[type="submit"] {
    background-color: #333;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 4px;
}

button[type="submit"]:hover {
    background-color: #555;
}

    </style>
</head>
<body>
<header>
    <h1>Twój Profil</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <li><a href="../posts/view.php">Przeglądaj posty</a></li>
            <li><a href="logout.php">Wyloguj</a></li>
        </ul>
    </nav>
</header>

<main>
    <h2>Informacje o użytkowniku</h2>
    <p><strong>Nazwa użytkownika:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Adres e-mail:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

    <h2>Zmień hasło</h2>
    <form action="" method="post">
        <label for="new_password">Nowe hasło:</label><br>
        <input type="password" id="new_password" name="new_password" required><br>
        <label for="confirm_password">Potwierdź nowe hasło:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <?php if (isset($password_error)): ?>
            <p style="color: red;"><?php echo $password_error; ?></p>
        <?php endif; ?>
        <button type="submit">Zmień hasło</button>
    </form>
</main>
</body>
</html>
