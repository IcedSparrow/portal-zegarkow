<?php
session_start();
session_unset(); // Usuń wszystkie zmienne sesyjne
session_destroy(); // Zniszcz sesję

header("Location: login.php"); // Przekieruj do strony logowania
exit();
?>
