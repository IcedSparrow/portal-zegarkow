<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>O mnie - Twój Blog</title>
    <meta name="description" content="Dzielę się myślami, pomysłami i doświadczeniami dotyczącymi zegarków. Inspiruję i pomagam pasjonatom zegarków.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="css/styles.css"> <!-- Adjust the path to your CSS file as needed -->
    <style>
/* Resetowanie stylów */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-image: url('../img/me.webp');
    background-size: cover;
    background-position: center;
}
.about-me {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
img {
    max-width: 100%;
    height: auto;
}

/* Stylizacja nagłówka */
header {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

header h1 {
    font-size: 36px;
    margin-bottom: 20px;
}

nav ul {
    list-style: none;
}

nav ul li {
    display: inline-block;
    margin-right: 20px;
}

nav ul li a {
    color: white; /* Zmiana koloru tekstu na biały */
    text-decoration: none;
    transition: color 0.3s ease; /* Dodanie animacji zmiany koloru tekstu */
}

nav ul li a:hover {
    color: yellow; /* Zmiana koloru tekstu po najechaniu myszką */
}

/* Nowe style dla ikon */
.social-icons {
    margin-top: 10px;
}

.social-icons a {
    display: inline-block;
    margin-right: 10px;
    font-size: 24px; /* Rozmiar ikon */
    color: white; /* Domyślny kolor ikon */
    transition: transform 0.3s ease, color 0.3s ease; /* Animacja przekształcenia i koloru */
}

.social-icons a:hover {
    transform: scale(1.2); /* Powiększenie ikony po najechaniu myszką */
}

/* Kolory ikon po najechaniu myszką */
.social-icons a:hover .fab.fa-youtube {
    color: red;
}

.social-icons a:hover .fab.fa-tiktok {
    color: purple;
}

.social-icons a:hover .fab.fa-twitter {
    color: lightblue;
}

.social-icons a:hover .fab.fa-instagram {
    color: pink;
}

/* Stylizacja treści głównej */
main {
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.5); /* Białe tło z 50% przezroczystości */
    max-width: 800px; /* Maksymalna szerokość kontenera */
    margin: 0 auto; /* Wyśrodkowanie kontenera */
    border: 2px solid black; /* Dodanie czarnego obramowania */
}

.about-me h2,
.about-me h3 {
    margin-top: 20px;
    color: darkgreen;
}

.about-me p {
    margin-bottom: 20px;
    color: black;
}

/* Stylizacja stopki */
footer {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

</style>


</head>
<body>
    <header>
        <h1>O mnie!</h1>
        <nav>
            <ul>
                <li><a href="../posts/index.php">Strona główna</a></li>
                <li><a href="../user/about.php">O mnie</a></li>
                <li><a href="contact.php">Kontakt</a></li>
            </ul>
            <!-- Dodaj ikony -->
            <div class="social-icons">
                <a href="https://www.tiktok.com/@zegarkidawida"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.youtube.com/channel/UCJBjln6ufVPDAHRGgfVH10g"><i class="fab fa-youtube"></i></a>
                <a href="https://twitter.com/ZegarkiDawid"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/zegarkidawida/"><i class="fab fa-instagram"></i></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="about-me">
            <h2>Witaj na moim blogu!</h2>
            <p>Jestem Dawid, pasjonat zegarków, sportu i programowania. Na tym blogu dzielę się moimi myślami, pomysłami i doświadczeniami dotyczącymi zegarków. Mam nadzieję, że znajdziesz tutaj coś, co Cię zainspiruje i pomoże.</p>
            
            <br>
            <h3>Dlaczego ten blog?</h3>
            <p>Moim celem jest inspiracja innych pasjonatów zegarków poprzez dzielenie się wiedzą i doświadczeniem. Chcę, aby ten blog stał się miejscem, gdzie ludzie mogą pogłębiać swoją pasję i rozwijać się jako kolekcjonerzy.</p>
            
            <br>
            <h3>Poznajmy się!</h3>
            <p>Wolny czas spędzam na badaniu różnych modeli zegarków, uprawianiu sportu i doskonaleniu się w programowaniu. Chętnie dzielę się swoimi spostrzeżeniami i doświadczeniami ze społecznością zegarkową.</p>

            <br>
            <h3>Moje zdjęcie</h3>
            <img src="../img/ja2.jpg" alt="Moje zdjęcie">
        </section>
    </main>
    <footer>
        <p>&copy; 2024 ZegarkiDawida. Wszelkie prawa zastrzeżone.</p>
    </footer>

</body>
</html>
