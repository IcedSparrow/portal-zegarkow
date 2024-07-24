<?php
session_start();
require_once "../includes/config.php"; // Ścieżka dostępu do pliku konfiguracyjnego

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    if (empty($username) || empty($email) || empty($_POST['password'])) {
        $error = 'Proszę wypełnić wszystkie pola.';
    } else {
        // Sprawdź, czy nazwa użytkownika jest już zajęta
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Nazwa użytkownika jest już zajęta.';
        } else {
            // Dodawanie użytkownika do bazy danych
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $error = 'Wystąpił błąd podczas rejestracji.';
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background: url('../img/back-ground-reg.webp') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}

header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.7); /* Kolor tła z przezroczystością */
    color: #fff; /* Kolor tekstu */
    padding: 20px; /* Wewnętrzny odstęp */
    text-align: center; /* Wyśrodkowanie tekstu w nagłówku */
    z-index: 999; /* Ustawiamy wyższy indeks warstwy, aby nagłówek był na samej górze */
}

h1 {
    margin: 0; /* Reset marginesu */
}

nav ul {
    list-style-type: none; /* Usuwa domyślne punkty listy */
    padding: 0; /* Reset wewnętrznego odstępu */
    margin: 0; /* Reset marginesu */
}

nav ul li {
    display: inline; /* Wyświetla elementy listy w jednej linii */
    margin-right: 20px; /* Odstęp między elementami listy */
}

nav ul li:last-child {
    margin-right: 0; /* Usunięcie marginesu z ostatniego elementu listy */
}

nav ul li a {
    color: #fff; /* Kolor linków */
    text-decoration: none; /* Usunięcie podkreślenia linków */
}

nav ul li a:hover {
    text-decoration: underline; /* Podkreślenie linku po najechaniu */
}

.container {
    background-color: rgba(255, 255, 255, 0.5); /* przezroczyste tło */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
    margin: 140px auto 0; /* Wyśrodkowanie formularza pionowo i poziomo */
}

h2 {
    margin-top: 0;
    color: #333;
}

input[type="text"],
input[type="email"],
input[type="password"],
button {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
}

button {
    background-color: #4caf50;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}

p.error {
    color: #f00;
    margin-top: 10px;
}

    </style>
</head>
<body>
    <header>
        <h1>Rejestracja</h1>
        <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
            <?php else: ?>
                <li><a href="../user/login.php">Logowanie</a></li>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
    </header>
    <div class="container">
        <h2>Rejestracja</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="text" name="username" placeholder="Nazwa użytkownika" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Hasło" required><br>
            <button type="submit">Zarejestruj się</button>
        </form>
    </div>
</body>
</html>

