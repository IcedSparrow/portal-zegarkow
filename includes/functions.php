<?php
// Załączenie pliku konfiguracyjnego
require_once 'config.php';

/**
 * Sanitizacja danych wejściowych
 *
 * @param string $data Dane wejściowe od użytkownika.
 * @return string Oczyszczone dane.
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Sprawdzenie, czy użytkownik jest zalogowany
 *
 * @return bool True, jeśli użytkownik jest zalogowany, w przeciwnym razie false.
 */
function isLoggedIn() {
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
        return true;
    }
    return false;
}
?>
