const WebSocket = require("ws");
const http = require("http");
const axios = require("axios");

const server = http.createServer();
const wss = new WebSocket.Server({ server });

// Configurações para APIs externas
const PANDASCORE_API_KEY = "gW4GaCKRtZI0irC8DqKVMSRXG6fwG1_U8LabmVnOb3hsq0sbEVM";

// Função para buscar partidas futuras com PandaScore (filtrando FURIA)
async function getUpcomingMatchesPandaScore() {
    try {
        const response = await axios.get("https://api.pandascore.co/matches/upcoming", {
            params: { token: PANDASCORE_API_KEY }
        });
        const furiaMatches = response.data.filter(match => 
            match.opponents && match.opponents.some(opponent => opponent.opponent && opponent.opponent.name.toLowerCase().includes("furia"))
        );
        return furiaMatches.map(match => ({
            id: match.id,
            game: match.videogame.name.toUpperCase(),
            date: match.scheduled_at ? new Date(match.scheduled_at).toLocaleString('pt-BR') : 'TBD',
            team: match.opponents && match.opponents.length > 0 ? match.opponents[0].opponent.name : 'TBD',
            opponent: match.opponents && match.opponents.length > 1 ? match.opponents[1].opponent.name : 'TBD',
            location: match.tournament ? match.tournament.name : 'TBD'
        }));
    } catch (error) {
        console.error("Erro ao buscar partidas com PandaScore:", error.message);
        return [];
    }
}

// Endpoint HTTP para fornecer partidas ao frontend
server.on("request", async (req, res) => {
    if (req.url === "/upcoming-matches") {
        res.setHeader("Content-Type", "application/json");
        res.setHeader("Access-Control-Allow-Origin", "*");
        try {
            const matches = await getUpcomingMatchesPandaScore();
            res.writeHead(200);
            res.end(JSON.stringify(matches));
        } catch (error) {
            console.error("Erro no endpoint /upcoming-matches:", error.message);
            res.writeHead(500);
            res.end(JSON.stringify({ error: "Erro ao buscar partidas. Tente novamente mais tarde!" }));
        }
    } else {
        res.writeHead(404);
        res.end();
    }
});

// Estado do usuário para controlar a conversa
const userStates = new Map();

// Função para gerar o menu principal
function getMainMenu() {
    return `🔥 O que você quer saber, Furioso?\n1 - Jogos\n2 - Vitórias\n3 - Torcida\n4 - Elenco\n5 - Jogadores\n6 - Estatísticas\nDigite o número ou comando (ex.: "jogos")!`;
}

// Função para validar comandos aproximados
function isSimilarCommand(input, command) {
    const cleanInput = input.toLowerCase().replace(/[^a-z0-9]/g, '');
    const cleanCommand = command.toLowerCase().replace(/[^a-z0-9]/g, '');
    return cleanInput.includes(cleanCommand) || cleanCommand.includes(cleanInput);
}

// Enviar atualizações periódicas para todos os clientes conectados
setInterval(async () => {
    try {
        const matchesPandaScore = await getUpcomingMatchesPandaScore();
        const message = (matchesPandaScore.length > 0)
            ? `🔔 Atenção, Furioso! Próxima partida da FURIA: ${matchesPandaScore[0].team} vs ${matchesPandaScore[0].opponent} em ${matchesPandaScore[0].date} (${matchesPandaScore[0].game})`
            : "🔔 Nenhuma partida da FURIA agendada no momento. Fique ligado!";
        wss.clients.forEach(client => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(JSON.stringify({ type: "chatbot_response", message }));
            }
        });
    } catch (error) {
        console.error("Erro ao enviar atualização periódica:", error.message);
    }
}, 60000);

// WebSocket para chat em tempo real
wss.on("connection", (ws) => {
    console.log("Novo cliente conectado");

    // Enviar mensagem de boas-vindas
    ws.send(JSON.stringify({ 
        type: "chatbot_response", 
        message: "🖤⚡️ Salve, Furioso! Bem-vindo ao FURIA Chat! Digite 'oi' ou qualquer coisa pra começar! 😎" 
    }));

    // Gerar um ID único para o cliente
    const clientId = Date.now().toString() + Math.random().toString(36).substr(2, 9);
    userStates.set(clientId, { step: "initial", lastTeam: null });

    // Manipular mensagens recebidas
    ws.on("message", async (message) => {
        let data;
        try {
            data = JSON.parse(message.toString());
        } catch (error) {
            console.error("Erro ao parsear mensagem:", error.message);
            return;
        }

        console.log("Mensagem recebida:", data);

        if (data.type === "chat") {
            let userState = userStates.get(clientId) || { step: "initial", lastTeam: null };
            let response = null;

            const msg = data.message.toLowerCase().trim();

            // Comandos diretos
            if (msg === "oi" || msg === "olá" || msg === "ola") {
                response = `E aí, Furioso? 🖤⚡️ Vamos falar sobre a FURIA! ${getMainMenu()}`;
                userState.step = "menu";
            } else if (isSimilarCommand(msg, "jogos") || msg === "1" || msg === "/proximojogo") {
                const matchesPandaScore = await getUpcomingMatchesPandaScore();
                if (matchesPandaScore.length > 0) {
                    response = `🎮 Próximos jogos da FURIA:\n${matchesPandaScore.slice(0, 3).map((m, i) => `${i + 1}. ${m.team} vs ${m.opponent} (${m.game}) em ${m.date} no ${m.location}`).join('\n')}\n\nQuer mais detalhes sobre algum jogo? Digite o número do jogo (ex.: 1).`;
                    userState.step = "jogos";
                    userState.matches = matchesPandaScore;
                } else {
                    response = "😔 Nenhuma partida da FURIA agendada no momento. Volte ao menu com 'oi'!";
                    userState.step = "initial";
                }
            } else if (userState.step === "jogos" && /^\d+$/.test(msg)) {
                const matchIndex = parseInt(msg) - 1;
                if (userState.matches && userState.matches[matchIndex]) {
                    const match = userState.matches[matchIndex];
                    response = `🎮 Detalhes do jogo:\n- Jogo: ${match.game}\n- Times: ${match.team} vs ${match.opponent}\n- Data: ${match.date}\n- Local: ${match.location}\n\nVoltar ao menu? Digite 'oi'.`;
                    userState.step = "initial";
                } else {
                    response = "Jogo inválido! Tente outro número ou volte ao menu com 'oi'.";
                }
            } else if (isSimilarCommand(msg, "vitórias") || msg === "2" || msg === "/ultimajogada") {
                response = "🏆 Última grande vitória da FURIA: 2x0 contra a NAVI no BLAST Premier (CS:GO)! Quer saber mais? Digite 'detalhes' ou volte ao menu com 'oi'.";
                userState.step = "vitorias";
            } else if (userState.step === "vitorias" && isSimilarCommand(msg, "detalhes")) {
                response = "🔥 FURIA arrasou contra a NAVI por 2x0 no BLAST Premier!\n- Mapas: Mirage (16-12) e Nuke (16-14)\n- Destaque: KSCERATO com 45 kills!\n\nVoltar ao menu? Digite 'oi'.";
                userState.step = "initial";
            } else if (isSimilarCommand(msg, "torcida") || msg === "3" || msg === "/torcida") {
                response = "🖤⚡️ A torcida FURIA é lendária! Quer mandar um grito de apoio? Digite sua mensagem agora (ex.: 'Vamos FURIA!') ou volte ao menu com 'oi'.";
                userState.step = "torcida";
            } else if (userState.step === "torcida") {
                response = `Valeu pelo apoio, Furioso! 🖤⚡️ Sua mensagem "${data.message}" foi registrada! Voltar ao menu? Digite 'oi'.`;
                userState.step = "initial";
            } else if (isSimilarCommand(msg, "elenco") || msg === "4") {
                response = "👥 Quer saber sobre o elenco de qual time?\n- CS:GO\n- VALORANT\n- LOL\n- RAINBOW SIX\n- KING LEAGUE\nDigite o nome do jogo (ex.: 'CS:GO') ou volte ao menu com 'oi'.";
                userState.step = "elenco";
            } else if (userState.step === "elenco") {
                switch (msg.toLowerCase()) {
                    case "cs:go":
                    case "csgo":
                        response = "👥 Elenco de CS:GO: arT, yuurih, KSCERATO, FalleN, chelo.\n\nQuer saber sobre outro time ou voltar ao menu? Digite o nome do jogo ou 'oi'.";
                        userState.lastTeam = "CS:GO";
                        break;
                    case "valorant":
                        response = "👥 Elenco de VALORANT: mwzera, kon4n, liazzi, havoc, nzr.\n\nQuer saber sobre outro time ou voltar ao menu? Digite o nome do jogo ou 'oi'.";
                        userState.lastTeam = "VALORANT";
                        break;
                    case "lol":
                    case "league of legends":
                        response = "👥 Elenco de LOL: fNb, Grevthar, RedBert, Netuno, Trigo.\n\nQuer saber sobre outro time ou voltar ao menu? Digite o nome do jogo ou 'oi'.";
                        userState.lastTeam = "LOL";
                        break;
                    case "rainbow six":
                    case "r6":
                        response = "👥 Elenco de RAINBOW SIX: FelipoX, Jv92, Kheyze, Fntzy, Soulz1.\n\nQuer saber sobre outro time ou voltar ao menu? Digite o nome do jogo ou 'oi'.";
                        userState.lastTeam = "RAINBOW SIX";
                        break;
                    case "king league":
                        response = "👥 Elenco de KING LEAGUE: Ainda em formação, mas cheio de craques! Fique ligado.\n\nQuer saber sobre outro time ou voltar ao menu? Digite o nome do jogo ou 'oi'.";
                        userState.lastTeam = "KING LEAGUE";
                        break;
                    default:
                        response = "Jogo não encontrado! 😔 Tente 'CS:GO', 'VALORANT', 'LOL', 'RAINBOW SIX' ou 'KING LEAGUE'. Ou volte ao menu com 'oi'.";
                        break;
                }
            } else if (isSimilarCommand(msg, "jogadores") || msg === "5") {
                response = "🌟 Quer saber sobre um jogador específico? Digite o nome (ex.: 'KSCERATO') ou escolha um time primeiro (ex.: 'CS:GO'). Ou volte ao menu com 'oi'.";
                userState.step = "jogadores";
            } else if (userState.step === "jogadores") {
                const players = {
                    "kscerato": { team: "CS:GO", info: "KSCERATO é o rei dos clutches! Destaque no BLAST Premier com 45 kills contra a NAVI." },
                    "mwzera": { team: "VALORANT", info: "mwzera é a estrela do VALORANT, conhecido por jogadas agressivas e precisão absurda." },
                    "fallen": { team: "CS:GO", info: "FalleN, a lenda do CS:GO, lidera a FURIA com estratégias brilhantes." },
                };
                const player = Object.keys(players).find(p => msg.toLowerCase().includes(p));
                if (player) {
                    response = `🌟 ${player.toUpperCase()} (${players[player].team}): ${players[player].info}\n\nQuer saber sobre outro jogador ou voltar ao menu? Digite o nome ou 'oi'.`;
                } else if (["cs:go", "valorant", "lol", "rainbow six", "king league"].includes(msg.toLowerCase())) {
                    response = `👥 Você escolheu ${msg.toUpperCase()}. Digite o nome de um jogador (ex.: 'KSCERATO') ou volte ao menu com 'oi'.`;
                    userState.lastTeam = msg.toUpperCase();
                } else {
                    response = "Jogador ou time não encontrado! 😔 Tente 'KSCERATO', 'mwzera' ou escolha um time (ex.: 'CS:GO'). Ou volte ao menu com 'oi'.";
                }
            } else if (isSimilarCommand(msg, "estatísticas") || msg === "6") {
                response = "📊 Quer estatísticas de qual time?\n- CS:GO\n- VALORANT\n- LOL\n- RAINBOW SIX\n- KING LEAGUE\nDigite o nome do jogo ou volte ao menu com 'oi'.";
                userState.step = "estatisticas";
            } else if (userState.step === "estatisticas") {
                switch (msg.toLowerCase()) {
                    case "cs:go":
                    case "csgo":
                        response = "📊 Estatísticas de CS:GO: 65% win rate em 2025, KSCERATO com 1.25 K/D médio.\n\nQuer mais detalhes ou voltar ao menu? Digite 'detalhes' ou 'oi'.";
                        userState.lastTeam = "CS:GO";
                        userState.step = "estatisticas_detalhes";
                        break;
                    case "valorant":
                        response = "📊 Estatísticas de VALORANT: 70% win rate em VCT 2025, mwzera com 220 ACS médio.\n\nQuer mais detalhes ou voltar ao menu? Digite 'detalhes' ou 'oi'.";
                        userState.lastTeam = "VALORANT";
                        userState.step = "estatisticas_detalhes";
                        break;
                    default:
                        response = "Jogo não encontrado! 😔 Tente 'CS:GO', 'VALORANT', 'LOL', 'RAINBOW SIX' ou 'KING LEAGUE'. Ou volte ao menu com 'oi'.";
                        break;
                }
            } else if (userState.step === "estatisticas_detalhes" && isSimilarCommand(msg, "detalhes")) {
                response = `📊 Mais estatísticas de ${userState.lastTeam}: Em 2025, a FURIA jogou 50 partidas, com 35 vitórias e 15 derrotas.\n\nVoltar ao menu? Digite 'oi'.`;
                userState.step = "initial";
            } else if (isSimilarCommand(msg, "kingleague") || msg === "/kingleague") {
                response = "👑 King League é puro fogo! A FURIA está liderando com 5 vitórias seguidas. Quer ver os destaques? Digite 'detalhes' ou volte ao menu com 'oi'.";
                userState.step = "kingleague";
            } else if (userState.step === "kingleague" && isSimilarCommand(msg, "detalhes")) {
                response = "🔥 Destaques da FURIA na King League: Goleada de 7x2 contra o PAIN! Veja o vídeo: [link].\n\nVoltar ao menu? Digite 'oi'.";
                userState.step = "initial";
            } else {
                response = `Não entendi, Furioso! 😅 Tente 'oi' para ver o menu ou comandos como 'jogos', 'vitórias', 'torcida', 'elenco', 'jogadores' ou 'estatísticas'.`;
                userState.step = "initial";
            }

            // Atualizar o estado do usuário
            userStates.set(clientId, userState);

            // Enviar resposta apenas para o cliente que enviou a mensagem
            if (response) {
                ws.send(JSON.stringify({ type: "chatbot_response", message: response }));
            }
        } else if (data.type === "match_update") {
            wss.clients.forEach(client => {
                if (client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify({ type: "match_update", matchId: data.matchId, score: data.score }));
                }
            });
        }
    });

    // Manipular erros na conexão
    ws.on("error", (error) => {
        console.error("Erro na conexão WebSocket:", error.message);
        ws.send(JSON.stringify({ type: "chatbot_response", message: "⚠️ Ops, algo deu errado! Tente novamente ou volte ao menu com 'oi'." }));
    });

    // Manipular desconexão
    ws.on("close", () => {
        console.log("Cliente desconectado:", clientId);
        userStates.delete(clientId);
    });
});

// Iniciar servidor
server.listen(8080, () => {
    console.log("Servidor de chatbot rodando na porta 8080");
});