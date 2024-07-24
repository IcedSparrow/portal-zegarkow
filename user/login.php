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
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Proszę podać nazwę użytkownika i hasło.';
    } else {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $user_id;
                header("Location: ../posts/index.php");
                exit;
            } else {
                $error = 'Nieprawidłowe hasło.';
            }
        } else {
            $error = 'Nie znaleziono użytkownika.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('../img/back-ground-log.webp'); /* Tutaj podaj ścieżkę do obrazka */
    background-size: cover;
    background-position: center;
}

header {
    background-color: rgba(51, 51, 51, 0.5); /* Kolor tła z przezroczystością */
    color: white;
    padding: 10px;
}


header h1 {
    margin: 0;
    text-align: center;
}

nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    text-align: center;

}

nav ul li {
    display: inline;
    margin-right: 10px;
}

nav ul li a {
    color: white;
    text-decoration: none;
}

.container {
    width: 300px;
    margin: 100px auto;
    background-color: rgba(255, 255, 255, 0.8); /* Kolor tła kontenera z lekką przezroczystością */
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Cień */
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.error-message {
    color: #ff0000;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
}

input[type="text"],
input[type="password"],
button[type="submit"] {
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    width: 100%; /* Szerokość formularza */
    box-sizing: border-box; /* Wliczanie paddingu w szerokość */
}

button[type="submit"] {
    background-color: #007bff;
    color: white;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

    </style>
</head>
<body>
<header>
    <h1>Login</h1>
    <nav>
        <ul>
            <li><a href="../posts/index.php">Strona główna</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
            <?php else: ?>
            <?php endif; ?>
            <li><a href="../user/about.php">O mnie</a></li>
        </ul>
    </nav>
</header>
    <div class="container">
        <h2>Logowanie</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            Nazwa użytkownika: <input type="text" name="username" required><br>
            Hasło: <input type="password" name="password" required><br>
            <button type="submit">Zaloguj się</button>
        </form>
    </div>
</body>

</html>
