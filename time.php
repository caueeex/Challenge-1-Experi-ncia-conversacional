<?php
// Recebe o time da URL
$team = isset($_GET['team']) ? $_GET['team'] : null;

// Dados dos times
$teams = [
    'CS:GO' => [
        'description' => 'Nossa equipe principal de Counter-Strike, conhecida por seu estilo agressivo e jogadas inovadoras.',
        'roster' => [
            ['name' => 'Skullz', 'photo' => 'skullz-photo.png'],
            ['name' => 'yuurih', 'photo' => 'yuurih-photo.png'],
            ['name' => 'KSCERATO', 'photo' => 'kscerato-photo.png'],
            ['name' => 'FalleN', 'photo' => 'fallen-foto.png'],
            ['name' => 'chelo', 'photo' => 'chelo-photo.png'],
        ],
        'achievements' => 'Campeã do BLAST Premier 2024',
        'socialLink' => 'https://twitter.com/FURIA_CSGO',
        'image' => 'furia-campea.png',
    ],
    'VALORANT' => [
        'description' => 'Nossa equipe de Valorant que vem dominando os cenários competitivos com estratégias arrojadas.',
        'roster' => [
            ['name' => 'mwzera', 'photo' => 'mwzera-photo.png'],
            ['name' => 'heat', 'photo' => 'heat-photo.png'],
            ['name' => 'khalil', 'photo' => 'khalil-photo.png'],
            ['name' => 'havoc', 'photo' => 'havoc-photo.png'],
            ['name' => 'pryze', 'photo' => 'pryze-photo.jpeg'],
        ],
        'achievements' => 'Finalista do VCT Americas 2024',
        'socialLink' => 'https://twitter.com/FURIA_VALORANT',
        'image' => 'furia-valorant.jpg',
    ],
    'LEAGUE OF LEGENDS' => [
        'description' => 'Nossa equipe de LoL que vem surpreendendo com performances consistentes nos torneios.',
        'roster' => [
            ['name' => 'Destroy', 'photo' => 'destroy-photo.png'],
            ['name' => 'Mir', 'photo' => 'mir-photo.png'],
            ['name' => 'Tutsz', 'photo' => 'tutsz-photo.png'],
            ['name' => 'Ayu', 'photo' => 'ayu-photo.png'],
            ['name' => 'Zay', 'photo' => 'zay-photo.png'],
        ],
        'achievements' => 'Playoffs do CBLOL 2024',
        'socialLink' => 'https://twitter.com/FURIA_LOL',
        'image' => 'furia-lol.jpg',
    ],
    'RAINBOW SIX' => [
        'description' => 'Nossa equipe de R6 que vem se destacando com táticas precisas e execuções impecáveis.',
        'roster' => [
            ['name' => 'Kheyze', 'photo' => 'kheyze-photo.png'],
            ['name' => 'nade', 'photo' => 'nade-photo.png'],
            ['name' => 'jv92', 'photo' => 'jv92-photo.png'],
            ['name' => 'HerdsZ', 'photo' => 'herdsz-photo.png'],
            ['name' => 'FelipoX', 'photo' => 'felipox-photo.png'],
        ],
        'achievements' => 'Top 4 no Six Invitational 2024',
        'socialLink' => 'https://twitter.com/FURIA_R6',
        'image' => 'furia-r6.jpg',
    ],
    'KING LEAGUE' => [
        'description' => 'Nossa equipe de King League que está revolucionando o cenário competitivo.',
        'roster' => [
            ['name' => 'dedo', 'photo' => 'dedo-photo.png'],
            ['name' => 'leleti', 'photo' => 'leleti-photo.png'],
            ['name' => 'jeffinho', 'photo' => 'jeffinho-photo.png'],
            ['name' => 'victor hugo', 'photo' => 'victor-hugo-photo.png'],
            ['name' => 'andrey batata', 'photo' => 'andrey-batata-photo.png'],
        ],
        'achievements' => 'Líder do campeonato 2024',
        'socialLink' => 'https://twitter.com/FURIA_KING',
        'image' => 'furia-kings-league.jpg',
    ]
];

// Verifica se o time existe
$teamData = isset($teams[$team]) ? $teams[$team] : null;
if (!$teamData) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Detalhes do time <?php echo htmlspecialchars($team); ?> da FURIA Esports">
    <meta name="keywords" content="FURIA, esports, <?php echo htmlspecialchars($team); ?>">
    <meta name="author" content="FURIA Esports">
    <title>FURIA Esports - <?php echo htmlspecialchars($team); ?></title>
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
            position: relative;
            overflow-x: hidden;
        }

        /* Canvas para os raios dourados */
        #lightning-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.3;
            pointer-events: none;
        }

        .furia-gold { background-color: var(--furia-gold); color: #000; }
        .text-furia-gold { color: var(--furia-gold); }
        .furia-gray { background-color: var(--furia-gray); }

        /* Botões com efeito neon */
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

        /* Animações */
        .fade-in { 
            opacity: 0; 
            transform: translateY(20px); 
            transition: opacity 0.8s ease-out, transform 0.8s ease-out; 
        }
        .fade-in.visible { 
            opacity: 1; 
            transform: translateY(0); 
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Efeito de gradiente no fundo */
        .gradient-bg {
            background: linear-gradient(135deg, #121212, #2D2D2D, #DAA520);
            background-size: 200% 200%;
            animation: gradientShift 10s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Estilização do banner */
        .team-banner {
            position: relative;
            height: 500px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        .team-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), var(--furia-dark));
            z-index: 1;
        }
        .team-banner-content {
            position: relative;
            z-index: 2;
        }

        /* Estilização dos cards de jogadores */
        .player-card {
            background: #1a1a1a;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .player-card:hover {
            border-color: var(--furia-gold);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(218, 165, 32, 0.3);
        }
        .player-card img {
            transition: transform 0.3s ease;
        }
        .player-card:hover img {
            transform: scale(1.05);
        }

        /* Estilização do texto */
        .highlight-text {
            background: linear-gradient(90deg, #DAA520, #E02424);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }

        /* Social Icons */
        .social-icon {
            transition: all 0.3s ease;
        }
        .social-icon:hover {
            transform: scale(1.2);
            color: var(--furia-gold);
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

        /* Responsividade */
        @media (max-width: 640px) {
            .team-banner {
                height: 350px;
            }
            .team-banner h1 {
                font-size: 2.5rem;
            }
            .team-banner p {
                font-size: 1rem;
            }
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
            .nav-links a {
                margin: 0.5rem 0;
                font-size: 1.1rem;
                padding: 0.5rem;
            }
            /* Seções */
            h2.text-3xl {
                font-size: 1.75rem;
            }
            .player-card {
                width: 90%;
                margin: 0 auto;
            }
            .player-card img {
                width: 120px;
                height: 120px;
            }
            .player-card h3 {
                font-size: 1.25rem;
            }
            .player-card p {
                font-size: 0.9rem;
            }
            .social-icon svg {
                width: 2rem;
                height: 2rem;
            }
            .btn-neon {
                font-size: 0.9rem;
                padding: 0.5rem 1.5rem;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .team-banner {
                height: 450px;
            }
            .grid-cols-1 {
                grid-template-columns: repeat(2, 1fr);
            }
            .player-card {
                width: 100%;
            }
        }
    </style>
</head>
<body class="min-h-screen" x-data="{ menuOpen: false }">
    <!-- Canvas para os raios dourados -->
    <canvas id="lightning-canvas"></canvas>

    <!-- Navigation -->
    <nav class="bg-black p-4 sticky top-0 z-10 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-white text-2xl font-bold hover:text-furia-gold transition-colors">
                FURIA<span class="text-furia-red">VERSE</span>
            </a>
            <div class="hamburger" @click="menuOpen = !menuOpen">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-links flex items-center space-x-4 text-sm" :class="{ 'open': menuOpen }">
                <a href="index.php#teams" class="btn-neon furia-gold text-black px-4 py-2 rounded text-sm">
                    VOLTAR
                </a>
            </div>
        </div>
    </nav>

    <!-- Team Banner -->
    <section class="team-banner fade-in" style="background-image: url('<?php echo htmlspecialchars($teamData['image']); ?>');">
        <div class="team-banner-content">
            <h1 class="text-5xl md:text-6xl font-bold mt-4 highlight-text">
                FURIA <?php echo htmlspecialchars($team); ?>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mt-4 max-w-2xl mx-auto">
                <?php echo htmlspecialchars($teamData['description']); ?>
            </p>
        </div>
    </section>

    <!-- Team Details -->
    <section class="container mx-auto py-16 gradient-bg fade-in">
        <div class="px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-furia-gold mb-8 text-center">SOBRE O TIME</h2>
            <div class="bg-furia-gray p-6 rounded-lg shadow-lg">
                <p class="text-gray-300 text-lg mb-4"><?php echo htmlspecialchars($teamData['description']); ?></p>
                <h3 class="text-2xl font-semibold text-furia-gold mb-4">Conquista Recente</h3>
                <p class="text-gray-300 text-lg"><?php echo htmlspecialchars($teamData['achievements']); ?></p>
            </div>
        </div>
    </section>

    <!-- Roster Section -->
    <section class="container mx-auto py-16 fade-in">
        <div class="px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-furia-gold mb-12 text-center">ELENCO</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($teamData['roster'] as $player): ?>
                    <div class="player-card p-6 rounded-lg text-center">
                        <img src="<?php echo htmlspecialchars($player['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($player['name']); ?>" 
                             class="w-32 h-32 rounded-full mx-auto mb-4 object-cover border-2 border-furia-gold">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($player['name']); ?></h3>
                        <p class="text-sm text-gray-400 mt-2">Jogador Profissional</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Social Section -->
    <section class="container mx-auto py-16 gradient-bg fade-in">
        <div class="px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-furia-gold mb-8">SIGA-NOS</h2>
            <a href="<?php echo htmlspecialchars($teamData['socialLink']); ?>" target="_blank" class="social-icon">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                </svg>
            </a>
            <a href="<?php echo htmlspecialchars($teamData['socialLink']); ?>" target="_blank" 
               class="btn-neon furia-gold text-black px-6 py-3 rounded-lg text-lg font-bold">
                Siga no Twitter
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-12 border-t border-furia-gray">
        <div class="container mx-auto text-center px-4">
            <p class="text-sm text-gray-400">© 2025 FURIA ESPORTS. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Animações de fade-in ao rolar a página
        const fadeInElements = document.querySelectorAll(".fade-in");
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                }
            });
        }, { threshold: 0.1 });
        fadeInElements.forEach(element => {
            observer.observe(element);
        });

        // Efeito de raios dourados
        const canvas = document.getElementById('lightning-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Função de interpolação linear (lerp) para suavizar transições
        function lerp(start, end, t) {
            return start + (end - start) * t;
        }

        class Lightning {
            constructor() {
                this.x = Math.random() * width;
                this.y = 0;
                this.segments = [];
                this.targetSegments = [];
                this.transition = 0;
                this.transitionSpeed = 0.05; // Velocidade da transição
                this.generatePath();
                this.targetSegments = [...this.segments];
            }

            generatePath() {
                let currentY = 0;
                this.segments = [];
                // Reduzindo o número de segmentos para desenho mais rápido
                const segmentHeight = height / 5; // Menos segmentos
                for (let i = 0; i < 5; i++) {
                    const nextY = currentY + segmentHeight;
                    const offsetX = (Math.random() - 0.5) * 80; // Reduzindo o deslocamento para movimentos mais suaves
                    this.segments.push({
                        x: this.x + offsetX,
                        y: nextY
                    });
                    currentY = nextY;
                    this.x += offsetX * 0.3;
                }
            }

            draw() {
                // Efeito de desvanecimento durante a transição
                const opacity = this.transition < 0.5 ? this.transition * 2 : 1 - (this.transition - 0.5) * 2;

                // Desenhar o raio principal
                ctx.beginPath();
                ctx.moveTo(this.x, this.y);
                for (let i = 0; i < this.segments.length; i++) {
                    const segmentX = lerp(
                        this.segments[i].x,
                        this.targetSegments[i].x,
                        this.transition
                    );
                    const segmentY = lerp(
                        this.segments[i].y,
                        this.targetSegments[i].y,
                        this.transition
                    );
                    ctx.lineTo(segmentX, segmentY);
                }
                ctx.strokeStyle = `rgba(218, 165, 32, ${opacity * 0.8})`;
                ctx.lineWidth = 2;
                ctx.stroke();

                // Efeito de brilho
                ctx.beginPath();
                ctx.moveTo(this.x, this.y);
                for (let i = 0; i < this.segments.length; i++) {
                    const segmentX = lerp(
                        this.segments[i].x,
                        this.targetSegments[i].x,
                        this.transition
                    );
                    const segmentY = lerp(
                        this.segments[i].y,
                        this.targetSegments[i].y,
                        this.transition
                    );
                    ctx.lineTo(segmentX, segmentY);
                }
                ctx.strokeStyle = `rgba(255, 215, 0, ${opacity * 0.3})`;
                ctx.lineWidth = 4;
                ctx.stroke();
            }

            update() {
                // Atualizar a transição
                if (this.transition < 1) {
                    this.transition += this.transitionSpeed;
                } else {
                    // Quando a transição termina, inicia uma nova
                    this.segments = [...this.targetSegments];
                    this.generatePath();
                    this.targetSegments = [...this.segments];
                    this.transition = 0;
                }
            }
        }

        const lightnings = [];
        for (let i = 0; i < 3; i++) {
            lightnings.push(new Lightning());
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < lightnings.length; i++) {
                lightnings[i].draw();
                // Aumentando a chance de atualização para maior velocidade
                if (Math.random() < 0.1) { // De 0.05 para 0.1
                    lightnings[i].transition = 0; // Reinicia a transição
                    lightnings[i].segments = [...lightnings[i].targetSegments];
                    lightnings[i].generatePath();
                    lightnings[i].targetSegments = [...lightnings[i].segments];
                }
                lightnings[i].update();
            }
            requestAnimationFrame(animate);
        }
        animate();
    </script>
</body>
</html>