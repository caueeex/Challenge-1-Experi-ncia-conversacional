-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/05/2025 às 09:18
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `furia_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `game` varchar(50) NOT NULL,
  `team` varchar(100) NOT NULL,
  `opponent` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `matches`
--

INSERT INTO `matches` (`id`, `game`, `team`, `opponent`, `date`, `created_at`) VALUES
(1, 'CS:GO', 'FURIA', 'NAVI', '2025-04-26 15:00:00', '2025-04-26 07:34:40'),
(2, 'VALORANT', 'FURIA', 'Team Liquid', '2025-04-25 19:00:00', '2025-04-26 07:34:40'),
(3, 'LOL', 'FURIA', 'RED Canids', '2025-04-27 14:00:00', '2025-04-26 07:34:40'),
(4, 'R6', 'FURIA', 'FaZe Clan', '2025-04-28 16:00:00', '2025-04-26 07:34:40'),
(5, 'KING', 'FURIA', 'Saiyans FC', '2025-04-29 20:00:00', '2025-04-26 07:34:40'),
(6, 'CS:GO', 'FURIA', 'G2 Esports', '2025-04-30 18:00:00', '2025-04-26 07:34:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `game` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `news`
--

INSERT INTO `news` (`id`, `title`, `summary`, `game`, `date`) VALUES
(1, 'FURIA Vence a NAVI no BLAST Premier', 'Uma vitória emocionante no último mapa!', 'CS:GO', '2025-04-26 07:34:40'),
(2, 'Novo Elenco de Valorant Anunciado', 'FURIA reforça seu time para o VCT 2025.', 'VALORANT', '2025-04-26 07:34:40'),
(3, 'FURIA no CBLOL 2025', 'Nosso time de LoL está pronto para competir!', 'LOL', '2025-04-26 07:34:40'),
(4, 'Vitória Épica no R6 Major', 'FURIA domina a competição internacional.', 'R6', '2025-04-26 07:34:40'),
(5, 'King League: FURIA na Final', 'Nosso time avança para a grande final!', 'KING', '2025-04-26 07:34:40'),
(6, 'FURIA CS:GO na ESL Pro League', 'Próximo desafio contra a G2 Esports.', 'CS:GO', '2025-04-26 07:34:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `points`, `created_at`) VALUES
(1, 'furiafan1', 'fan1@furia.com', '$2y$10$X./gR3z9k2X2hL5kJ5pLueWqL6kV5aV9kXzP3pL2W7tL5uR8vT.', 50, '2025-04-26 07:34:40'),
(2, 'furiafan2', 'fan2@furia.com', '$2y$10$X./gR3z9k2X2hL5kJ5pLueWqL6kV5aV9kXzP3pL2W7tL5uR8vT.', 30, '2025-04-26 07:34:40');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
