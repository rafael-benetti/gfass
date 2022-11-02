-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 02/11/2022 às 18:53
-- Versão do servidor: 10.4.14-MariaDB
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `asaisurf_sis`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(9) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `usuarios_id` int(9) NOT NULL,
  `tipo` varchar(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `descricao`, `chave`, `usuarios_id`, `tipo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'gasolina', 'd1c663d4bff802e8a4a8f0e870f4b64e', 2, 'd', '2022-10-13 06:13:43', '2022-10-20 15:23:41', '2022-10-20 15:23:41'),
(2, 'Patrocínio', '324d44d0eef233414587969f018e845f', 2, 'r', '2022-10-13 06:13:52', '2022-10-20 15:24:09', NULL),
(3, 'ddd', '40cb33fe4e9cba9e2a1ccb6794486a6f', 2, 'd', '2022-10-19 20:56:59', '2022-10-20 15:23:36', '2022-10-20 15:23:36'),
(4, 'rrrr', 'eed76f7432e21880a3b3a5de01ede862', 2, 'd', '2022-10-19 20:57:12', '2022-10-20 15:23:26', '2022-10-20 15:23:26'),
(5, 'ASD', 'bef718d339a1fe9d18bd6b0536157571', 2, 'd', '2022-10-19 21:10:26', '2022-10-20 15:23:32', '2022-10-20 15:23:32'),
(6, 'Gerais', '97e3972603e6cdc7c629d12a1c4e764c', 2, 'd', '2022-10-20 15:24:56', '2022-10-20 15:24:56', NULL),
(7, 'Inscrição', '6420dd6aabe37d28aa1d67d5249901f0', 2, 'r', '2022-10-20 15:25:24', '2022-10-20 15:25:24', NULL),
(8, 'Patrocínio (Receita)', '6b2d4c8af85e3fb586d7c37ddb945dfe', 9, 'r', '2022-11-02 11:22:01', '2022-11-02 17:21:37', NULL),
(9, 'Inscrição (Receita)', 'bd02b2168d15f9566020455b9b4b8d2e', 9, 'r', '2022-11-02 14:19:02', '2022-11-02 17:21:31', NULL),
(10, 'Diversas (Receita)', '06a9b768f60f61079f86ee4e6222ad54', 9, 'r', '2022-11-02 14:19:27', '2022-11-02 17:21:15', NULL),
(11, '(Despesa) Diversas', 'b5b08fc6730032f33140a453360b02fb', 9, 'd', '2022-11-02 14:19:42', '2022-11-02 18:10:18', NULL),
(12, 'Apoios (Receita)	', '33b01235dbb85ae5d8b749b704272ea4', 9, 'r', '2022-11-02 17:22:30', '2022-11-02 17:28:28', NULL),
(13, 'Apoios (Receita)', 'ae9dd7110064a8ed74202d9bda22fb75', 9, 'r', '2022-11-02 17:26:26', '2022-11-02 17:54:25', '2022-11-02 17:54:25'),
(14, '(Despesa) Eventos', '22b99973aeea26b16426eded7f0d0f4c', 9, 'd', '2022-11-02 18:10:38', '2022-11-02 18:10:38', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos`
--

CREATE TABLE `lancamentos` (
  `id` int(9) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `usuarios_id` int(9) NOT NULL,
  `categorias_id` int(9) NOT NULL,
  `valor` decimal(8,2) UNSIGNED NOT NULL,
  `anexo` varchar(255) NOT NULL COMMENT 'Anexo',
  `data` date NOT NULL,
  `notificar_por_email` tinyint(1) NOT NULL DEFAULT 2 COMMENT 'Indica se será enviado um email de notificação quando o lançamento vencer. 1 => SIM; 2 => NÃO',
  `consolidado` tinyint(1) NOT NULL DEFAULT 2 COMMENT 'Indica se o lançamento entrará nos cálculos de saldo. 1 => SIM; 2 => NÃO',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `lancamentos`
--

INSERT INTO `lancamentos` (`id`, `descricao`, `chave`, `usuarios_id`, `categorias_id`, `valor`, `anexo`, `data`, `notificar_por_email`, `consolidado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(73, 'Surf Pro', 'f6d8ccfa6235b63ad6f31e0dda619aa6', 9, 14, '700.00', '', '2022-02-05', 2, 1, '2022-11-02 18:16:28', '2022-11-02 18:16:28', NULL),
(74, 'Glauco Fotos', '32ccb7775aacaf440375234315dd751c', 9, 14, '150.00', '', '2022-02-05', 2, 1, '2022-11-02 18:16:42', '2022-11-02 18:16:42', NULL),
(72, 'Jean Joga', 'ae095d2b3402e70bab334b34a723f5cd', 9, 14, '80.00', '', '2022-02-05', 2, 1, '2022-11-02 18:16:15', '2022-11-02 18:16:15', NULL),
(70, 'Fábio Careca', '0884579cf7609d9caa0d20e7fa7d847d', 9, 14, '150.00', '', '2022-02-05', 2, 1, '2022-11-02 18:15:49', '2022-11-02 18:15:49', NULL),
(71, 'Juliano Seco', 'a80abbbc2c807c74ba43c85b2309611c', 9, 14, '250.00', '', '2022-02-05', 2, 1, '2022-11-02 18:16:02', '2022-11-02 18:16:02', NULL),
(69, 'Erivelton', 'fb0e4bc0cf93d18108b44fa7c553c5ea', 9, 14, '350.00', '', '2022-02-05', 2, 1, '2022-11-02 18:15:33', '2022-11-02 18:15:33', NULL),
(68, 'Zenoni', '8fab3a063669b312c92da4c1c3c3342a', 9, 14, '250.00', '', '2022-02-05', 2, 1, '2022-11-02 18:15:12', '2022-11-02 18:15:12', NULL),
(66, 'Lona do Evento', '621d6c4f82b1e2c0a33488ebf670fcf1', 9, 14, '120.00', '', '2022-02-05', 2, 1, '2022-11-02 18:14:32', '2022-11-02 18:14:32', NULL),
(67, 'rodrigo ajuda de custo', '41998817a3868e80f9bd7dabfe3270d9', 9, 14, '250.00', '', '2022-02-05', 2, 1, '2022-11-02 18:14:53', '2022-11-02 18:14:53', NULL),
(65, 'Marmitas', '3f1ef06359cb3be3288b5d96e59d3e60', 9, 14, '182.00', '', '2022-02-05', 2, 1, '2022-11-02 18:14:05', '2022-11-02 18:14:05', NULL),
(64, 'laus registro civil', 'dcaf850dad0bb97542d0b863d5c5bdb2', 9, 11, '125.33', '', '2022-02-06', 2, 1, '2022-11-02 18:09:14', '2022-11-02 18:09:14', NULL),
(63, 'litoral corretora', '239a28752de64794ef712893915f260e', 9, 11, '120.00', '', '2022-02-06', 2, 1, '2022-11-02 18:08:53', '2022-11-02 18:08:53', NULL),
(62, 'cc unicred união', 'cc0a2a746d65d45f4a164b0d2f5a1d30', 9, 11, '70.00', '', '2022-02-06', 2, 1, '2022-11-02 18:08:34', '2022-11-02 18:08:34', NULL),
(60, 'Certificado PFA1', 'dfb66b1ec72a1c307936c0bae2d4274f', 9, 11, '120.00', '', '2022-02-06', 2, 1, '2022-11-02 18:07:49', '2022-11-02 18:07:49', NULL),
(59, 'arrecadação FRJ', '53559c25b16da5103e53cf6d4a17812c', 9, 11, '285.17', '', '2022-02-06', 2, 1, '2022-11-02 18:07:15', '2022-11-02 18:07:15', NULL),
(58, 'operador nacional sistema de registro eletonico', 'd6e4c9db2064dd77b5e0a304a93adf29', 9, 11, '75.99', '', '2022-02-06', 2, 1, '2022-11-02 18:06:27', '2022-11-02 18:06:27', NULL),
(55, 'Apoio de empresas', 'a6ce6f29d87cd2d71574cbab9e43f152', 9, 12, '600.00', '', '2022-02-05', 2, 1, '2022-11-02 17:57:29', '2022-11-02 17:57:29', NULL),
(56, 'Inscrições Atletas', '9cb7e61c04761ac2f05b419bb4fc7be1', 9, 9, '3080.00', '', '2022-02-05', 2, 1, '2022-11-02 17:57:57', '2022-11-02 17:57:57', NULL),
(75, 'Águas', '4ba6cc8a3ef3b862c2298b1c790ac57c', 9, 14, '32.48', '', '2022-02-05', 2, 1, '2022-11-02 18:17:05', '2022-11-02 18:17:05', NULL),
(76, 'TNT Para mesa do evento (tecido)', '3c27d8bc19ba9278f45d9b045da26a0d', 9, 14, '31.92', '', '2022-02-05', 2, 1, '2022-11-02 18:17:35', '2022-11-02 18:17:35', NULL),
(77, 'Anderson Vieira', 'd082d185ff16b661b80c4f56952a338e', 9, 14, '20.00', '', '2022-02-05', 2, 1, '2022-11-02 18:17:53', '2022-11-02 18:25:56', '2022-11-02 18:25:56'),
(78, 'Namor Reembolso', 'c0793c021d624dabc75d6b4facc2b41b', 9, 14, '30.00', '', '2022-02-05', 2, 1, '2022-11-02 18:18:28', '2022-11-02 18:18:28', NULL),
(79, 'Anderson Vieira devolução de inscrição', '6695c8b38655201e34f513135aebcc5b', 9, 14, '70.00', '', '2022-02-05', 2, 1, '2022-11-02 18:19:01', '2022-11-02 18:29:16', '2022-11-02 18:29:16'),
(80, 'Vilmar Amorin', 'd23b33824c67e88d49c3c02896e04e2f', 9, 11, '182.00', '', '2022-02-05', 2, 1, '2022-11-02 18:22:44', '2022-11-02 18:22:44', NULL),
(81, 'Anderson Vieira devolução de inscrição', '2d1375e71e53588a0c8ba365a7b84424', 9, 14, '30.00', '', '2022-02-05', 2, 1, '2022-11-02 18:29:05', '2022-11-02 18:29:05', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `metodos`
--

CREATE TABLE `metodos` (
  `id` int(9) NOT NULL,
  `nome_amigavel` varchar(255) NOT NULL,
  `nome_metodo` varchar(100) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `paginas_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `metodos`
--

INSERT INTO `metodos` (`id`, `nome_amigavel`, `nome_metodo`, `chave`, `paginas_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Visualizar', 'index', '51dd2c85e9606ebbff49ce4a27cbeabd', 1, '2020-07-17 20:46:39', '2020-07-17 20:46:39', NULL),
(2, 'Visualizar', 'index', '4d78fa7788bfacf2f69d488524410f0e', 2, '2020-07-17 20:47:06', '2020-07-17 20:47:06', NULL),
(3, 'Novo', 'create', '7089a0f0439b5ac7845ff827c9e9349e', 2, '2020-07-17 20:47:06', '2020-07-17 20:47:06', NULL),
(4, 'Salvar', 'store', '44affff42c7310755dc48676e46d4b33', 2, '2020-07-17 20:47:06', '2020-07-17 20:47:06', NULL),
(5, 'Editar', 'edit', '09a06017df6ef44a3dafe7b24996bb29', 2, '2020-07-17 20:47:06', '2020-07-17 20:47:06', NULL),
(6, 'Apagar', 'delete', 'cc3355f04d0e361aff65f623cc475eb4', 2, '2020-07-17 20:47:06', '2020-07-17 20:47:06', NULL),
(7, 'Visualizar', 'index', '8dabb08a4173bd3ca75bc203fef8277b', 3, '2020-07-17 20:50:11', '2020-07-17 20:50:11', NULL),
(8, 'Novo', 'create', 'ba9a3160116ab0e480e29fd3e8301d93', 3, '2020-07-17 20:50:11', '2020-07-17 20:50:11', NULL),
(9, 'Salvar', 'store', '9c99ff9bf495baef2a56db6c9d9f3485', 3, '2020-07-17 20:50:11', '2020-07-17 20:50:11', NULL),
(10, 'Editar', 'edit', '8425afe03deab784e4e11a270804f9bb', 3, '2020-07-17 20:50:11', '2020-07-17 20:50:11', NULL),
(11, 'Apagar', 'delete', '1095662a5fafd59cfe029b796b8885e1', 3, '2020-07-17 20:50:11', '2020-07-17 20:50:11', NULL),
(12, 'Visualizar', 'index', '12dabbda6651a46f1d63f7d661007f5a', 4, '2020-07-17 20:50:24', '2020-07-17 20:50:24', NULL),
(13, 'Novo', 'create', 'affe0819cb1b8783634a3b8c6c52c672', 4, '2020-07-17 20:50:24', '2020-07-17 20:50:24', NULL),
(14, 'Salvar', 'store', '41f872aeb3d574cc0aec1ba82050c7c5', 4, '2020-07-17 20:50:24', '2020-07-17 20:50:24', NULL),
(15, 'Editar', 'edit', 'd117e7bb3e2e89854fb1af2aeb91f49d', 4, '2020-07-17 20:50:24', '2020-07-17 20:50:24', NULL),
(16, 'Apagar', 'delete', '4849c33b92809df36140587abf8d20f4', 4, '2020-07-17 20:50:24', '2020-07-17 20:50:24', NULL),
(17, 'Visualizar', 'index', 'e40605be68cb3acb17735552fa5ca8ce', 5, '2020-07-17 20:50:32', '2020-07-17 20:50:32', NULL),
(18, 'Gerar Relatório', 'getDados', '9d1022ef5521371c9267a8dc07c0c5d8', 5, '2020-07-17 20:50:32', '2020-07-17 20:50:32', NULL),
(19, 'Visualizar', 'index', 'ffee4fe1220364d5a3bad5e7fbfe4059', 6, '2020-07-17 20:50:44', '2020-07-17 20:50:44', NULL),
(20, 'Novo', 'create', '301ed25e6c59e25baf7cd3d7e2dcfc19', 6, '2020-07-17 20:50:44', '2020-07-17 20:50:44', NULL),
(21, 'Editar', 'edit', 'ef043c9561d8be574278a8f0db303d48', 6, '2020-07-17 20:50:44', '2020-07-17 20:50:44', NULL),
(22, 'Salvar', 'store', '413f923f4e138f7162fed02f082f6b15', 6, '2020-07-17 20:50:44', '2020-07-17 20:50:44', NULL),
(23, 'Apagar', 'delete', '3265fe0dc182a4cc981ba65be1a7a91f', 6, '2020-07-17 20:50:44', '2020-07-17 20:50:44', NULL),
(24, 'Visualizar', 'index', 'dbc09556816ad3959836349f87a71a8f', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(25, 'Novo', 'create', '2f8a25a24bc5631b00bf0d992efaa883', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(26, 'Salvar', 'store', 'd3c6ce07279861898040721fc4048d46', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(27, 'Editar', 'edit', '1956513894e3013f41eaa907628e9ac5', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(28, 'Visualizar QRCode Google Auth', 'googleAuth', '18c6ad9be5bc34c8637dd2422a3f0da5', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(29, 'Habilitar autenticação em 2 fatores', 'storeGoogleAuth', '0f15d71c102c5cc665a291c31575e58e', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(30, 'Desativar autenticação em 2 fatores', 'desativaAuth2Fatores', '3df2fef843c72f67ea4cfb1e5e48954e', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(31, 'Criar Códigos de Backup', 'createBackupCodes', '3d04f18bd0ce4113e48811fd4727c661', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(32, 'Ver Foto', 'getFoto', '30c9b34a0d447fb81284c839eae11dbc', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(33, 'Apagar', 'delete', '3a630f6b58ed5ffd03bb7038dc641ea7', 7, '2020-07-17 20:51:02', '2020-07-17 20:51:02', NULL),
(34, 'Salvar', 'store', 'afc821f973480a09815716a037dd9637', 8, '2020-07-17 20:51:12', '2020-07-17 20:51:12', NULL),
(35, 'Recuperar', 'get', '9a595bfe3f92a4057ee5e6d2e8f72b0a', 8, '2020-07-17 20:51:12', '2020-07-17 20:51:12', NULL),
(36, 'Gráfico por Ano', 'getPorAno', '13eb59711ea505e93857a6a171aeb6b4', 9, '2020-07-17 20:51:22', '2020-07-17 20:51:22', NULL),
(37, 'Gráfico Por Categoria', 'getPorCategoria', 'bd24101d1d3bfe7df3e19761e5945d1c', 9, '2020-07-17 20:51:22', '2020-07-17 20:51:22', NULL),
(38, 'Grava Foto', 'storeFoto', '861baebc094b9442e81f6c8db89c2a51', 10, '2020-07-17 20:51:28', '2020-07-17 20:51:28', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` text NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

CREATE TABLE `orcamentos` (
  `id` int(9) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `usuarios_id` int(9) NOT NULL,
  `categorias_id` int(9) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(8,2) UNSIGNED NOT NULL,
  `notificar_por_email` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `paginas`
--

CREATE TABLE `paginas` (
  `id` int(9) NOT NULL,
  `nome_amigavel` varchar(200) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `nome_classe` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `paginas`
--

INSERT INTO `paginas` (`id`, `nome_amigavel`, `chave`, `nome_classe`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Home', '26cf40c6e789a11d55011900da0fe1c5', 'Home', '2020-06-17 13:50:14', '2020-06-17 13:50:18', NULL),
(2, 'Lançamentos', '6f5e39b199a255b7397332eebc66a486', 'Lancamento', '2020-06-17 13:50:28', '2020-06-17 13:50:38', NULL),
(3, 'Categorias', '4e758d3087ffe133270883ec4912f5d8', 'Categoria', '2020-06-17 13:50:57', '2020-07-17 20:50:11', NULL),
(4, 'Orçamentos', '467e6a6e01e7ee7e3c3be2eadfcc8d34', 'Orcamento', '2020-06-17 13:51:14', '2020-07-17 20:50:24', NULL),
(5, 'Relatório', '5cd86bd5ef1243fe8c36cf2ca3f0227f', 'Relatorio', '2020-06-17 13:51:33', '2020-07-17 20:50:32', NULL),
(6, 'Perfis', '35d1fc432b0dfb5363b9eeb3449e2052', 'Perfil', '2020-06-17 13:51:55', '2020-07-17 20:50:44', NULL),
(7, 'Usuários', '256da99f09904a4d0d30912931ca8afa', 'Usuario', '2020-06-17 13:52:11', '2020-07-17 20:51:02', NULL),
(8, 'Categorias Ajax', 'abcdfcd77f7bff8710769de3794ce968', 'Ajax\\Categoria', '2020-06-17 13:52:39', '2020-07-17 20:51:12', NULL),
(9, 'Grafico Ajax', '887598053f0eeb7f85c92aab2a2baf20', 'Ajax\\Grafico', '2020-06-23 15:16:04', '2020-07-17 20:51:22', NULL),
(10, 'Usuário Ajax', '2c1832fcee45dbb0a2849f40f8fa7fe2', 'Ajax\\Usuario', '2020-07-05 19:24:06', '2020-07-17 20:51:28', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfis`
--

CREATE TABLE `perfis` (
  `id` int(9) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `usuarios_id` int(9) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `perfis`
--

INSERT INTO `perfis` (`id`, `descricao`, `chave`, `usuarios_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Presidente', 'f11049c67ee23560270a1a192ca45162', 1, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(2, 'Dir_Financeiro', '6f36660ba5cff8262aad956624b75d67', 1, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(3, 'Associado', '35c82a264592ae70318c0beefdec466a', 1, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(4, 'Presidente', '600624107ef1303d2fdcbc2a3dfdf1c9', 9, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(5, 'Associado', '5896449722b7fae3ef3b4c66e28396a9', 9, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(6, 'Financeiro', 'e066b8796978b07daa33e6b4e94d9883', 9, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(9) NOT NULL,
  `regras` varchar(200) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `perfis_id` int(11) NOT NULL,
  `paginas_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `regras`, `chave`, `perfis_id`, `paginas_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'n,index', 'eec032a7894c8faf96567511786d29b9', 1, 1, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(2, 'n,index,create,store,edit,delete', 'd3b9adacf5b57444724f390a02b068cf', 1, 2, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(3, 'n,index,create,store,edit,delete', '2b78b22dcc7dc1b7c49983dc592f7511', 1, 3, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(4, 'n,index,create,store,edit,delete', '14d529af32d80c1d4c50e20bad67f140', 1, 4, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(5, 'n,index,getDados', '6799c57b87c4ad200782feaca7e48c9a', 1, 5, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(6, 'n,index,create,edit,store,delete', '3525785e8d6d2aaaf65eee8179586a82', 1, 6, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(7, 'n,index,create,store,edit,googleAuth,storeGoogleAuth,desativaAuth2Fatores,createBackupCodes,getFoto,delete', '184281900f9c39fed82615f9e8b53fe1', 1, 7, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(8, 'n,store,get', '2dd4f1d60baf5622a79bccde41a64668', 1, 8, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(9, 'n,getPorAno,getPorCategoria', 'f8e50106ecff3294d6697e1dcf8c287e', 1, 9, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(10, 'n,storeFoto', 'c470e666ab43b86351ad565caa710e33', 1, 10, '2022-10-13 05:46:21', '2022-10-13 05:46:21', NULL),
(11, 'index', 'f36ddb003792468c6f4fd6a51758b3ed', 2, 1, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(12, 'index,create,store,edit,delete', '2b61193237155e312f0dd5116c0b3853', 2, 2, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(13, 'index,create,store,edit,delete', '8a4c4668871ef9485c9c3efedff40473', 2, 3, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(14, 'index,create,store,edit,delete', '37f67ffc93136c01c8715dc2c3f82863', 2, 4, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(15, 'index,getDados', 'a4da8890339fc9227842565de34cbd26', 2, 5, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(16, 'index', 'be32f1c0525fe5ed5e38a4180e6dadd1', 2, 6, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(17, 'index', 'b30abf2b177b46722509b8339f4ddfa9', 2, 7, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(18, 'store,get', 'b353b996efecb63610c4be0c63aec720', 2, 8, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(19, 'getPorAno,getPorCategoria', 'fff13226ed1b9908365abe5c989f6c4f', 2, 9, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(20, 'storeFoto', '921df4c8a0fcf9d1355ad80930c5d180', 2, 10, '2022-11-01 03:09:08', '2022-11-01 03:11:45', NULL),
(21, 'index', 'e69c3619813d13019a50b04751e0a483', 3, 1, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(22, 'index', '1e0274653b0dab44ab9b5260e0622605', 3, 2, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(23, 'index', 'ee1824716fbf2c26cf3cc0de05eaa14f', 3, 3, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(24, 'index', 'f67bcf535c21aef264f21342feb0ca71', 3, 4, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(25, 'index', '2e9b19361ba024a6cf5e950ff66ec569', 3, 5, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(26, 'index', 'c31f80e51bf77c9bb94f14018890c410', 3, 6, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(27, 'n', '000e1ad51739c973330fc0d29620ef08', 3, 7, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(28, 'n', 'a9a141011a58398ec8b129ba4795fbc1', 3, 8, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(29, 'n', '5a15da37eeb106f991ec91df52f74958', 3, 9, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(30, 'storeFoto', 'd0f6019ad76475fbdf3e1fa7ceab8ee1', 3, 10, '2022-11-01 03:10:05', '2022-11-01 03:10:05', NULL),
(31, 'n,index', 'a2393aec4a058829cd53b0b76a01f062', 4, 1, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(32, 'n,index,create,store,edit,delete', '8b00760c032919ab914c046fe8313d66', 4, 2, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(33, 'n,index,create,store,edit', '6db9df9969c8f3412461add01201a102', 4, 3, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(34, 'n,index,create,store,edit,delete', 'b93a965fde5ab8657b3615f119977e10', 4, 4, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(35, 'n,index,getDados', '561ad7d17ac9a4414161decb29e1aec0', 4, 5, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(36, 'n,index,create,edit,store,delete', 'eef4cf9599e665a2dd72e11e81b65964', 4, 6, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(37, 'n,index,create,store,edit,googleAuth,storeGoogleAuth,desativaAuth2Fatores,createBackupCodes,getFoto,delete', '875e898ad2d978702c56c58f8be01fca', 4, 7, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(38, 'n,store,get', '8524790f885a60e60c96f9f00815d65b', 4, 8, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(39, 'n,getPorAno,getPorCategoria', '83d4f18ae8d5d6dbe34545a066ed7286', 4, 9, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(40, 'n,storeFoto', '6864e83a398bd978dc544ddef901d1ec', 4, 10, '2022-11-02 11:03:56', '2022-11-02 17:30:26', NULL),
(41, 'index', 'bde64bdac63bc573d8c94739ee52e284', 5, 1, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(42, 'index', '10e6d65a246bff1a2b6242961967076d', 5, 2, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(43, 'n', '31cbb1252acc68c128279a40d47205ee', 5, 3, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(44, 'n', 'd9b510405a7d8323249969c8b52f988d', 5, 4, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(45, 'index,getDados', 'a22a9fc0c7ffdb6815de49801043fa8e', 5, 5, '2022-11-02 15:41:09', '2022-11-02 15:43:25', NULL),
(46, 'n', '370e23c66a88200cd950bc2a73d0a250', 5, 6, '2022-11-02 15:41:10', '2022-11-02 15:43:25', NULL),
(47, 'index', 'a0eca0c079d0e084122dd0724ca8c062', 5, 7, '2022-11-02 15:41:10', '2022-11-02 15:43:25', NULL),
(48, 'n', '2a028ac541190baa960b46e2dc699880', 5, 8, '2022-11-02 15:41:10', '2022-11-02 15:43:25', NULL),
(49, 'getPorAno,getPorCategoria', 'd0c662afdc369dc4c70afacd30b7d4c9', 5, 9, '2022-11-02 15:41:10', '2022-11-02 15:43:25', NULL),
(50, 'storeFoto', '9c374d53eaca1f7766db070c9658d6df', 5, 10, '2022-11-02 15:41:10', '2022-11-02 15:43:25', NULL),
(51, 'index', '5d133bc821b20673a8199ff70cdd7925', 6, 1, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(52, 'index,create,store,edit,delete', '48d77b059c7c4cdc620eb400da3078a1', 6, 2, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(53, 'index', '4255d1224cf559fb3d9e520f4216c3ef', 6, 3, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(54, 'index,create,store,edit,delete', '9c3e0c1c3c78db91e73928afd6a21d40', 6, 4, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(55, 'index,getDados', '49904c3e819aa71e6ecc2bd0afbdc23f', 6, 5, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(56, 'index', 'eeee062cfdf098f743b940b34411fc58', 6, 6, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(57, 'index', 'bcdd4a16ba8601fd16c97a6d3cb843ec', 6, 7, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(58, 'store,get', '2e3d98e5d51a42efac7cd30e3eba99a3', 6, 8, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(59, 'getPorAno,getPorCategoria', '4f49cac59428a50fe45aeaa8cc098ff9', 6, 9, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL),
(60, 'storeFoto', 'df5b527aba1243e5ff5075f52dba7fe8', 6, 10, '2022-11-02 15:58:50', '2022-11-02 15:58:50', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `recovery_codes`
--

CREATE TABLE `recovery_codes` (
  `id` int(9) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `usuarios_id` int(9) NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `token_redefinicao_senha`
--

CREATE TABLE `token_redefinicao_senha` (
  `id` int(9) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `usuarios_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `token_redefinicao_senha`
--

INSERT INTO `token_redefinicao_senha` (`id`, `chave`, `usuarios_id`, `token`, `ativo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '198356eb6a728f6f6bf861d7f6515ffa', 8, '8bad3cc0614159a5b8cd5b4b40a064f9', 0, '2022-11-02 10:55:13', '2022-11-02 10:55:51', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(9) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `chave` varchar(255) NOT NULL,
  `perfis_id` int(9) DEFAULT NULL,
  `usuario_pai` int(9) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_confirmado` tinyint(1) NOT NULL DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `senha` varchar(255) NOT NULL,
  `token_confirmacao_email` varchar(255) DEFAULT NULL,
  `token_criado_em` datetime DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `admin` tinyint(1) DEFAULT 0,
  `secret_google_auth` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `chave`, `perfis_id`, `usuario_pai`, `email`, `email_confirmado`, `foto`, `senha`, `token_confirmacao_email`, `token_criado_em`, `ativo`, `admin`, `secret_google_auth`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 'Rafa Asai', 'e5c444fdcdca0cd61f387940b726c238', NULL, NULL, 'rafael@asaisurf.com.br', 1, NULL, '$2y$10$szgXu.8IaZTkbQaLvAFA0Oyz6OmX4d0pS544U46z6WSeLIEaIbUqW', '7817b26e72240f7bf6944a237295fb22', NULL, 1, 0, NULL, '2022-11-02 10:57:40', '2022-11-02 10:57:40', NULL),
(10, 'Renato Bynato', '79d33130dd0b8480d75ceaebfd7c3d90', 4, 9, 'by@asaisurf.com.br', 1, NULL, '$2y$10$YbedbYevUFC.I662xoar9ugR.1FnvlidTHIBHwxcZSBWniW0A51UC', '19d4ec4f8a56266a6ab786322c3f3ac9', NULL, 1, 0, NULL, '2022-11-02 11:04:52', '2022-11-02 11:05:51', NULL),
(11, 'Bruno Ávila', '349199e1375d1e71d1a48ef5e53ba57f', 4, 9, 'bru@asaisurf.com.br', 1, '20221102/1667406459_79e596ab6ff0896c42fb.jpeg', '$2y$10$TNzyTp8Ab3kSOjcify1jVOWcRr7LrQtXpD0QJC69/zEJwx9e/T7EC', '5a4e442538ff02fe17a7838054b82ad1', NULL, 1, 0, NULL, '2022-11-02 11:06:57', '2022-11-02 13:27:48', NULL),
(12, 'Juliano Seco', '84979a1e66d733a1730615856209331a', 5, 9, 'ju@asaisurf.com.br', 1, NULL, '$2y$10$eDAMW/Q/jhSree5DW6YopOlugiQpcmq/Z/AgO2VYxauxh9LCEIIly', '2df5ab589bd64e91cfbff8f8e5eea55e', NULL, 1, 0, NULL, '2022-11-02 15:41:41', '2022-11-02 15:41:41', NULL),
(8, 'DSSWEB5', '93a15dcf720901a0b85d86d5cfff4b6d', NULL, NULL, 'dssweb5@gmail.com', 1, NULL, '$2y$10$4LcHt4YbhLeEu2DrJBxMwe.De4I6C3cgcpzYVCK.4Ue8J4qKpKxCS', 'e5577d6254950375b60d46166641f9c4', NULL, 1, 1, NULL, '2022-11-01 03:04:48', '2022-11-02 10:55:51', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorias_usuarios_id_foreign` (`usuarios_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `lancamentos`
--
ALTER TABLE `lancamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lancamentos_categorias_id_foreign` (`categorias_id`),
  ADD KEY `lancamentos_usuarios_id_foreign` (`usuarios_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `metodos`
--
ALTER TABLE `metodos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metodos_paginas_id_foreign` (`paginas_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamentos_categorias_id_foreign` (`categorias_id`),
  ADD KEY `orcamentos_usuarios_id_foreign` (`usuarios_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `paginas`
--
ALTER TABLE `paginas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `perfis`
--
ALTER TABLE `perfis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfis_usuarios_id_foreign` (`usuarios_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissoes_paginas_id_foreign` (`paginas_id`),
  ADD KEY `permissoes_perfis_id_foreign` (`perfis_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `recovery_codes`
--
ALTER TABLE `recovery_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recovery_codes_usuarios_id_foreign` (`usuarios_id`);

--
-- Índices de tabela `token_redefinicao_senha`
--
ALTER TABLE `token_redefinicao_senha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token_redefinicao_senha_usuarios_id_foreign` (`usuarios_id`),
  ADD KEY `chave` (`chave`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chave` (`chave`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `lancamentos`
--
ALTER TABLE `lancamentos`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de tabela `metodos`
--
ALTER TABLE `metodos`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `paginas`
--
ALTER TABLE `paginas`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `perfis`
--
ALTER TABLE `perfis`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de tabela `recovery_codes`
--
ALTER TABLE `recovery_codes`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `token_redefinicao_senha`
--
ALTER TABLE `token_redefinicao_senha`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
