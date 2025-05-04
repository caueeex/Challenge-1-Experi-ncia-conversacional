<?php
session_start();

// Database connection (only for login)
$conn = new mysqli("localhost", "root", "", "furia_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
        }
    }
    $stmt->close();
}

// Handle points update (gamification)
if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $conn->query("UPDATE users SET points = points + 10 WHERE id = $user_id");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Plataforma oficial da FURIA Esports - acompanhe partidas, notícias e interaja com nossos times!">
    <meta name="keywords" content="FURIA, esports, gaming, CS:GO, VALORANT, League of Legends, Rainbow Six, King League">
    <meta name="author" content="FURIA Esports">
    <title>FURIA Esports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="https://www.furia.gg/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        :root {
            --furia-red: #E02424;
            --furia-gold: #DAA520;
            --furia-dark: #121212;
            --furia-gray: #2D2D2D;
            --furia-light: #F5F5F5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--furia-dark);
            color: white;
            scroll-behavior: smooth;
            -webkit-tap-highlight-color: transparent;
        }

        .furia-red {
            background-color: var(--furia-red);
        }

        .furia-gold {
            background-color: var(--furia-gold);
            color: #000;
        }

        .text-furia-gold {
            color: var(--furia-gold);
        }

        .furia-gray {
            background-color: var(--furia-gray);
        }

        .text-furia-red {
            color: var(--furia-red);
        }

        .team-card {
            background: #1a1a1a;
            border: 1px solid #444;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .team-card:hover {
            transform: perspective(1000px) rotateX(2deg) rotateY(2deg) scale(1.05);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.3);
        }

        .match-card,
        .news-card {
            background: #1a1a1a;
            border: 1px solid #444;
            transition: all 0.3s ease;
        }

        .match-card:hover,
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.3);
        }

        .fade-in {
            opacity: 0;
            transition: opacity 0.5s ease-in, transform 0.5s ease-in;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .parallax-section {
            position: relative;
            overflow: hidden;
        }

        .parallax-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 0;
            will-change: transform;
        }

        #hero-bg {
            background-image: url('assets/furiabg.jpeg');
        }

        .parallax-content {
            position: relative;
            z-index: 1;
            will-change: transform;
        }

        .hero-gradient {
            background: linear-gradient(-45deg, #E02424, #DAA520, #121212, #2D2D2D);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .btn-neon {
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            z-index: 1;
            touch-action: manipulation;
        }

        .btn-neon:hover {
            color: black;
            box-shadow: 0 0 10px var(--furia-gold),
                0 0 20px var(--furia-gold),
                0 0 40px var(--furia-gold);
        }

        .btn-neon:after {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--furia-gold);
            transition: all 0.3s;
            z-index: -1;
        }

        .btn-neon:hover:after {
            left: 0;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes liveBlink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .live-indicator {
            animation: liveBlink 1.5s infinite;
        }

        /* Chatbot */
        .chatbot-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--furia-gold);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            transition: all 0.3s;
        }

        .chatbot-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px var(--furia-gold);
        }

        .chatbot-window {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            max-width: 90vw;
            height: 500px;
            max-height: 70vh;
            background-color: #1a1a1a;
            border: 1px solid #444;
            border-radius: 10px;
            display: none;
            flex-direction: column;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .chatbot-window.open {
            display: flex;
        }

        .chatbot-header {
            background-color: #2D2D2D;
            padding: 10px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chatbot-header img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .chatbot-header span {
            flex-grow: 1;
        }

        .chatbot-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            background-color: #1a1a1a;
        }

        .chatbot-footer {
            padding: 10px;
            border-top: 1px solid #444;
            display: flex;
            align-items: center;
        }

        .bot-message,
        .user-message {
            margin-bottom: 8px;
            padding: 8px 12px;
            border-radius: 15px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .bot-message {
            background-color: #333;
            color: white;
        }

        .user-message {
            background-color: var(--furia-gold);
            color: black;
            margin-left: auto;
            text-align: right;
        }

        .bot-chat-input {
            flex: 1;
            background-color: #1a1a1a;
            color: white;
            border: 1px solid #444;
            padding: 8px;
            border-radius: 20px;
            margin-right: 10px;
            outline: none;
        }

        .bot-chat-input:focus {
            border-color: var(--furia-gold);
        }

        .bot-chat-send {
            background-color: var(--furia-gold);
            color: black;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .bot-chat-send:hover {
            background-color: #e0b92e;
        }

        .bot-chat-error {
            color: #E02424;
            font-size: 12px;
            margin-top: 8px;
            display: none;
            text-align: center;
        }

        /* Social Icons */
        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: scale(1.2);
            color: var(--furia-gold);
        }

        /* Scrollbar */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Ajuste nos cards para evitar corte */
        .teams-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            padding-bottom: 1rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .team-card {
            flex: 0 0 auto;
            width: 280px;
            scroll-snap-align: center;
        }

        /* Menu Hambúrguer */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: white;
            margin: 2px 0;
            transition: all 0.3s ease;
        }

        .nav-links {
            transition: all 0.3s ease;
        }

        .nav-links.open {
            display: flex;
        }

        /* Estilo para botões de filtro */
        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background-color: var(--furia-gold) !important;
            color: black !important;
        }

        /* Responsividade */
        @media (max-width: 640px) {

            /* Geral */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            /* Navegação */
            nav .flex {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .hamburger {
                display: flex;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background: var(--furia-dark);
                padding: 1rem;
                border-bottom: 1px solid #444;
            }

            .nav-links.open {
                display: flex;
            }

            .nav-links a,
            .nav-links button {
                margin: 0.5rem 0;
                font-size: 1.1rem;
                padding: 0.5rem;
            }

            /* Hero Section */
            .h-screen {
                height: 70vh;
            }

            h1.text-4xl {
                font-size: 2rem;
            }

            p.text-xl {
                font-size: 1rem;
            }

            .space-x-4>a {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                margin: 0.5rem;
                display: inline-block;
            }

            #hero-bg {
                background-attachment: scroll;
            }

            /* Teams Section */
            .teams-container {
                padding-left: 0;
                padding-right: 0;
            }

            .team-card {
                width: 90%;
                margin: 0 auto;
            }

            .team-card img {
                width: 60px !important;
                height: auto;
            }

            .team-card p {
                font-size: 0.9rem;
            }

            /* Matches Section */
            .match-card {
                width: 90%;
                margin: 0 auto 1rem;
            }

            .match-card .text-lg {
                font-size: 1rem;
            }

            .match-card .text-sm {
                font-size: 0.8rem;
            }

            .match-card button {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            .flex.gap-2 button {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                margin: 0.2rem;
            }

            /* News Section */
            .news-card {
                width: 90%;
                margin: 0 auto 1rem;
            }

            .news-card img {
                height: 120px;
            }

            .news-card h3 {
                font-size: 1rem;
            }

            .news-card p {
                font-size: 0.8rem;
            }

            /* Sponsors Section */
            .gap-8 {
                gap: 1rem;
            }

            .h-12 {
                height: 2rem;
            }

            /* Chatbot */
            .chatbot-icon {
                width: 50px;
                height: 50px;
                bottom: 15px;
                right: 15px;
            }

            .chatbot-window {
                width: 90vw;
                right: 5vw;
                bottom: 70px;
                height: 60vh;
                max-height: 80vh;
            }

            .chatbot-header img {
                width: 30px;
                height: 30px;
            }

            .chatbot-header span {
                font-size: 0.9rem;
            }

            .bot-chat-input {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            .bot-chat-send {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            .bot-message,
            .user-message {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }

            /* Footer */
            footer .grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            footer input,
            footer button {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            footer .social-icon svg {
                width: 1.5rem;
                height: 1.5rem;
            }

            /* Modal de Login */
            .max-w-md {
                width: 90%;
            }

            .p-8 {
                padding: 1.5rem;
            }

            .space-y-4>input,
            .space-y-4>button {
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            /* Modal de Vídeo */
            .md\:w-4\/5,
            .md\:h-4\/5 {
                width: 100%;
                height: 50%;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .grid-cols-1 {
                grid-template-columns: repeat(2, 1fr);
            }

            .team-card {
                width: 300px;
            }

            .match-card,
            .news-card {
                width: 100%;
            }

            .chatbot-header img {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>

<body x-data="{ 
        darkMode: true, 
        showLogin: false, 
        userPoints: localStorage.getItem('userPoints') ? parseInt(localStorage.getItem('userPoints')) : 0,
        favoriteTeam: localStorage.getItem('favoriteTeam') || 'CS:GO',
        menuOpen: false
    }"
    :class="{ 'dark': darkMode }"
    class="text-white min-h-screen">
    <!-- Navigation -->
    <nav class="bg-black p-4 sticky top-0 z-10 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-bold hover:text-furia-gold transition-colors">
                FURIA<span class="text-furia-gold">VERSE</span>
            </a>
            <div class="hamburger" @click="menuOpen = !menuOpen">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-links flex items-center space-x-4 text-sm justify-center"
                :class="{ 'open': menuOpen }">
                <a href="#teams" class="text-white hover:text-furia-gold transition-colors">EQUIPES</a>
                <a href="#matches" class="text-white hover:text-furia-gold transition-colors">JOGOS</a>
                <a href="#news" class="text-white hover:text-furia-gold transition-colors">NOTÍCIAS</a>
                <span class="text-furia-gold">Pontos: <span x-text="userPoints"></span></span>
            </div>
        </div>
    </nav>

    <!-- Login Modal -->
    <div x-show="showLogin"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-20">
        <div class="bg-furia-gray p-8 rounded max-w-md w-11/12 relative">
            <h2 class="text-xl font-bold mb-4 text-furia-gold">Conectar</h2>
            <form method="POST" class="space-y-4">
                <input type="email" name="email" placeholder="E-mail"
                    class="w-full p-3 bg-black border border-gray-600 rounded text-white focus:border-furia-gold focus:outline-none">
                <input type="password" name="password" placeholder="Senha"
                    class="w-full p-3 bg-black border border-gray-600 rounded text-white focus:border-furia-gold focus:outline-none">
                <button type="submit" name="login"
                    class="w-full p-3 furia-gold text-black rounded btn-neon">
                    Entrar
                </button>
            </form>
            <button @click="showLogin = false" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="h-screen flex items-center justify-center bg-cover bg-center relative parallax-section fade-in hero-gradient">
        <div class="parallax-bg" id="hero-bg"></div>
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="container mx-auto text-center parallax-content px-4">
            <h1 class="text-4xl sm:text-7xl font-bold text-white mb-4 transform transition-all duration-500 hover:scale-105">
                FURIA ESPORTS
            </h1>
            <p class="text-xl sm:text-2xl text-furia-gold mb-8 animate-pulse">DOMINE O JOGO COM A GENTE!</p>
            <div class="space-x-4">
                <a href="#matches" class="btn-neon furia-gold text-black px-6 py-3 rounded-lg text-lg font-bold">
                    VEJA AS PARTIDAS
                </a>
                <a href="#teams" class="btn-neon furia-red text-white px-6 py-3 rounded-lg text-lg font-bold">
                    CONHEÇA OS TIMES
                </a>
            </div>
        </div>
    </section>

    <!-- Teams Section -->
    <section id="teams" class="container mx-auto py-16 fade-in">
        <div class="px-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-furia-gold mb-12 text-center">NOSSAS EQUIPES</h2>
            <div class="teams-container scrollbar-hide">
                <div class="team-card p-6 rounded-lg text-center"
                    :class="{ 'border-2 border-furia-gold': favoriteTeam === 'CS:GO' }"
                    @click="favoriteTeam = 'CS:GO'; localStorage.setItem('favoriteTeam', 'CS:GO')">
                    <span class="text-5xl mb-4 float-animation">
                        <img src="assets/cs2.png" alt="Icon Cs2" width="20%">
                    </span>
                    <h3 class="text-xl font-bold">FURIA CS:GO</h3>
                    <p class="text-sm text-gray-400 mt-2">Nossa equipe principal de Counter-Strike, conhecida por seu estilo agressivo e jogadas inovadoras.</p>
                    <div class="mt-4 text-furia-gold text-sm font-semibold" x-show="favoriteTeam === 'CS:GO'">⭐ SEU TIME FAVORITO</div>
                    <a href="time.php?team=CS:GO" class="mt-4 btn-neon furia-gold text-black px-4 py-2 rounded text-sm inline-block">
                        VER TIME
                    </a>
                </div>
                <div class="team-card p-6 rounded-lg text-center"
                    :class="{ 'border-2 border-furia-gold': favoriteTeam === 'VALORANT' }"
                    @click="favoriteTeam = 'VALORANT'; localStorage.setItem('favoriteTeam', 'VALORANT')">
                    <span class="text-5xl mb-4 float-animation">
                        <img src="assets/valorant.png" alt="Icon Valorant" width="30%">
                    </span>
                    <h3 class="text-xl font-bold">VALORANT</h3>
                    <p class="text-sm text-gray-400 mt-2">Nossa equipe de Valorant que vem dominando os cenários competitivos com estratégias arrojadas.</p>
                    <div class="mt-4 text-furia-gold text-sm font-semibold" x-show="favoriteTeam === 'VALORANT'">⭐ SEU TIME FAVORITO</div>
                    <a href="time.php?team=VALORANT" class="mt-4 btn-neon furia-gold text-black px-4 py-2 rounded text-sm inline-block">
                        VER TIME
                    </a>
                </div>
                <div class="team-card p-6 rounded-lg text-center"
                    :class="{ 'border-2 border-furia-gold': favoriteTeam === 'LEAGUE OF LEGENDS' }"
                    @click="favoriteTeam = 'LEAGUE OF LEGENDS'; localStorage.setItem('favoriteTeam', 'LEAGUE OF LEGENDS')">
                    <span class="text-5xl mb-4 float-animation">
                        <img src="assets/lol.png" alt="Icon League Of Legends" width="30%">
                    </span>
                    <h3 class="text-xl font-bold">LEAGUE OF LEGENDS</h3>
                    <p class="text-sm text-gray-400 mt-2">Nossa equipe de LoL que vem surpreendendo com performances consistentes nos torneios.</p>
                    <div class="mt-4 text-furia-gold text-sm font-semibold" x-show="favoriteTeam === 'LEAGUE OF LEGENDS'">⭐ SEU TIME FAVORITO</div>
                    <a href="time.php?team=LEAGUE OF LEGENDS" class="mt-4 btn-neon furia-gold text-black px-4 py-2 rounded text-sm inline-block">
                        VER TIME
                    </a>
                </div>
                <div class="team-card p-6 rounded-lg text-center"
                    :class="{ 'border-2 border-furia-gold': favoriteTeam === 'RAINBOW SIX' }"
                    @click="favoriteTeam = 'RAINBOW SIX'; localStorage.setItem('favoriteTeam', 'RAINBOW SIX')">
                    <span class="text-5xl mb-4 float-animation">
                        <img src="assets/rainbow-six.png" alt="Icon Rainbow Six" width="30%">
                    </span>
                    <h3 class="text-xl font-bold">RAINBOW SIX</h3>
                    <p class="text-sm text-gray-400 mt-2">Nossa equipe de R6 que vem se destacando com táticas precisas e execuções impecáveis.</p>
                    <div class="mt-4 text-furia-gold text-sm font-semibold" x-show="favoriteTeam === 'RAINBOW SIX'">⭐ SEU TIME FAVORITO</div>
                    <a href="time.php?team=RAINBOW SIX" class="mt-4 btn-neon furia-gold text-black px-4 py-2 rounded text-sm inline-block">
                        VER TIME
                    </a>
                </div>
                <div class="team-card p-6 rounded-lg text-center"
                    :class="{ 'border-2 border-furia-gold': favoriteTeam === 'KING LEAGUE' }"
                    @click="favoriteTeam = 'KING LEAGUE'; localStorage.setItem('favoriteTeam', 'KING LEAGUE')">
                    <span class="text-5xl mb-4 float-animation">
                        <img src="assets/kings-league.png" alt="Icon Kings League" width="30%">
                    </span>
                    <h3 class="text-xl font-bold">KING LEAGUE</h3>
                    <p class="text-sm text-gray-400 mt-2">Nossa equipe de King League que está revolucionando o cenário competitivo.</p>
                    <div class="mt-4 text-furia-gold text-sm font-semibold" x-show="favoriteTeam === 'KING LEAGUE'">⭐ SEU TIME FAVORITO</div>
                    <a href="time.php?team=KING LEAGUE" class="mt-4 btn-neon furia-gold text-black px-4 py-2 rounded text-sm inline-block">
                        VER TIME
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Matches Section -->
    <section id="matches" class="container mx-auto py-16 fade-in" x-data="{
        selectedGame: 'all',
        matches: [
            { id: 1, game: 'csgo', team: 'FURIA', opponent: 'NAVI', date: '15/05 - 20:00', location: 'BLAST Premier' },
            { id: 2, game: 'valorant', team: 'FURIA', opponent: 'LOUD', date: '16/05 - 19:00', location: 'VCT Americas' },
            { id: 3, game: 'lol', team: 'FURIA', opponent: 'LOUD', date: '17/05 - 18:00', location: 'CBLOL' },
            { id: 4, game: 'r6', team: 'FURIA', opponent: 'FAZE', date: '18/05 - 21:00', location: 'Six Invitational' },
            { id: 5, game: 'king', team: 'FURIA', opponent: 'PAIN', date: '19/05 - 20:30', location: 'King League' }
        ],
        error: null
    }">
        <div class="px-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-furia-gold mb-12 text-center">CALENDÁRIO DE PARTIDAS</h2>
            <div x-show="error" class="text-furia-red text-center mb-4" x-text="error"></div>
            <div class="flex flex-wrap gap-2 sm:gap-4 mb-8 justify-center">
                <button class="filter-btn btn-neon furia-gold text-black px-4 py-2 rounded text-sm"
                    @click="selectedGame = 'all'"
                    :class="{ 'active': selectedGame === 'all' }">
                    TODOS
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'csgo'"
                    :class="{ 'active': selectedGame === 'csgo' }">
                    CS:GO
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'valorant'"
                    :class="{ 'active': selectedGame === 'valorant' }">
                    VALORANT
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'lol'"
                    :class="{ 'active': selectedGame === 'lol' }">
                    LOL
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'r6'"
                    :class="{ 'active': selectedGame === 'r6' }">
                    R6
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'king'"
                    :class="{ 'active': selectedGame === 'king' }">
                    KING
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="match in matches" :key="match.id">
                    <div class="match-card p-6 rounded-lg"
                        x-show="selectedGame === 'all' || selectedGame === match.game">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-sm text-gray-400"
                                x-text="match.game === 'csgo' ? 'CS:GO' : 
                                          match.game === 'valorant' ? 'VALORANT' : 
                                          match.game === 'lol' ? 'LEAGUE OF LEGENDS' : 
                                          match.game === 'r6' ? 'RAINBOW SIX' : 'KING LEAGUE'"></span>
                            <span class="text-furia-gold text-sm font-semibold" x-text="match.date"></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-bold" x-text="match.team"></span>
                            <span class="text-furia-gold text-lg font-bold px-4">VS</span>
                            <span class="text-lg font-bold" x-text="match.opponent"></span>
                        </div>
                        <p class="text-sm text-gray-400" x-text="`Local: ${match.location}`"></p>
                        <div class="mt-4 flex justify-between items-center">
                            <button class="text-furia-gold text-sm font-semibold hover:underline" @click="openVideoModal('https://www.youtube.com/embed/dQw4w9WgXcQ')">ASSISTA AGORA</button>
                            <div class="text-furia-gold text-sm font-bold" :id="'live-score-' + match.id">
                                <span class="live-indicator">•</span> AO VIVO EM BREVE
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="container mx-auto py-16 fade-in" x-data="{
        selectedGame: 'all',
        news: [
            { id: 1, game: 'csgo', title: 'FURIA vence NAVI por 2x0!', summary: 'FURIA domina no BLAST Premier com vitória sólida contra a NAVI, mostrando um desempenho impressionante.', image: 'furia-navi.png' },
            { id: 2, game: 'valorant', title: 'mwzera brilha em partida decisiva!', summary: 'FURIA avança no campeonato de VALORANT após atuação espetacular de mwzera contra a LOUD.', image: 'mwzera.png' },
            { id: 3, game: 'lol', title: 'FURIA no CBLOL!', summary: 'Equipe de LOL garante vaga nos playoffs do CBLOL após vitória emocionante na última rodada.', image: 'furia-cblol.png' },
            { id: 4, game: 'r6', title: 'Estratégia inovadora da FURIA', summary: 'Nova formação da FURIA surpreende adversários no Six Invitational com táticas revolucionárias.', image: 'rainbow-six-siege.png' },
            { id: 5, game: 'king', title: 'FURIA lidera a King League', summary: 'Time da FURIA assume a liderança do campeonato após série invicta de 5 partidas.', image: 'kings-league-furia.jpg' }
        ]
    }">
        <div class="px-4">
            <h2 class="text-3xl sm:text-4xl font-bold text-furia-gold mb-12 text-center">ÚLTIMAS NOTÍCIAS</h2>
            <div class="flex flex-wrap gap-2 sm:gap-4 mb-8 justify-center">
                <button class="filter-btn btn-neon furia-gold text-black px-4 py-2 rounded text-sm"
                    @click="selectedGame = 'all'"
                    :class="{ 'active': selectedGame === 'all' }">
                    TODOS
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'csgo'"
                    :class="{ 'active': selectedGame === 'csgo' }">
                    CS:GO
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'valorant'"
                    :class="{ 'active': selectedGame === 'valorant' }">
                    VALORANT
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'lol'"
                    :class="{ 'active': selectedGame === 'lol' }">
                    LOL
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'r6'"
                    :class="{ 'active': selectedGame === 'r6' }">
                    R6
                </button>
                <button class="filter-btn bg-furia-gray text-white px-4 py-2 rounded text-sm hover:bg-opacity-80 transition"
                    @click="selectedGame = 'king'"
                    :class="{ 'active': selectedGame === 'king' }">
                    KING
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="newsItem in news" :key="newsItem.id">
                    <div class="news-card p-6 rounded-lg"
                        x-show="selectedGame === 'all' || selectedGame === newsItem.game">
                        <img :src="'assets/' + newsItem.image" :alt="newsItem.title" class="w-full h-40 object-cover rounded mb-4">
                        <span class="text-4xl mb-4 block"
                            x-text="newsItem.game === 'csgo' ? '🎯' : 
                                     newsItem.game === 'valorant' ? '💥' : 
                                     newsItem.game === 'lol' ? '🧙' : 
                                     newsItem.game === 'r6' ? '🔫' : '👑'"></span>
                        <h3 class="text-xl font-bold mb-2" x-text="newsItem.title"></h3>
                        <p class="text-sm text-gray-400 mb-4" x-text="newsItem.summary"></p>
                        <a href="#" class="text-furia-gold text-sm font-semibold hover:underline">LER MAIS →</a>
                    </div>
                </template>
            </div>
            <div class="text-center mt-12">
                <button class="btn-neon furia-gold text-black px-6 py-3 rounded-lg text-lg font-bold">
                    VER TODAS AS NOTÍCIAS
                </button>
            </div>
        </div>
    </section>

    <!-- Sponsors Section -->
    <section class="py-16 bg-black border-t border-furia-gray fade-in">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-furia-gold mb-12">NOSSOS PATROCINADORES</h2>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                <img src="assets/adidas.png" alt="Icon Adidas"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                <img src="assets/cruzeiro-do-sul.png" alt="Icon Cruzeiro do Sul"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                <img src="assets/lenovo-legion.png" alt="Icon Lenovo Legion"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                <img src="assets/pokerstars.png" alt="Icon Pokerstars"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                <img src="assets/redbull.png" alt="Icon Red Bull"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
                <img src="assets/hellmanns.png" alt="Icon Hellmans"
                    class="h-12 md:h-16 opacity-80 hover:opacity-100 transition-opacity cursor-pointer">
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex-col items-center justify-center" id="video-modal">
        <div class="absolute top-4 right-4 z-50">
            <button class="text-white text-4xl hover:text-furia-gold transition-colors" id="close-video">×</button>
        </div>
        <div class="w-full h-full md:w-4/5 md:h-4/5 flex items-center justify-center p-4">
            <iframe id="video-frame" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>

    <!-- Chatbot Floating Button and Window -->
    <div id="chatbot-container">
        <div id="chatbot-icon" class="chatbot-icon pulse-animation">
            <svg class="w-6 h-6" fill="none" stroke="black" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </div>

        <div id="chatbot-window" class="chatbot-window">
            <div class="chatbot-header">
                <img src="assets/mascote.png" alt="FURIA Mascot" style="border-radius: 20px;">
                <span class="text-white font-bold">FURIA Bot</span>
                <button id="chatbot-close" class="text-white hover:text-furia-gold">✕</button>
            </div>
            <div class="chatbot-body" id="chatbot-body">
                <div class="bot-message">
                    🔥 Bem-vindo ao FURIA Chat! Digite "oi" para começar ou use /proximojogo, /ultimajogada, /torcida ou /kingleague!
                    <span class="text-gray-500 text-xs ml-2"></span>
                </div>
            </div>
            <div class="chatbot-footer">
                <input type="text" id="chatbot-input" class="bot-chat-input" placeholder="Digite sua mensagem...">
                <button id="chatbot-send" class="bot-chat-send">ENVIAR</button>
            </div>
            <p id="chatbot-error" class="bot-chat-error">Erro: Não foi possível conectar ao bot. Verifique se o servidor está ativo.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black py-12 fade-in border-t border-furia-gray">
        <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 px-4">
            <div>
                <h3 class="text-lg font-bold text-furia-gold mb-4">FURIA<span class="text-white">VERSE</span></h3>
                <p class="text-sm text-gray-400">Sua plataforma completa para se conectar com a FURIA e todos os times e esportes.</p>
                <div class="flex space-x-4 mt-4">
                    <a href="https://twitter.com/furia" target="_blank" class="social-icon" aria-label="Twitter da FURIA">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                        </svg>
                    </a>
                    <a href="https://instagram.com/furia" target="_blank" class="social-icon" aria-label="Instagram da FURIA">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.326 3.608 1.301.975.975 1.24 2.242 1.301 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.326 2.633-1.301 3.608-.975.975-2.242 1.24-3.608 1.301-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.326-3.608-1.301-.975-.975-1.24-2.242-1.301-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.326-2.633 1.301-3.608C5.216 2.489 6.483 2.224 7.849 2.163c1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.67.014-4.947.072-1.627.074-3.002.378-4.122 1.498S1.55 4.51 1.476 6.137c-.058 1.277-.072 1.688-.072 4.947s.014 3.67.072 4.947c.074 1.627.378 3.002 1.498 4.122s2.495 1.424 4.122 1.498c1.277.058 1.688.072 4.947.072s3.67-.014 4.947-.072c1.627-.074 3.002-.378 4.122-1.498s1.424-2.495 1.498-4.122c.058-1.277.072-1.688.072-4.947s-.014-3.67-.072-4.947c-.074-1.627-.378-3.002-1.498-4.122s-2.495-1.424-4.122-1.498c-1.277-.058-1.688-.072-4.947-.072zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z" />
                        </svg>
                    </a>
                    <a href="https://youtube.com/furia" target="_blank" class="social-icon" aria-label="YouTube da FURIA">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.615 3.184c-1.614-.246-5.385-.246-7.615-.246s-6.001 0-7.615.246C2.771 3.43 1.986 4.215 1.74 5.829 1.494 7.443 1.494 12 1.494 12s0 4.557.246 6.171c.246 1.614 1.031 2.399 2.645 2.645 1.614.246 5.385.246 7.615.246s6.001 0 7.615-.246c1.614-.246 2.399-1.031 2.645-2.645.246-1.614.246-6.171.246-6.171s0-4.557-.246-6.171c-.246-1.614-1.031-2.399-2.645-2.645zM9.994 16.335v-8.67l6.667 4.335-6.667 4.335z" />
                        </svg>
                    </a>
                    <a href="https://twitch.tv/furia" target="_blank" class="social-icon" aria-label="Twitch da FURIA">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2.149 0L.536 4.029v16.452h5.803V24h3.616l3.52-3.52h5.424L23.464 12V0H2.149zm3.52 17.955V15.43H3.145v-12h17.31v8.452h-5.424v3.52h-3.52l-1.904 1.904H5.669zm5.424-8.452h2.048v5.424H11.093v-5.424zm5.424 0h2.048v5.424H16.517v-5.424z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-bold text-furia-gold mb-4">EQUIPES</h3>
                <ul class="text-sm text-gray-400 space-y-2">
                    <li><a href="#teams" class="hover:text-furia-gold transition-colors">CS:GO</a></li>
                    <li><a href="#teams" class="hover:text-furia-gold transition-colors">VALORANT</a></li>
                    <li><a href="#teams" class="hover:text-furia-gold transition-colors">League of Legends</a></li>
                    <li><a href="#teams" class="hover:text-furia-gold transition-colors">Rainbow Six</a></li>
                    <li><a href="#teams" class="hover:text-furia-gold transition-colors">King League</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold text-furia-gold mb-4">LINKS ÚTEIS</h3>
                <ul class="text-sm text-gray-400 space-y-2">
                    <li><a href="#" class="hover:text-furia-gold transition-colors">Sobre Nós</a></li>
                    <li><a href="#" class="hover:text-furia-gold transition-colors">Loja Oficial</a></li>
                    <li><a href="#" class="hover:text-furia-gold transition-colors">Parceiros</a></li>
                    <li><a href="#" class="hover:text-furia-gold transition-colors">Carreiras</a></li>
                    <li><a href="#" class="hover:text-furia-gold transition-colors">Contato</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold text-furia-gold mb-4">NEWSLETTER</h3>
                <p class="text-sm text-gray-400 mb-4">Inscreva-se para receber as últimas notícias e atualizações da FURIA.</p>
                <form class="space-y-2">
                    <input type="email" placeholder="Seu e-mail"
                        class="w-full p-3 bg-furia-gray border border-gray-600 rounded text-white focus:border-furia-gold focus:outline-none">
                    <button type="submit" class="btn-neon furia-gold text-black px-4 py-2 rounded w-full sm:w-auto">
                        INSCREVER-SE
                    </button>
                </form>
            </div>
        </div>
        <p class="text-center text-sm text-gray-400 mt-8">© 2025 FURIA ESPORTS. Todos os direitos reservados.</p>
    </footer>

    <script>
        // Função para abrir o modal de vídeo
        function openVideoModal(url) {
            const videoModal = document.getElementById('video-modal');
            const videoFrame = document.getElementById('video-frame');
            videoFrame.src = url;
            videoModal.classList.remove('hidden');
            videoModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        // Função para fechar o modal de vídeo
        function closeVideoModal() {
            const videoModal = document.getElementById('video-modal');
            const videoFrame = document.getElementById('video-frame');
            videoFrame.src = '';
            videoModal.classList.add('hidden');
            videoModal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Event listener para fechar o modal de vídeo
        document.getElementById('close-video').addEventListener('click', closeVideoModal);

        // Fechar o modal ao clicar fora do vídeo
        document.getElementById('video-modal').addEventListener('click', (event) => {
            if (event.target === document.getElementById('video-modal')) {
                closeVideoModal();
            }
        });

        // Controlar a abertura e fechamento da janela do chatbot
        const chatbotIcon = document.getElementById('chatbot-icon');
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatbotClose = document.getElementById('chatbot-close');

        chatbotIcon.addEventListener('click', () => {
            chatbotWindow.classList.toggle('open');
        });

        chatbotClose.addEventListener('click', () => {
            chatbotWindow.classList.remove('open');
        });

        document.addEventListener('click', (event) => {
            if (!chatbotWindow.contains(event.target) && !chatbotIcon.contains(event.target) && chatbotWindow.classList.contains('open')) {
                chatbotWindow.classList.remove('open');
            }
        });

        // WebSocket para o chatbot e atualizações de partidas
        let ws;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 5;

        function connectWebSocket() {
            try {
                ws = new WebSocket("ws://localhost:8080");
            } catch (error) {
                console.error("Falha inicial na conexão WebSocket:", error);
                document.getElementById("chatbot-error").style.display = "block";
                attemptReconnect();
                return;
            }

            ws.onopen = () => {
                console.log("Conexão WebSocket estabelecida");
                reconnectAttempts = 0;
                document.getElementById("chatbot-error").style.display = "none";
                updateTimestamps();
            };

            ws.onerror = (error) => {
                console.error("Erro WebSocket:", error);
                document.getElementById("chatbot-error").style.display = "block";
            };

            ws.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    console.log("Mensagem recebida do WebSocket:", data);
                    if (data.type === 'chatbot_response') {
                        const chatbotBody = document.getElementById("chatbot-body");
                        const messageHtml = `<div class="bot-message">${data.message} <span class="text-gray-500 text-xs ml-2"></span></div>`;
                        chatbotBody.innerHTML += messageHtml;
                        chatbotBody.scrollTop = chatbotBody.scrollHeight;
                        updateTimestamps();
                    } else if (data.type === "match_update") {
                        const scoreElement = document.getElementById(`live-score-${data.matchId}`);
                        if (scoreElement) {
                            scoreElement.innerHTML = `Placar ao vivo: ${data.score} <span class="live-indicator">•</span>`;
                        }
                    }
                } catch (error) {
                    console.error("Erro ao processar mensagem do WebSocket:", error);
                }
            };

            ws.onclose = () => {
                console.log("Conexão WebSocket fechada");
                document.getElementById("chatbot-error").style.display = "block";
                attemptReconnect();
            };
        }

        function attemptReconnect() {
            if (reconnectAttempts < maxReconnectAttempts) {
                reconnectAttempts++;
                console.log(`Tentativa de reconexão ${reconnectAttempts}/${maxReconnectAttempts}...`);
                setTimeout(connectWebSocket, 5000);
            } else {
                console.error("Número máximo de tentativas de reconexão atingido. Verifique o servidor WebSocket.");
            }
        }

        connectWebSocket();

        // Função para enviar mensagens do chatbot
        function sendMessage() {
            const input = document.getElementById("chatbot-input");
            const message = input.value.trim();
            if (!message || !ws || ws.readyState !== WebSocket.OPEN) {
                document.getElementById("chatbot-error").style.display = "block";
                return;
            }
            const messageHtml = `<div class="user-message">${message} <span class="text-gray-500 text-xs ml-2"></span></div>`;
            const chatbotBody = document.getElementById("chatbot-body");
            chatbotBody.innerHTML += messageHtml;
            ws.send(JSON.stringify({
                type: "chat",
                user: "Usuário",
                message
            }));
            input.value = '';
            chatbotBody.scrollTop = chatbotBody.scrollHeight;
            updateTimestamps();
            const userPointsElement = document.querySelector("[x-text='userPoints']");
            let userPoints = parseInt(localStorage.getItem("userPoints") || "0") + 5;
            localStorage.setItem("userPoints", userPoints);
            userPointsElement.textContent = userPoints;
        }

        document.getElementById("chatbot-send").addEventListener("click", sendMessage);
        document.getElementById("chatbot-input").addEventListener("keypress", (e) => {
            if (e.key === "Enter") sendMessage();
        });

        function updateTimestamps() {
            const messageTimestamps = document.querySelectorAll(".bot-message .text-gray-500, .user-message .text-gray-500");
            messageTimestamps.forEach(span => {
                span.textContent = new Date().toLocaleTimeString();
            });
        }

        // Smooth scrolling para links de âncora
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener("click", function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute("href"));
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth"
                    });
                }
            });
        });

        // Animações de fade-in ao rolar a página
        const fadeInElements = document.querySelectorAll(".fade-in");
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                }
            });
        }, {
            threshold: 0.1
        });
        fadeInElements.forEach(element => {
            element.style.transform = "translateY(20px)";
            observer.observe(element);
        });

        // Simulação de atualizações ao vivo para partidas
        setInterval(() => {
            if (!ws || ws.readyState !== WebSocket.OPEN) return;
            const matchCards = document.querySelectorAll(".match-card");
            matchCards.forEach(card => {
                const matchId = card.querySelector("[id^='live-score-']").id.split("-")[2];
                ws.send(JSON.stringify({
                    type: "match_update",
                    matchId,
                    score: `${Math.floor(Math.random() * 5)} - ${Math.floor(Math.random() * 5)}`
                }));
            });
        }, 30000);

        // Efeito de parallax apenas para a seção Hero
        function updateParallax() {
            const scrollPosition = window.scrollY;
            const heroSection = document.querySelector("#hero");
            if (heroSection) {
                const bg = heroSection.querySelector(".parallax-bg");
                const rect = heroSection.getBoundingClientRect();
                const speed = 0.3;
                const offset = (scrollPosition - (rect.top + scrollPosition)) * speed;
                if (window.innerWidth > 640) {
                    bg.style.transform = `translateY(${offset}px)`;
                } else {
                    bg.style.transform = "translateY(0)";
                }
            }
            requestAnimationFrame(updateParallax);
        }
        requestAnimationFrame(updateParallax);
    </script>
</body>

</html>
<?php $conn->close(); ?>