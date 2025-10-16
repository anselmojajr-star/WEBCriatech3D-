-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 18/07/2025 às 17:31
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ansel383_criatech3d`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acesso_perfil_horario`
--

CREATE TABLE `acesso_perfil_horario` (
  `id` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `dia_semana` enum('domingo','segunda','terca','quarta','quinta','sexta','sabado') COLLATE utf8_unicode_ci NOT NULL,
  `acesso_liberado` tinyint(1) DEFAULT '0',
  `hora_inicio_1` time DEFAULT NULL,
  `hora_fim_1` time DEFAULT NULL,
  `hora_inicio_2` time DEFAULT NULL,
  `hora_fim_2` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `acesso_usuario_horario`
--

CREATE TABLE `acesso_usuario_horario` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `dia_semana` enum('domingo','segunda','terca','quarta','quinta','sexta','sabado') COLLATE utf8_unicode_ci NOT NULL,
  `acesso_liberado` tinyint(1) DEFAULT '0',
  `hora_inicio_1` time DEFAULT NULL,
  `hora_fim_1` time DEFAULT NULL,
  `hora_inicio_2` time DEFAULT NULL,
  `hora_fim_2` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aditivo`
--

CREATE TABLE `aditivo` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `aditivo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prazo_inicio` date DEFAULT NULL,
  `prazo_fim` date DEFAULT NULL,
  `totalPessoas` int(11) DEFAULT NULL,
  `totalEquipes` int(11) DEFAULT NULL,
  `valor` decimal(12,2) DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

CREATE TABLE `cidades` (
  `id` int(11) NOT NULL,
  `cidade` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `config_ociosidade`
--

CREATE TABLE `config_ociosidade` (
  `id` int(11) NOT NULL,
  `tempo_limite_minutos` int(11) NOT NULL DEFAULT '30',
  `data_atualizacao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contrato`
--

CREATE TABLE `contrato` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `contrato` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoContrato` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prazo_inicio` date NOT NULL,
  `prazo_entrega` date NOT NULL,
  `totalPessoas` int(11) DEFAULT NULL,
  `totalEquipes` int(11) DEFAULT NULL,
  `valorTotal` decimal(12,2) DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_estado` int(11) NOT NULL,
  `id_cidade` int(11) NOT NULL,
  `endereco` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `equipe` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estados`
--

CREATE TABLE `estados` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` char(2) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matricula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e-mail` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `funcionario`
--

INSERT INTO `funcionario` (`id`, `nome`, `matricula`, `cpf`, `e-mail`, `data_cadastro`) VALUES
(1, 'anselmo junior', '1', '78130417200', 'anselmo.jajr@gmail.com', '2025-07-16 16:10:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `loginperfil`
--

CREATE TABLE `loginperfil` (
  `id` int(11) NOT NULL,
  `id_login` int(11) DEFAULT NULL,
  `id_perfil` int(11) DEFAULT NULL,
  `atribuido_por` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `loginperfil`
--

INSERT INTO `loginperfil` (`id`, `id_login`, `id_perfil`, `atribuido_por`, `data_cadastro`) VALUES
(5, 3, 4, NULL, '2025-07-04 12:16:14');

-- --------------------------------------------------------

--
-- Estrutura para tabela `log_acesso_usuarios`
--

CREATE TABLE `log_acesso_usuarios` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_evento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ex: "login", "logout", "atividade"',
  `timestamp_evento` datetime DEFAULT CURRENT_TIMESTAMP,
  `session_id_php` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID da sessão PHP associada (para rastrear a mesma sessão)',
  `ip_acesso` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `log_acesso_usuarios`
--

INSERT INTO `log_acesso_usuarios` (`id`, `id_usuario`, `tipo_evento`, `timestamp_evento`, `session_id_php`, `ip_acesso`) VALUES
(1, 3, 'login', '2025-07-10 21:29:13', 'lfjjab0r5p7jdlqr2r19qitfse', '::1'),
(2, 3, 'login', '2025-07-10 22:36:19', 'lfjjab0r5p7jdlqr2r19qitfse', '::1'),
(3, 3, 'logout', '2025-07-11 00:02:56', 'lfjjab0r5p7jdlqr2r19qitfse', '::1'),
(6, 3, 'login', '2025-07-11 00:11:46', 'lfjjab0r5p7jdlqr2r19qitfse', '::1'),
(7, 3, 'login', '2025-07-11 00:24:22', 'ecsmp9d2oftmj51ftn1t50c61b', '::1'),
(8, 3, 'logout', '2025-07-11 00:45:07', 'ecsmp9d2oftmj51ftn1t50c61b', '::1'),
(10, 3, 'login', '2025-07-11 01:15:34', 'lfjjab0r5p7jdlqr2r19qitfse', '::1'),
(11, 3, 'login', '2025-07-11 10:58:28', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(12, 3, 'login', '2025-07-11 11:30:44', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(13, 3, 'login', '2025-07-11 12:05:17', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(14, 3, 'login', '2025-07-11 12:36:38', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(15, 3, 'login', '2025-07-11 15:09:53', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(16, 3, 'logout', '2025-07-11 15:11:21', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(19, 3, 'login', '2025-07-11 15:13:59', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(20, 3, 'logout', '2025-07-11 15:42:05', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(21, 3, 'login', '2025-07-11 15:42:17', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(22, 3, 'login', '2025-07-11 18:39:30', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(23, 3, 'login', '2025-07-11 19:14:21', '9qnj1634gbim0dqmlfebg3qegh', '::1'),
(24, 3, 'logout', '2025-07-11 19:34:07', '9qnj1634gbim0dqmlfebg3qegh', '::1'),
(28, 3, 'login', '2025-07-11 19:35:08', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(30, 3, 'login', '2025-07-11 21:11:26', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(32, 3, 'login', '2025-07-11 22:11:17', 'bu9dausks3f4uhasbeefaj030q', '::1'),
(48, 3, 'login', '2025-07-12 01:11:00', 'u01jbjnropdphvk6b9ie0j99ie', '::1'),
(49, 3, 'logout', '2025-07-12 01:12:39', 'u01jbjnropdphvk6b9ie0j99ie', '::1'),
(58, 3, 'login', '2025-07-12 10:48:18', 'ia52i5m7jnndc06btr6feq79qp', '::1'),
(59, 3, 'logout', '2025-07-12 10:54:12', 'ia52i5m7jnndc06btr6feq79qp', '::1'),
(64, 3, 'login', '2025-07-12 11:45:18', '4l1jhui8ka4jedaoqjc0rvek9i', '::1'),
(65, 3, 'login', '2025-07-12 18:33:53', '4l1jhui8ka4jedaoqjc0rvek9i', '::1'),
(66, 3, 'logout', '2025-07-12 18:40:16', '4l1jhui8ka4jedaoqjc0rvek9i', '::1'),
(68, 3, 'login', '2025-07-13 19:02:56', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(69, 3, 'logout', '2025-07-13 19:11:48', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(72, 3, 'login', '2025-07-13 19:13:24', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(73, 3, 'logout', '2025-07-13 20:24:48', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(74, 3, 'login', '2025-07-13 20:25:16', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(75, 3, 'login', '2025-07-13 23:20:25', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(76, 3, 'logout', '2025-07-13 23:22:57', 'vvrmhq5pi936niqvkhn2akdtjl', '::1'),
(81, 3, 'login', '2025-07-14 00:34:24', 'pum6tijhu8lucdd1b5aljm47a7', '::1'),
(82, 3, 'login', '2025-07-14 02:02:02', 'pum6tijhu8lucdd1b5aljm47a7', '::1'),
(83, 3, 'login', '2025-07-14 02:02:05', 'pum6tijhu8lucdd1b5aljm47a7', '::1'),
(84, 3, 'logout', '2025-07-14 02:09:19', 'pum6tijhu8lucdd1b5aljm47a7', '::1'),
(86, 3, 'login', '2025-07-15 00:21:55', 'm83rh42jt3huus0oak7f38td04', '::1'),
(87, 3, 'logout', '2025-07-15 00:34:08', 'm83rh42jt3huus0oak7f38td04', '::1'),
(90, 3, 'login', '2025-07-15 00:49:28', 'm83rh42jt3huus0oak7f38td04', '::1'),
(91, 3, 'login', '2025-07-15 01:14:02', 'bim5niv9jrj5p53rqo443erqqi', '::1'),
(92, 3, 'login', '2025-07-15 01:15:49', 'm83rh42jt3huus0oak7f38td04', '::1'),
(93, 3, 'login', '2025-07-15 02:43:35', 'm83rh42jt3huus0oak7f38td04', '::1'),
(94, 3, 'logout', '2025-07-15 03:11:54', 'm83rh42jt3huus0oak7f38td04', '::1'),
(97, 3, 'login', '2025-07-15 03:23:10', 'm83rh42jt3huus0oak7f38td04', '::1'),
(103, 3, 'login', '2025-07-15 11:57:38', 'm83rh42jt3huus0oak7f38td04', '::1'),
(105, 3, 'login', '2025-07-15 12:38:10', 'm83rh42jt3huus0oak7f38td04', '::1'),
(106, 3, 'login', '2025-07-15 15:49:15', 'gd3pfg8ei83e4lhct7j9t1aluu', '::1'),
(108, 3, 'login', '2025-07-15 16:53:03', 'gd3pfg8ei83e4lhct7j9t1aluu', '::1'),
(109, 3, 'logout', '2025-07-15 16:57:04', 'gd3pfg8ei83e4lhct7j9t1aluu', '::1'),
(110, 3, 'login', '2025-07-15 16:57:15', 'gd3pfg8ei83e4lhct7j9t1aluu', '::1'),
(111, 3, 'login', '2025-07-15 17:46:47', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(112, 3, 'logout', '2025-07-15 17:48:14', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(113, 3, 'login', '2025-07-15 17:48:21', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(114, 3, 'logout', '2025-07-15 17:49:42', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(115, 3, 'login', '2025-07-15 17:49:48', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(118, 3, 'logout', '2025-07-15 18:18:10', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(119, 3, 'login', '2025-07-15 18:18:17', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(120, 3, 'logout', '2025-07-15 18:18:40', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(123, 3, 'login', '2025-07-15 18:19:01', 'iu07etvnc3hmgq0sg87opl16ma', '::1'),
(124, 3, 'login', '2025-07-15 19:31:59', 'tgloa6s8qsk2to9hc381kplaig', '127.0.0.1'),
(126, 3, 'login', '2025-07-15 19:40:52', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(128, 3, 'login', '2025-07-15 20:04:11', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(129, 3, 'login', '2025-07-15 20:11:50', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(130, 3, 'login', '2025-07-15 20:11:56', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(131, 3, 'login', '2025-07-15 20:12:07', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(132, 3, 'login', '2025-07-15 20:13:19', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(133, 3, 'login', '2025-07-15 20:17:10', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(134, 3, 'login', '2025-07-15 20:17:19', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(135, 3, 'login', '2025-07-15 21:38:47', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(136, 3, 'login', '2025-07-15 21:39:10', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(137, 3, 'login', '2025-07-15 21:41:00', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(138, 3, 'login', '2025-07-15 21:47:27', 'eue0ahkp633lbi47ic33b0k13u', '::1'),
(139, 3, 'login', '2025-07-15 21:48:15', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(140, 3, 'login', '2025-07-15 21:48:50', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(141, 3, 'login', '2025-07-15 22:38:59', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(142, 3, 'login', '2025-07-16 06:59:01', 'tgloa6s8qsk2to9hc381kplaig', '::1'),
(143, 3, 'login', '2025-07-16 10:27:22', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(144, 3, 'logout', '2025-07-16 10:28:11', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(145, 3, 'login', '2025-07-16 10:28:30', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(146, 3, 'logout', '2025-07-16 10:28:35', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(147, 3, 'login', '2025-07-16 10:29:00', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(148, 3, 'logout', '2025-07-16 11:22:08', '0pgulo4qdu109f6ldiisei7phv', '::1'),
(149, 3, 'login', '2025-07-16 11:22:31', 'b6nro7s4sd983vt7ntisepvahr', '::1'),
(150, 3, 'login', '2025-07-17 14:42:02', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(151, 3, 'login', '2025-07-17 14:42:14', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(152, 3, 'login', '2025-07-17 14:43:08', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(153, 3, 'login', '2025-07-17 15:10:45', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(154, 3, 'logout', '2025-07-17 15:16:15', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(155, 3, 'login', '2025-07-17 15:16:30', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(156, 3, 'logout', '2025-07-17 15:18:20', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(157, 3, 'login', '2025-07-17 15:18:31', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(158, 3, 'logout', '2025-07-17 15:30:16', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(159, 3, 'login', '2025-07-17 16:00:24', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(160, 3, 'login', '2025-07-17 16:02:34', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(161, 3, 'logout', '2025-07-17 16:02:41', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(162, 3, 'login', '2025-07-17 16:03:45', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(163, 3, 'logout', '2025-07-17 16:12:07', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(164, 3, 'login', '2025-07-17 16:12:18', 'f9i91p3s7324l4rb8r13r4h2e3', '::1'),
(165, 3, 'login', '2025-07-17 16:13:59', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(166, 3, 'login', '2025-07-17 16:14:39', 'v3khdd1c1l651fiugjej174vd3', '::1'),
(167, 3, 'login', '2025-07-17 16:41:43', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(168, 3, 'login', '2025-07-17 16:42:02', 'bb6kjjkjhgmk9ap3rft4ae48i6', '::1'),
(169, 3, 'logout', '2025-07-17 16:42:18', 'bb6kjjkjhgmk9ap3rft4ae48i6', '::1'),
(170, 3, 'login', '2025-07-17 16:50:54', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(171, 3, 'logout', '2025-07-17 17:02:09', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(172, 3, 'login', '2025-07-17 17:02:17', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(173, 3, 'logout', '2025-07-17 17:02:22', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(174, 3, 'login', '2025-07-17 17:02:31', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(175, 3, 'logout', '2025-07-17 17:24:02', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(176, 3, 'login', '2025-07-17 17:24:10', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(177, 3, 'login', '2025-07-17 17:24:24', 'omiro9habq7i3ia9mntf4rt6bl', '::1'),
(178, 3, 'login', '2025-07-17 17:25:16', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(179, 3, 'logout', '2025-07-17 17:26:25', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(180, 3, 'login', '2025-07-17 17:26:33', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(181, 3, 'logout', '2025-07-17 17:42:22', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(182, 3, 'login', '2025-07-17 17:42:31', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(183, 3, 'logout', '2025-07-17 17:43:58', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(184, 3, 'login', '2025-07-17 17:44:08', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(185, 3, 'logout', '2025-07-17 17:44:40', 'etg4em90i4t824mrr4idtvohbp', '::1'),
(186, 3, 'login', '2025-07-17 17:46:06', '0bu411m2aaiiip5nqk0ni9bcom', '::1'),
(187, 3, 'logout', '2025-07-17 17:46:16', '0bu411m2aaiiip5nqk0ni9bcom', '::1'),
(188, 3, 'login', '2025-07-17 17:48:05', '0bu411m2aaiiip5nqk0ni9bcom', '::1'),
(189, 3, 'login', '2025-07-18 11:14:44', 'mdpij2pq7ndrt275u67q8mqp4h', '::1'),
(190, 3, 'logout', '2025-07-18 11:59:58', 'mdpij2pq7ndrt275u67q8mqp4h', '::1'),
(191, 3, 'login', '2025-07-18 12:00:17', 'm09v6lkrn68t7rvqg1tv2d0fi2', '::1'),
(192, 3, 'login', '2025-07-18 12:04:39', 'c4rn6staqapkj19d2d2be8oqc3', '::1'),
(193, 3, 'login', '2025-07-18 12:46:07', 'qildioha3lrqd2ok4r7qvluoon', '::1'),
(194, 3, 'login', '2025-07-18 15:13:51', '2parc60tptn6kan9ud53mlucko', '::1'),
(195, 3, 'login', '2025-07-18 15:50:26', '33chbiubi8alvjdp45nn123j8d', '::1'),
(196, 3, 'logout', '2025-07-18 15:58:51', '33chbiubi8alvjdp45nn123j8d', '::1'),
(197, 3, 'login', '2025-07-18 15:59:03', '33chbiubi8alvjdp45nn123j8d', '::1'),
(198, 3, 'login', '2025-07-18 16:28:30', 'jdhn4lr4aun5g24vstiqe7j9o8', '::1'),
(199, 3, 'login', '2025-07-18 16:32:15', 'jdhn4lr4aun5g24vstiqe7j9o8', '::1'),
(200, 3, 'login', '2025-07-18 16:43:35', 'jdhn4lr4aun5g24vstiqe7j9o8', '::1'),
(201, 3, 'logout', '2025-07-18 16:45:20', 'jdhn4lr4aun5g24vstiqe7j9o8', '::1'),
(202, 3, 'login', '2025-07-18 16:45:29', 'jdhn4lr4aun5g24vstiqe7j9o8', '::1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `medidas`
--

CREATE TABLE `medidas` (
  `id` int(11) NOT NULL,
  `medidas` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modulopermissao`
--

CREATE TABLE `modulopermissao` (
  `id` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_perfil` int(11) NOT NULL,
  `visualizar` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rota` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil`
--

CREATE TABLE `perfil` (
  `id` int(11) NOT NULL,
  `perfil` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil`
--

INSERT INTO `perfil` (`id`, `perfil`, `data_cadastro`) VALUES
(4, 'adm', '2025-07-04 11:54:46'),
(5, 'analista', '2025-07-04 11:54:46'),
(6, 'equipe', '2025-07-04 11:54:47'),
(7, 'fiscal', '2025-07-04 11:54:47'),
(8, 'financeiro', '2025-07-04 11:54:47'),
(9, 'Gerente de Permissões', '2025-07-11 16:28:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao`
--

CREATE TABLE `permissao` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `permissao`
--

INSERT INTO `permissao` (`id`, `nome`, `data_cadastro`) VALUES
(8, 'editar', '2025-07-04 22:45:20'),
(9, 'deletar', '2025-07-04 22:46:25'),
(10, 'cadastrar', '2025-07-04 22:52:57'),
(11, 'relatorio', '2025-07-04 22:52:57'),
(12, 'projeto', '2025-07-04 22:55:00'),
(13, 'adm', '2025-07-04 22:55:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `projeto`
--

CREATE TABLE `projeto` (
  `id` int(11) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `projeto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoProjeto` varchar(65) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TotalPontos` int(11) DEFAULT NULL,
  `perimetro` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prazo_inicio` date DEFAULT NULL,
  `prazo_entrega` date DEFAULT NULL,
  `descricao` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `p_atividade`
--

CREATE TABLE `p_atividade` (
  `id` int(11) NOT NULL,
  `id_servico` int(11) NOT NULL,
  `atividade` varchar(56) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `p_estrutura`
--

CREATE TABLE `p_estrutura` (
  `id` int(11) NOT NULL,
  `estrutura` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_atividade` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `codigoEstrutura` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `p_material`
--

CREATE TABLE `p_material` (
  `id` int(11) NOT NULL,
  `id_medida` int(11) NOT NULL,
  `material` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoMaterial` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servico`
--

CREATE TABLE `servico` (
  `id` int(11) NOT NULL,
  `servico` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` enum('s','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes_ativas`
--

CREATE TABLE `sessoes_ativas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `session_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_login` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `sessoes_ativas`
--

INSERT INTO `sessoes_ativas` (`id`, `id_usuario`, `session_id`, `ip`, `data_login`) VALUES
(89, 11, 'tgloa6s8qsk2to9hc381kplaig', '::1', '2025-07-15 18:18:48'),
(94, 4, 'eue0ahkp633lbi47ic33b0k13u', '::1', '2025-07-15 19:41:34'),
(149, 3, 'jdhn4lr4aun5g24vstiqe7j9o8', '::1', '2025-07-18 16:45:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuariopermissao`
--

CREATE TABLE `usuariopermissao` (
  `id` int(11) NOT NULL,
  `id_loginPerfil` int(11) DEFAULT NULL,
  `editar` double DEFAULT NULL,
  `deletar` double DEFAULT NULL,
  `cadastrar` double DEFAULT NULL,
  `autorizado_por` int(11) DEFAULT NULL,
  `cadastrado_por` int(11) DEFAULT NULL,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuariopermissao`
--

INSERT INTO `usuariopermissao` (`id`, `id_loginPerfil`, `editar`, `deletar`, `cadastrar`, `autorizado_por`, `cadastrado_por`, `ultima_alteracao_por`, `data_cadastro`, `data_ultima_alteracao`) VALUES
(2, 5, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-04 23:03:23', '2025-07-10 17:38:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_funcionario` int(11) DEFAULT NULL,
  `username` varchar(70) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `tentativas` int(11) DEFAULT '0',
  `bloqueado` tinyint(1) DEFAULT '0',
  `ativo` tinyint(4) DEFAULT '1',
  `criado_por` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_alteracao_por` int(11) DEFAULT NULL,
  `data_ultima_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_funcionario`, `username`, `senha`, `tentativas`, `bloqueado`, `ativo`, `criado_por`, `data_cadastro`, `ultima_alteracao_por`, `data_ultima_alteracao`, `data_inicio`, `data_fim`) VALUES
(3, 1, 'anselmo', '$2y$10$HZP3Z4AY52jECy3Lz4cmuO9dXlhb8luXX8TKxZ3otI2lf118zkaVG', 0, 0, 1, NULL, '2025-07-02 19:30:54', NULL, '2025-07-17 16:03:45', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_modulo_permissao`
--

CREATE TABLE `usuario_modulo_permissao` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `permitido` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Negado, 1=Permitido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acesso_perfil_horario`
--
ALTER TABLE `acesso_perfil_horario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Índices de tabela `acesso_usuario_horario`
--
ALTER TABLE `acesso_usuario_horario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `aditivo`
--
ALTER TABLE `aditivo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aditivo` (`aditivo`),
  ADD KEY `fk_contrato_aditivo` (`id_contrato`),
  ADD KEY `fk_aditivo_ultima_alteracao_por` (`ultima_alteracao_por`);

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cidade_UNIQUE` (`cidade`);

--
-- Índices de tabela `config_ociosidade`
--
ALTER TABLE `config_ociosidade`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `contrato`
--
ALTER TABLE `contrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contrato` (`contrato`),
  ADD UNIQUE KEY `codigoContrato` (`codigoContrato`),
  ADD KEY `fk_empresa` (`id_empresa`),
  ADD KEY `id_servico_idx` (`id_servico`),
  ADD KEY `fk_contrato_ultima_alteracao_por` (`ultima_alteracao_por`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`),
  ADD KEY `id_estado` (`id_estado`),
  ADD KEY `id_cidade` (`id_cidade`),
  ADD KEY `fk_empresa_ultima_alteracao_por` (`ultima_alteracao_por`);

--
-- Índices de tabela `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipe` (`equipe`);

--
-- Índices de tabela `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD UNIQUE KEY `cpf_UNIQUE` (`cpf`),
  ADD UNIQUE KEY `e-mail_UNIQUE` (`e-mail`);

--
-- Índices de tabela `loginperfil`
--
ALTER TABLE `loginperfil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_loginPerfil` (`id_login`),
  ADD KEY `fk_perfil_loginPerfil` (`id_perfil`);

--
-- Índices de tabela `log_acesso_usuarios`
--
ALTER TABLE `log_acesso_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_usuario` (`id_usuario`),
  ADD KEY `idx_tipo_evento` (`tipo_evento`),
  ADD KEY `idx_timestamp_evento` (`timestamp_evento`);

--
-- Índices de tabela `medidas`
--
ALTER TABLE `medidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `medidas` (`medidas`);

--
-- Índices de tabela `modulopermissao`
--
ALTER TABLE `modulopermissao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_perfil` (`id_perfil`);

--
-- Índices de tabela `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_UNIQUE` (`nome`),
  ADD UNIQUE KEY `rota_UNIQUE` (`rota`);

--
-- Índices de tabela `perfil`
--
ALTER TABLE `perfil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `perfil` (`perfil`);

--
-- Índices de tabela `permissao`
--
ALTER TABLE `permissao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `projeto`
--
ALTER TABLE `projeto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projeto` (`projeto`),
  ADD UNIQUE KEY `codigoProjeto` (`codigoProjeto`),
  ADD KEY `fk_contrato` (`id_contrato`),
  ADD KEY `fk_projeto_ultima_alteracao_por` (`ultima_alteracao_por`);

--
-- Índices de tabela `p_atividade`
--
ALTER TABLE `p_atividade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atividade` (`atividade`),
  ADD KEY `fk_servico` (`id_servico`);

--
-- Índices de tabela `p_estrutura`
--
ALTER TABLE `p_estrutura`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigoEstrutura` (`codigoEstrutura`),
  ADD KEY `fk_atividade` (`id_atividade`),
  ADD KEY `fk_material` (`id_material`);

--
-- Índices de tabela `p_material`
--
ALTER TABLE `p_material`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material` (`material`),
  ADD UNIQUE KEY `codigoMaterial` (`codigoMaterial`),
  ADD KEY `fk_medida` (`id_medida`);

--
-- Índices de tabela `servico`
--
ALTER TABLE `servico`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `servico` (`servico`);

--
-- Índices de tabela `sessoes_ativas`
--
ALTER TABLE `sessoes_ativas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuariopermissao`
--
ALTER TABLE `usuariopermissao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_loginPerfil` (`id_loginPerfil`),
  ADD KEY `fk_up_cadastrado_por` (`cadastrado_por`),
  ADD KEY `fk_up_ultima_alteracao_por` (`ultima_alteracao_por`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_UNIQUE` (`username`),
  ADD KEY `fk_ultima_alteracao_por` (`ultima_alteracao_por`),
  ADD KEY `id_funcionario_idx` (`id_funcionario`);

--
-- Índices de tabela `usuario_modulo_permissao`
--
ALTER TABLE `usuario_modulo_permissao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usuario_modulo_unique` (`id_usuario`,`id_modulo`),
  ADD KEY `fk_ump_usuario` (`id_usuario`),
  ADD KEY `fk_ump_modulo` (`id_modulo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acesso_perfil_horario`
--
ALTER TABLE `acesso_perfil_horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `acesso_usuario_horario`
--
ALTER TABLE `acesso_usuario_horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aditivo`
--
ALTER TABLE `aditivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `config_ociosidade`
--
ALTER TABLE `config_ociosidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contrato`
--
ALTER TABLE `contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `loginperfil`
--
ALTER TABLE `loginperfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `log_acesso_usuarios`
--
ALTER TABLE `log_acesso_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT de tabela `medidas`
--
ALTER TABLE `medidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modulopermissao`
--
ALTER TABLE `modulopermissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de tabela `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `perfil`
--
ALTER TABLE `perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `permissao`
--
ALTER TABLE `permissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `projeto`
--
ALTER TABLE `projeto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `p_atividade`
--
ALTER TABLE `p_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `p_estrutura`
--
ALTER TABLE `p_estrutura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `p_material`
--
ALTER TABLE `p_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servico`
--
ALTER TABLE `servico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sessoes_ativas`
--
ALTER TABLE `sessoes_ativas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT de tabela `usuariopermissao`
--
ALTER TABLE `usuariopermissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `usuario_modulo_permissao`
--
ALTER TABLE `usuario_modulo_permissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `acesso_perfil_horario`
--
ALTER TABLE `acesso_perfil_horario`
  ADD CONSTRAINT `acesso_perfil_horario_ibfk_1` FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `acesso_usuario_horario`
--
ALTER TABLE `acesso_usuario_horario`
  ADD CONSTRAINT `acesso_usuario_horario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `aditivo`
--
ALTER TABLE `aditivo`
  ADD CONSTRAINT `fk_aditivo_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_contrato_aditivo` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`);

--
-- Restrições para tabelas `contrato`
--
ALTER TABLE `contrato`
  ADD CONSTRAINT `fk_contrato_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `id_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id`);

--
-- Restrições para tabelas `empresas`
--
ALTER TABLE `empresas`
  ADD CONSTRAINT `fk_empresa_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `id_cidade` FOREIGN KEY (`id_cidade`) REFERENCES `cidades` (`id`),
  ADD CONSTRAINT `id_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id`);

--
-- Restrições para tabelas `loginperfil`
--
ALTER TABLE `loginperfil`
  ADD CONSTRAINT `fk_login_loginPerfil` FOREIGN KEY (`id_login`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_perfil_loginPerfil` FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id`);

--
-- Restrições para tabelas `log_acesso_usuarios`
--
ALTER TABLE `log_acesso_usuarios`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `modulopermissao`
--
ALTER TABLE `modulopermissao`
  ADD CONSTRAINT `modulopermissao_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`),
  ADD CONSTRAINT `modulopermissao_ibfk_2` FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id`);

--
-- Restrições para tabelas `projeto`
--
ALTER TABLE `projeto`
  ADD CONSTRAINT `fk_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`),
  ADD CONSTRAINT `fk_projeto_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `p_atividade`
--
ALTER TABLE `p_atividade`
  ADD CONSTRAINT `fk_servico_atividade` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id`);

--
-- Restrições para tabelas `p_estrutura`
--
ALTER TABLE `p_estrutura`
  ADD CONSTRAINT `fk_atividade_estrutura` FOREIGN KEY (`id_atividade`) REFERENCES `p_atividade` (`id`),
  ADD CONSTRAINT `fk_material_estrutura` FOREIGN KEY (`id_material`) REFERENCES `p_material` (`id`);

--
-- Restrições para tabelas `p_material`
--
ALTER TABLE `p_material`
  ADD CONSTRAINT `fk_medida_material` FOREIGN KEY (`id_medida`) REFERENCES `medidas` (`id`);

--
-- Restrições para tabelas `usuariopermissao`
--
ALTER TABLE `usuariopermissao`
  ADD CONSTRAINT `fk_up_cadastrado_por` FOREIGN KEY (`cadastrado_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_up_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `usuariopermissao_ibfk_1` FOREIGN KEY (`id_loginPerfil`) REFERENCES `loginperfil` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_ultima_alteracao_por` FOREIGN KEY (`ultima_alteracao_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `id_funcionario` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `usuario_modulo_permissao`
--
ALTER TABLE `usuario_modulo_permissao`
  ADD CONSTRAINT `fk_ump_modulo` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ump_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
