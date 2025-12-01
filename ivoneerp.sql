-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 09:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ivoneerp`
--

-- --------------------------------------------------------

--
-- Table structure for table `aprovacoes`
--

CREATE TABLE `aprovacoes` (
  `id` int(11) NOT NULL,
  `id_ferias_licenca` int(11) NOT NULL,
  `id_aprovador` int(11) NOT NULL,
  `data_aprovacao` date DEFAULT NULL,
  `status` enum('Aprovado','Rejeitado') NOT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armazem`
--

CREATE TABLE `armazem` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(50) DEFAULT NULL,
  `responsavel` int(11) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `estado` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `usuario_cadastro` int(11) NOT NULL,
  `data_cadastro` datetime NOT NULL,
  `usuario_atualizacao` int(11) DEFAULT NULL,
  `data_atualizacao` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armazem_movimentos`
--

CREATE TABLE `armazem_movimentos` (
  `id` int(11) NOT NULL,
  `armazem_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `tipo_movimento` enum('entrada','saida','transferencia') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_movimento` datetime NOT NULL,
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `armazem_stock`
--

CREATE TABLE `armazem_stock` (
  `id` int(11) NOT NULL,
  `armazem_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `lote` varchar(50) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `prazo` date DEFAULT NULL,
  `preco_custo` decimal(10,2) DEFAULT NULL,
  `fornecedor_id` int(11) DEFAULT NULL,
  `data_entrada` datetime NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `estado` enum('ativo','inativo') NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artigos_devolvidos`
--

CREATE TABLE `artigos_devolvidos` (
  `id` int(11) NOT NULL,
  `produto` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `cliente` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `devolucao` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `artigos_devolvidos`
--

INSERT INTO `artigos_devolvidos` (`id`, `produto`, `qtd`, `preco`, `total`, `iva`, `lote`, `cliente`, `usuario`, `devolucao`, `data`) VALUES
(1, 1517, 4, 20.00, 80.00, 0.00, 'AAB25002', 1, 22, 1, '2025-11-08 17:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `artigos_devolvidos_temp`
--

CREATE TABLE `artigos_devolvidos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` int(11) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `artigos_devolvidos_temp`
--

INSERT INTO `artigos_devolvidos_temp` (`id`, `artigo`, `qtd`, `preco`, `total`, `iva`, `lote`, `user`, `data`) VALUES
(1, 83493, 1, 63.00, 63.00, 10, 'ABA23007', 24, '2025-11-07 06:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `data_avaliacao` date NOT NULL,
  `nota` decimal(5,2) NOT NULL,
  `feedback` text DEFAULT NULL,
  `pontos_fortes` text DEFAULT NULL,
  `pontos_a_melhorar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `balanco_saldo`
--

CREATE TABLE `balanco_saldo` (
  `id` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `data1` varchar(20) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `balanco_saldo`
--

INSERT INTO `balanco_saldo` (`id`, `saldo`, `data1`, `data`) VALUES
(1, 6247266.00, '2025-11-10 20:54:59', '2025-04-09 12:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `beneficios`
--

CREATE TABLE `beneficios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('Refeição','Transporte','Plano de Saúde','Plano Dental','Outros') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caixa_recepcao`
--

CREATE TABLE `caixa_recepcao` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `valor_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_entradas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_dinheiro` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_mpesa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_emola` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_pos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_saidas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_final` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `usuario_abertura` int(11) DEFAULT NULL,
  `usuario_fechamento` int(11) DEFAULT NULL,
  `data_abertura` timestamp NULL DEFAULT NULL,
  `data_fechamento` timestamp NULL DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `caixa_recepcao`
--

INSERT INTO `caixa_recepcao` (`id`, `data`, `valor_inicial`, `total_entradas`, `total_dinheiro`, `total_mpesa`, `total_emola`, `total_pos`, `total_saidas`, `saldo_final`, `observacoes`, `usuario_abertura`, `usuario_fechamento`, `data_abertura`, `data_fechamento`, `status`) VALUES
(1, '2025-11-21', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 25, NULL, '2025-11-21 12:45:33', NULL, 'aberto'),
(2, '2025-11-23', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 25, NULL, '2025-11-23 10:20:40', NULL, 'aberto'),
(3, '2025-11-24', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 25, NULL, '2025-11-24 07:30:48', NULL, 'aberto'),
(4, '2025-11-25', 500.00, 38500.00, 33500.00, 5000.00, 0.00, 0.00, 0.00, 38500.00, NULL, 6, 6, '2025-11-25 18:57:54', '2025-11-25 18:57:51', 'aberto'),
(5, '2025-11-26', 0.00, 4000.00, 4000.00, 0.00, 0.00, 0.00, 0.00, 4000.00, NULL, 25, 25, '2025-11-26 07:16:02', '2025-11-26 10:16:49', 'fechado');

-- --------------------------------------------------------

--
-- Table structure for table `candidaturas`
--

CREATE TABLE `candidaturas` (
  `id` int(11) NOT NULL,
  `id_vaga` int(11) NOT NULL,
  `nome_candidato` varchar(100) NOT NULL,
  `contato` varchar(50) DEFAULT NULL,
  `status` enum('Em Análise','Aprovado','Rejeitado') DEFAULT 'Em Análise'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cargos`
--

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `salario_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categoria_produtos`
--

CREATE TABLE `categoria_produtos` (
  `id` int(11) NOT NULL,
  `categoria` varchar(200) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `nuit` int(11) NOT NULL,
  `apelido` varchar(255) NOT NULL,
  `contacto` int(11) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `desconto` int(11) NOT NULL,
  `qtd_factura` int(11) NOT NULL DEFAULT 0,
  `qtd_notas_credito` int(11) NOT NULL DEFAULT 0,
  `qtd_nota_debito` int(11) NOT NULL DEFAULT 0,
  `qtd_cotacao` int(11) NOT NULL DEFAULT 0,
  `qtd_vds` int(11) NOT NULL DEFAULT 0,
  `qtd_recibo` int(11) NOT NULL DEFAULT 0,
  `qtd_devolucao` int(11) NOT NULL DEFAULT 0,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `nuit`, `apelido`, `contacto`, `endereco`, `desconto`, `qtd_factura`, `qtd_notas_credito`, `qtd_nota_debito`, `qtd_cotacao`, `qtd_vds`, `qtd_recibo`, `qtd_devolucao`, `data`) VALUES
(1, 'Consumidor', 0, 'Final', 0, 'Tete', 0, 41, 0, 8, 15, 0, 0, 0, '2023-10-06 12:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `colaboradores`
--

CREATE TABLE `colaboradores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `data_contratacao` date DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colaborador_beneficios`
--

CREATE TABLE `colaborador_beneficios` (
  `id` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `id_beneficio` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colaborador_cargos`
--

CREATE TABLE `colaborador_cargos` (
  `id` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colaborador_treinamentos`
--

CREATE TABLE `colaborador_treinamentos` (
  `id` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `id_treinamento` int(11) NOT NULL,
  `data_participacao` date NOT NULL,
  `data_validade` date DEFAULT NULL,
  `certificado` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `condicao_pagamento`
--

CREATE TABLE `condicao_pagamento` (
  `id` int(11) NOT NULL,
  `descricao` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `confirmacao_caixa`
--

CREATE TABLE `confirmacao_caixa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `data_hora` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cotacao`
--

CREATE TABLE `cotacao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `disconto` decimal(10,2) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cotacao_recepcao`
--

CREATE TABLE `cotacao_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `prazo` date DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cotacao_recepcao`
--

INSERT INTO `cotacao_recepcao` (`id`, `n_doc`, `paciente`, `empresa_id`, `valor`, `prazo`, `serie`, `usuario`, `dataa`, `data_criacao`) VALUES
(1, 1, 2, 1, 1140.00, '2025-11-25', 2025, 6, '2025-11-25', '2025-11-25 19:55:01');

-- --------------------------------------------------------

--
-- Table structure for table `ct_artigos_cotados`
--

CREATE TABLE `ct_artigos_cotados` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `cotacao` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ct_artigos_temp`
--

CREATE TABLE `ct_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ct_artigos_temp`
--

INSERT INTO `ct_artigos_temp` (`id`, `artigo`, `qtd`, `preco`, `total`, `iva`, `user`, `data`) VALUES
(1, 6, 1, 45.00, 45.00, 0.00, 22, '2025-11-08 09:38:48'),
(2, 5, 1, 201.00, 201.00, 0.00, 22, '2025-11-08 09:38:49'),
(3, 1, 1, 82.00, 82.00, 0.00, 22, '2025-11-08 09:38:49'),
(4, 7, 1, 225.00, 225.00, 0.00, 22, '2025-11-08 09:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `ct_servicos_fact`
--

CREATE TABLE `ct_servicos_fact` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `cotacao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ct_servicos_fact`
--

INSERT INTO `ct_servicos_fact` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `cotacao_id`) VALUES
(1, 1, 1, 1140.00, 1140.00, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ct_servicos_temp`
--

CREATE TABLE `ct_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `despesas`
--

CREATE TABLE `despesas` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `categoria` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `status` enum('Pendente','Pago') DEFAULT 'Pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devolucao`
--

CREATE TABLE `devolucao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `motivo` text NOT NULL,
  `idpedido` int(11) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idperiodo` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `devolucao`
--

INSERT INTO `devolucao` (`id`, `n_doc`, `descricao`, `valor`, `modo`, `motivo`, `idpedido`, `serie`, `idcliente`, `iduser`, `idperiodo`, `data`) VALUES
(1, 1, '2025-11-08 19:12:53', 80.00, 'Numerario', '                                \n                            ', 12, '2025', 1, 22, 3, '2025-11-08 17:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `devolucao_recepcao`
--

CREATE TABLE `devolucao_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devolucao_recepcao`
--

INSERT INTO `devolucao_recepcao` (`id`, `n_doc`, `factura_recepcao_id`, `paciente`, `empresa_id`, `valor`, `motivo`, `metodo`, `serie`, `usuario`, `dataa`, `data_criacao`) VALUES
(1, 1, 6, 2, 1, 1140.00, 'kkbkb', 'Dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:11:40'),
(2, 2, 6, 2, 1, 1140.00, 'asdasd', 'Dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:25:53'),
(3, 3, 6, 2, 1, 1140.00, 'asdasd', 'Dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:30:11'),
(4, 4, 6, 2, 1, 1140.00, 'adasd', 'Dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:33:44'),
(5, 5, 9, 3, 2, 1840.00, 'asda', 'Dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `disconto`
--

CREATE TABLE `disconto` (
  `id` int(11) NOT NULL,
  `percentagem` double NOT NULL,
  `motivo` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `documento` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dv_servicos_fact`
--

CREATE TABLE `dv_servicos_fact` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `devolucao_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dv_servicos_fact`
--

INSERT INTO `dv_servicos_fact` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `devolucao_id`) VALUES
(1, 1, 1, 1140.00, 1140.00, 25, 1),
(2, 1, 1, 1140.00, 1140.00, 25, 2),
(3, 1, 1, 1140.00, 1140.00, 25, 3),
(4, 1, 1, 1140.00, 1140.00, 25, 4),
(5, 2, 1, 1840.00, 1840.00, 25, 5);

-- --------------------------------------------------------

--
-- Table structure for table `dv_servicos_temp`
--

CREATE TABLE `dv_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `factura_recepcao_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dv_servicos_temp`
--

INSERT INTO `dv_servicos_temp` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `empresa_id`, `factura_recepcao_id`) VALUES
(9, 2, 1, 1840.00, 1840.00, 25, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `nuit` int(11) NOT NULL,
  `endereco` text NOT NULL,
  `provincia` varchar(255) NOT NULL,
  `pais` varchar(255) NOT NULL,
  `contacto` varchar(255) NOT NULL,
  `capital_social` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `banco` varchar(255) NOT NULL,
  `conta` varchar(255) NOT NULL,
  `nib` varchar(255) NOT NULL,
  `img` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `empresa`
--

INSERT INTO `empresa` (`id`, `nome`, `nuit`, `endereco`, `provincia`, `pais`, `contacto`, `capital_social`, `email`, `banco`, `conta`, `nib`, `img`, `data`) VALUES
(1, 'Farmacia Bandula, SU', 401813144, 'Bairro Matundo - Rua Marginal Rio', 'Tete', 'Mocambique', '840428515', 'Metical', 'farmaciabandula@gmail.com', 'BCI', '32739372210001', '000800002739372210128', 'icn.jpg', '2023-05-10 16:26:10');

-- --------------------------------------------------------

--
-- Table structure for table `empresas_seguros`
--

CREATE TABLE `empresas_seguros` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `nuit` varchar(50) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `tabela_precos_id` int(11) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `data_inicio_contrato` date DEFAULT NULL,
  `data_fim_contrato` date DEFAULT NULL,
  `desconto_geral` decimal(5,2) DEFAULT 0.00 COMMENT 'Desconto percentual geral',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `empresas_seguros`
--

INSERT INTO `empresas_seguros` (`id`, `nome`, `nuit`, `contacto`, `email`, `endereco`, `tabela_precos_id`, `contrato`, `data_inicio_contrato`, `data_fim_contrato`, `desconto_geral`, `ativo`, `observacoes`, `data_criacao`, `usuario_criacao`) VALUES
(1, 'Nagi', '123442', '843133322', NULL, NULL, 1, NULL, '2025-11-04', '2027-06-15', 5.00, 1, NULL, '2025-11-22 21:25:52', 6),
(2, 'Gindal', '123456789', '+258 84 531 2', 'gindal@info.com', 'Matundo', 2, NULL, '2025-11-25', '2027-06-25', 8.00, 1, NULL, '2025-11-25 21:08:46', 6);

-- --------------------------------------------------------

--
-- Table structure for table `entrada_caixa`
--

CREATE TABLE `entrada_caixa` (
  `id` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `caixa` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entrada_stock`
--

CREATE TABLE `entrada_stock` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `grupo` int(11) NOT NULL,
  `familia` int(11) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `entrada_stock`
--

INSERT INTO `entrada_stock` (`id`, `n_doc`, `descricao`, `serie`, `grupo`, `familia`, `lote`, `prazo`, `user`, `data`) VALUES
(1, 1, '2025-11-05 09:43:12', 2025, 25, 17, 'GF89', '42025-03-01', 22, '2025-11-05 09:43:12'),
(2, 2, '2025-11-05 09:50:39', 2025, 25, 17, 'GF89', '2026-03-05', 22, '2025-11-05 09:50:39'),
(3, 3, '2025-11-05 09:52:18', 2025, 25, 17, 'GF52', '2028-03-01', 22, '2025-11-05 09:52:18'),
(4, 4, '2025-11-05 09:55:27', 2025, 25, 17, 'GF69', '2028-02-28', 22, '2025-11-05 09:55:27'),
(5, 5, '2025-11-05 09:56:26', 2025, 25, 17, 'GF92', '2027-01-30', 22, '2025-11-05 09:56:26'),
(6, 6, '2025-11-05 09:57:30', 2025, 25, 17, '323232691', '2027-11-30', 22, '2025-11-05 09:57:30'),
(7, 7, '2025-11-05 09:59:28', 2025, 25, 17, 'ARI72', '2029-10-31', 22, '2025-11-05 09:59:28'),
(8, 8, '2025-11-05 10:08:46', 2025, 25, 17, 'DE-100070', '2028-03-31', 22, '2025-11-05 10:08:46'),
(10, 10, '2025-11-05 10:14:51', 2025, 25, 17, '24268', '2027-09-30', 22, '2025-11-05 10:14:51'),
(11, 11, '2025-11-05 10:15:55', 2025, 25, 17, '2404028', '2027-04-30', 22, '2025-11-05 10:15:55'),
(12, 12, '2025-11-05 10:16:55', 2025, 25, 17, '2412060', '2027-06-30', 22, '2025-11-05 10:16:55'),
(15, 13, '2025-11-05 10:34:13', 2025, 25, 17, 'E0140238', '2027-10-31', 22, '2025-11-05 10:34:13'),
(16, 14, '2025-11-05 10:35:57', 2025, 25, 17, 'AEL24017A', '2026-09-30', 22, '2025-11-05 10:35:57'),
(17, 15, '2025-11-05 10:36:50', 2025, 25, 17, 'ABS24001A', '2027-01-31', 22, '2025-11-05 10:36:50'),
(18, 16, '2025-11-05 10:38:09', 2025, 25, 17, 'AAR24004', '2026-09-30', 22, '2025-11-05 10:38:09'),
(19, 17, '2025-11-05 10:39:30', 2025, 25, 17, 'MZ250305', '2030-02-28', 22, '2025-11-05 10:39:30'),
(20, 18, '2025-11-05 10:40:40', 2025, 25, 17, 'MZ240901', '2027-08-30', 22, '2025-11-05 10:40:40'),
(21, 19, '2025-11-05 10:44:58', 2025, 25, 17, 'AEC25002A', '2027-12-31', 22, '2025-11-05 10:44:58'),
(22, 20, '2025-11-05 10:45:38', 2025, 25, 17, '1085', '2027-11-30', 22, '2025-11-05 10:45:38'),
(23, 21, '2025-11-05 10:47:53', 2025, 25, 17, '2502010', '2028-02-28', 22, '2025-11-05 10:47:53'),
(24, 22, '2025-11-05 10:49:22', 2025, 25, 17, 'MC25025', '2027-01-31', 22, '2025-11-05 10:49:22'),
(25, 23, '2025-11-05 10:50:48', 2025, 25, 17, 'ML24785', '2027-10-31', 22, '2025-11-05 10:50:48'),
(26, 24, '2025-11-05 10:52:03', 2025, 25, 17, '1024265', '2029-08-31', 22, '2025-11-05 10:52:03'),
(27, 25, '2025-11-05 10:54:28', 2025, 25, 17, 'C183', '2027-02-28', 22, '2025-11-05 10:54:28'),
(28, 26, '2025-11-05 10:56:03', 2025, 25, 17, 'HF4003,HF4004', '2027-06-30', 22, '2025-11-05 10:56:03'),
(29, 27, '2025-11-05 10:56:59', 2025, 25, 17, '202402', '2029-02-28', 22, '2025-11-05 10:56:59'),
(30, 28, '2025-11-05 10:58:33', 2025, 25, 17, 'MZ240501', '2029-04-30', 22, '2025-11-05 10:58:33'),
(31, 29, '2025-11-05 10:59:35', 2025, 25, 17, 'ADJ25002A', '2027-12-31', 22, '2025-11-05 10:59:35'),
(32, 30, '2025-11-05 11:01:31', 2025, 25, 17, 'ACG24008', '2027-11-30', 22, '2025-11-05 11:01:31'),
(33, 31, '2025-11-05 11:03:18', 2025, 25, 17, 'ADA23006', '2026-05-31', 22, '2025-11-05 11:03:18'),
(34, 32, '2025-11-05 11:04:34', 2025, 25, 17, 'ABD24001', '2026-12-31', 22, '2025-11-05 11:04:34'),
(35, 33, '2025-11-05 11:06:22', 2025, 25, 17, 'AFR25002A', '2027-12-31', 22, '2025-11-05 11:06:22'),
(36, 34, '2025-11-05 11:07:44', 2025, 25, 17, 'N25B18', '2027-01-31', 22, '2025-11-05 11:07:44'),
(37, 35, '2025-11-05 11:08:54', 2025, 25, 17, '4G05', '2026-06-30', 22, '2025-11-05 11:08:54'),
(38, 36, '2025-11-05 11:10:10', 2025, 25, 17, '15E0150042', '2028-02-28', 22, '2025-11-05 11:10:10'),
(39, 37, '2025-11-05 11:11:36', 2025, 25, 17, '0130128', '2026-11-30', 22, '2025-11-05 11:11:36'),
(40, 38, '2025-11-05 11:14:23', 2025, 25, 17, 'E0140254', '2027-10-31', 22, '2025-11-05 11:14:23'),
(41, 39, '2025-11-05 11:16:04', 2025, 25, 17, 'E014015A', '2027-12-31', 22, '2025-11-05 11:16:04'),
(42, 40, '2025-11-05 11:18:03', 2025, 25, 17, 'E0150001', '2027-12-31', 22, '2025-11-05 11:18:03'),
(43, 41, '2025-11-05 11:21:12', 2025, 25, 17, 'AEV24007', '2027-10-31', 22, '2025-11-05 11:21:12'),
(44, 42, '2025-11-05 11:21:57', 2025, 25, 17, 'ACL24008A', '2027-10-31', 22, '2025-11-05 11:21:57'),
(45, 43, '2025-11-05 11:26:39', 2025, 25, 17, 'ADW24004', '2027-03-31', 22, '2025-11-05 11:26:39'),
(46, 44, '2025-11-05 11:28:19', 2025, 25, 17, 'ACH24005', '2027-11-30', 22, '2025-11-05 11:28:19'),
(47, 45, '2025-11-05 11:30:09', 2025, 25, 17, 'ABG24009', '2027-09-30', 22, '2025-11-05 11:30:09'),
(48, 46, '2025-11-05 11:32:43', 2025, 25, 17, 'AEA25001', '2027-12-31', 22, '2025-11-05 11:32:43'),
(49, 47, '2025-11-05 11:34:24', 2025, 25, 17, 'AAB25002', '2027-12-31', 22, '2025-11-05 11:34:24'),
(50, 48, '2025-11-05 11:37:28', 2025, 25, 17, 'AAL24005A', '2027-10-31', 22, '2025-11-05 11:37:28'),
(51, 49, '2025-11-05 11:38:53', 2025, 25, 17, 'AHQ24004', '2027-03-06', 22, '2025-11-05 11:38:53'),
(52, 50, '2025-11-05 11:40:57', 2025, 25, 17, 'AFQ25002', '2028-01-30', 22, '2025-11-05 11:40:57'),
(53, 51, '2025-11-05 11:42:18', 2025, 25, 17, 'AGV25001', '2028-01-31', 22, '2025-11-05 11:42:18'),
(54, 52, '2025-11-05 11:43:16', 2025, 25, 17, 'ACI24016A', '2027-06-30', 22, '2025-11-05 11:43:16'),
(55, 53, '2025-11-05 11:46:59', 2025, 25, 17, 'ABW25004', '2027-12-31', 22, '2025-11-05 11:46:59'),
(56, 54, '2025-11-05 11:47:37', 2025, 25, 17, 'ACQ24021', '2027-10-31', 22, '2025-11-05 11:47:37'),
(57, 55, '2025-11-05 11:48:40', 2025, 25, 17, 'AAA24044', '2027-10-31', 22, '2025-11-05 11:48:40'),
(58, 56, '2025-11-05 11:50:04', 2025, 25, 17, 'ADT25001', '2027-11-30', 22, '2025-11-05 11:50:04'),
(59, 57, '2025-11-05 11:52:05', 2025, 25, 17, 'AKN25001', '2028-03-31', 22, '2025-11-05 11:52:05'),
(60, 58, '2025-11-05 11:52:39', 2025, 25, 17, 'AKM2429', '2027-09-30', 22, '2025-11-05 11:52:39'),
(61, 59, '2025-11-05 11:54:00', 2025, 25, 17, 'ACO24007', '2027-10-31', 22, '2025-11-05 11:54:00'),
(62, 60, '2025-11-05 11:55:04', 2025, 25, 17, 'AAD25001', '2028-01-31', 22, '2025-11-05 11:55:04'),
(63, 61, '2025-11-05 11:56:29', 2025, 25, 17, 'E01150023', '2028-01-31', 22, '2025-11-05 11:56:29'),
(64, 62, '2025-11-05 11:57:12', 2025, 25, 17, 'ABE25001', '2027-12-30', 22, '2025-11-05 11:57:12'),
(65, 63, '2025-11-05 11:59:20', 2025, 25, 17, 'ABP24002', '2026-06-30', 22, '2025-11-05 11:59:20'),
(66, 64, '2025-11-05 12:00:31', 2025, 25, 17, 'AHO24008', '2027-10-31', 22, '2025-11-05 12:00:31'),
(67, 65, '2025-11-05 12:01:01', 2025, 25, 17, 'ABH24006', '2027-05-31', 22, '2025-11-05 12:01:01'),
(68, 66, '2025-11-05 12:03:07', 2025, 25, 17, 'CBDXD4004', '2026-05-31', 22, '2025-11-05 12:03:07'),
(69, 67, '2025-11-05 12:05:54', 2025, 25, 17, 'AEQ25001', '2027-02-28', 22, '2025-11-05 12:05:54'),
(70, 68, '2025-11-05 12:08:39', 2025, 25, 17, 'ABF25001A', '2026-10-31', 22, '2025-11-05 12:08:39'),
(71, 69, '2025-11-05 12:10:02', 2025, 25, 17, 'ACX25004A', '2028-03-31', 22, '2025-11-05 12:10:02'),
(72, 70, '2025-11-05 12:11:36', 2025, 25, 17, 'ADP24019A', '2027-07-31', 22, '2025-11-05 12:11:36'),
(73, 71, '2025-11-05 12:13:17', 2025, 25, 17, 'ADD24007', '2027-08-31', 22, '2025-11-05 12:13:17'),
(74, 72, '2025-11-05 12:14:56', 2025, 25, 17, 'AAF24003A', '2027-11-30', 22, '2025-11-05 12:14:56'),
(75, 73, '2025-11-05 12:17:20', 2025, 25, 17, 'BX028', '2026-07-31', 22, '2025-11-05 12:17:20'),
(76, 74, '2025-11-05 12:18:21', 2025, 25, 17, 'PB026', '2026-07-31', 22, '2025-11-05 12:18:21'),
(77, 75, '2025-11-05 12:22:14', 2025, 25, 17, 'B25071', '2027-01-31', 22, '2025-11-05 12:22:14'),
(78, 76, '2025-11-05 12:24:29', 2025, 25, 17, 'D25075', '2027-01-31', 22, '2025-11-05 12:24:29'),
(79, 77, '2025-11-05 12:25:56', 2025, 25, 17, '24022801', '2026-11-30', 22, '2025-11-05 12:25:56'),
(80, 78, '2025-11-05 12:27:13', 2025, 25, 17, 'PA053', '2026-07-31', 22, '2025-11-05 12:27:13'),
(81, 79, '2025-11-05 12:28:40', 2025, 25, 17, 'AA076', '2026-09-30', 22, '2025-11-05 12:28:40'),
(82, 80, '2025-11-05 12:29:47', 2025, 25, 17, 'AAG25001A', '2027-12-31', 22, '2025-11-05 12:29:47'),
(83, 81, '2025-11-05 12:31:02', 2025, 25, 17, 'AAZ24020A', '2027-10-31', 22, '2025-11-05 12:31:02'),
(84, 82, '2025-11-05 12:31:50', 2025, 25, 17, 'ABQ24004A', '2027-07-31', 22, '2025-11-05 12:31:50'),
(85, 83, '2025-11-05 12:33:19', 2025, 25, 17, 'ALB25001A', '2028-01-31', 22, '2025-11-05 12:33:19'),
(86, 84, '2025-11-05 12:35:32', 2025, 25, 17, 'ABY25001A', '2028-02-28', 22, '2025-11-05 12:35:32'),
(87, 85, '2025-11-05 12:36:28', 2025, 25, 17, 'ACY25001A', '2028-02-28', 22, '2025-11-05 12:36:28'),
(88, 86, '2025-11-05 12:38:24', 2025, 25, 17, 'ACC23015A', '2026-09-30', 22, '2025-11-05 12:38:24'),
(89, 87, '2025-11-05 12:39:36', 2025, 25, 17, 'AAE25001A', '2027-12-31', 22, '2025-11-05 12:39:36'),
(90, 88, '2025-11-05 12:41:56', 2025, 25, 17, 'AAS25001A', '2028-01-31', 22, '2025-11-05 12:41:56'),
(91, 89, '2025-11-05 12:43:53', 2025, 25, 17, 'AFS24003A', '2027-05-31', 22, '2025-11-05 12:43:53'),
(92, 90, '2025-11-05 12:46:07', 2025, 25, 17, 'E0150004', '2027-12-31', 22, '2025-11-05 12:46:07'),
(93, 91, '2025-11-05 12:47:57', 2025, 25, 17, 'AJZ24004A', '2027-10-31', 22, '2025-11-05 12:47:57'),
(94, 92, '2025-11-05 12:50:39', 2025, 25, 17, 'ABO25001', '2028-01-31', 22, '2025-11-05 12:50:39'),
(95, 93, '2025-11-05 12:54:35', 2025, 25, 17, 'AOE37', '0202-09-30', 22, '2025-11-05 12:54:35'),
(96, 94, '2025-11-05 12:56:04', 2025, 25, 17, 'WG24456', '2027-05-30', 22, '2025-11-05 12:56:04'),
(97, 95, '2025-11-05 12:57:52', 2025, 25, 17, 'BA4006', '2027-09-30', 22, '2025-11-05 12:57:52'),
(98, 96, '2025-11-05 13:00:07', 2025, 25, 17, 'GPN', '2027-07-31', 22, '2025-11-05 13:00:07'),
(99, 97, '2025-11-05 13:02:04', 2025, 25, 17, 'ATF10', '2027-09-30', 22, '2025-11-05 13:02:04'),
(100, 98, '2025-11-05 13:08:13', 2025, 25, 17, 'HA001', '2027-06-30', 22, '2025-11-05 13:08:13'),
(101, 99, '2025-11-05 13:09:30', 2025, 25, 17, 'QB24021,QB24022', '2027-06-30', 22, '2025-11-05 13:09:30'),
(102, 100, '2025-11-05 13:12:18', 2025, 25, 17, 'WR2501', '2027-12-31', 22, '2025-11-05 13:12:18'),
(103, 101, '2025-11-05 13:15:29', 2025, 25, 17, 'CPC132,133,134', '2027-05-31', 22, '2025-11-05 13:15:29'),
(104, 102, '2025-11-05 13:16:59', 2025, 25, 17, 'BJ4006', '2027-04-30', 22, '2025-11-05 13:16:59'),
(105, 103, '2025-11-05 13:20:26', 2025, 25, 17, 'CD24007', '2027-06-30', 22, '2025-11-05 13:20:26'),
(106, 104, '2025-11-05 13:22:13', 2025, 25, 17, 'NZ4007,NZ4008', '2027-06-30', 22, '2025-11-05 13:22:13'),
(107, 105, '2025-11-05 13:25:04', 2025, 25, 17, 'M0895TOM0898', '2027-07-31', 22, '2025-11-05 13:25:04'),
(108, 106, '2025-11-05 13:27:32', 2025, 25, 17, '2419570L001', '2027-09-30', 22, '2025-11-05 13:27:32'),
(109, 107, '2025-11-05 13:30:57', 2025, 25, 17, 'L2434', '2027-01-31', 22, '2025-11-05 13:30:57'),
(110, 108, '2025-11-05 13:32:03', 2025, 25, 17, 'CB250032,CVB25004', '2028-03-31', 22, '2025-11-05 13:32:03'),
(111, 109, '2025-11-05 13:32:46', 2025, 25, 17, 'PGL97PGL101', '2028-10-31', 22, '2025-11-05 13:32:46'),
(112, 110, '2025-11-05 13:34:04', 2025, 25, 17, 'L2677TOL2680', '2027-02-28', 22, '2025-11-05 13:34:04'),
(113, 111, '2025-11-05 13:35:26', 2025, 25, 17, 'S2520021', '2026-12-31', 22, '2025-11-05 13:35:26'),
(114, 112, '2025-11-05 13:37:04', 2025, 25, 17, 'AM4001,AM4002,AM4003', '2027-01-31', 22, '2025-11-05 13:37:04'),
(115, 113, '2025-11-05 13:39:28', 2025, 25, 17, 'L2498TOL2501', '2027-01-31', 22, '2025-11-05 13:39:28'),
(116, 114, '2025-11-05 13:41:38', 2025, 25, 17, '24189470T005L,241894', '2027-03-30', 22, '2025-11-05 13:41:38'),
(117, 115, '2025-11-05 13:42:14', 2025, 25, 17, '2415226L001', '2027-02-28', 22, '2025-11-05 13:42:14'),
(118, 116, '2025-11-05 13:43:22', 2025, 25, 17, 'CN4054,CN4055', '2027-02-28', 22, '2025-11-05 13:43:22'),
(119, 117, '2025-11-05 13:44:34', 2025, 25, 17, '13425Z060', '2028-01-31', 22, '2025-11-05 13:44:34'),
(120, 118, '2025-11-05 13:47:41', 2025, 25, 17, 'DLC4TC011,DLC4TC012', '2027-09-30', 22, '2025-11-05 13:47:41'),
(121, 119, '2025-11-05 13:48:51', 2025, 25, 17, 'GV014,GV015', '2026-07-31', 22, '2025-11-05 13:48:51'),
(122, 120, '2025-11-05 13:50:24', 2025, 25, 17, '0G010,0G011', '2026-07-31', 22, '2025-11-05 13:50:24'),
(123, 121, '2025-11-05 13:51:18', 2025, 25, 17, 'D25161', '2027-03-31', 22, '2025-11-05 13:51:18'),
(124, 122, '2025-11-05 13:52:21', 2025, 25, 17, 'L2348', '2027-10-31', 22, '2025-11-05 13:52:21'),
(125, 123, '2025-11-05 13:55:39', 2025, 25, 17, '243041026', '2027-04-30', 22, '2025-11-05 13:55:39'),
(126, 124, '2025-11-05 13:56:22', 2025, 25, 17, 'WH24027', '2027-01-31', 22, '2025-11-05 13:56:22'),
(127, 125, '2025-11-05 14:00:32', 2025, 25, 17, 'DLR24HG001,G002,G3', '2027-06-30', 22, '2025-11-05 14:00:32'),
(128, 126, '2025-11-05 14:01:43', 2025, 25, 17, '122110', '2027-05-31', 22, '2025-11-05 14:01:43'),
(129, 127, '2025-11-05 14:04:17', 2025, 25, 17, 'MTR54,55,56,57,58,5', '2027-04-30', 22, '2025-11-05 14:04:17'),
(130, 128, '2025-11-05 14:05:22', 2025, 25, 17, 'EF4003,EF4004,EF4005', '2027-08-31', 22, '2025-11-05 14:05:22'),
(131, 129, '2025-11-05 14:06:03', 2025, 25, 17, 'WS2511', '2027-12-31', 22, '2025-11-05 14:06:03'),
(132, 130, '2025-11-05 14:07:22', 2025, 25, 17, 'PET858,859,860,861', '2027-12-31', 22, '2025-11-05 14:07:22'),
(133, 131, '2025-11-05 14:08:32', 2025, 25, 17, 'XE4I16', '2027-08-31', 22, '2025-11-05 14:08:32'),
(134, 132, '2025-11-05 14:09:26', 2025, 25, 17, '20230717', '2028-07-31', 22, '2025-11-05 14:09:26'),
(135, 133, '2025-11-05 14:10:27', 2025, 25, 17, 'CK24007', '2027-06-30', 22, '2025-11-05 14:10:27'),
(136, 134, '2025-11-05 14:11:17', 2025, 25, 17, 'GA24003', '2027-06-30', 22, '2025-11-05 14:11:17'),
(137, 135, '2025-11-05 14:12:43', 2025, 25, 17, '240442', '2027-03-31', 22, '2025-11-05 14:12:43'),
(138, 136, '2025-11-05 14:13:43', 2025, 25, 17, '241066', '2027-05-30', 22, '2025-11-05 14:13:43'),
(139, 137, '2025-11-05 14:14:35', 2025, 25, 17, '230373', '2026-03-31', 22, '2025-11-05 14:14:35'),
(140, 138, '2025-11-05 14:15:26', 2025, 25, 17, '2310181', '2026-06-30', 22, '2025-11-05 14:15:26'),
(141, 139, '2025-11-05 14:16:21', 2025, 25, 17, 'M1455', '2026-08-31', 22, '2025-11-05 14:16:21'),
(142, 140, '2025-11-05 14:17:17', 2025, 25, 17, 'M1459', '2026-08-30', 22, '2025-11-05 14:17:17'),
(143, 141, '2025-11-05 14:22:09', 2025, 25, 17, '2318178', '2027-10-31', 22, '2025-11-05 14:22:09'),
(144, 142, '2025-11-05 14:24:56', 2025, 25, 17, '240719', '2027-04-30', 22, '2025-11-05 14:24:56'),
(145, 143, '2025-11-05 14:27:54', 2025, 25, 17, '240972', '2027-05-30', 22, '2025-11-05 14:27:54'),
(146, 144, '2025-11-05 14:28:44', 2025, 25, 17, 'N1498', '2027-06-30', 22, '2025-11-05 14:28:44'),
(147, 145, '2025-11-05 14:29:36', 2025, 25, 17, '240739', '2027-04-30', 22, '2025-11-05 14:29:36'),
(148, 146, '2025-11-05 14:30:15', 2025, 25, 17, '240772', '2027-04-30', 22, '2025-11-05 14:30:15'),
(149, 147, '2025-11-05 14:31:33', 2025, 25, 17, 'BB0164B', '2027-02-28', 22, '2025-11-05 14:31:33'),
(150, 148, '2025-11-05 14:35:11', 2025, 25, 17, 'AK1234H', '2026-11-30', 22, '2025-11-05 14:35:11'),
(151, 149, '2025-11-05 14:37:24', 2025, 25, 17, '242229', '2026-11-30', 22, '2025-11-05 14:37:24'),
(152, 150, '2025-11-05 14:40:36', 2025, 25, 17, 'BE2411-40', '2027-07-30', 22, '2025-11-05 14:40:36'),
(153, 151, '2025-11-05 14:43:52', 2025, 25, 17, 'DC010-20', '2028-02-28', 22, '2025-11-05 14:43:52'),
(154, 152, '2025-11-05 14:46:23', 2025, 25, 17, 'DT065-85', '2027-01-30', 22, '2025-11-05 14:46:23'),
(155, 153, '2025-11-05 14:57:47', 2025, 25, 17, 'DL-014-15', '2028-02-28', 22, '2025-11-05 14:57:47'),
(156, 154, '2025-11-05 14:58:41', 2025, 25, 17, 'BT85-97', '2026-07-30', 22, '2025-11-05 14:58:41'),
(157, 155, '2025-11-05 15:00:48', 2025, 25, 17, 'D35003', '2028-01-30', 22, '2025-11-05 15:00:48'),
(158, 156, '2025-11-05 15:02:36', 2025, 25, 17, 'CE144-47', '2026-08-30', 22, '2025-11-05 15:02:36'),
(159, 157, '2025-11-05 15:03:51', 2025, 25, 17, 'DE055-60', '2028-02-28', 22, '2025-11-05 15:03:51'),
(160, 158, '2025-11-05 15:07:12', 2025, 25, 17, 'CE103-108', '2027-06-30', 22, '2025-11-05 15:07:12'),
(161, 159, '2025-11-05 15:09:38', 2025, 25, 17, 'SR4489', '2027-08-30', 22, '2025-11-05 15:09:38'),
(162, 160, '2025-11-05 15:10:51', 2025, 25, 17, '2408155-160', '2027-07-30', 22, '2025-11-05 15:10:51'),
(163, 161, '2025-11-05 15:11:36', 2025, 25, 17, 'NL4087', '2027-02-28', 22, '2025-11-05 15:11:36'),
(164, 162, '2025-11-05 15:15:30', 2025, 25, 17, 'BCHZ2401', '2027-01-30', 22, '2025-11-05 15:15:30'),
(165, 163, '2025-11-05 15:17:49', 2025, 25, 17, 'CT110', '2027-04-30', 22, '2025-11-05 15:17:49'),
(166, 164, '2025-11-05 15:19:54', 2025, 25, 17, 'D14325001', '2028-01-31', 22, '2025-11-05 15:19:54'),
(167, 165, '2025-11-05 15:22:45', 2025, 25, 17, 'B19725001', '2027-12-31', 22, '2025-11-05 15:22:45'),
(168, 166, '2025-11-05 15:23:51', 2025, 25, 17, 'D17225001', '2028-01-31', 22, '2025-11-05 15:23:51'),
(169, 167, '2025-11-05 15:27:46', 2025, 25, 17, 'D12025001', '2028-01-31', 22, '2025-11-05 15:27:46'),
(170, 168, '2025-11-05 15:31:05', 2025, 25, 17, '010P2416X', '2025-12-26', 22, '2025-11-05 15:31:05'),
(171, 169, '2025-11-05 15:33:02', 2025, 25, 17, 'D14125001', '2028-01-31', 22, '2025-11-05 15:33:02'),
(172, 170, '2025-11-05 15:38:43', 2025, 25, 17, '24GO26', '2026-06-30', 22, '2025-11-05 15:38:43'),
(173, 171, '2025-11-05 15:40:06', 2025, 25, 17, '24MK136', '2027-10-31', 22, '2025-11-05 15:40:06'),
(174, 172, '2025-11-05 15:42:21', 2025, 25, 17, 'HELL111', '2027-03-31', 22, '2025-11-05 15:42:21'),
(175, 173, '2025-11-05 15:45:20', 2025, 25, 17, 'HEML111', '2027-03-31', 22, '2025-11-05 15:45:20'),
(176, 174, '2025-11-05 15:46:16', 2025, 25, 17, 'HERL110', '2027-03-31', 22, '2025-11-05 15:46:16'),
(177, 175, '2025-11-05 15:49:58', 2025, 25, 17, 'HETL111', '2027-03-31', 22, '2025-11-05 15:49:58'),
(178, 176, '2025-11-05 15:53:21', 2025, 25, 17, 'D26824001', '2027-01-31', 22, '2025-11-05 15:53:21'),
(179, 177, '2025-11-05 15:54:33', 2025, 25, 17, '10241431', '2027-05-31', 22, '2025-11-05 15:54:33'),
(180, 178, '2025-11-05 15:55:29', 2025, 25, 17, '10241310', '2027-04-30', 22, '2025-11-05 15:55:29'),
(181, 179, '2025-11-05 15:57:38', 2025, 25, 17, '085G24', '2027-07-31', 22, '2025-11-05 15:57:38'),
(182, 180, '2025-11-05 15:59:48', 2025, 25, 17, 'D10125001', '2027-12-31', 22, '2025-11-05 15:59:48'),
(183, 181, '2025-11-05 16:02:49', 2025, 25, 17, 'XD10EJ73E', '2027-01-07', 22, '2025-11-05 16:02:49'),
(184, 182, '2025-11-05 16:04:37', 2025, 25, 17, '#######', '2028-12-31', 22, '2025-11-05 16:04:37'),
(185, 183, '2025-11-05 16:08:19', 2025, 25, 17, 'D10525001', '2027-12-31', 22, '2025-11-05 16:08:19'),
(186, 184, '2025-11-05 16:09:23', 2025, 25, 17, 'D10325001', '2028-01-31', 22, '2025-11-05 16:09:23'),
(187, 185, '2025-11-05 16:12:45', 2025, 25, 17, 'NFPL41010', '2027-10-31', 22, '2025-11-05 16:12:45'),
(188, 186, '2025-11-05 16:14:02', 2025, 25, 17, '24G011', '2027-06-30', 22, '2025-11-05 16:14:02'),
(189, 187, '2025-11-05 16:32:48', 2025, 25, 17, '24K031', '2026-10-31', 22, '2025-11-05 16:32:48'),
(190, 188, '2025-11-05 16:34:41', 2025, 25, 17, '20250227', '2030-02-26', 22, '2025-11-05 16:34:41'),
(191, 189, '2025-11-05 16:36:15', 2025, 25, 17, '#######', '2030-03-03', 22, '2025-11-05 16:36:15'),
(192, 190, '2025-11-05 16:38:28', 2025, 25, 17, 'D04225001', '2027-12-31', 22, '2025-11-05 16:38:28'),
(193, 191, '2025-11-05 16:40:26', 2025, 25, 17, 'CYH002', '2028-01-31', 22, '2025-11-05 16:40:26'),
(194, 192, '2025-11-05 16:47:31', 2025, 25, 17, 'MZ250318', '2030-02-28', 22, '2025-11-05 16:47:31'),
(195, 193, '2025-11-05 16:48:53', 2025, 25, 17, '24104001', '2029-03-30', 22, '2025-11-05 16:48:53'),
(196, 194, '2025-11-05 16:49:50', 2025, 25, 17, '24044033', '2029-03-31', 22, '2025-11-05 16:49:50'),
(197, 195, '2025-11-05 16:50:45', 2025, 25, 17, '24014003', '2028-12-30', 22, '2025-11-05 16:50:45'),
(198, 196, '2025-11-05 16:53:31', 2025, 25, 17, '20240816', '2026-08-21', 22, '2025-11-05 16:53:31'),
(199, 197, '2025-11-05 16:54:16', 2025, 25, 17, 'MZ250121', '2026-12-31', 22, '2025-11-05 16:54:16'),
(200, 198, '2025-11-05 16:55:50', 2025, 25, 17, 'GP039BE', '2028-02-28', 22, '2025-11-05 16:55:50'),
(201, 199, '2025-11-05 16:59:39', 2025, 25, 17, 'GP02U9P', '2026-07-31', 22, '2025-11-05 16:59:39'),
(202, 200, '2025-11-05 17:01:07', 2025, 25, 17, 'MZ240903', '2027-08-31', 22, '2025-11-05 17:01:07'),
(203, 201, '2025-11-05 17:04:32', 2025, 25, 17, 'MZ240903', '2027-08-31', 22, '2025-11-05 17:04:32'),
(204, 202, '2025-11-05 17:06:34', 2025, 25, 17, 'AAD25001', '2028-01-31', 22, '2025-11-05 17:06:34'),
(205, 203, '2025-11-05 17:10:38', 2025, 25, 17, '2410086', '2027-09-30', 22, '2025-11-05 17:10:38'),
(206, 204, '2025-11-05 17:11:42', 2025, 25, 17, '2410131', '2027-09-30', 22, '2025-11-05 17:11:42'),
(207, 205, '2025-11-05 17:13:03', 2025, 25, 17, 'U24C0165A', '2027-09-30', 22, '2025-11-05 17:13:03'),
(208, 206, '2025-11-05 17:14:56', 2025, 25, 17, '2403194A', '2027-02-28', 22, '2025-11-05 17:14:56'),
(209, 207, '2025-11-05 17:20:41', 2025, 25, 17, 'NAD2410C', '2026-12-30', 22, '2025-11-05 17:20:41'),
(210, 208, '2025-11-05 17:21:56', 2025, 25, 17, 'NA24391B', '2027-01-30', 22, '2025-11-05 17:21:56'),
(211, 209, '2025-11-05 17:23:11', 2025, 25, 17, '116257', '2027-04-30', 22, '2025-11-05 17:23:11'),
(212, 210, '2025-11-05 17:24:31', 2025, 25, 17, '120476', '20299-11-30', 22, '2025-11-05 17:24:31'),
(213, 211, '2025-11-05 17:27:41', 2025, 25, 17, '230755', '2026-06-30', 22, '2025-11-05 17:27:41'),
(214, 212, '2025-11-06 09:28:12', 2025, 25, 17, '2409049,2409050,2410130A', '2027-08-30', 22, '2025-11-06 09:28:12'),
(215, 213, '2025-11-06 09:30:00', 2025, 25, 17, '2408098,2408096', '2027-07-30', 22, '2025-11-06 09:30:00'),
(216, 214, '2025-11-06 09:31:32', 2025, 25, 17, '2411100', '2027-10-23', 22, '2025-11-06 09:31:32'),
(217, 215, '2025-11-06 09:33:08', 2025, 25, 17, '2409047', '2027-08-30', 22, '2025-11-06 09:33:08'),
(218, 216, '2025-11-06 09:34:12', 2025, 25, 17, '2408076,2408077,2407248A', '2027-06-30', 22, '2025-11-06 09:34:12'),
(219, 217, '2025-11-06 09:35:55', 2025, 25, 17, '2407248A', '2027-06-30', 22, '2025-11-06 09:35:55'),
(220, 218, '2025-11-06 09:37:51', 2025, 25, 17, '2410095/96/97,2410174/175', '2027-09-30', 22, '2025-11-06 09:37:51'),
(221, 219, '2025-11-06 09:39:40', 2025, 25, 17, 'HCG24080032,24080033', '2026-08-30', 22, '2025-11-06 09:39:40'),
(222, 220, '2025-11-06 09:40:15', 2025, 25, 17, '25014', '2030-03-30', 22, '2025-11-06 09:40:15'),
(223, 221, '2025-11-06 09:54:10', 2025, 25, 17, 'S366524038TO,S33524040', '2027-03-30', 22, '2025-11-06 09:54:10'),
(224, 222, '2025-11-06 10:41:22', 2025, 25, 17, 'K053240762', '2027-10-30', 22, '2025-11-06 10:41:22'),
(225, 223, '2025-11-06 10:49:23', 2025, 25, 17, '118328', '2029-07-30', 22, '2025-11-06 10:49:23'),
(226, 224, '2025-11-06 10:50:30', 2025, 25, 17, 'T5149', '2028-02-03', 22, '2025-11-06 10:50:30'),
(227, 225, '2025-11-06 10:54:49', 2025, 25, 17, '24150302', '2027-09-30', 22, '2025-11-06 10:54:49'),
(228, 226, '2025-11-06 10:57:36', 2025, 25, 17, 'U24T1302A', '2024-09-30', 22, '2025-11-06 10:57:36'),
(229, 227, '2025-11-06 11:02:54', 2025, 25, 17, '24159001', '2026-09-30', 22, '2025-11-06 11:02:54'),
(230, 228, '2025-11-06 11:07:52', 2025, 25, 17, 'MAC2402A', '2027-04-30', 22, '2025-11-06 11:07:52'),
(231, 229, '2025-11-06 11:13:26', 2025, 25, 17, '123239/240/241/242', '2030-04-30', 22, '2025-11-06 11:13:26'),
(232, 230, '2025-11-06 11:16:58', 2025, 25, 17, 'K720240052', '2027-03-30', 22, '2025-11-06 11:16:58'),
(233, 231, '2025-11-06 11:23:57', 2025, 25, 17, 'mo-5073', '2026-10-30', 22, '2025-11-06 11:23:57'),
(234, 232, '2025-11-06 11:27:35', 2025, 25, 17, '82SI106806', '2026-08-30', 22, '2025-11-06 11:27:35'),
(235, 233, '2025-11-06 11:31:16', 2025, 25, 17, '82SH106602 ', '2026-07-30', 22, '2025-11-06 11:31:16'),
(236, 234, '2025-11-06 11:34:29', 2025, 25, 17, '82TG204601', '2027-06-30', 22, '2025-11-06 11:34:29'),
(237, 235, '2025-11-06 11:37:35', 2025, 25, 17, '82SI104801,02,03,04,05', '0026-08-20', 22, '2025-11-06 11:37:35'),
(238, 236, '2025-11-06 11:40:00', 2025, 25, 17, 'EK0204A', '2027-11-30', 22, '2025-11-06 11:40:00'),
(239, 237, '2025-11-06 11:42:49', 2025, 25, 17, '4814Z001', '2027-02-28', 22, '2025-11-06 11:42:49'),
(240, 238, '2025-11-06 11:46:06', 2025, 25, 17, 'CN2402', '275760-03-06', 22, '2025-11-06 11:46:06'),
(241, 239, '2025-11-06 11:49:55', 2025, 25, 17, '202403', '2026-05-04', 22, '2025-11-06 11:49:55'),
(242, 240, '2025-11-06 11:54:11', 2025, 25, 17, 'L2191/L2192', '2026-09-30', 22, '2025-11-06 11:54:11'),
(243, 241, '2025-11-06 11:58:01', 2025, 25, 17, '25014052', '2029-12-31', 22, '2025-11-06 11:58:01'),
(244, 242, '2025-11-06 12:00:09', 2025, 25, 17, 'MZ221017', '2035-03-03', 22, '2025-11-06 12:00:09'),
(245, 243, '2025-11-06 12:05:53', 2025, 25, 17, 'FB4008', '2027-04-30', 22, '2025-11-06 12:05:53'),
(246, 244, '2025-11-06 12:08:15', 2025, 25, 17, '26440113', '2027-02-28', 22, '2025-11-06 12:08:15'),
(247, 245, '2025-11-06 12:11:48', 2025, 25, 17, 'ADI24008A', '2027-09-30', 22, '2025-11-06 12:11:48'),
(248, 246, '2025-11-06 12:14:51', 2025, 25, 17, 'ABJ24002A', '2027-04-30', 22, '2025-11-06 12:14:51'),
(249, 247, '2025-11-06 12:20:53', 2025, 25, 17, '2402180-1', '2026-10-31', 22, '2025-11-06 12:20:53'),
(250, 248, '2025-11-06 12:24:36', 2025, 25, 17, 'AFL24003A', '2027-01-31', 22, '2025-11-06 12:24:36'),
(251, 249, '2025-11-06 12:27:21', 2025, 25, 17, 'ADX23004A', '2026-08-31', 22, '2025-11-06 12:27:21'),
(252, 250, '2025-11-06 12:31:23', 2025, 25, 17, 'ACJ25001A', '2027-12-31', 22, '2025-11-06 12:31:23'),
(253, 251, '2025-11-06 12:33:28', 2025, 25, 17, 'ADC24002A', '2027-01-31', 22, '2025-11-06 12:33:28'),
(254, 252, '2025-11-06 12:35:27', 2025, 25, 17, 'ABA23007', '2026-10-31', 22, '2025-11-06 12:35:27'),
(255, 253, '2025-11-06 12:41:01', 2025, 25, 17, 'AAM24003A', '2027-02-28', 22, '2025-11-06 12:41:01'),
(256, 254, '2025-11-06 12:43:03', 2025, 25, 17, 'AHL24001A', '2027-04-30', 22, '2025-11-06 12:43:03'),
(257, 255, '2025-11-06 12:44:59', 2025, 25, 17, 'AEJ25003A', '2028-03-31', 22, '2025-11-06 12:44:59'),
(258, 256, '2025-11-06 12:46:59', 2025, 25, 17, 'A-109002', '2026-08-31', 22, '2025-11-06 12:46:59'),
(259, 257, '2025-11-06 12:49:34', 2025, 25, 17, 'E0140254', '2027-10-31', 22, '2025-11-06 12:49:34'),
(260, 258, '2025-11-06 12:52:34', 2025, 25, 17, '1010', '2028-01-31', 22, '2025-11-06 12:52:34'),
(261, 259, '2025-11-06 12:55:20', 2025, 25, 17, '632F241', '2028-02-28', 22, '2025-11-06 12:55:20'),
(262, 260, '2025-11-06 12:57:34', 2025, 25, 17, '303E24MU', '2027-04-30', 22, '2025-11-06 12:57:34'),
(263, 261, '2025-11-06 12:59:34', 2025, 25, 17, '4Z098', '2027-01-31', 22, '2025-11-06 12:59:34'),
(264, 262, '2025-11-06 13:02:28', 2025, 25, 17, '240750', '2029-12-31', 22, '2025-11-06 13:02:28'),
(265, 263, '2025-11-06 13:04:39', 2025, 25, 17, '230695', '2028-10-31', 22, '2025-11-06 13:04:39'),
(266, 264, '2025-11-06 13:09:29', 2025, 25, 17, '250042', '2028-02-28', 22, '2025-11-06 13:09:29'),
(267, 265, '2025-11-06 13:11:41', 2025, 25, 17, '2405053', '2028-05-31', 22, '2025-11-06 13:11:41'),
(268, 266, '2025-11-06 13:14:01', 2025, 25, 17, '29465', '2027-02-28', 22, '2025-11-06 13:14:01'),
(269, 267, '2025-11-06 13:17:45', 2025, 25, 17, '5758', '2028-03-31', 22, '2025-11-06 13:17:45'),
(270, 268, '2025-11-06 13:19:39', 2025, 25, 17, '29174', '2026-11-30', 22, '2025-11-06 13:19:39'),
(271, 269, '2025-11-06 13:22:27', 2025, 25, 17, 'C23124', '2028-01-31', 22, '2025-11-06 13:22:27'),
(272, 270, '2025-11-06 13:24:27', 2025, 25, 17, '1962-19666', '2026-02-28', 22, '2025-11-06 13:24:27'),
(273, 271, '2025-11-06 13:26:17', 2025, 25, 17, 'BEC-056', '2026-07-30', 22, '2025-11-06 13:26:17'),
(274, 272, '2025-11-06 13:28:04', 2025, 25, 17, 'E0130129', '2026-11-30', 22, '2025-11-06 13:28:04'),
(275, 273, '2025-11-06 13:30:50', 2025, 25, 17, 'NZ240902', '2027-08-30', 22, '2025-11-06 13:30:50'),
(276, 274, '2025-11-06 13:32:53', 2025, 25, 17, 'ML24783', '2026-10-31', 22, '2025-11-06 13:32:53'),
(277, 275, '2025-11-06 13:35:56', 2025, 25, 17, 'D20424001', '2027-07-31', 22, '2025-11-06 13:35:56'),
(278, 276, '2025-11-06 13:38:11', 2025, 25, 17, 'D20324001', '2027-07-31', 22, '2025-11-06 13:38:11'),
(279, 277, '2025-11-06 13:40:16', 2025, 25, 17, '24353002', '2026-03-31', 22, '2025-11-06 13:40:16'),
(280, 278, '2025-11-06 13:47:50', 2025, 25, 17, '20402033', '2026-07-31', 22, '2025-11-06 13:47:50'),
(281, 279, '2025-11-06 13:51:01', 2025, 25, 17, '24B007', '2027-04-30', 22, '2025-11-06 13:51:01'),
(282, 280, '2025-11-06 13:52:51', 2025, 25, 17, '23G081', '2026-06-30', 22, '2025-11-06 13:52:51'),
(283, 281, '2025-11-06 13:54:13', 2025, 25, 17, '11301013', '2025-12-31', 22, '2025-11-06 13:54:13'),
(284, 282, '2025-11-06 13:55:43', 2025, 25, 17, '23MI017', '2026-08-31', 22, '2025-11-06 13:55:43'),
(285, 283, '2025-11-06 13:57:22', 2025, 25, 17, '24443001', '2026-04-30', 22, '2025-11-06 13:57:22'),
(286, 284, '2025-11-06 13:59:06', 2025, 25, 17, '083G24', '2029-07-31', 22, '2025-11-06 13:59:06'),
(287, 285, '2025-11-06 14:01:13', 2025, 25, 17, 'SS528', '2026-02-28', 22, '2025-11-06 14:01:13'),
(288, 286, '2025-11-06 14:02:56', 2025, 25, 17, 'GO1K12', '2027-03-31', 22, '2025-11-06 14:02:56'),
(289, 287, '2025-11-06 14:04:03', 2025, 25, 17, 'D752402', '2027-08-31', 22, '2025-11-06 14:04:03'),
(290, 288, '2025-11-06 14:05:51', 2025, 25, 17, '24K105', '2027-10-31', 22, '2025-11-06 14:05:51'),
(291, 289, '2025-11-06 14:08:46', 2025, 25, 17, 'D04225001', '2027-12-31', 22, '2025-11-06 14:08:46'),
(292, 290, '2025-11-06 14:10:29', 2025, 25, 17, 'CYH002', '2028-01-31', 22, '2025-11-06 14:10:29'),
(293, 291, '2025-11-06 14:12:15', 2025, 25, 17, 'EK36', '2026-12-31', 22, '2025-11-06 14:12:15'),
(294, 292, '2025-11-06 16:18:05', 2025, 25, 17, '4197036', '2029-09-03', 23, '2025-11-06 16:18:05'),
(295, 293, '2025-11-06 16:24:58', 2025, 25, 17, 'NR01873A', '2026-09-30', 23, '2025-11-06 16:24:58'),
(296, 294, '2025-11-06 16:26:36', 2025, 25, 17, 'NR02280A', '2029-03-30', 23, '2025-11-06 16:26:36'),
(297, 295, '2025-11-06 16:32:45', 2025, 25, 17, '2402082', '2029-01-30', 23, '2025-11-06 16:32:45'),
(298, 296, '2025-11-06 16:39:55', 2025, 25, 17, '2402081', '2029-01-30', 23, '2025-11-06 16:39:55'),
(299, 297, '2025-11-06 16:41:53', 2025, 25, 17, '24028801', '2029-01-30', 23, '2025-11-06 16:41:53'),
(300, 298, '2025-11-06 16:43:06', 2025, 25, 17, '2402083', '2029-01-30', 23, '2025-11-06 16:43:06'),
(301, 299, '2025-11-06 16:47:23', 2025, 25, 17, 'CRI24277', '2024-08-19', 23, '2025-11-06 16:47:23'),
(302, 300, '2025-11-06 16:48:43', 2025, 25, 17, 'CSM24281', '2029-01-30', 23, '2025-11-06 16:48:43'),
(303, 301, '2025-11-06 16:49:47', 2025, 25, 17, 'CSM24287', '2029-01-30', 23, '2025-11-06 16:49:47'),
(304, 302, '2025-11-06 16:50:59', 2025, 25, 17, 'CDO24284', '2029-01-30', 23, '2025-11-06 16:50:59'),
(305, 303, '2025-11-06 16:55:08', 2025, 25, 17, 'NR02337A', '2027-10-30', 23, '2025-11-06 16:55:08'),
(306, 304, '2025-11-06 17:01:33', 2025, 25, 17, 'CRI24376', '2029-09-30', 23, '2025-11-06 17:01:33'),
(307, 305, '2025-11-06 17:06:59', 2025, 25, 17, '230327', '2026-02-28', 23, '2025-11-06 17:06:59'),
(308, 306, '2025-11-06 17:08:53', 2025, 25, 17, '230328', '2026-02-28', 23, '2025-11-06 17:08:53'),
(309, 307, '2025-11-06 17:17:59', 2025, 25, 17, 'L241066', '2027-05-30', 23, '2025-11-06 17:17:59'),
(310, 308, '2025-11-06 17:19:44', 2025, 25, 17, 'L230373', '2026-03-30', 23, '2025-11-06 17:19:44'),
(311, 309, '2025-11-07 09:49:39', 2025, 25, 17, 'AHL24001A', '2027-04-30', 22, '2025-11-07 09:49:39'),
(312, 310, '2025-11-07 10:52:08', 2025, 25, 17, 'ACY25001A', '2028-02-28', 22, '2025-11-07 10:52:08'),
(313, 311, '2025-11-07 11:14:07', 2025, 25, 17, 'DC24007', '2027-06-30', 22, '2025-11-07 11:14:07'),
(314, 312, '2025-11-07 11:19:33', 2025, 25, 17, '2419570L001', '2027-09-30', 22, '2025-11-07 11:19:33'),
(315, 313, '2025-11-08 11:30:02', 2025, 25, 17, 'U24T1302A', '2027-10-30', 22, '2025-11-08 11:30:02'),
(316, 314, '2025-11-08 19:28:11', 2025, 25, 17, 'AAB25003A', '2027-12-30', 22, '2025-11-08 19:28:11'),
(317, 315, '2025-11-10 10:22:15', 2025, 25, 17, '632F241', '2028-02-28', 22, '2025-11-10 10:22:15'),
(318, 316, '2025-11-10 10:50:33', 2025, 25, 17, '632F241', '2028-01-31', 22, '2025-11-10 10:50:33'),
(319, 317, '2025-11-10 10:52:19', 2025, 25, 17, '632F241', '2028-02-28', 22, '2025-11-10 10:52:19'),
(320, 318, '2025-11-10 12:20:33', 2025, 25, 17, 'PA053', '2026-07-31', 22, '2025-11-10 12:20:33'),
(321, 319, '2025-11-10 13:19:16', 2025, 25, 17, 'AA076', '2026-09-30', 22, '2025-11-10 13:19:16'),
(322, 320, '2025-11-10 13:33:45', 2025, 9, 17, 'ABQ24004A', '2027-07-31', 22, '2025-11-10 13:33:45'),
(323, 321, '2025-11-10 13:41:03', 2025, 25, 17, 'ABQ24004A', '2027-07-31', 22, '2025-11-10 13:41:03'),
(324, 322, '2025-11-10 13:49:19', 2025, 9, 17, 'ABY25001A', '2028-02-28', 22, '2025-11-10 13:49:19'),
(325, 323, '2025-11-10 13:55:49', 2025, 9, 17, 'ACC23015A', '2026-09-30', 22, '2025-11-10 13:55:49'),
(326, 324, '2025-11-10 14:11:00', 2025, 9, 17, 'AFS24003A', '2027-05-31', 22, '2025-11-10 14:11:00'),
(327, 325, '2025-11-10 14:14:14', 2025, 9, 17, 'AJZ24004A', '2027-10-31', 22, '2025-11-10 14:14:14'),
(328, 326, '2025-11-10 14:27:25', 2025, 9, 17, 'ADD24007A', '2027-08-31', 22, '2025-11-10 14:27:25'),
(329, 327, '2025-11-10 14:31:55', 2025, 9, 17, 'ABH24006', '2027-05-31', 22, '2025-11-10 14:31:55'),
(330, 328, '2025-11-10 14:56:18', 2025, 10, 17, 'ACI24016A', '2027-06-30', 22, '2025-11-10 14:56:18'),
(331, 329, '2025-11-10 14:59:28', 2025, 10, 17, 'ABW25004', '2027-12-31', 22, '2025-11-10 14:59:28'),
(332, 330, '2025-11-10 15:11:07', 2025, 10, 17, 'ACQ2021', '2027-10-31', 22, '2025-11-10 15:11:07'),
(333, 331, '2025-11-10 15:14:37', 2025, 10, 17, 'AAA24044', '2027-10-31', 22, '2025-11-10 15:14:37'),
(334, 332, '2025-11-10 15:18:08', 2025, 10, 17, 'ADT25001', '2027-10-31', 22, '2025-11-10 15:18:08'),
(335, 333, '2025-11-10 15:22:10', 2025, 10, 17, 'ADS24007', '2027-11-30', 22, '2025-11-10 15:22:10'),
(336, 334, '2025-11-10 15:29:39', 2025, 13, 17, 'ABE25001', '2027-12-30', 22, '2025-11-10 15:29:39'),
(337, 335, '2025-11-10 15:34:07', 2025, 13, 17, 'AEV24007', '2027-10-30', 22, '2025-11-10 15:34:07'),
(338, 336, '2025-11-10 15:42:21', 2025, 13, 17, 'ACL24008A', '2027-10-31', 22, '2025-11-10 15:42:21'),
(339, 337, '2025-11-10 15:51:36', 2025, 25, 17, 'ADW24004', '2027-03-31', 22, '2025-11-10 15:51:36'),
(340, 338, '2025-11-10 15:55:21', 2025, 13, 17, 'ACH24005', '2027-11-30', 22, '2025-11-10 15:55:21'),
(341, 339, '2025-11-10 16:01:17', 2025, 11, 17, 'ABG24009', '2027-09-30', 22, '2025-11-10 16:01:17'),
(342, 340, '2025-11-10 16:05:42', 2025, 11, 17, 'ABG24009', '2027-09-30', 22, '2025-11-10 16:05:42'),
(343, 341, '2025-11-11 11:53:32', 2025, 14, 17, 'AEA25001', '2027-12-31', 22, '2025-11-11 11:53:32'),
(344, 342, '2025-11-11 12:01:23', 2025, 18, 17, 'AFQ25002', '2028-01-31', 22, '2025-11-11 12:01:23'),
(345, 343, '2025-11-11 12:05:31', 2025, 25, 17, 'AFQ25001A', '2028-03-31', 22, '2025-11-11 12:05:31'),
(346, 344, '2025-11-11 12:08:11', 2025, 14, 17, 'ADJ25002A', '2027-12-31', 22, '2025-11-11 12:08:11'),
(347, 345, '2025-11-11 12:13:52', 2025, 14, 17, 'AAM24003', '2027-02-28', 22, '2025-11-11 12:13:52'),
(348, 346, '2025-11-11 12:56:08', 2025, 14, 17, 'ACG24004', '2027-11-30', 22, '2025-11-11 12:56:08'),
(349, 347, '2025-11-11 12:59:40', 2025, 14, 17, 'AHL24001', '2027-04-30', 22, '2025-11-11 12:59:40'),
(350, 348, '2025-11-11 13:05:20', 2025, 19, 17, 'AEJ25003A', '2028-03-31', 22, '2025-11-11 13:05:20'),
(351, 349, '2025-11-11 13:14:07', 2025, 19, 17, 'ADA23006', '2026-05-31', 22, '2025-11-11 13:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `entrega`
--

CREATE TABLE `entrega` (
  `identrega` int(11) NOT NULL,
  `qtdentrega` double NOT NULL,
  `precoentrega` decimal(10,2) NOT NULL,
  `totalentrega` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `clienteentrega` int(11) NOT NULL,
  `usuarioentrega` int(11) NOT NULL,
  `pedidoentrega` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `datavenda` datetime NOT NULL,
  `produtoentrega` int(11) DEFAULT NULL,
  `lote` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `entrega`
--

INSERT INTO `entrega` (`identrega`, `qtdentrega`, `precoentrega`, `totalentrega`, `iva`, `clienteentrega`, `usuarioentrega`, `pedidoentrega`, `periodo`, `datavenda`, `produtoentrega`, `lote`) VALUES
(1, 1, 63.00, 63.00, 10.08, 1, 22, 1, 3, '2025-11-06 17:28:06', 83493, 'ABA23007'),
(2, 1, 119.00, 119.00, 0.00, 1, 23, 2, 4, '2025-11-06 17:38:47', 716, 'AEL24017A'),
(3, 2, 25.00, 50.00, 0.00, 1, 22, 3, 3, '2025-11-06 17:47:44', 631, 'ABQ24004A'),
(4, 1, 55.00, 55.00, 0.00, 1, 22, 3, 3, '2025-11-06 17:47:44', 476, 'DC010-20'),
(5, 2, 15.00, 30.00, 0.00, 1, 22, 3, 3, '2025-11-06 17:47:44', 965, 'ABW25004'),
(6, 1, 19.00, 19.00, 0.00, 1, 23, 4, 4, '2025-11-06 20:27:33', 938, 'HETL111'),
(7, 1, 20.00, 20.00, 0.00, 1, 24, 6, 5, '2025-11-07 12:41:27', 1577, 'HERL110'),
(8, 1, 37.00, 37.00, 0.00, 1, 24, 7, 5, '2025-11-07 13:13:25', 83370, 'MZ240901'),
(9, 1, 10.00, 10.00, 0.00, 1, 24, 8, 5, '2025-11-07 14:15:06', 965, 'ABW25004'),
(10, 1, 20.00, 20.00, 0.00, 1, 24, 8, 5, '2025-11-07 14:15:06', 722, 'ACQ24021'),
(11, 1, 180.00, 180.00, 0.00, 1, 23, 9, 7, '2025-11-07 15:08:58', 104, 'WS2511'),
(12, 1, 55.00, 55.00, 0.00, 1, 23, 9, 7, '2025-11-07 15:08:58', 476, 'DC010-20'),
(13, 1, 98.00, 98.00, 0.00, 1, 23, 10, 7, '2025-11-07 16:37:14', 1647, 'DL-014-15'),
(14, 1, 119.00, 119.00, 0.00, 1, 23, 10, 7, '2025-11-07 16:37:14', 1714, 'D12025001'),
(15, 4, 20.00, 80.00, 0.00, 1, 24, 11, 10, '2025-11-08 18:00:24', 1517, 'AAB25002'),
(16, 4, 20.00, 80.00, 0.00, 1, 24, 12, 10, '2025-11-08 18:02:19', 1517, 'AAB25002'),
(17, 2, 10.00, 20.00, 0.00, 1, 24, 13, 10, '2025-11-08 18:06:42', 1480, 'AAA24044'),
(18, 1, 10.00, 10.00, 0.00, 1, 24, 13, 10, '2025-11-08 18:06:42', 965, 'ABW25004'),
(19, 1, 130.00, 130.00, 0.00, 1, 24, 14, 10, '2025-11-08 19:07:14', 1423, 'D04225001'),
(20, 1, 328.00, 328.00, 0.00, 1, 22, 15, 3, '2025-11-10 10:22:43', 250, '632F241'),
(21, 1, 328.00, 328.00, 0.00, 1, 24, 16, 11, '2025-11-10 10:46:23', 250, '632F241'),
(22, 1, 100.00, 100.00, 0.00, 1, 24, 17, 11, '2025-11-10 10:58:19', 1209, 'HCG24080032,24080033'),
(23, 2, 13.00, 26.00, 0.00, 1, 23, 18, 8, '2025-11-10 15:04:17', 1672, 'ABQ24004A'),
(24, 1, 10.00, 10.00, 0.00, 1, 23, 19, 8, '2025-11-10 18:03:24', 714, 'ACI24016A'),
(25, 1, 10.00, 10.00, 0.00, 1, 23, 19, 8, '2025-11-10 18:03:24', 965, 'ABW25004'),
(26, 2, 20.00, 40.00, 0.00, 1, 23, 20, 8, '2025-11-10 18:08:26', 1573, '24GO26'),
(27, 2, 47.00, 94.00, 15.04, 1, 23, 21, 8, '2025-11-10 20:05:09', 83467, 'U24T1302A'),
(28, 90, 17.00, 1530.00, 0.00, 1, 22, 22, 3, '2025-11-24 10:58:55', 83443, 'ADA23006'),
(29, 1, 25.00, 25.00, 0.00, 1, 22, 22, 3, '2025-11-24 10:58:55', 10, 'WG24456'),
(30, 1, 52.00, 52.00, 0.00, 1, 22, 22, 3, '2025-11-24 10:58:55', 8, 'MZ240501'),
(31, 4, 134.00, 536.00, 0.00, 1, 22, 22, 3, '2025-11-24 10:58:55', 4, '202402'),
(32, 1, 177.00, 177.00, 0.00, 1, 22, 22, 3, '2025-11-24 10:58:55', 20, 'WR2501'),
(33, 6, 52.00, 312.00, 0.00, 1, 22, 23, 3, '2025-11-25 13:09:51', 8, 'MZ240501'),
(34, 2, 52.00, 104.00, 0.00, 1, 22, 24, 3, '2025-11-25 17:03:43', 8, 'MZ240501'),
(35, 1, 25.00, 25.00, 0.00, 1, 22, 24, 3, '2025-11-25 17:03:43', 10, 'WG24456');

-- --------------------------------------------------------

--
-- Table structure for table `equipamentos`
--

CREATE TABLE `equipamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('Disponível','Emprestado','Manutenção') DEFAULT 'Disponível',
  `id_colaborador` int(11) DEFAULT NULL,
  `tipo` varchar(255) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_aquisicao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `es_artigos`
--

CREATE TABLE `es_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `es` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `es_artigos`
--

INSERT INTO `es_artigos` (`id`, `artigo`, `qtd`, `es`, `user`, `data`) VALUES
(1, 232, 2, 1, 22, '2025-11-05 07:43:12'),
(2, 195, 10, 2, 22, '2025-11-05 07:50:40'),
(3, 266, 10, 2, 22, '2025-11-05 07:50:40'),
(4, 274, 5, 2, 22, '2025-11-05 07:50:40'),
(5, 83438, 5, 2, 22, '2025-11-05 07:50:40'),
(6, 1270, 10, 3, 22, '2025-11-05 07:52:18'),
(7, 1360, 10, 4, 22, '2025-11-05 07:55:27'),
(8, 1521, 10, 5, 22, '2025-11-05 07:56:26'),
(9, 1465, 10, 6, 22, '2025-11-05 07:57:30'),
(10, 918, 10, 7, 22, '2025-11-05 07:59:28'),
(11, 83453, 10, 8, 22, '2025-11-05 08:08:46'),
(13, 1310, 10, 10, 22, '2025-11-05 08:14:51'),
(14, 83339, 10, 11, 22, '2025-11-05 08:15:55'),
(15, 1452, 10, 12, 22, '2025-11-05 08:16:55'),
(16, 1463, 0, 13, 22, '2025-11-05 08:18:19'),
(18, 641, 10, 15, 22, '2025-11-05 08:34:13'),
(19, 716, 10, 16, 22, '2025-11-05 08:35:57'),
(20, 657, 10, 17, 22, '2025-11-05 08:36:50'),
(21, 1503, 10, 18, 22, '2025-11-05 08:38:09'),
(22, 83302, 10, 19, 22, '2025-11-05 08:39:30'),
(23, 83370, 10, 20, 22, '2025-11-05 08:40:40'),
(24, 613, 10, 21, 22, '2025-11-05 08:44:58'),
(25, 612, 10, 22, 22, '2025-11-05 08:45:38'),
(26, 1459, 50, 23, 22, '2025-11-05 08:47:53'),
(27, 47, 50, 24, 22, '2025-11-05 08:49:22'),
(28, 922, 20, 25, 22, '2025-11-05 08:50:48'),
(29, 83414, 50, 26, 22, '2025-11-05 08:52:03'),
(30, 1172, 10, 27, 22, '2025-11-05 08:54:28'),
(31, 1220, 10, 28, 22, '2025-11-05 08:56:03'),
(32, 4, 10, 29, 22, '2025-11-05 08:56:59'),
(33, 8, 10, 30, 22, '2025-11-05 08:58:33'),
(34, 1481, 10, 31, 22, '2025-11-05 08:59:35'),
(35, 896, 10, 32, 22, '2025-11-05 09:01:31'),
(36, 83443, 10, 33, 22, '2025-11-05 09:03:18'),
(37, 1494, 10, 34, 22, '2025-11-05 09:04:34'),
(38, 1495, 10, 34, 22, '2025-11-05 09:04:34'),
(39, 931, 10, 35, 22, '2025-11-05 09:06:22'),
(40, 1632, 10, 36, 22, '2025-11-05 09:07:44'),
(41, 1633, 10, 37, 22, '2025-11-05 09:08:54'),
(42, 1250, 10, 37, 22, '2025-11-05 09:08:54'),
(43, 83378, 10, 38, 22, '2025-11-05 09:10:10'),
(44, 581, 10, 39, 22, '2025-11-05 09:11:36'),
(45, 1507, 10, 40, 22, '2025-11-05 09:14:23'),
(46, 744, 10, 41, 22, '2025-11-05 09:16:04'),
(47, 1622, 10, 42, 22, '2025-11-05 09:18:03'),
(48, 1484, 10, 43, 22, '2025-11-05 09:21:12'),
(49, 1482, 10, 44, 22, '2025-11-05 09:21:57'),
(50, 1483, 10, 44, 22, '2025-11-05 09:21:57'),
(51, 1485, 10, 45, 22, '2025-11-05 09:26:39'),
(52, 83375, 10, 46, 22, '2025-11-05 09:28:19'),
(53, 760, 10, 47, 22, '2025-11-05 09:30:09'),
(54, 1673, 10, 48, 22, '2025-11-05 09:32:43'),
(55, 1517, 10, 49, 22, '2025-11-05 09:34:24'),
(56, 1331, 10, 50, 22, '2025-11-05 09:37:28'),
(57, 1506, 10, 51, 22, '2025-11-05 09:38:53'),
(58, 800, 10, 52, 22, '2025-11-05 09:40:57'),
(59, 1497, 10, 53, 22, '2025-11-05 09:42:18'),
(60, 714, 10, 54, 22, '2025-11-05 09:43:16'),
(61, 965, 10, 55, 22, '2025-11-05 09:46:59'),
(62, 722, 10, 56, 22, '2025-11-05 09:47:37'),
(63, 1480, 10, 57, 22, '2025-11-05 09:48:40'),
(64, 1361, 10, 58, 22, '2025-11-05 09:50:04'),
(65, 1249, 10, 59, 22, '2025-11-05 09:52:05'),
(66, 1620, 10, 60, 22, '2025-11-05 09:52:39'),
(67, 913, 10, 61, 22, '2025-11-05 09:54:00'),
(68, 763, 10, 62, 22, '2025-11-05 09:55:04'),
(69, 83387, 10, 62, 22, '2025-11-05 09:55:04'),
(70, 1630, 10, 63, 22, '2025-11-05 09:56:29'),
(71, 836, 10, 64, 22, '2025-11-05 09:57:12'),
(72, 1260, 10, 65, 22, '2025-11-05 09:59:20'),
(73, 622, 10, 66, 22, '2025-11-05 10:00:31'),
(74, 83365, 10, 67, 22, '2025-11-05 10:01:01'),
(75, 1512, 10, 68, 22, '2025-11-05 10:03:07'),
(76, 83369, 10, 69, 22, '2025-11-05 10:05:54'),
(77, 909, 10, 70, 22, '2025-11-05 10:08:39'),
(78, 1246, 10, 71, 22, '2025-11-05 10:10:02'),
(79, 1333, 10, 72, 22, '2025-11-05 10:11:36'),
(80, 718, 10, 73, 22, '2025-11-05 10:13:17'),
(81, 83305, 10, 74, 22, '2025-11-05 10:14:56'),
(82, 777, 10, 75, 22, '2025-11-05 10:17:20'),
(83, 755, 10, 76, 22, '2025-11-05 10:18:21'),
(84, 1501, 10, 77, 22, '2025-11-05 10:22:14'),
(85, 756, 10, 78, 22, '2025-11-05 10:24:29'),
(86, 83382, 10, 79, 22, '2025-11-05 10:25:56'),
(87, 1502, 10, 80, 22, '2025-11-05 10:27:13'),
(88, 1277, 10, 81, 22, '2025-11-05 10:28:40'),
(89, 1672, 10, 82, 22, '2025-11-05 10:29:47'),
(90, 1496, 10, 83, 22, '2025-11-05 10:31:02'),
(91, 631, 10, 84, 22, '2025-11-05 10:31:50'),
(92, 632, 10, 85, 22, '2025-11-05 10:33:19'),
(93, 1247, 10, 86, 22, '2025-11-05 10:35:32'),
(94, 1246, 5, 87, 22, '2025-11-05 10:36:28'),
(95, 1498, 10, 88, 22, '2025-11-05 10:38:24'),
(96, 1499, 10, 88, 22, '2025-11-05 10:38:24'),
(97, 946, 10, 89, 22, '2025-11-05 10:39:36'),
(98, 850, 10, 90, 22, '2025-11-05 10:41:56'),
(99, 623, 10, 91, 22, '2025-11-05 10:43:53'),
(100, 557, 5, 92, 22, '2025-11-05 10:46:07'),
(101, 1274, 10, 93, 22, '2025-11-05 10:47:57'),
(102, 1454, 10, 94, 22, '2025-11-05 10:50:39'),
(103, 9, 10, 95, 22, '2025-11-05 10:54:36'),
(104, 10, 10, 96, 22, '2025-11-05 10:56:04'),
(105, 1548, 10, 97, 22, '2025-11-05 10:57:52'),
(106, 15, 10, 98, 22, '2025-11-05 11:00:07'),
(107, 16, 10, 99, 22, '2025-11-05 11:02:04'),
(108, 738, 10, 100, 22, '2025-11-05 11:08:13'),
(109, 758, 10, 101, 22, '2025-11-05 11:09:30'),
(110, 20, 10, 102, 22, '2025-11-05 11:12:18'),
(111, 1614, 10, 102, 22, '2025-11-05 11:12:18'),
(112, 200, 10, 103, 22, '2025-11-05 11:15:29'),
(113, 23, 10, 104, 22, '2025-11-05 11:16:59'),
(114, 202, 10, 105, 22, '2025-11-05 11:20:26'),
(115, 26, 10, 106, 22, '2025-11-05 11:22:13'),
(116, 29, 10, 107, 22, '2025-11-05 11:25:04'),
(117, 702, 10, 108, 22, '2025-11-05 11:27:32'),
(118, 1603, 10, 109, 22, '2025-11-05 11:30:57'),
(119, 1604, 10, 109, 22, '2025-11-05 11:30:57'),
(120, 1605, 10, 109, 22, '2025-11-05 11:30:57'),
(121, 1534, 10, 110, 22, '2025-11-05 11:32:03'),
(122, 1089, 10, 111, 22, '2025-11-05 11:32:46'),
(123, 83348, 10, 112, 22, '2025-11-05 11:34:04'),
(124, 41, 10, 113, 22, '2025-11-05 11:35:26'),
(125, 1334, 10, 114, 22, '2025-11-05 11:37:04'),
(126, 83333, 10, 115, 22, '2025-11-05 11:39:28'),
(127, 83334, 10, 115, 22, '2025-11-05 11:39:28'),
(128, 42, 100, 116, 22, '2025-11-05 11:41:38'),
(129, 1434, 10, 117, 22, '2025-11-05 11:42:14'),
(130, 1406, 10, 118, 22, '2025-11-05 11:43:22'),
(131, 47, 10, 119, 22, '2025-11-05 11:44:34'),
(132, 83449, 10, 119, 22, '2025-11-05 11:44:34'),
(133, 1370, 10, 120, 22, '2025-11-05 11:47:41'),
(134, 48, 10, 121, 22, '2025-11-05 11:48:51'),
(135, 51, 10, 122, 22, '2025-11-05 11:50:24'),
(136, 52, 10, 123, 22, '2025-11-05 11:51:18'),
(137, 1098, 10, 124, 22, '2025-11-05 11:52:21'),
(138, 951, 50, 125, 22, '2025-11-05 11:55:39'),
(139, 81, 10, 126, 22, '2025-11-05 11:56:22'),
(140, 83326, 10, 127, 22, '2025-11-05 12:00:32'),
(141, 89, 10, 128, 22, '2025-11-05 12:01:43'),
(142, 1615, 10, 129, 22, '2025-11-05 12:04:17'),
(143, 1076, 10, 130, 22, '2025-11-05 12:05:22'),
(144, 104, 10, 131, 22, '2025-11-05 12:06:03'),
(145, 1010, 10, 132, 22, '2025-11-05 12:07:22'),
(146, 673, 10, 133, 22, '2025-11-05 12:08:32'),
(147, 1524, 10, 134, 22, '2025-11-05 12:09:26'),
(148, 120, 10, 135, 22, '2025-11-05 12:10:27'),
(149, 950, 5, 136, 22, '2025-11-05 12:11:17'),
(150, 144, 10, 137, 22, '2025-11-05 12:12:43'),
(151, 179, 2, 138, 22, '2025-11-05 12:13:43'),
(152, 180, 2, 139, 22, '2025-11-05 12:14:35'),
(153, 182, 5, 140, 22, '2025-11-05 12:15:26'),
(154, 192, 10, 141, 22, '2025-11-05 12:16:21'),
(155, 193, 10, 142, 22, '2025-11-05 12:17:17'),
(156, 199, 10, 143, 22, '2025-11-05 12:22:09'),
(157, 200, 10, 144, 22, '2025-11-05 12:24:56'),
(158, 1649, 5, 145, 22, '2025-11-05 12:27:54'),
(159, 203, 5, 146, 22, '2025-11-05 12:28:44'),
(160, 224, 3, 147, 22, '2025-11-05 12:29:36'),
(161, 223, 3, 148, 22, '2025-11-05 12:30:15'),
(162, 225, 3, 149, 22, '2025-11-05 12:31:33'),
(163, 991, 3, 150, 22, '2025-11-05 12:35:11'),
(164, 230, 3, 151, 22, '2025-11-05 12:37:24'),
(165, 717, 90, 152, 22, '2025-11-05 12:40:36'),
(166, 476, 10, 153, 22, '2025-11-05 12:43:52'),
(167, 1083, 50, 154, 22, '2025-11-05 12:46:23'),
(168, 1647, 20, 155, 22, '2025-11-05 12:57:47'),
(169, 721, 100, 156, 22, '2025-11-05 12:58:41'),
(170, 1403, 10, 157, 22, '2025-11-05 13:00:48'),
(171, 1079, 10, 158, 22, '2025-11-05 13:02:36'),
(172, 1599, 10, 159, 22, '2025-11-05 13:03:51'),
(173, 950, 10, 160, 22, '2025-11-05 13:07:12'),
(174, 484, 40, 161, 22, '2025-11-05 13:09:38'),
(175, 480, 10, 162, 22, '2025-11-05 13:10:51'),
(176, 1438, 10, 163, 22, '2025-11-05 13:11:36'),
(177, 490, 10, 164, 22, '2025-11-05 13:15:30'),
(178, 469, 80, 165, 22, '2025-11-05 13:17:49'),
(179, 1424, 10, 166, 22, '2025-11-05 13:19:54'),
(180, 659, 12, 167, 22, '2025-11-05 13:22:45'),
(181, 1431, 10, 168, 22, '2025-11-05 13:23:51'),
(182, 1714, 12, 169, 22, '2025-11-05 13:27:46'),
(183, 719, 15, 170, 22, '2025-11-05 13:31:05'),
(184, 1712, 10, 171, 22, '2025-11-05 13:33:02'),
(185, 1573, 10, 172, 22, '2025-11-05 13:38:43'),
(186, 973, 10, 173, 22, '2025-11-05 13:40:06'),
(187, 937, 5, 174, 22, '2025-11-05 13:42:21'),
(188, 1576, 25, 175, 22, '2025-11-05 13:45:20'),
(189, 1577, 25, 176, 22, '2025-11-05 13:46:16'),
(190, 938, 50, 177, 22, '2025-11-05 13:49:58'),
(191, 741, 12, 178, 22, '2025-11-05 13:53:21'),
(192, 750, 10, 179, 22, '2025-11-05 13:54:33'),
(193, 749, 10, 180, 22, '2025-11-05 13:55:29'),
(194, 1266, 10, 181, 22, '2025-11-05 13:57:38'),
(195, 1715, 10, 182, 22, '2025-11-05 13:59:48'),
(196, 1179, 2, 183, 22, '2025-11-05 14:02:49'),
(197, 1191, 1, 184, 22, '2025-11-05 14:04:37'),
(198, 975, 10, 185, 22, '2025-11-05 14:08:19'),
(199, 976, 10, 186, 22, '2025-11-05 14:09:23'),
(200, 364, 10, 187, 22, '2025-11-05 14:12:45'),
(201, 842, 10, 188, 22, '2025-11-05 14:14:02'),
(202, 981, 10, 189, 22, '2025-11-05 14:32:48'),
(203, 593, 10, 190, 22, '2025-11-05 14:34:41'),
(204, 1173, 2, 191, 22, '2025-11-05 14:36:15'),
(205, 1423, 10, 192, 22, '2025-11-05 14:38:28'),
(206, 968, 10, 193, 22, '2025-11-05 14:40:26'),
(207, 83420, 2, 194, 22, '2025-11-05 14:47:31'),
(208, 54, 24, 195, 22, '2025-11-05 14:48:53'),
(209, 58, 24, 196, 22, '2025-11-05 14:49:50'),
(210, 60, 24, 197, 22, '2025-11-05 14:50:45'),
(211, 31, 1, 198, 22, '2025-11-05 14:53:31'),
(212, 1180, 1, 199, 22, '2025-11-05 14:54:16'),
(213, 293, 2, 200, 22, '2025-11-05 14:55:50'),
(214, 292, 2, 201, 22, '2025-11-05 14:59:39'),
(215, 1328, 1, 202, 22, '2025-11-05 15:01:07'),
(216, 83448, 100, 203, 22, '2025-11-05 15:04:32'),
(217, 763, 1, 204, 22, '2025-11-05 15:06:34'),
(218, 1608, 20, 205, 22, '2025-11-05 15:10:38'),
(219, 1668, 100, 206, 22, '2025-11-05 15:11:42'),
(220, 1207, 100, 207, 22, '2025-11-05 15:13:03'),
(221, 1667, 10, 208, 22, '2025-11-05 15:14:56'),
(222, 1552, 10, 209, 22, '2025-11-05 15:20:41'),
(223, 1606, 11, 210, 22, '2025-11-05 15:21:56'),
(224, 923, 50, 211, 22, '2025-11-05 15:23:11'),
(225, 983, 10, 212, 22, '2025-11-05 15:24:31'),
(226, 655, 10, 213, 22, '2025-11-05 15:27:41'),
(227, 672, 1, 214, 22, '2025-11-06 07:28:12'),
(228, 1210, 10, 215, 22, '2025-11-06 07:30:00'),
(229, 731, 10, 216, 22, '2025-11-06 07:31:32'),
(230, 727, 10, 217, 22, '2025-11-06 07:33:08'),
(231, 726, 10, 218, 22, '2025-11-06 07:34:12'),
(232, 911, 10, 219, 22, '2025-11-06 07:35:55'),
(233, 1663, 10, 220, 22, '2025-11-06 07:37:51'),
(234, 1664, 10, 220, 22, '2025-11-06 07:37:51'),
(235, 1665, 10, 220, 22, '2025-11-06 07:37:51'),
(236, 1209, 80, 221, 22, '2025-11-06 07:39:40'),
(237, 592, 50, 222, 22, '2025-11-06 07:40:15'),
(238, 83462, 100, 223, 22, '2025-11-06 07:54:10'),
(239, 83463, 200, 224, 22, '2025-11-06 08:41:22'),
(240, 83464, 50, 225, 22, '2025-11-06 08:49:23'),
(241, 83465, 50, 226, 22, '2025-11-06 08:50:30'),
(242, 83466, 100, 227, 22, '2025-11-06 08:54:49'),
(243, 83467, 100, 228, 22, '2025-11-06 08:57:36'),
(244, 83468, 100, 229, 22, '2025-11-06 09:02:54'),
(245, 83469, 10, 230, 22, '2025-11-06 09:07:52'),
(246, 83470, 30, 231, 22, '2025-11-06 09:13:26'),
(247, 83471, 200, 232, 22, '2025-11-06 09:16:58'),
(248, 83472, 100, 233, 22, '2025-11-06 09:23:57'),
(249, 83473, 15, 234, 22, '2025-11-06 09:27:35'),
(250, 83474, 15, 234, 22, '2025-11-06 09:27:35'),
(251, 83475, 15, 234, 22, '2025-11-06 09:27:35'),
(252, 352, 10, 235, 22, '2025-11-06 09:31:16'),
(253, 83450, 10, 236, 22, '2025-11-06 09:34:29'),
(254, 83476, 15, 237, 22, '2025-11-06 09:37:35'),
(255, 83477, 10, 238, 22, '2025-11-06 09:40:00'),
(256, 83478, 10, 239, 22, '2025-11-06 09:42:49'),
(257, 83479, 10, 240, 22, '2025-11-06 09:46:06'),
(258, 83480, 5, 241, 22, '2025-11-06 09:49:55'),
(259, 83481, 10, 242, 22, '2025-11-06 09:54:11'),
(260, 83482, 24, 243, 22, '2025-11-06 09:58:01'),
(261, 83483, 1, 244, 22, '2025-11-06 10:00:09'),
(262, 83484, 100, 245, 22, '2025-11-06 10:05:53'),
(263, 83485, 10, 246, 22, '2025-11-06 10:08:15'),
(264, 83486, 100, 247, 22, '2025-11-06 10:11:48'),
(265, 83487, 10, 248, 22, '2025-11-06 10:14:51'),
(266, 83488, 10, 249, 22, '2025-11-06 10:20:53'),
(267, 83489, 10, 250, 22, '2025-11-06 10:24:36'),
(268, 83490, 5, 251, 22, '2025-11-06 10:27:21'),
(269, 83491, 100, 252, 22, '2025-11-06 10:31:23'),
(270, 83492, 100, 253, 22, '2025-11-06 10:33:28'),
(271, 83493, 10, 254, 22, '2025-11-06 10:35:27'),
(272, 83495, 10, 255, 22, '2025-11-06 10:41:01'),
(273, 83496, 10, 256, 22, '2025-11-06 10:43:03'),
(274, 83497, 10, 257, 22, '2025-11-06 10:44:59'),
(275, 83498, 10, 258, 22, '2025-11-06 10:46:59'),
(276, 83499, 10, 259, 22, '2025-11-06 10:49:34'),
(277, 83500, 10, 260, 22, '2025-11-06 10:52:34'),
(278, 249, 10, 261, 22, '2025-11-06 10:55:20'),
(279, 256, 1, 262, 22, '2025-11-06 10:57:34'),
(280, 83502, 10, 262, 22, '2025-11-06 10:57:34'),
(281, 83503, 10, 263, 22, '2025-11-06 10:59:34'),
(282, 83504, 10, 264, 22, '2025-11-06 11:02:28'),
(283, 83505, 10, 265, 22, '2025-11-06 11:04:39'),
(284, 83506, 10, 266, 22, '2025-11-06 11:09:29'),
(285, 83507, 10, 267, 22, '2025-11-06 11:11:41'),
(286, 83508, 10, 268, 22, '2025-11-06 11:14:01'),
(287, 83509, 10, 269, 22, '2025-11-06 11:17:45'),
(288, 83510, 10, 270, 22, '2025-11-06 11:19:39'),
(289, 83511, 10, 271, 22, '2025-11-06 11:22:27'),
(290, 83512, 10, 272, 22, '2025-11-06 11:24:27'),
(291, 83513, 10, 273, 22, '2025-11-06 11:26:17'),
(292, 83514, 10, 274, 22, '2025-11-06 11:28:04'),
(293, 83515, 10, 275, 22, '2025-11-06 11:30:50'),
(294, 83516, 2, 276, 22, '2025-11-06 11:32:53'),
(295, 83517, 10, 277, 22, '2025-11-06 11:35:56'),
(296, 83518, 10, 278, 22, '2025-11-06 11:38:11'),
(297, 83519, 12, 279, 22, '2025-11-06 11:40:16'),
(298, 83521, 12, 280, 22, '2025-11-06 11:47:50'),
(299, 83522, 10, 281, 22, '2025-11-06 11:51:01'),
(300, 83523, 20, 282, 22, '2025-11-06 11:52:51'),
(301, 83524, 10, 283, 22, '2025-11-06 11:54:13'),
(302, 83525, 10, 284, 22, '2025-11-06 11:55:43'),
(303, 83526, 10, 285, 22, '2025-11-06 11:57:22'),
(304, 83527, 10, 286, 22, '2025-11-06 11:59:06'),
(305, 83528, 20, 287, 22, '2025-11-06 12:01:13'),
(306, 83529, 1, 288, 22, '2025-11-06 12:02:56'),
(307, 83530, 10, 289, 22, '2025-11-06 12:04:03'),
(308, 83531, 10, 290, 22, '2025-11-06 12:05:51'),
(309, 83532, 2, 291, 22, '2025-11-06 12:08:46'),
(310, 83533, 10, 292, 22, '2025-11-06 12:10:29'),
(311, 83534, 10, 293, 22, '2025-11-06 12:12:15'),
(312, 526, 60, 294, 23, '2025-11-06 14:18:05'),
(313, 83535, 40, 295, 23, '2025-11-06 14:24:58'),
(314, 524, 40, 296, 23, '2025-11-06 14:26:36'),
(315, 83536, 96, 297, 23, '2025-11-06 14:32:45'),
(316, 83537, 96, 298, 23, '2025-11-06 14:39:55'),
(317, 83538, 96, 299, 23, '2025-11-06 14:41:53'),
(318, 83539, 96, 300, 23, '2025-11-06 14:43:06'),
(319, 83540, 48, 301, 23, '2025-11-06 14:47:23'),
(320, 83541, 48, 302, 23, '2025-11-06 14:48:43'),
(321, 83542, 48, 303, 23, '2025-11-06 14:49:47'),
(322, 83543, 48, 304, 23, '2025-11-06 14:50:59'),
(323, 83544, 10, 305, 23, '2025-11-06 14:55:08'),
(324, 83545, 48, 306, 23, '2025-11-06 15:01:33'),
(325, 83546, 40, 307, 23, '2025-11-06 15:06:59'),
(326, 83547, 40, 308, 23, '2025-11-06 15:08:53'),
(327, 83548, 12, 309, 23, '2025-11-06 15:17:59'),
(328, 83549, 12, 310, 23, '2025-11-06 15:19:44'),
(329, 83551, 100, 311, 22, '2025-11-07 07:49:39'),
(330, 83552, 100, 312, 22, '2025-11-07 08:52:08'),
(331, 1088, 10, 313, 22, '2025-11-07 09:14:07'),
(332, 785, 10, 314, 22, '2025-11-07 09:19:33'),
(333, 83467, 100, 315, 22, '2025-11-08 09:30:02'),
(334, 1517, 290, 316, 22, '2025-11-08 17:28:11'),
(335, 250, 10, 317, 22, '2025-11-10 08:22:15'),
(336, 250, 1, 318, 22, '2025-11-10 08:50:33'),
(337, 250, 9, 319, 22, '2025-11-10 08:52:19'),
(338, 1502, 100, 320, 22, '2025-11-10 10:20:33'),
(339, 83553, 100, 321, 22, '2025-11-10 11:19:16'),
(340, 1672, 90, 322, 22, '2025-11-10 11:33:45'),
(341, 631, 90, 323, 22, '2025-11-10 11:41:03'),
(342, 1247, 90, 324, 22, '2025-11-10 11:49:19'),
(343, 1362, 90, 325, 22, '2025-11-10 11:55:49'),
(344, 623, 90, 326, 22, '2025-11-10 12:11:00'),
(345, 1274, 90, 327, 22, '2025-11-10 12:14:14'),
(346, 622, 90, 328, 22, '2025-11-10 12:27:25'),
(347, 83365, 90, 329, 22, '2025-11-10 12:31:55'),
(348, 714, 90, 330, 22, '2025-11-10 12:56:18'),
(349, 965, 90, 331, 22, '2025-11-10 12:59:28'),
(350, 722, 90, 332, 22, '2025-11-10 13:11:07'),
(351, 1480, 90, 333, 22, '2025-11-10 13:14:37'),
(352, 1361, 90, 334, 22, '2025-11-10 13:18:08'),
(353, 1486, 100, 335, 22, '2025-11-10 13:22:10'),
(354, 836, 90, 336, 22, '2025-11-10 13:29:39'),
(355, 1484, 90, 337, 22, '2025-11-10 13:34:07'),
(356, 1483, 90, 338, 22, '2025-11-10 13:42:21'),
(357, 1485, 90, 339, 22, '2025-11-10 13:51:36'),
(358, 83375, 90, 340, 22, '2025-11-10 13:55:21'),
(359, 760, 10, 341, 22, '2025-11-10 14:01:17'),
(360, 760, 80, 342, 22, '2025-11-10 14:05:42'),
(361, 797, 90, 343, 22, '2025-11-11 09:53:32'),
(362, 800, 90, 344, 22, '2025-11-11 10:01:23'),
(363, 83494, 100, 345, 22, '2025-11-11 10:05:31'),
(364, 1481, 90, 346, 22, '2025-11-11 10:08:11'),
(365, 83495, 90, 347, 22, '2025-11-11 10:13:52'),
(366, 896, 90, 348, 22, '2025-11-11 10:56:08'),
(367, 83551, 90, 349, 22, '2025-11-11 10:59:40'),
(368, 83497, 90, 350, 22, '2025-11-11 11:05:20'),
(369, 83443, 90, 351, 22, '2025-11-11 11:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `es_artigos_temp`
--

CREATE TABLE `es_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `factura`
--

CREATE TABLE `factura` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `motivo_iva` text DEFAULT NULL,
  `disconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `serie` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `statuss` int(11) NOT NULL DEFAULT 0,
  `apolice` varchar(255) DEFAULT NULL,
  `condicoes` varchar(255) NOT NULL,
  `codigo1` varchar(255) DEFAULT NULL,
  `codigo2` varchar(255) DEFAULT NULL,
  `codigo3` varchar(255) DEFAULT NULL,
  `nota_credito` int(11) NOT NULL DEFAULT 0,
  `recibo` int(11) NOT NULL DEFAULT 0,
  `nota_debito` int(11) NOT NULL DEFAULT 0,
  `cotacao` int(11) NOT NULL DEFAULT 0,
  `cliente` int(11) NOT NULL,
  `utente` varchar(500) DEFAULT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `factura`
--

INSERT INTO `factura` (`id`, `n_doc`, `descricao`, `valor`, `iva`, `motivo_iva`, `disconto`, `serie`, `prazo`, `metodo`, `statuss`, `apolice`, `condicoes`, `codigo1`, `codigo2`, `codigo3`, `nota_credito`, `recibo`, `nota_debito`, `cotacao`, `cliente`, `utente`, `usuario`, `dataa`, `data`) VALUES
(1, 1, '2025-11-06 14:14:53', 97.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:14:53'),
(2, 2, '2025-11-06 14:18:39', 100.00, 0.00, NULL, 0.00, 2025, '', '1', 1, '', '', '', '', '', 0, 1, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:18:39'),
(3, 3, '2025-11-06 14:19:41', 100.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:19:41'),
(4, 4, '2025-11-06 14:22:16', 60.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:22:16'),
(5, 5, '2025-11-06 14:27:18', 10.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:27:18'),
(6, 6, '2025-11-06 14:28:51', 20.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:28:51'),
(7, 7, '2025-11-06 14:31:01', 10.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:31:01'),
(8, 8, '2025-11-06 14:32:49', 30.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:32:49'),
(9, 9, '2025-11-06 14:34:59', 14.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:34:59'),
(10, 10, '2025-11-06 14:49:46', 30.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 22, '2025-11-06', '2025-11-06 12:49:46'),
(11, 11, '2025-11-06 15:16:29', 166.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 23, '2025-11-06', '2025-11-06 13:16:29'),
(12, 12, '2025-11-06 15:18:08', 134.00, 0.00, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 23, '2025-11-06', '2025-11-06 13:18:08'),
(13, 13, '2025-11-06 17:12:01', 63.00, 10.08, NULL, 0.00, 2025, '', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 23, '2025-11-06', '2025-11-06 15:12:01'),
(14, 14, '2025-11-24 10:30:32', 134.00, 0.00, NULL, 0.00, 2025, '2025-11-24', '1', 0, '', '', '', '', '', 0, 0, 0, 0, 1, '', 25, '2025-11-24', '2025-11-24 08:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `factura_recepcao`
--

CREATE TABLE `factura_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `disconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `serie` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `condicoes` varchar(255) NOT NULL,
  `apolice` varchar(255) DEFAULT NULL,
  `codigo1` varchar(255) DEFAULT NULL,
  `codigo2` varchar(255) DEFAULT NULL,
  `codigo3` varchar(255) DEFAULT NULL,
  `paciente` int(11) NOT NULL COMMENT 'ID do paciente (similar a cliente na farmácia)',
  `empresa_id` int(11) DEFAULT NULL COMMENT 'ID da empresa/seguro',
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factura_recepcao`
--

INSERT INTO `factura_recepcao` (`id`, `n_doc`, `descricao`, `valor`, `iva`, `disconto`, `serie`, `prazo`, `metodo`, `condicoes`, `apolice`, `codigo1`, `codigo2`, `codigo3`, `paciente`, `empresa_id`, `usuario`, `dataa`, `data`) VALUES
(1, 1, '2025-11-24 14:00:54', 3325.00, 0.00, 0.00, 2025, '2025-10-30', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 22, '2025-11-24', '2025-11-24 12:00:54'),
(2, 2, '2025-11-24 14:04:28', 1140.00, 0.00, 0.00, 2025, '2025-11-24', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 22, '2025-11-24', '2025-11-24 12:04:28'),
(3, 3, '2025-11-24 14:16:03', 1140.00, 0.00, 0.00, 2025, '2025-11-07', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 25, '2025-11-24', '2025-11-24 12:16:03'),
(6, 4, '2025-11-24 14:20:51', -3420.00, 0.00, 0.00, 2025, '2025-11-24', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 25, '2025-11-24', '2025-11-24 12:20:51'),
(7, 5, '2025-11-24 20:34:45', 22705.00, 0.00, 0.00, 2025, '2025-11-21', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 25, '2025-11-24', '2025-11-24 18:34:45'),
(8, 6, '2025-11-25 23:06:17', 1140.00, 0.00, 0.00, 2025, '2025-11-25', 'dinheiro', 'imediato', '', '', '', '', 2, 1, 6, '2025-11-25', '2025-11-25 21:06:17'),
(9, 7, '2025-11-25 23:30:58', 0.00, 0.00, 0.00, 2025, '2025-11-25', 'dinheiro', 'imediato', '', '', '', '', 3, 2, 6, '2025-11-25', '2025-11-25 21:30:58'),
(10, 8, '2025-11-26 11:51:08', 1104.00, 0.00, 0.00, 2025, '2025-12-05', 'dinheiro', '30_dias', '', '', '', '', 3, 2, 25, '2025-11-26', '2025-11-26 09:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `familia_artigos`
--

CREATE TABLE `familia_artigos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `setor` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `familia_artigos`
--

INSERT INTO `familia_artigos` (`id`, `descricao`, `setor`, `data`) VALUES
(8, 'Govind Farmaceutica Limitada', 9, '2024-03-18 08:38:41'),
(9, 'Shani Lda', 9, '2024-03-20 08:48:09'),
(10, 'Orbis Pharma', 9, '2024-03-26 09:43:04'),
(11, 'WELLWORTH  LDA', 9, '2024-03-26 10:16:18'),
(12, 'MEDIS FARMACEUTTICA,LDA', 9, '2024-03-26 10:18:33'),
(13, 'ACE HEALTHCARE LIMITADA', 9, '2024-03-26 10:20:45'),
(14, 'AFRI  FARMACIA', 9, '2024-03-26 10:21:51'),
(15, 'ACE Healthcare Limitada', 9, '2024-03-26 10:50:23'),
(16, 'NATUR PHARME', 9, '2024-05-06 12:55:50'),
(17, 'LIAF TOBACCO LIMITADA', 9, '2024-07-20 04:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `faturas_atendimento`
--

CREATE TABLE `faturas_atendimento` (
  `id` int(11) NOT NULL,
  `numero_fatura` varchar(50) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL COMMENT 'Empresa para cobrança posterior',
  `tipo_documento` enum('fatura','vds','cotacao') NOT NULL DEFAULT 'fatura',
  `data_atendimento` date NOT NULL,
  `hora_atendimento` time DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_pago` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total já pago (para pagamentos parciais)',
  `status` enum('pendente','parcial','paga','vencido','cancelada') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `usuario_criacao` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_cancelamento` int(11) DEFAULT NULL,
  `data_cancelamento` timestamp NULL DEFAULT NULL,
  `motivo_cancelamento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faturas_atendimento`
--

INSERT INTO `faturas_atendimento` (`id`, `numero_fatura`, `paciente_id`, `empresa_id`, `tipo_documento`, `data_atendimento`, `hora_atendimento`, `subtotal`, `desconto`, `total`, `valor_pago`, `status`, `observacoes`, `usuario_criacao`, `data_criacao`, `usuario_cancelamento`, `data_cancelamento`, `motivo_cancelamento`) VALUES
(1, 'FAT-2025-000001', 1, NULL, 'fatura', '2025-11-22', '19:15:00', 2000.00, 400.00, 1600.00, 0.00, 'paga', NULL, 25, '2025-11-22 18:25:02', NULL, NULL, NULL),
(2, 'FAT-2025-000002', 2, NULL, 'fatura', '2025-11-22', '22:38:00', 2000.00, 0.00, 2000.00, 0.00, 'cancelada', NULL, 6, '2025-11-22 21:38:35', 6, '2025-11-22 21:38:45', 'Cancelado pelo usuário'),
(3, 'FAT-2025-000003', 2, 1, 'fatura', '2025-11-22', '22:39:00', 1900.00, 0.00, 1900.00, 1900.00, 'paga', NULL, 6, '2025-11-22 21:39:43', NULL, NULL, NULL),
(4, 'FAT-2025-000004', 2, 1, 'fatura', '2025-11-23', '10:16:00', 1900.00, 0.00, 1900.00, 1900.00, 'paga', NULL, 25, '2025-11-23 09:19:37', NULL, NULL, NULL),
(5, 'FAT-2025-000005', 1, NULL, 'fatura', '2025-11-23', '11:42:00', 1500.00, 0.00, 1500.00, 1500.00, 'paga', NULL, 25, '2025-11-23 10:43:11', NULL, NULL, NULL),
(6, 'FAT-2025-000006', 2, 1, 'fatura', '2025-11-24', '08:28:00', 1425.00, 0.00, 1425.00, 1425.00, 'paga', NULL, 25, '2025-11-24 07:29:26', NULL, NULL, NULL),
(7, 'FAT-2025-000007', 1, NULL, 'fatura', '2025-11-24', '08:58:00', 3200.00, 0.00, 3200.00, 0.00, 'cancelada', 'gddhf', 1, '2025-11-24 08:01:31', 25, '2025-11-25 10:19:23', 'Cancelado pelo usuário');

-- --------------------------------------------------------

--
-- Table structure for table `fatura_servicos`
--

CREATE TABLE `fatura_servicos` (
  `id` int(11) NOT NULL,
  `fatura_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fatura_servicos`
--

INSERT INTO `fatura_servicos` (`id`, `fatura_id`, `servico_id`, `quantidade`, `preco_unitario`, `subtotal`, `data`) VALUES
(1, 1, 2, 1, 2000.00, 2000.00, '2025-11-22 18:25:02'),
(2, 2, 2, 1, 2000.00, 2000.00, '2025-11-22 21:38:35'),
(3, 3, 2, 1, 1900.00, 1900.00, '2025-11-22 21:39:43'),
(4, 4, 2, 1, 1900.00, 1900.00, '2025-11-23 09:19:37'),
(5, 5, 3, 1, 1500.00, 1500.00, '2025-11-23 10:43:11'),
(6, 6, 3, 1, 1425.00, 1425.00, '2025-11-24 07:29:26'),
(7, 7, 2, 1, 2000.00, 2000.00, '2025-11-24 08:01:31'),
(8, 7, 1, 1, 1200.00, 1200.00, '2025-11-24 08:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `fa_artigos_fact`
--

CREATE TABLE `fa_artigos_fact` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `lote` varchar(255) DEFAULT NULL,
  `user` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fa_artigos_fact`
--

INSERT INTO `fa_artigos_fact` (`id`, `artigo`, `qtd`, `preco`, `iva`, `total`, `lote`, `user`, `factura`, `data`) VALUES
(1, 1210, 1, 97.00, 0.00, 97.00, '2408098,2408096', 22, 1, '2025-11-06 12:14:53'),
(2, 717, 1, 100.00, 0.00, 100.00, 'BE2411-40', 22, 2, '2025-11-06 12:18:39'),
(3, 717, 1, 100.00, 0.00, 100.00, 'BE2411-40', 22, 3, '2025-11-06 12:19:41'),
(4, 722, 3, 20.00, 0.00, 60.00, 'ACQ24021', 22, 4, '2025-11-06 12:22:16'),
(5, 1480, 1, 10.00, 0.00, 10.00, 'AAA24044', 22, 5, '2025-11-06 12:27:19'),
(6, 1573, 1, 20.00, 0.00, 20.00, '24GO26', 22, 6, '2025-11-06 12:28:51'),
(7, 714, 1, 10.00, 0.00, 10.00, 'ACI24016A', 22, 7, '2025-11-06 12:31:01'),
(8, 1207, 1, 30.00, 0.00, 30.00, 'U24C0165A', 22, 8, '2025-11-06 12:32:49'),
(9, 1480, 1, 10.00, 0.00, 10.00, 'AAA24044', 22, 9, '2025-11-06 12:34:59'),
(10, 83302, 2, 2.00, 0.00, 4.00, 'MZ250305', 22, 9, '2025-11-06 12:34:59'),
(11, 965, 2, 15.00, 0.00, 30.00, 'ABW25004', 22, 10, '2025-11-06 12:49:46'),
(12, 938, 2, 19.00, 0.00, 38.00, 'HETL111', 23, 11, '2025-11-06 13:16:29'),
(13, 1423, 1, 128.00, 0.00, 128.00, 'D04225001', 23, 11, '2025-11-06 13:16:29'),
(14, 4, 1, 134.00, 0.00, 134.00, '202402', 23, 12, '2025-11-06 13:18:08'),
(15, 83493, 1, 63.00, 10.08, 63.00, 'ABA23007', 23, 13, '2025-11-06 15:12:01'),
(16, 4, 1, 134.00, 0.00, 134.00, '202402', 25, 14, '2025-11-24 08:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `fa_artigos_temp`
--

CREATE TABLE `fa_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `lote` varchar(255) DEFAULT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fa_artigos_temp`
--

INSERT INTO `fa_artigos_temp` (`id`, `artigo`, `qtd`, `preco`, `iva`, `total`, `lote`, `user`, `data`) VALUES
(26, 4, 1, 134.00, 0.00, 134.00, '202402', 1, '2025-11-24 08:00:23'),
(28, 8, 1, 52.00, 0.00, 52.00, 'MZ240501', 25, '2025-11-24 08:33:05'),
(29, 4, 1, 134.00, 0.00, 134.00, '202402', 25, '2025-11-24 09:51:27'),
(30, 4, 1, 134.00, 0.00, 134.00, '202402', 22, '2025-11-25 15:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `fa_servicos_fact_recepcao`
--

CREATE TABLE `fa_servicos_fact_recepcao` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL COMMENT 'ID do serviço (similar a artigo na farmácia)',
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `factura` int(11) NOT NULL COMMENT 'ID da fatura (factura_recepcao.id)',
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fa_servicos_fact_recepcao`
--

INSERT INTO `fa_servicos_fact_recepcao` (`id`, `servico`, `qtd`, `preco`, `iva`, `total`, `user`, `factura`, `data`) VALUES
(1, 2, 1, 1900.00, 0.00, 1900.00, 22, 1, '2025-11-24 12:00:54'),
(2, 3, 1, 1425.00, 0.00, 1425.00, 22, 1, '2025-11-24 12:00:54'),
(3, 1, 1, 1140.00, 0.00, 1140.00, 22, 2, '2025-11-24 12:04:28'),
(4, 1, 1, 1140.00, 0.00, 1140.00, 25, 3, '2025-11-24 12:16:03'),
(5, 1, 1, 1140.00, 0.00, 1140.00, 25, 6, '2025-11-24 12:20:51'),
(6, 1, 1, 1140.00, 0.00, 1140.00, 25, 7, '2025-11-24 18:34:45'),
(7, 3, 1, 1425.00, 0.00, 1425.00, 25, 7, '2025-11-24 18:34:45'),
(8, 2, 2, 1900.00, 0.00, 5700.00, 25, 7, '2025-11-24 18:34:45'),
(9, 1, 1, 1140.00, 0.00, 1140.00, 6, 8, '2025-11-25 21:06:17'),
(10, 2, 1, 1840.00, 0.00, 1840.00, 6, 9, '2025-11-25 21:30:58'),
(11, 1, 1, 1104.00, 0.00, 1104.00, 25, 10, '2025-11-26 09:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `fa_servicos_temp`
--

CREATE TABLE `fa_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL COMMENT 'ID do serviço da tabela servicos_clinica',
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL COMMENT 'Preço unitário do serviço',
  `total` decimal(10,2) NOT NULL COMMENT 'Preço total (preco * qtd)',
  `user` int(11) NOT NULL COMMENT 'ID do usuário que está criando a fatura',
  `empresa_id` int(11) DEFAULT NULL COMMENT 'ID da empresa selecionada',
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fa_servicos_temp`
--

INSERT INTO `fa_servicos_temp` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `empresa_id`, `data`) VALUES
(32, 1, 1, 1140.00, 1140.00, 25, 1, '2025-11-26 09:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `ferias_licencas`
--

CREATE TABLE `ferias_licencas` (
  `id` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `tipo` enum('Férias','Licença','Outros') NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('Solicitado','Aprovado','Rejeitado') DEFAULT 'Solicitado',
  `saldo_dias` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filadeespera`
--

CREATE TABLE `filadeespera` (
  `idfiladeespera` int(11) NOT NULL,
  `produtofiladeespera` int(11) NOT NULL,
  `qtdfiladeespera` double NOT NULL,
  `precofiladeespera` decimal(10,2) NOT NULL,
  `totalfiladeespera` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `usuariofiladeespera` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp(),
  `lotes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `filadeespera`
--

INSERT INTO `filadeespera` (`idfiladeespera`, `produtofiladeespera`, `qtdfiladeespera`, `precofiladeespera`, `totalfiladeespera`, `iva`, `lote`, `usuariofiladeespera`, `data`, `lotes`) VALUES
(57, 83467, 2, 47.00, 94.00, 15.04, 'U24T1302A', 23, '2025-11-11 09:23:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL,
  `nome` varchar(500) NOT NULL,
  `nuit` int(11) NOT NULL,
  `contacto` varchar(50) NOT NULL,
  `endereco` text NOT NULL,
  `ordem_compra` int(11) NOT NULL DEFAULT 0,
  `desconto` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `fornecedor`
--

INSERT INTO `fornecedor` (`id`, `nome`, `nuit`, `contacto`, `endereco`, `ordem_compra`, `desconto`, `data`) VALUES
(2, 'MOZAMBIQUE LEAF TOBACCO', 400027285, '25825224052', 'ESTRADA N 7 BAIRRO PADUE', 0, 0, '2024-07-20 03:47:53'),
(3, 'JD EQUIPAMENTOS, LDA', 0, '0', 'BEIRA', 0, 0, '2024-08-14 13:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `funcionario`
--

CREATE TABLE `funcionario` (
  `idfuncionario` int(11) NOT NULL,
  `nomefuncionario` varchar(255) NOT NULL,
  `apelidofuncionario` varchar(255) NOT NULL,
  `sexofuncionario` varchar(10) NOT NULL,
  `bi` varchar(17) NOT NULL,
  `enderecofuncionario` varchar(255) NOT NULL,
  `contactofuncionario` varchar(20) NOT NULL,
  `nuit` int(11) NOT NULL,
  `emailfuncionario` varchar(50) NOT NULL,
  `empresafuncionario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grupo_artigos`
--

CREATE TABLE `grupo_artigos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `familia` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `grupo_artigos`
--

INSERT INTO `grupo_artigos` (`id`, `descricao`, `familia`, `data`) VALUES
(8, 'Farmacos', 8, '2024-03-18 08:39:28'),
(9, 'ANTIBIOTICOS/ANTIFUNGAL', 9, '2024-03-20 08:48:56'),
(10, 'ANALGESICOS, ANTI-INFLAMATORIOS', 9, '2024-03-20 08:49:25'),
(11, 'ANTIALERGICOS E ANTI-GRIPAIS', 9, '2024-03-20 08:49:47'),
(12, 'ANTI-MALARICOS', 9, '2024-03-20 08:50:09'),
(13, 'APARELHO DIGESTIVO', 9, '2024-03-20 08:50:30'),
(14, 'APARELHO RESPIRATORIO (ANTI-TUSSICOS E EXPECTORANTES)', 9, '2024-03-20 08:51:01'),
(15, 'VITAMINAS', 9, '2024-03-20 08:51:29'),
(16, 'APARELHO GENITO-URINARIO/ HORMONAS ', 9, '2024-03-20 08:52:06'),
(17, 'DERMATOLOGIA (CREMES, ANTI-SEPTICOS E DESINFECTANTES)', 9, '2024-03-20 08:52:26'),
(18, 'APARELHO CARDIOVASCULAR', 9, '2024-03-20 08:52:54'),
(19, 'ANTI-DIABETICOS', 9, '2024-03-20 08:53:12'),
(20, 'OFTAMOLOGIA', 9, '2024-03-20 08:53:39'),
(21, 'DIVERSOS', 9, '2024-03-20 08:54:04'),
(22, 'DIVERSOS', 9, '2024-03-20 08:54:05'),
(23, 'INJECTAVEIS', 9, '2024-03-20 08:54:32'),
(24, 'NATUR PHARME', 0, '2024-05-06 12:54:48'),
(25, 'ANTIQUAGULANTE', 0, '2024-05-07 08:49:22');

-- --------------------------------------------------------

--
-- Table structure for table `historico_atendimentos`
--

CREATE TABLE `historico_atendimentos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `fatura_id` int(11) DEFAULT NULL,
  `tipo_atendimento` varchar(100) DEFAULT NULL,
  `servicos_realizados` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_atendimento` date NOT NULL,
  `usuario_registo` int(11) DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historico_atendimentos`
--

INSERT INTO `historico_atendimentos` (`id`, `paciente_id`, `fatura_id`, `tipo_atendimento`, `servicos_realizados`, `observacoes`, `data_atendimento`, `usuario_registo`, `data_registo`) VALUES
(1, 1, 1, 'Fatura de Atendimento', 'Consulta Especializada', NULL, '2025-11-22', 25, '2025-11-22 18:25:02'),
(2, 2, 2, 'Fatura de Atendimento', 'Consulta Especializada', NULL, '2025-11-22', 6, '2025-11-22 21:38:35'),
(3, 2, 3, 'Fatura de Atendimento', 'Consulta Especializada', NULL, '2025-11-22', 6, '2025-11-22 21:39:43'),
(4, 2, 4, 'Fatura de Atendimento', 'Consulta Especializada', NULL, '2025-11-23', 25, '2025-11-23 09:19:37'),
(5, 1, 5, 'Fatura de Atendimento', 'Exame de Sangue', NULL, '2025-11-23', 25, '2025-11-23 10:43:11'),
(6, 2, 6, 'Fatura de Atendimento', 'Exame de Sangue', NULL, '2025-11-24', 25, '2025-11-24 07:29:26'),
(7, 1, 7, 'Fatura de Atendimento', 'Consulta Especializada, Consulta Geral', NULL, '2025-11-24', 1, '2025-11-24 08:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `historico_equipamentos`
--

CREATE TABLE `historico_equipamentos` (
  `id` int(11) NOT NULL,
  `id_equipamento` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `data_entrega` date NOT NULL,
  `data_devolucao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `01` int(11) NOT NULL DEFAULT 0,
  `02` int(11) NOT NULL DEFAULT 0,
  `03` int(11) NOT NULL DEFAULT 0,
  `04` int(11) NOT NULL DEFAULT 0,
  `05` int(11) NOT NULL DEFAULT 0,
  `06` int(11) NOT NULL DEFAULT 0,
  `07` int(11) NOT NULL DEFAULT 0,
  `08` int(11) NOT NULL DEFAULT 0,
  `09` int(11) NOT NULL DEFAULT 0,
  `10` int(11) NOT NULL DEFAULT 0,
  `11` int(11) NOT NULL DEFAULT 0,
  `12` int(11) NOT NULL DEFAULT 0,
  `13` int(11) NOT NULL DEFAULT 0,
  `14` int(11) NOT NULL DEFAULT 0,
  `15` int(11) NOT NULL DEFAULT 0,
  `16` int(11) NOT NULL DEFAULT 0,
  `17` int(11) NOT NULL DEFAULT 0,
  `18` int(11) NOT NULL DEFAULT 0,
  `19` int(11) NOT NULL DEFAULT 0,
  `20` int(11) NOT NULL DEFAULT 0,
  `21` int(11) NOT NULL DEFAULT 0,
  `22` int(11) NOT NULL DEFAULT 0,
  `23` int(11) NOT NULL DEFAULT 0,
  `24` int(11) NOT NULL DEFAULT 0,
  `25` int(11) NOT NULL DEFAULT 0,
  `26` int(11) NOT NULL DEFAULT 0,
  `27` int(11) NOT NULL DEFAULT 0,
  `28` int(11) NOT NULL DEFAULT 0,
  `29` int(11) NOT NULL DEFAULT 0,
  `30` int(11) NOT NULL DEFAULT 0,
  `31` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `inventario`
--

INSERT INTO `inventario` (`id`, `artigo`, `01`, `02`, `03`, `04`, `05`, `06`, `07`, `08`, `09`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`, `24`, `25`, `26`, `27`, `28`, `29`, `30`, `31`) VALUES
(1, 4, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 8, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 9, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 10, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 15, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(6, 16, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 20, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(8, 23, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 26, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(10, 29, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(11, 31, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 41, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(13, 42, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(14, 47, 0, 0, 0, 0, 0, 0, 60, 0, 0, 60, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(15, 48, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(16, 51, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(17, 52, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 54, 0, 0, 0, 0, 0, 0, 24, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 58, 0, 0, 0, 0, 0, 0, 24, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(20, 60, 0, 0, 0, 0, 0, 0, 24, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(21, 81, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(22, 89, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(23, 104, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(24, 120, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(25, 144, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(26, 179, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(27, 180, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(28, 182, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(29, 192, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(30, 193, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(31, 195, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(32, 199, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(33, 200, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(34, 202, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(35, 203, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(36, 223, 0, 0, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(37, 224, 0, 0, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(38, 225, 0, 0, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(39, 230, 0, 0, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(40, 232, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(41, 249, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(42, 256, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(43, 266, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(44, 274, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(45, 292, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(46, 293, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(47, 352, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(48, 364, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(49, 469, 0, 0, 0, 0, 0, 0, 80, 0, 0, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(50, 476, 0, 0, 0, 0, 0, 0, 8, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(51, 480, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(52, 484, 0, 0, 0, 0, 0, 0, 40, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(53, 490, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(54, 524, 0, 0, 0, 0, 0, 0, 40, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(55, 526, 0, 0, 0, 0, 0, 0, 60, 0, 0, 60, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(56, 557, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(57, 581, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(58, 592, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(59, 593, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(60, 612, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(61, 613, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(62, 622, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(63, 623, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(64, 631, 0, 0, 0, 0, 0, 0, 8, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(65, 632, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(66, 641, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(67, 655, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(68, 657, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(69, 659, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(70, 672, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(71, 673, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(72, 702, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(73, 714, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(74, 716, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(75, 717, 0, 0, 0, 0, 0, 0, 88, 0, 0, 88, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(76, 718, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(77, 719, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(78, 721, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(79, 722, 0, 0, 0, 0, 0, 0, 6, 0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(80, 726, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(81, 727, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(82, 731, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(83, 738, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(84, 741, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(85, 744, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(86, 749, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(87, 750, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(88, 755, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(89, 756, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(90, 758, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(91, 760, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(92, 763, 0, 0, 0, 0, 0, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(93, 777, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(94, 785, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(95, 800, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(96, 836, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(97, 842, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(98, 850, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(99, 896, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(100, 909, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(101, 911, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(102, 913, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(103, 918, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(104, 922, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(105, 923, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(106, 931, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(107, 937, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(108, 938, 0, 0, 0, 0, 0, 0, 47, 0, 0, 47, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(109, 946, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(110, 950, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(111, 951, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(112, 965, 0, 0, 0, 0, 0, 0, 5, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(113, 968, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(114, 973, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(115, 975, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(116, 976, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(117, 981, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(118, 983, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(119, 991, 0, 0, 0, 0, 0, 0, 3, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(120, 1010, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(121, 1076, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(122, 1079, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(123, 1083, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(124, 1088, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(125, 1089, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(126, 1098, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(127, 1172, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(128, 1173, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(129, 1179, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(130, 1180, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(131, 1191, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(132, 1207, 0, 0, 0, 0, 0, 0, 99, 0, 0, 99, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(133, 1209, 0, 0, 0, 0, 0, 0, 80, 0, 0, 79, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(134, 1210, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(135, 1220, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(136, 1246, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(137, 1247, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(138, 1249, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(139, 1250, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(140, 1260, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(141, 1266, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(142, 1270, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(143, 1274, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(144, 1277, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(145, 1310, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(146, 1328, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(147, 1331, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(148, 1333, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(149, 1334, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(150, 1360, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(151, 1361, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(152, 1370, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(153, 1403, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(154, 1406, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(155, 1423, 0, 0, 0, 0, 0, 0, 9, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(156, 1424, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(157, 1431, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(158, 1434, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(159, 1438, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(160, 1452, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(161, 1454, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(162, 1459, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(163, 1463, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(164, 1465, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(165, 1480, 0, 0, 0, 0, 0, 0, 8, 0, 0, 6, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(166, 1481, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(167, 1482, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(168, 1483, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(169, 1484, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(170, 1485, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(171, 1494, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(172, 1495, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(173, 1496, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(174, 1497, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(175, 1498, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(176, 1499, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(177, 1501, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(178, 1502, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(179, 1503, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(180, 1506, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(181, 1507, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(182, 1512, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(183, 1517, 0, 0, 0, 0, 0, 0, 10, 0, 0, 296, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(184, 1521, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(185, 1524, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(186, 1534, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(187, 1548, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(188, 1552, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(189, 1573, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(190, 1576, 0, 0, 0, 0, 0, 0, 25, 0, 0, 25, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(191, 1577, 0, 0, 0, 0, 0, 0, 24, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(192, 1599, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(193, 1603, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(194, 1604, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(195, 1605, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(196, 1606, 0, 0, 0, 0, 0, 0, 11, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(197, 1608, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(198, 1614, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(199, 1615, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(200, 1620, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(201, 1622, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(202, 1630, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(203, 1632, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(204, 1633, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(205, 1647, 0, 0, 0, 0, 0, 0, 20, 0, 0, 19, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(206, 1649, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(207, 1663, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(208, 1664, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(209, 1665, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(210, 1667, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(211, 1668, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(212, 1672, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(213, 1673, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(214, 1712, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(215, 1714, 0, 0, 0, 0, 0, 0, 12, 0, 0, 11, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(216, 1715, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(217, 83302, 0, 0, 0, 0, 0, 0, 8, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(218, 83305, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(219, 83326, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(220, 83333, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(221, 83334, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(222, 83339, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(223, 83348, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(224, 83365, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(225, 83369, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(226, 83370, 0, 0, 0, 0, 0, 0, 9, 0, 0, 9, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(227, 83375, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(228, 83378, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(229, 83382, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(230, 83387, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(231, 83414, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(232, 83420, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(233, 83438, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(234, 83443, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(235, 83448, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(236, 83449, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(237, 83450, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(238, 83453, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(239, 83462, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(240, 83463, 0, 0, 0, 0, 0, 0, 200, 0, 0, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(241, 83464, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(242, 83465, 0, 0, 0, 0, 0, 0, 50, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(243, 83466, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(244, 83467, 0, 0, 0, 0, 0, 0, 100, 0, 0, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(245, 83468, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(246, 83469, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(247, 83470, 0, 0, 0, 0, 0, 0, 30, 0, 0, 30, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(248, 83471, 0, 0, 0, 0, 0, 0, 200, 0, 0, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(249, 83472, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(250, 83473, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(251, 83474, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(252, 83475, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(253, 83476, 0, 0, 0, 0, 0, 0, 15, 0, 0, 15, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(254, 83477, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(255, 83478, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(256, 83479, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(257, 83480, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(258, 83481, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(259, 83482, 0, 0, 0, 0, 0, 0, 24, 0, 0, 24, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(260, 83483, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(261, 83484, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(262, 83485, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(263, 83486, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(264, 83487, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(265, 83488, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(266, 83489, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(267, 83490, 0, 0, 0, 0, 0, 0, 5, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(268, 83491, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(269, 83492, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(270, 83493, 0, 0, 0, 0, 0, 0, 8, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(271, 83495, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(272, 83496, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(273, 83497, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(274, 83498, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(275, 83499, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(276, 83500, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(277, 83502, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(278, 83503, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(279, 83504, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(280, 83505, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(281, 83506, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(282, 83507, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(283, 83508, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(284, 83509, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(285, 83510, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(286, 83511, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(287, 83512, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(288, 83513, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(289, 83514, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(290, 83515, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(291, 83516, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(292, 83517, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(293, 83518, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(294, 83519, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(295, 83521, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(296, 83522, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(297, 83523, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(298, 83524, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(299, 83525, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(300, 83526, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(301, 83527, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(302, 83528, 0, 0, 0, 0, 0, 0, 20, 0, 0, 20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(303, 83529, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(304, 83530, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(305, 83531, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(306, 83532, 0, 0, 0, 0, 0, 0, 2, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(307, 83533, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(308, 83534, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(309, 83535, 0, 0, 0, 0, 0, 0, 40, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(310, 83536, 0, 0, 0, 0, 0, 0, 96, 0, 0, 96, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(311, 83537, 0, 0, 0, 0, 0, 0, 96, 0, 0, 96, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(312, 83538, 0, 0, 0, 0, 0, 0, 96, 0, 0, 96, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(313, 83539, 0, 0, 0, 0, 0, 0, 96, 0, 0, 96, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(314, 83540, 0, 0, 0, 0, 0, 0, 48, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(315, 83541, 0, 0, 0, 0, 0, 0, 48, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(316, 83542, 0, 0, 0, 0, 0, 0, 48, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(317, 83543, 0, 0, 0, 0, 0, 0, 48, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(318, 83544, 0, 0, 0, 0, 0, 0, 10, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(319, 83545, 0, 0, 0, 0, 0, 0, 48, 0, 0, 48, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(320, 83546, 0, 0, 0, 0, 0, 0, 40, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(321, 83547, 0, 0, 0, 0, 0, 0, 40, 0, 0, 40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(322, 83548, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(323, 83549, 0, 0, 0, 0, 0, 0, 12, 0, 0, 12, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(324, 83551, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(325, 83552, 0, 0, 0, 0, 0, 0, 100, 0, 0, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(326, 250, 0, 0, 0, 0, 0, 0, 0, 0, 0, 18, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `itens_comprados`
--

CREATE TABLE `itens_comprados` (
  `id` int(11) NOT NULL,
  `id_artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iva`
--

CREATE TABLE `iva` (
  `id` int(11) NOT NULL,
  `percentagem` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `iva`
--

INSERT INTO `iva` (`id`, `percentagem`, `motivo`, `data`) VALUES
(1, 16, 'Taxa de Iva Normal', '2023-04-30 16:56:44'),
(2, 0, 'Isento por seu um estabelecimento pequeno', '2023-09-03 15:18:55'),
(3, 5, 'Para clinicas', '2023-09-13 09:11:19'),
(5, 0, '', '2024-06-17 08:27:00'),
(6, 16, 'Compra com IVA', '2024-06-17 08:27:48');

-- --------------------------------------------------------

--
-- Table structure for table `licenca`
--

CREATE TABLE `licenca` (
  `id` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `status` enum('ativa','inativa') DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `licenca`
--

INSERT INTO `licenca` (`id`, `data_inicio`, `data_fim`, `status`) VALUES
(1, '2024-12-05', '2025-12-31', 'ativa');

-- --------------------------------------------------------

--
-- Table structure for table `metodo_pagamento`
--

CREATE TABLE `metodo_pagamento` (
  `id` int(11) NOT NULL,
  `descricao` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `metodo_pagamento`
--

INSERT INTO `metodo_pagamento` (`id`, `descricao`) VALUES
(1, 'Numerario'),
(2, 'Emola'),
(3, 'Mpesa'),
(4, 'POS');

-- --------------------------------------------------------

--
-- Table structure for table `nc_artigos`
--

CREATE TABLE `nc_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `id_nota` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nc_artigos_temp`
--

CREATE TABLE `nc_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` varchar(255) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `nc_artigos_temp`
--

INSERT INTO `nc_artigos_temp` (`id`, `artigo`, `qtd`, `preco`, `total`, `iva`, `lote`, `user`, `data`) VALUES
(2, 1573, 1, 20.00, 20.00, 0.00, '24GO26', 25, '2025-11-24 09:01:40'),
(3, 1210, 1, 97.00, 97.00, 0.00, '2408098,2408096', 22, '2025-11-25 11:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `nc_servicos_fact`
--

CREATE TABLE `nc_servicos_fact` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `nota_credito_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nc_servicos_fact`
--

INSERT INTO `nc_servicos_fact` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `nota_credito_id`) VALUES
(1, 2, 3, 1900.00, 3800.00, 25, 1),
(2, 3, 1, 1425.00, 1425.00, 25, 1),
(3, 1, 1, 1140.00, 1140.00, 25, 2),
(4, 1, 1, 1140.00, 1140.00, 25, 3),
(5, 1, 1, 1140.00, 1140.00, 25, 4),
(6, 2, 1, 1900.00, 1900.00, 25, 5),
(7, 1, 1, 1140.00, 1140.00, 25, 6),
(8, 1, 1, 1140.00, 1140.00, 25, 7);

-- --------------------------------------------------------

--
-- Table structure for table `nc_servicos_temp`
--

CREATE TABLE `nc_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nc_servicos_temp`
--

INSERT INTO `nc_servicos_temp` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `empresa_id`) VALUES
(2, 1, 1, 1104.00, 1104.00, 6, 2),
(13, 2, 1, 1840.00, 1840.00, 25, 2),
(14, 1, 1, 1104.00, 1104.00, 25, 2);

-- --------------------------------------------------------

--
-- Table structure for table `nd_artigos`
--

CREATE TABLE `nd_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `id_nd` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nd_artigos_temp`
--

CREATE TABLE `nd_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `lote` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nd_servicos_fact`
--

CREATE TABLE `nd_servicos_fact` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `nota_debito_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nd_servicos_fact`
--

INSERT INTO `nd_servicos_fact` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `nota_debito_id`) VALUES
(1, 2, 4, 1900.00, 9500.00, 25, 1),
(2, 1, 1, 1140.00, 1140.00, 25, 2),
(3, 2, 1, 1900.00, 1900.00, 25, 3),
(4, 2, 1, 1900.00, 1900.00, 25, 4),
(5, 1, 1, 1140.00, 1140.00, 25, 5);

-- --------------------------------------------------------

--
-- Table structure for table `nd_servicos_temp`
--

CREATE TABLE `nd_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nota_credito_recepcao`
--

CREATE TABLE `nota_credito_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nota_credito_recepcao`
--

INSERT INTO `nota_credito_recepcao` (`id`, `n_doc`, `factura_recepcao_id`, `paciente`, `empresa_id`, `valor`, `motivo`, `serie`, `usuario`, `dataa`, `data_criacao`) VALUES
(1, 1, 7, 2, 1, 5225.00, 'sdas', 2025, 25, '2025-11-26', '2025-11-26 08:01:28'),
(2, 2, 8, 2, 1, 1140.00, 'asdadasd', 2025, 25, '2025-11-26', '2025-11-26 08:08:51'),
(3, 3, 7, 2, 1, 1140.00, 'asdasd', 2025, 25, '2025-11-26', '2025-11-26 08:15:18'),
(4, 4, 7, 2, 1, 1140.00, 'adbabdabsd', 2025, 25, '2025-11-26', '2025-11-26 08:22:21'),
(5, 5, 7, 2, 1, 1900.00, 'adas asda', 2025, 25, '2025-11-26', '2025-11-26 08:26:43'),
(6, 6, 7, 2, 1, 1140.00, 'sasdasdddansdad sadasd', 2025, 25, '2025-11-26', '2025-11-26 08:27:15'),
(7, 7, 7, 2, 1, 1140.00, 'asknd aksndk', 2025, 25, '2025-11-26', '2025-11-26 08:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `nota_debito`
--

CREATE TABLE `nota_debito` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `disconto` decimal(10,2) NOT NULL,
  `serie` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `condicoes` varchar(255) NOT NULL,
  `apolice` varchar(255) NOT NULL,
  `codigo1` varchar(255) NOT NULL,
  `codigo2` varchar(255) NOT NULL,
  `motivo` text DEFAULT NULL,
  `cliente` int(11) NOT NULL,
  `utente` text NOT NULL,
  `id_factura` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nota_debito_recepcao`
--

CREATE TABLE `nota_debito_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nota_debito_recepcao`
--

INSERT INTO `nota_debito_recepcao` (`id`, `n_doc`, `factura_recepcao_id`, `paciente`, `empresa_id`, `valor`, `motivo`, `serie`, `usuario`, `dataa`, `data_criacao`) VALUES
(1, 1, 7, 2, 1, 9500.00, 'sadads', 2025, 25, '2025-11-26', '2025-11-26 08:30:38'),
(2, 2, 7, 2, 1, 1140.00, 'jugugg', 2025, 25, '2025-11-26', '2025-11-26 08:33:20'),
(3, 3, 7, 2, 1, 1900.00, 'asdasd', 2025, 25, '2025-11-26', '2025-11-26 08:37:19'),
(4, 4, 7, 2, 1, 1900.00, 'sadasd', 2025, 25, '2025-11-26', '2025-11-26 08:38:19'),
(5, 5, 7, 2, 1, 1140.00, 'asdasd', 2025, 25, '2025-11-26', '2025-11-26 08:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `nota_de_credito`
--

CREATE TABLE `nota_de_credito` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` int(11) NOT NULL,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `motivo` varchar(20000) NOT NULL,
  `cliente` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordem_compra`
--

CREATE TABLE `ordem_compra` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL,
  `fornecedor` int(11) NOT NULL,
  `ref_factura` varchar(255) DEFAULT NULL,
  `valor_pago` decimal(10,2) NOT NULL DEFAULT 0.00,
  `iva_pago` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prazo` varchar(255) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordem_compra_artigos`
--

CREATE TABLE `ordem_compra_artigos` (
  `id` int(11) NOT NULL,
  `artigo` varchar(500) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `ordem_compra` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordem_compra_artigos_temp`
--

CREATE TABLE `ordem_compra_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` varchar(500) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `numero_processo` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `apelido` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` enum('M','F','Outro') DEFAULT NULL,
  `documento_tipo` varchar(50) DEFAULT NULL,
  `documento_numero` varchar(100) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `contacto_alternativo` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `provincia` varchar(255) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL COMMENT 'Empresa/Seguro associado',
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_registo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pacientes`
--

INSERT INTO `pacientes` (`id`, `numero_processo`, `nome`, `apelido`, `data_nascimento`, `sexo`, `documento_tipo`, `documento_numero`, `contacto`, `contacto_alternativo`, `email`, `endereco`, `bairro`, `cidade`, `provincia`, `empresa_id`, `observacoes`, `ativo`, `data_registo`, `usuario_registo`) VALUES
(1, '01', 'Domingos ', 'Covane', '2025-11-21', 'M', 'BI', '202020393E', '853123357', NULL, 'emersoncovane22@yahoo.com', 'Matundo', 'Matundo', 'Tete', 'Tete', NULL, 'Chegou mal', 1, '2025-11-21 12:50:29', 25),
(2, 'PROC-2025-000001', 'Iverson', 'machava', '2025-11-22', 'M', 'BI', '202020393E', '+258 84 531 2333', NULL, 'iversonmachava06@gmail.com', 'Matundo', 'Matundo', 'Matundo, Tete', 'Tete', 1, NULL, 1, '2025-11-22 21:23:56', 6),
(3, 'PROC-2025-000002', 'Emerson', 'Covane', '2025-11-25', 'M', 'BI', '202020393W', '+258 25 884 5312', NULL, 'emersoncovane23@gmail.com', 'Matundo', NULL, 'Matundo, Tete', 'Tete', 2, NULL, 1, '2025-11-25 21:07:33', 6);

-- --------------------------------------------------------

--
-- Table structure for table `paciente_empresa_historico`
--

CREATE TABLE `paciente_empresa_historico` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `usuario_registo` int(11) DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paciente_empresa_historico`
--

INSERT INTO `paciente_empresa_historico` (`id`, `paciente_id`, `empresa_id`, `data_inicio`, `data_fim`, `observacoes`, `usuario_registo`, `data_registo`) VALUES
(1, 2, 1, '2025-11-22', NULL, NULL, 6, '2025-11-22 21:39:20'),
(2, 3, 1, '2025-11-25', '2025-11-25', NULL, 6, '2025-11-25 21:07:33'),
(3, 3, 2, '2025-11-25', NULL, NULL, 6, '2025-11-25 21:09:31');

-- --------------------------------------------------------

--
-- Table structure for table `pagamentos_recepcao`
--

CREATE TABLE `pagamentos_recepcao` (
  `id` int(11) NOT NULL,
  `fatura_id` int(11) DEFAULT NULL,
  `factura_recepcao_id` int(11) DEFAULT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('dinheiro','m-pesa','emola','pos','transferencia','fatura_empresa') NOT NULL,
  `referencia_pagamento` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `usuario` int(11) NOT NULL,
  `data_pagamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagamentos_recepcao`
--

INSERT INTO `pagamentos_recepcao` (`id`, `fatura_id`, `factura_recepcao_id`, `valor_pago`, `metodo_pagamento`, `referencia_pagamento`, `observacoes`, `usuario`, `data_pagamento`) VALUES
(1, 1, NULL, 1600.00, 'dinheiro', NULL, NULL, 25, '2025-11-22 18:25:31'),
(2, 3, NULL, 1900.00, 'dinheiro', NULL, NULL, 6, '2025-11-22 21:40:14'),
(3, 4, NULL, 1900.00, 'dinheiro', NULL, NULL, 25, '2025-11-23 10:04:29'),
(4, 5, NULL, 1500.00, 'emola', 'fff6r66cj', NULL, 25, '2025-11-23 10:43:43'),
(5, 6, NULL, 1425.00, 'emola', 'hgyfyfyfy', NULL, 25, '2025-11-24 07:29:48');

-- --------------------------------------------------------

--
-- Table structure for table `pedido`
--

CREATE TABLE `pedido` (
  `idpedido` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descpedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `serie` int(11) NOT NULL,
  `pagamentopedido` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `meses` varchar(20) DEFAULT NULL,
  `modo` varchar(255) DEFAULT NULL,
  `trocopedido` decimal(10,2) DEFAULT NULL,
  `disconto` decimal(10,2) DEFAULT NULL,
  `clientepedido` int(11) NOT NULL,
  `userpedido` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `devolucao` int(11) DEFAULT 0,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `pedido`
--

INSERT INTO `pedido` (`idpedido`, `n_doc`, `descpedido`, `serie`, `pagamentopedido`, `iva`, `meses`, `modo`, `trocopedido`, `disconto`, `clientepedido`, `userpedido`, `periodo`, `devolucao`, `data`) VALUES
(1, 1, '2025-11-06 15:28:06', 2025, 63.00, 10.08, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-06 15:28:06'),
(2, 2, '2025-11-06 15:38:47', 2025, 119.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 4, 0, '2025-11-06 15:38:47'),
(3, 3, '2025-11-06 15:47:44', 2025, 135.00, 0.00, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-06 15:47:44'),
(4, 4, '2025-11-06 18:27:33', 2025, 19.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 4, 0, '2025-11-06 18:27:33'),
(5, 5, '2025-11-07 06:34:59', 2025, 0.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 5, 0, '2025-11-07 06:34:59'),
(6, 6, '2025-11-07 10:41:27', 2025, 20.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 5, 0, '2025-11-07 10:41:27'),
(7, 7, '2025-11-07 11:13:25', 2025, 37.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 5, 0, '2025-11-07 11:13:25'),
(8, 8, '2025-11-07 12:15:06', 2025, 30.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 5, 0, '2025-11-07 12:15:06'),
(9, 9, '2025-11-07 13:08:58', 2025, 235.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 7, 0, '2025-11-07 13:08:58'),
(10, 10, '2025-11-07 14:37:14', 2025, 217.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 7, 0, '2025-11-07 14:37:14'),
(11, 11, '2025-11-08 16:00:24', 2025, 80.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 10, 0, '2025-11-08 16:00:24'),
(12, 12, '2025-11-08 16:02:19', 2025, 80.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 10, 1, '2025-11-08 16:02:19'),
(13, 13, '2025-11-08 16:06:42', 2025, 30.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 10, 0, '2025-11-08 16:06:42'),
(14, 14, '2025-11-08 17:07:14', 2025, 130.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 10, 0, '2025-11-08 17:07:14'),
(15, 15, '2025-11-10 08:22:43', 2025, 328.00, 0.00, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-10 08:22:43'),
(16, 16, '2025-11-10 08:46:23', 2025, 328.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 11, 0, '2025-11-10 08:46:23'),
(17, 17, '2025-11-10 08:58:19', 2025, 100.00, 0.00, NULL, '1', NULL, 0.00, 1, 24, 11, 0, '2025-11-10 08:58:19'),
(18, 18, '2025-11-10 13:04:17', 2025, 26.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 8, 0, '2025-11-10 13:04:17'),
(19, 19, '2025-11-10 16:03:24', 2025, 20.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 8, 0, '2025-11-10 16:03:24'),
(20, 20, '2025-11-10 16:08:26', 2025, 40.00, 0.00, NULL, '1', NULL, 0.00, 1, 23, 8, 0, '2025-11-10 16:08:26'),
(21, 21, '2025-11-10 18:05:09', 2025, 94.00, 15.04, NULL, '1', NULL, 0.00, 1, 23, 8, 0, '2025-11-10 18:05:09'),
(22, 22, '2025-11-24 08:58:55', 2025, 2320.00, 0.00, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-24 08:58:55'),
(23, 23, '2025-11-25 11:09:51', 2025, 312.00, 0.00, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-25 11:09:51'),
(24, 24, '2025-11-25 15:03:43', 2025, 129.00, 0.00, NULL, '1', NULL, 0.00, 1, 22, 3, 0, '2025-11-25 15:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `periodicidade`
--

CREATE TABLE `periodicidade` (
  `id` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periodo`
--

CREATE TABLE `periodo` (
  `idperiodo` int(11) NOT NULL,
  `diaperiodo` varchar(7) NOT NULL DEFAULT 'fechado',
  `serie` int(11) NOT NULL,
  `aberturaperiodo` decimal(10,0) DEFAULT NULL,
  `fechoperiodo` decimal(10,0) DEFAULT NULL,
  `numero_devolucoes` int(11) NOT NULL DEFAULT 0,
  `dataaberturaperiodo` datetime NOT NULL,
  `datafechoperiodo` datetime NOT NULL,
  `usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `periodo`
--

INSERT INTO `periodo` (`idperiodo`, `diaperiodo`, `serie`, `aberturaperiodo`, `fechoperiodo`, `numero_devolucoes`, `dataaberturaperiodo`, `datafechoperiodo`, `usuario`) VALUES
(3, 'Aberto', 2025, 1, 3218, 0, '2025-11-06 17:27:06', '2025-11-25 17:03:43', 22),
(4, 'Fechado', 2025, 0, 138, 0, '2025-11-06 17:35:23', '2025-11-06 20:27:33', 23),
(5, 'Fechado', 2025, 0, 87, 0, '2025-11-07 08:30:15', '2025-11-07 14:15:06', 24),
(7, 'Fechado', 2025, 0, 452, 0, '2025-11-07 15:04:38', '2025-11-07 16:37:14', 23),
(8, 'Aberto', 2025, 0, 195, 0, '2025-11-08 08:19:46', '2025-11-10 20:05:09', 23),
(10, 'Fechado', 2025, 0, 320, 0, '2025-11-08 17:00:04', '2025-11-08 19:07:14', 24),
(11, 'Fechado', 2025, 0, 428, 0, '2025-11-10 09:17:14', '2025-11-10 10:58:19', 24),
(12, 'Fechado', 2025, 0, 0, 0, '2025-11-10 18:06:17', '2025-11-10 18:06:17', 0),
(13, 'Aberto', 2025, 0, 0, 0, '2025-11-23 12:49:29', '2025-11-23 12:49:29', 0);

-- --------------------------------------------------------

--
-- Table structure for table `produto`
--

CREATE TABLE `produto` (
  `idproduto` int(11) NOT NULL,
  `prefico` varchar(20) DEFAULT NULL,
  `nomeproduto` varchar(1000) NOT NULL,
  `stock` double DEFAULT NULL,
  `stock_min` double DEFAULT NULL,
  `stocavel` int(11) NOT NULL DEFAULT 0,
  `preco_compra` decimal(10,2) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `iva` int(11) NOT NULL,
  `codbar` mediumtext DEFAULT NULL,
  `grupo` int(11) NOT NULL,
  `familia` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produto`
--

INSERT INTO `produto` (`idproduto`, `prefico`, `nomeproduto`, `stock`, `stock_min`, `stocavel`, `preco_compra`, `preco`, `iva`, `codbar`, `grupo`, `familia`, `data`) VALUES
(1, 'FARM', 'NALGEN 500 CP', 167, 0, 1, 0.00, 82.00, 0, '08/2025', 8, 8, '2025-03-28 00:38:15'),
(4, 'FARM', 'ALGODAO 100G', 4, 0, 1, 90.00, 134.00, 0, '28/02/2029', 8, 8, '2025-11-07 06:42:55'),
(5, 'FARM', 'ALGODAO 200G', 0, 0, 1, 135.00, 201.00, 0, 'AL002', 8, 8, '2025-01-20 07:18:41'),
(6, 'FARM', 'ALBENDAZOLE SUSP 10ML-GZOLE-S', 71, 0, 1, 30.00, 45.00, 0, 'ALB002', 8, 8, '2025-01-28 17:31:50'),
(7, 'FARM', 'ALCOHOL 70%', 3, 0, 1, 150.86, 225.00, 0, 'ALC001', 8, 8, '2024-09-04 22:54:59'),
(8, 'FARM', 'ALGODAO 50GMS0', 7, 0, 1, 0.00, 52.00, 0, '30/04/2029', 8, 8, '2025-11-07 07:12:46'),
(9, 'FARM', 'AMILORIDO 5MG COMP 10x10 AMILOGEN', 103, 0, 1, 0.00, 15.00, 0, '', 8, 8, '2025-03-28 12:44:58'),
(10, 'FARM', 'AMITRIPTILINE HYDROCHLORIDE COMP 25MG 10X10 AMIZOL', -1, 0, 1, 140.00, 25.00, 0, 'AMI002', 8, 8, '2025-11-07 09:03:55'),
(13, 'FARM', 'AMPICILINA SODIUM INJ 1G', 0, 0, 1, 55.00, 82.00, 0, 'AMP001', 8, 8, '2024-04-27 17:46:54'),
(15, 'FARM', 'ASPIRINA 500MG COMP 10X10 GENSPRIN', 0, 0, 1, 99.00, 15.00, 0, 'ASP001', 8, 8, '2025-11-07 09:05:29'),
(16, 'FARM', 'AZITAB AZITROMICINA COMP 500MG', 3, 0, 1, 60.00, 25.00, 0, '30/06/26', 8, 8, '2025-11-08 09:40:54'),
(17, 'FARM', 'SPASGEN 10MG COMP 2X10', 2, 0, 1, 0.00, 65.00, 0, '02/2026', 8, 8, '2025-03-28 13:29:45'),
(18, 'FARM', 'CADEIRA DE RODAS GENERICS (WHEEL CHAIR)', 0, 0, 1, 9500.00, 14155.00, 0, 'CAD002', 8, 8, '2024-04-27 17:46:54'),
(19, 'FARM', 'CALGEN-D 3X10 CALCIO+VIT D3', 8, 0, 1, 0.00, 148.66, 0, '01/25', 8, 8, '2025-01-14 13:40:47'),
(20, 'FARM', 'CASTOR OIL 100ML', 17, 0, 1, 119.00, 177.00, 0, 'CAS001', 8, 8, '2025-11-07 06:42:55'),
(21, 'FARM', 'CHLORAMPHENICOL POMADA OFTALMICA 1% KLORAXIN', 0, 0, 1, 22.00, 33.00, 0, 'CHL001', 8, 8, '2024-04-27 17:46:54'),
(22, 'FARM', 'CIPROGEN 500 MG CP', 153, 0, 1, 0.00, 52.00, 0, '07/2025', 8, 8, '2025-03-28 22:09:33'),
(23, 'FARM', 'CIPROFLOXACIN GOTAS OFT 10ML CIPROGEN', 91, 0, 1, 0.00, 97.00, 0, 'CIP002', 8, 8, '2025-11-07 09:11:27'),
(24, 'FARM', 'CLAVUGEN DS 100 ML 156.25/5ML', 4, 2, 1, 100.00, 149.00, 0, '11/2025', 8, 8, '2025-07-25 11:56:53'),
(26, 'FARM', 'CLOTRIMAZOLE VAGINAL TAB 100MG CLOTRILIN', 0, 0, 1, 47.00, 70.00, 0, 'CLO002', 8, 8, '2025-01-30 20:26:19'),
(28, 'FARM', 'CLOTRIMAZOLE P/FERIDAS DA BOCA 15ML-CLOGEN ORAL', 2, 0, 1, 43.00, 64.00, 0, 'CLO003', 8, 8, '2024-08-22 12:25:46'),
(29, 'FARM', 'CLORFENIRAMINA COMP METADIL', 0, 0, 1, 500.00, 745.00, 0, 'CLO005', 8, 8, '2024-04-27 17:46:54'),
(30, 'FARM', 'CO-TRIMOXAZOLE 480MG COMP 10X10 COTRIGEN', 16, 20, 1, 12.00, 17.90, 0, '31/01/2027', 8, 8, '2025-06-19 14:49:45'),
(31, 'FARM', 'COMPRENSAS DE GAZE COM PARAFINA 10cm X 10cm', 0, 0, 1, 105.00, 156.00, 0, '31/11/2025', 8, 8, '2025-02-01 07:24:12'),
(32, 'FARM', 'CLARA PEL CREAM 15GM', 5, 0, 1, 99.00, 148.00, 0, '01/2026', 8, 8, '2024-11-20 07:05:15'),
(33, 'FARM', 'DANAZOL CAPS 200MG (DAZOL)', 8, 0, 1, 699.00, 347.33, 0, 'DAN001', 8, 8, '2025-02-02 02:46:11'),
(35, 'FARM', 'DISPOGEN (DESCARTAVEL SERINGA COM AGULHA 10ML)', 61, 0, 1, 520.00, 8.00, 0, '04/05/2028', 8, 8, '2025-01-25 10:34:03'),
(36, 'FARM', 'DISPOGEN (DESCARTAVEL SERINGA COM AGULHA 1ML)', 0, 0, 1, 490.00, 730.00, 0, 'DES003', 8, 8, '2024-04-27 17:46:54'),
(40, 'FARM', 'METADOL DOXICICLINA CAPS', 0, 0, 1, 270.00, 402.00, 0, 'DOX002', 8, 8, '2024-04-27 17:46:54'),
(41, 'FARM', 'VITAMINA E E-VITA 3X10 CAPS (cartela)', 7, 0, 1, 0.00, 454.00, 0, 'E001', 8, 8, '2025-11-07 09:25:24'),
(42, 'FARM', 'FLUGEN TABLET 1X4s', 18, 0, 1, 10.00, 15.00, 0, 'FLU001', 8, 8, '2025-01-20 06:02:28'),
(43, 'FARM', 'FORACHE SOLUCAO PARA DOR DE DENTE 5ML', 17, 0, 1, 0.00, 118.00, 0, 'FOR001', 8, 8, '2025-03-28 19:21:00'),
(44, 'FARM', 'FUROSEMIDE 20MG INJ DIASIX', 0, 0, 1, 22.00, 33.00, 0, 'FUR001', 8, 8, '2024-04-27 17:46:54'),
(45, 'FARM', 'GENLON 125ML', 10, 0, 1, 65.00, 97.00, 0, '04/26', 8, 8, '2025-01-26 03:53:52'),
(46, 'FARM', 'GENLON 75ML', 9, 0, 1, 45.00, 67.00, 0, '04/26', 8, 8, '2025-02-02 05:10:26'),
(47, 'FARM', 'GENCEF INJ 1G (CEFTRIAXONE)', 44, 0, 1, 0.00, 103.00, 0, '29/05/2027', 8, 8, '2025-11-07 09:29:14'),
(48, 'FARM', 'PEN-G FENOXIMETIL PENICELINA COMP. 500MG 10X10', 683, 0, 1, 49.90, 63.00, 0, '31/07/2026', 8, 8, '2025-11-07 09:40:37'),
(49, 'FARM', 'AMOXIGEN AMOXICILINA CPS 500MG 10X10', 4, 0, 1, 0.00, 33.00, 0, '30/09/2025', 8, 8, '2024-12-10 22:59:39'),
(50, 'FARM', 'GENDOL S 100ML PARACETAMOL XAROPE', 71, 0, 1, 42.00, 63.00, 0, '02/2026', 8, 8, '2025-03-28 03:16:47'),
(51, 'FARM', 'GENMOX 250MG AMOXICILINA XAROPE', 26, 0, 1, 0.00, 97.00, 0, '09/2026', 8, 8, '2025-03-28 20:08:27'),
(52, 'FARM', 'GENMOX 125MG XAROPE', 73, 0, 1, 45.00, 67.00, 0, '09/2025', 8, 8, '2025-03-27 17:42:16'),
(54, 'FARM', 'KAMASUTRA CONDOMS CHOCOLATE', 0, 0, 1, 35.00, 55.00, 0, 'GF230', 8, 8, '2025-11-07 13:39:16'),
(55, 'FARM', 'KAMASUTRE CONDOMS CONTOURED', 0, 0, 1, 35.00, 55.00, 0, 'GF231', 8, 8, '2025-11-07 13:39:42'),
(56, 'FARM', 'KAMASUTRA CONDOMS PLAIN', 0, 0, 1, 35.00, 55.00, 0, 'GF232', 8, 8, '2025-11-07 13:40:05'),
(57, 'FARM', 'KAMASUTRA  WET N WILD', 0, 0, 1, 45.00, 70.00, 0, 'GF234', 8, 8, '2025-11-07 13:40:46'),
(58, 'FARM', 'KAMASUTRA WARM INTIMACY', 0, 0, 1, 45.00, 70.00, 0, 'GF235', 8, 8, '2025-11-07 13:41:28'),
(59, 'FARM', 'KAMASUTRA CHILL THRILL', 0, 0, 1, 45.00, 70.00, 0, 'GF236', 8, 8, '2025-11-07 13:41:57'),
(60, 'FARM', 'KAMASUTRA CONDOMS EXTRA LARGE', 0, 0, 1, 45.00, 70.00, 0, 'GF237', 8, 8, '2025-11-07 13:42:15'),
(61, 'FARM', 'KAMASUTRA PRAZER PROLONGADO LONG LAST)', 0, 0, 1, 45.00, 70.00, 0, 'GF239', 8, 8, '2025-11-07 13:42:33'),
(62, 'FARM', 'KAMASUTRA RIBBED', 0, 0, 1, 35.00, 55.00, 0, 'GF240', 8, 8, '2025-11-07 13:42:53'),
(63, 'FARM', 'KAMASUTRA CONDOMS STRAWBERRY ( MORANGO)', 0, 0, 1, 35.00, 55.00, 0, 'GF241', 8, 8, '2025-11-07 13:43:15'),
(64, 'FARM', 'KAMASUTRA CONDOMS INTENCITY', 0, 0, 1, 45.00, 67.00, 0, 'GF242', 8, 8, '2024-04-27 17:46:54'),
(65, 'FARM', 'KAMAUTRA CONDOMS BLACKCURRANT (UVA)', 0, 0, 1, 35.00, 52.00, 0, 'GF243', 8, 8, '2024-04-27 17:46:54'),
(66, 'FARM', 'KAMASUTRA CONDOMS DOTTED ( PONTILHADO)', 0, 0, 1, 35.00, 52.00, 0, 'GF244', 8, 8, '2024-04-27 17:46:54'),
(67, 'FARM', 'KAMASUTRA (SUPERTHIN)', 0, 0, 1, 45.00, 67.00, 0, 'GF245', 8, 8, '2024-04-27 17:46:54'),
(69, 'FARM', 'HYDROCHLOROTHIAZIDA + AMILORIDO COMP 10x10 MODUGEN', 3, 0, 1, 149.00, 23.00, 0, 'H001', 8, 8, '2024-12-13 02:32:13'),
(70, 'FARM', 'HYDROCHLOROTHIAZIDA 50MG COMP  HYGEN', 90, 0, 1, 110.00, 17.00, 0, 'H002', 8, 8, '2025-02-02 15:48:18'),
(72, 'FARM', 'INDOMETACINA CAP INDOSULE', 0, 0, 1, 190.00, 283.00, 0, 'IND001', 8, 8, '2024-04-27 17:46:54'),
(73, 'FARM', 'IV CATHETER PERIFERICO 18G', 0, 0, 1, 28.00, 42.00, 0, 'IV001', 8, 8, '2024-04-27 17:46:54'),
(74, 'FARM', 'I.V CANNULA WITH WINGS 20G', 0, 0, 1, 25.00, 37.00, 0, 'IV002', 8, 8, '2024-04-27 17:46:54'),
(75, 'FARM', 'I.V CANNULA WITH WINGS 22G', 0, 0, 1, 25.00, 37.00, 0, 'IV003', 8, 8, '2024-04-27 17:46:54'),
(76, 'FARM', 'I.V CANNULA WITH WINGS 24G', 0, 0, 1, 25.00, 37.00, 0, 'IV004', 8, 8, '2024-04-27 17:46:54'),
(77, 'FARM', 'I.V CANNULA WITH WINGS 26G', 0, 0, 1, 27.00, 40.00, 0, 'IV005', 8, 8, '2024-04-27 17:46:54'),
(79, 'FARM', 'KETACONAZOLE 200MG COMP 10X10', 11, 0, 1, 370.00, 70.00, 0, 'KET001', 8, 8, '2024-08-03 03:32:45'),
(81, 'FARM', 'LEVONORGESTREL BP 1,5MG (CO-PILL) 1S', 0, 0, 1, 40.00, 60.00, 0, 'LEV001', 8, 8, '2024-04-27 17:46:54'),
(83, 'FARM', 'LIGADURA DE GAZE 10M X 10CM 12', 5, 0, 1, 0.00, 30.00, 0, 'LIG002', 8, 8, '2025-11-07 09:44:01'),
(84, 'FARM', 'LUVAS LATEX P/EXAMES (GRANDES) 100\'S', 0, 0, 1, 350.00, 522.00, 0, 'LUV001', 8, 8, '2024-04-27 17:46:54'),
(85, 'FARM', 'LUVAS LATEX P/EXAMES (MEDIAS) 100\'S', 1, 0, 1, 350.00, 522.00, 0, 'LUV002', 8, 8, '2025-01-14 21:24:03'),
(86, 'FARM', 'LUVAS LATEX P/EXAMES (SMALL) 100\'S', 0, 0, 1, 350.00, 522.00, 0, 'LUV003', 8, 8, '2024-04-27 17:46:54'),
(87, 'FARM', 'LUVAS CIRURGICAS ESTERILIZADAS DE LATEX COM PO 6', 0, 0, 1, 23.00, 34.00, 0, 'LUV004', 8, 8, '2024-04-27 17:46:54'),
(88, 'FARM', 'LUVAS CIRURGICAS ESTERILIZADAS DE LATEX COM PO 6,5', 0, 0, 1, 23.00, 34.00, 0, 'LUV005', 8, 8, '2024-05-30 07:03:04'),
(89, 'FARM', 'LUVAS CIRURGICAS ESTERILIZADAS DE LATEX COM PO 7', 0, 0, 1, 23.00, 34.00, 0, 'LUV006', 8, 8, '2024-08-15 12:58:33'),
(90, 'FARM', 'LUVAS CIRURGICAS ESTERILIZADAS DE LATEX COM PO 7,5', 0, 0, 1, 23.00, 34.00, 0, '5/2027', 8, 8, '2024-11-19 13:51:11'),
(91, 'FARM', 'LUVAS CIRURGICAS ESTERILIZADAS DE LATEX 8.0', 1, 0, 1, 25.00, 37.00, 0, 'LUV008', 8, 8, '2024-11-03 06:17:04'),
(93, 'FARM', 'MENTHOSIL COUGH LOZENGES-MINT', 1, 0, 1, 0.00, 55.00, 0, '30/9/2025', 8, 8, '2024-12-10 13:05:49'),
(94, 'FARM', 'METHYLERGONOVINE MALEATE TABLETS (LERIN)', 0, 0, 1, 225.00, 335.00, 0, 'MET001', 8, 8, '2024-04-27 17:46:54'),
(95, 'FARM', 'METHYLERGONOVINE MALEATE INJECTION (LERIN)', 0, 0, 1, 50.00, 75.00, 0, 'MET002', 8, 8, '2024-04-27 17:46:54'),
(96, 'FARM', 'METROGEN 250MG COMP', 44, 10, 1, 0.00, 13.00, 0, '04/2027', 8, 8, '2025-04-22 14:10:05'),
(97, 'FARM', 'MENTHOSIL COUGH LOZEBGES-ORANGE', 3, 0, 1, 0.00, 55.00, 0, '30/08/2025', 8, 8, '2025-01-07 06:17:38'),
(98, 'FARM', 'MODCLOX CLOXACILINA 500MG CAPS', 0, 0, 1, 40.00, 60.00, 0, 'MOD001', 8, 8, '2024-04-27 17:46:54'),
(99, 'FARM', 'MULTIGEN MULTIVITAMINAS COMP', 0, 100, 1, 10.00, 14.20, 0, '30/11/2026', 8, 8, '2025-05-07 13:00:41'),
(100, 'FARM', 'NORETHISTERONE COMP 5MG (STERON)', 0, 0, 1, 95.00, 142.00, 0, 'NOR001', 8, 8, '2024-04-27 17:46:54'),
(101, 'FARM', 'NYSTANTIN ORAL SUS 30ML NYSTAGEN S', 0, 0, 1, 0.00, 148.00, 0, '06/25', 8, 8, '2025-11-07 09:30:18'),
(102, 'FARM', 'OMEPRAZOLE 20MG CAPS 10X10 GENPRAZOL', 0, 30, 10, 13.00, 19.40, 0, 'OME001', 8, 8, '2025-05-06 09:27:49'),
(103, 'FARM', 'OSTEGEN 3X10 CAPS', 0, 0, 1, 399.00, 595.00, 0, 'OST001', 8, 8, '2024-04-27 17:46:54'),
(104, 'FARM', 'OTO-4 (GOTAS PARA ORELHA) 10ML', 37, 0, 1, 52.00, 180.00, 0, 'OTO001', 8, 8, '2025-11-07 13:07:23'),
(105, 'FARM', 'AGUA OXIGENADA', 0, 0, 1, 80.00, 119.00, 0, '06/2026', 8, 8, '2025-01-20 15:39:51'),
(109, 'FARM', 'PARACETAMOL IV 10MG/ML) 100ML', 0, 0, 1, 140.00, 209.00, 0, 'PAR003', 8, 8, '2024-04-27 17:46:54'),
(110, 'FARM', 'PENICILINA BENZANTENICA 2,4 MEGA UI INJ', 7, 1, 1, 65.00, 82.00, 0, '10/2026', 8, 8, '2025-04-17 08:19:13'),
(111, 'FARM', 'PIROXICAM 20MG CAPS 10X10 PIROXIGEN', 0, 0, 1, 155.00, 231.00, 0, 'PIR001', 8, 8, '2024-04-27 17:46:54'),
(112, 'FARM', 'SABUSOL SABONATE 100GM', 12, 10, 1, 69.00, 90.00, 0, '28/02/2027', 8, 8, '2025-06-12 10:48:03'),
(114, 'FARM', 'SALGEN 4MG SALBUTAMOL COMP', 6, 0, 1, 60.00, 8.90, 0, '05/2026', 8, 8, '2024-08-16 23:55:07'),
(115, 'FARM', 'SALBUTAMOL SUSP SALGEN S', 10, 0, 1, 50.00, 75.00, 0, 'SAL002', 8, 8, '2025-03-27 15:28:24'),
(116, 'FARM', 'DISPOGEN (SERINGAS 2ML-100\'S)', 0, 0, 1, 474.14, 706.00, 0, 'SE001', 8, 8, '2024-04-27 17:46:54'),
(117, 'FARM', 'DISPOGEN SERINGAS 5ML', 0, 0, 1, 0.00, 8.00, 0, '03/2027', 8, 8, '2024-08-22 01:48:46'),
(118, 'FARM', 'DISPOGEN (SERINGAS 20ML 50\'S)', 0, 0, 1, 409.48, 610.00, 0, 'SE003', 8, 8, '2024-04-27 17:46:54'),
(119, 'FARM', 'DISPOGEN (SERINGA DESCARTAVEL 50ML)', 0, 0, 1, 550.00, 820.00, 0, 'SE004', 8, 8, '2024-04-27 17:46:54'),
(120, 'FARM', 'SILVADEX CREME', 1, 5, 1, 47.00, 73.00, 0, '30/06/2027', 8, 8, '2025-09-22 17:00:23'),
(121, 'FARM', 'SILDENAFIL TAB BP 100MG VIPROGRA', 0, 0, 1, 35.00, 52.00, 0, 'SIL002', 8, 8, '2024-04-27 17:46:54'),
(123, 'FARM', 'SOLA GEL SPF 50-100ML', 0, 1, 1, 99.00, 297.00, 0, 'SOL001', 8, 8, '2025-05-31 12:45:02'),
(124, 'FARM', 'FESATE (SAL FERROSO) CP 200MG', 327, 0, 1, 0.00, 15.00, 0, '7/25', 8, 8, '2025-03-28 14:08:17'),
(125, 'FARM', 'TETANUS TOXOID BP SINGLE DOES AMPOULE 0.5ML', 0, 0, 1, 240.00, 358.00, 0, 'TET002', 8, 8, '2024-04-27 17:46:54'),
(132, 'FARM', 'TESTE DE GRAVIDEZ PREGNANCY CASSETE', 0, 10, 1, 50.00, 109.00, 0, '11/26', 8, 8, '2025-07-03 15:00:45'),
(133, 'FARM', 'URINE BAGS 1\'S 2000ML', 0, 0, 1, 58.00, 86.00, 0, 'UR001', 8, 8, '2024-04-27 17:46:54'),
(134, 'FARM', 'GENVITA', 221, 10, 0, 1.00, 11.00, 0, '30/04/2027', 8, 8, '2025-04-15 12:36:45'),
(135, 'FARM', 'VIVASOL FEMININE WASH 100ML', 0, 0, 1, 0.00, 118.00, 0, '01/26', 8, 8, '2025-01-17 10:16:47'),
(136, 'FARM', 'ACARBOSE BLUEPHARMA 100MG CX 50 COMP', 0, 0, 1, 384.00, 568.00, 0, 'GF01', 8, 8, '2024-04-27 17:46:54'),
(137, 'FARM', 'ACARBOSE BLUEPHARMA 50MG CX 50 COMP', 0, 0, 1, 573.00, 849.00, 0, 'GF02', 8, 8, '2024-04-27 17:46:54'),
(138, 'FARM', 'AMLODIPINA BLUEPHARMA 10MG CX 60 COMP', 0, 10, 1, 50.00, 147.00, 0, '30/03/2027', 8, 8, '2025-11-07 09:33:12'),
(139, 'FARM', 'OMEPRAZOL BLUEPHARMA 20MG CX 56 CAP', 2, 0, 1, 0.00, 55.60, 0, '30/04/2025', 8, 8, '2025-01-31 06:42:44'),
(140, 'FARM', 'PANTOPRAZOL BLUEPHARMA 20MG CX 56 COMP GVD', 2, 3, 1, 94.00, 196.50, 0, '1/26', 8, 8, '2025-06-12 12:37:18'),
(141, '', 'PANTOPRAZOL BLUEPHARMA 40MG CX 56 COMP', 6, 0, 1, 103.00, 351.00, 0, '31/08/2026', 0, 0, '2025-06-12 12:36:29'),
(142, 'FARM', 'PARACETAMOL BLUEPHARMA 1000MG CX 20 COMP', 0, 0, 1, 0.00, 165.00, 0, 'GF108', 8, 8, '2025-11-07 09:33:41'),
(143, 'FARM', 'PARACETAMOL BLUEPHARMA 500MG CX 20 COMP', 0, 2, 1, 50.00, 142.00, 0, '30/08/2028', 8, 8, '2025-11-07 09:34:03'),
(144, 'FARM', 'AMLODIPINA BLUEPHARMA 5MG CX 60', 0, 0, 1, 1229.00, 73.00, 0, 'GF11', 8, 8, '2025-11-07 09:36:06'),
(145, 'FARM', 'QUETIAPINA BLUEPHARMA 100MG CX 60 COMP', 0, 0, 1, 146.00, 216.00, 0, 'GF110', 8, 8, '2024-04-27 17:46:54'),
(146, 'FARM', 'QUETIAPINA BLUEPHARMA 25MG CX 20 COMP', 0, 0, 1, 687.00, 1017.00, 0, 'GF111', 8, 8, '2024-04-27 17:46:54'),
(147, 'FARM', 'RAMIPRIL BLUEPHARMA 10MG CX 60 COMP', 0, 0, 1, 391.00, 579.00, 0, 'GF112', 8, 8, '2024-04-27 17:46:54'),
(148, 'FARM', 'RAMIPRIL BLUEPHARMA 2,5MG CX 60 COMP', 0, 0, 1, 405.00, 600.00, 0, 'GF113', 8, 8, '2024-04-27 17:46:54'),
(149, 'FARM', 'RAMIPRIL BLUEPHARMA 5MG CX 60 COMP', 0, 0, 1, 358.00, 529.00, 0, 'GF114', 8, 8, '2024-04-27 17:46:54'),
(151, 'FARM', 'RISPERIDONA BLUEPHARMA 2MG CX 60 COMP', 0, 0, 1, 1510.00, 2238.00, 0, 'GF121', 8, 8, '2024-04-27 17:46:54'),
(152, 'FARM', 'RISPERIDONA BLUEPHARMA 3MG CX 60 COMP', 0, 0, 1, 927.00, 1373.00, 0, 'GF122', 8, 8, '2024-04-27 17:46:54'),
(153, 'FARM', 'ROSUVASTATINA BLUEPHARMA 10MG CX 60 COMP', 0, 0, 1, 894.00, 1324.00, 0, 'GF123', 8, 8, '2024-04-27 17:46:54'),
(154, 'FARM', 'ROSUVASTATINA BLUEPHARMA 20MG CX 30 COMP', 0, 0, 1, 731.00, 1082.00, 0, 'GF124', 8, 8, '2024-04-27 17:46:54'),
(155, 'FARM', 'ROSUVASTATINA BLUEPHARMA 5MG CX 60 COMP', 0, 0, 1, 666.00, 986.00, 0, 'GF125', 8, 8, '2024-04-27 17:46:54'),
(156, 'FARM', 'SERTRALINA BLUEPHARMA 100MG CX 60 COMP', 0, 0, 1, 1064.00, 1577.00, 0, 'GF129', 8, 8, '2024-04-27 17:46:54'),
(157, 'FARM', 'SILDENAFIL BLUEPHARMA 100MG CX 4 COMP REVESTIDO', 0, 0, 1, 892.00, 1322.00, 0, 'GF131', 8, 8, '2024-04-27 17:46:54'),
(158, 'FARM', 'SILDENAFIL BLUEPHARMA 50MG CX 4 COMP REVESTIDO', 7, 0, 1, 0.00, 330.50, 0, '', 8, 8, '2024-06-02 13:26:15'),
(159, 'FARM', 'SINVASTATINA BLUEPHARMA 10MG CX 56 COMP', 0, 0, 1, 0.00, 218.00, 0, '11/2025', 8, 8, '2024-07-05 11:43:04'),
(160, 'FARM', 'SINVASTATINA BLUEPHARMA 20MG CX 56 COMP', 5, 0, 1, 775.00, 121.50, 0, 'GF134', 8, 8, '2024-11-22 15:34:19'),
(161, 'FARM', 'SINVASTATINA BLUEPHARMA 40MG CX 56 COMP', 0, 10, 1, 95.00, 165.00, 0, 'GF135', 8, 8, '2025-10-11 14:34:01'),
(163, 'FARM', 'QUETIAPINA BLUEPHARMA 200MG CX60 COMP', 0, 0, 1, 2720.00, 4031.00, 0, 'GF137', 8, 8, '2024-04-27 17:46:54'),
(164, 'FARM', 'QUETIAPINA BLUEPHARMA 300MG CX 60 COMP', 0, 0, 1, 2720.00, 4031.00, 0, 'GF138', 8, 8, '2024-04-27 17:46:54'),
(166, 'FARM', 'TADALAFIL BLUEPHARMA 10MG CX 4 COMP', 0, 0, 1, 208.00, 307.00, 0, 'GF139', 8, 8, '2024-04-27 17:46:54'),
(167, 'FARM', 'AQUAMARIS FORTE SPRAY  30ML', 0, 0, 1, 0.00, 307.00, 0, '02/26', 8, 8, '2024-08-31 22:52:20'),
(168, 'FARM', 'TADALAFIL BLUEPHARMA 20MG CX 4 COMP', 0, 0, 1, 386.00, 571.00, 0, 'GF140', 8, 8, '2024-04-27 17:46:54'),
(169, 'FARM', 'TANSULOSINA BLUEPHARMA 400mcg CX 30 CAPS', 0, 0, 1, 0.00, 571.00, 0, '30/11/2026', 8, 8, '2024-08-27 23:53:14'),
(170, 'FARM', 'TERBINAFINA BLUEPHARMA 250MG  CX 28 COMP', 0, 0, 1, 106.00, 157.00, 0, 'GF142', 8, 8, '2024-04-27 17:46:54'),
(171, 'FARM', 'TRAMADOL+ PARACETAMOL BLUEPHA 37.5+325MG CX 20 COMP', 0, 5, 1, 52.00, 76.00, 0, 'GF145', 8, 8, '2025-05-06 11:02:29'),
(172, 'FARM', 'TRAMADOL+PARACETAMOL BLUEPHARM 75+650MG CX 20 COMP', 0, 0, 1, 0.00, 212.00, 0, 'GF146', 8, 8, '2025-01-18 22:48:59'),
(173, 'FARM', 'AQUAMARIS CLASSICO SPRAY NASAL 30ML', 4, 0, 1, 0.00, 301.00, 0, '04/2026', 8, 8, '2025-01-27 17:37:00'),
(174, 'FARM', 'ACETILCISTEINA BLUEPHARMA 600MG CX 20 COMP. EFERV.', 0, 0, 1, 370.00, 548.00, 0, 'GF162', 8, 8, '2024-04-27 17:46:54'),
(175, 'FARM', 'IRBESARTAN BLUEPHARMA 75MG CX 28 COMP', 0, 0, 1, 515.00, 763.00, 0, 'GF165', 8, 8, '2024-04-27 17:46:54'),
(176, 'FARM', 'LANSOPRAZOL BLUEPHARMA 15MG CX 56 COMP', 0, 0, 1, 278.00, 411.00, 0, 'GF166', 8, 8, '2024-04-27 17:46:54'),
(177, 'FARM', 'ALPRAZOLAM BLUEPHARMA 0.25MG CX 60 COMP. Govind', 5, 6, 1, 54.00, 80.00, 0, '7/26', 8, 8, '2025-07-08 09:36:10'),
(178, 'FARM', 'ALPRAZOLAM BLUEPHARMA 0.25MG CX 60 COMP.', 0, 0, 1, 292.00, 433.00, 0, 'GF167', 8, 8, '2024-04-27 17:46:54'),
(179, 'FARM', 'ALPRAZOLAM BLUEPHARMA 0.5MG CX 60 COMP.', 0, 0, 1, 319.00, 482.00, 0, 'GF168', 8, 8, '2025-11-07 09:36:48'),
(180, 'FARM', 'ALPRAZOLAM BLUEPHARMA 1MG CX 60 COMP.', 0, 0, 1, 380.00, 480.00, 0, 'GF169', 8, 8, '2025-11-07 09:37:10'),
(181, 'FARM', 'ATENOLOL BLUEPHARMA 100MG CX 60 COMP', 0, 0, 1, 199.00, 294.00, 0, 'GF17', 8, 8, '2024-04-27 17:46:54'),
(182, 'FARM', 'AZITROMICINA BLUEPHARMA 500MG CX 3', 0, 2, 1, 62.00, 101.00, 0, '03/26', 8, 8, '2025-07-03 14:03:56'),
(184, 'FARM', 'ATORVASTATINA BLUEPHARMA 10MG CX 56 COMP', 0, 0, 1, 470.00, 696.00, 0, 'GF214', 8, 8, '2024-04-27 17:46:54'),
(185, 'FARM', 'ATORVASTATINA BLUEPHARMA 20MG CX 28 COMP', 0, 0, 1, 725.00, 1074.00, 0, 'GF215', 8, 8, '2024-04-27 17:46:54'),
(186, 'FARM', 'ATORVASTATINA BLUEPHARMA 40MG CX 28COMP', 0, 0, 1, 484.00, 716.00, 0, 'GF216', 8, 8, '2024-04-27 17:46:54'),
(187, 'FARM', 'PAROXETINA BLUEPHARMA 20MG CX 60 COMP', 0, 0, 1, 744.00, 1102.00, 0, 'GF217', 8, 8, '2024-04-27 17:46:54'),
(188, 'FARM', 'CANDESARTAN + HCTZ BLUEPHARMA 16+12,5MG CX 56 COMP', 0, 0, 1, 744.00, 1102.00, 0, 'GF22', 8, 8, '2024-04-27 17:46:54'),
(190, 'FARM', 'LEVETIRACETAM BLUEPHARMA 250MG CX 60 COMP', 0, 0, 1, 550.00, 814.00, 0, 'GF221', 8, 8, '2024-04-27 17:46:54'),
(191, 'FARM', 'ACIDO ALENDRONICO 70MG CX 4 COMP', 0, 0, 1, 213.00, 315.00, 0, 'GF247', 8, 8, '2024-04-27 17:46:54'),
(192, 'FARM', 'BROMEXINA BLUEPHARMA 0,8MG/ML 200ML Xrpe', 21, 0, 1, 0.00, 312.00, 0, '8/26', 8, 8, '2025-11-07 07:31:14'),
(193, 'FARM', 'BROMEXINA BLUEPHARMA 1,6MG/ML 200ML Xpe', 21, 0, 1, 0.00, 332.00, 0, '8/26', 8, 8, '2025-11-07 07:31:41'),
(194, 'FARM', 'LEVOFLOXACINA BLUEPHARMA 500MG CX 10COMP', 0, 0, 1, 300.00, 444.00, 0, 'GF254', 8, 8, '2024-04-27 17:46:54'),
(195, 'FARM', 'BEN-U-RON 40mg/ml 85ml  xarope', 2, 0, 1, 0.00, 484.00, 0, '01/2026', 8, 8, '2025-11-07 07:15:41'),
(196, 'FARM', 'LISINOPRIL+HCTZ BLUEPHARMA 20MG+12,5MG CX 60 COMP', 0, 0, 1, 451.00, 667.00, 0, 'GF258', 8, 8, '2024-04-27 17:46:54'),
(197, 'FARM', 'BROMETO DE IPATROPIO BLUEPHARMA 20UG/DOSE INAL', 0, 0, 1, 720.00, 1066.00, 0, 'GF32', 8, 8, '2024-04-27 17:46:54'),
(198, 'FARM', 'CANDESARTAN BLUEPHARMA 16MG CX 56 COMP', 0, 0, 1, 140.00, 207.00, 0, 'GF34', 8, 8, '2024-04-27 17:46:54'),
(199, 'FARM', 'CETIRIZINA BLUEPHARMA 10MG CX 20 COMP', 29, 20, 1, 50.00, 216.00, 0, '10/2026', 8, 8, '2025-11-07 07:32:56'),
(200, 'FARM', 'CIPROFLOXACINA BLUEPHARMA 500MG CX 1 COMP', 208, 0, 1, 0.00, 570.00, 0, '02/2026', 8, 8, '2025-11-07 07:34:44'),
(201, 'FARM', 'CLARITROMICINA BASI 500MG CX 16 COMP', 10, 0, 1, 0.00, 515.00, 0, '30/11/2025', 8, 8, '2024-07-11 09:34:40'),
(202, 'FARM', 'CLOTRIMAZOL CREME BLUEPHARMA 10MG/G 20G', 0, 0, 1, 108.00, 180.00, 0, 'GF45', 8, 8, '2025-11-07 07:36:56'),
(203, 'FARM', 'CLOTRIMAZOL CREME VAGINAL BLUEPHARMA 10MG/G 50G', 0, 0, 1, 308.00, 541.00, 0, '9/2025', 8, 8, '2025-11-07 07:37:23'),
(205, 'FARM', 'CO-APROVEL 300+12,5MG CX 28 COMP IRBESARTAN+HCTZ', 0, 0, 1, 951.00, 1409.00, 0, 'GF48', 8, 8, '2024-04-27 17:46:54'),
(206, 'FARM', 'CANDESARTAN BLUEPHARMA 32MG CX 56 COMP', 0, 0, 1, 951.00, 1409.00, 0, 'GF49', 8, 8, '2024-04-27 17:46:54'),
(208, 'FARM', 'CONDESARTAN BLUEPHARMA 8MGCX 28 COMP', 0, 0, 1, 188.00, 279.00, 0, 'GF50', 8, 8, '2024-04-27 17:46:54'),
(209, 'FARM', 'DESLORATADINA BLUEPHARMA 5MG CX 20 COMP', 0, 0, 1, 242.00, 359.00, 0, 'GF51', 8, 8, '2024-04-27 17:46:54'),
(212, 'FARM', 'ENALAPRIL BLUEPHARMA 20MG CX 60 COMP', 0, 0, 1, 705.00, 1044.00, 0, 'GF57', 8, 8, '2024-04-27 17:46:54'),
(213, 'FARM', 'ENALAPRIL BLUEPHARMA 5MG CX 60 COMP', 0, 0, 1, 738.00, 1093.00, 0, 'GF58', 8, 8, '2024-04-27 17:46:54'),
(214, 'FARM', 'ENALAPRIL + HCTZ BLUEPHARMA 20MG+12,5MG CX 60 COMP', 0, 0, 1, 429.00, 636.00, 0, 'GF59', 8, 8, '2024-04-27 17:46:54'),
(215, 'FARM', 'ESCITALOPRAM BLUEPHARMA 10MG CX 56 COMP', 0, 0, 1, 757.00, 1121.00, 0, 'GF60', 8, 8, '2024-04-27 17:46:54'),
(216, 'FARM', 'ESCITALOPRAM BLUEPHARMA 20MG CX 56 COMP', 0, 0, 1, 896.00, 1328.00, 0, 'GF61', 8, 8, '2024-04-27 17:46:54'),
(217, 'FARM', 'ESOMEPRAZOL BLUEPHARMA 20MG CX 56 COMP', 0, 0, 1, 1126.00, 1668.00, 0, 'GF62', 8, 8, '2024-04-27 17:46:54'),
(218, 'FARM', 'ESOMEPRAZOL BLUEPHARMA 40MG CX 56 COMP', 0, 0, 1, 585.00, 866.00, 0, 'GF63', 8, 8, '2024-04-27 17:46:54'),
(219, 'FARM', 'ETORICOXIB BLUEPHARMA 120MG CX 7 COMP', 0, 0, 1, 1043.00, 1545.00, 0, 'GF65', 8, 8, '2024-04-27 17:46:54'),
(220, 'FARM', 'FINASTERIDA BLUEPHARMA 5MG CX 56 COMP', 0, 0, 1, 337.00, 498.00, 0, 'GF67', 8, 8, '2024-04-27 17:46:54'),
(221, 'FARM', 'GLICAZIDA BLUEPHARMA 80MG CX 60 COMP', 0, 0, 1, 1205.00, 1785.00, 0, 'GF72', 8, 8, '2024-04-27 17:46:54'),
(222, 'FARM', 'LEVETIRACETAM BLUEPHARMA 500MG CX 60 COMP', 0, 0, 1, 438.00, 649.00, 0, 'GF78', 8, 8, '2024-04-27 17:46:54'),
(223, 'FARM', 'IRBESARTAN BLUEPHARMA 150MG CX 28 COMP', 0, 0, 1, 606.00, 715.00, 0, 'GF80', 8, 8, '2025-11-07 07:37:51'),
(224, 'FARM', 'IRBESARTAN BLUEPHARMA 300MGCX 28 COMP', 0, 0, 1, 518.00, 990.00, 0, 'GF81', 8, 8, '2025-11-07 07:38:18'),
(225, 'FARM', 'IRBESARTAN+HCTZ BLUEPHARMA 300MG+12,5MG CX 28 COMP', 0, 0, 1, 404.00, 806.00, 0, 'GF82', 8, 8, '2025-11-07 07:38:41'),
(226, 'FARM', 'IRBESARTAN BLUEPHARMA 150MG + 12,5MG', 0, 0, 1, 493.00, 644.00, 0, 'GF83', 8, 8, '2025-11-07 07:39:56'),
(227, 'FARM', 'LANZOPRAZOL BLUEPHARMA 30MG CX 56 CAP', 0, 0, 1, 1545.00, 2289.00, 0, 'GF84', 8, 8, '2024-04-27 17:46:54'),
(228, 'FARM', 'LEVETIRACETAM BLUEPHARMA 1000MG CX 60 COMP', 0, 0, 1, 441.00, 653.00, 0, 'GF85', 8, 8, '2024-04-27 17:46:54'),
(229, 'FARM', 'LISINOPRIL BLUEPHARMA 20MG CX 56 COMP', 0, 0, 1, 100.00, 169.50, 0, '30/11/2026', 8, 8, '2025-06-12 12:25:42'),
(230, 'FARM', 'LISINOPRIL BLUEPHARMA 5MG CX 56 COMP', 0, 0, 1, 50.00, 138.00, 0, '30/11/2026', 8, 8, '2025-11-07 07:42:57'),
(231, 'FARM', 'LOSARTAN BLUEPHARMA 100MG CX 60 COMP', 0, 0, 1, 437.00, 647.00, 0, 'GF88', 8, 8, '2024-04-27 17:46:54'),
(232, 'FARM', 'LOSARTAN BLUEPHARMA 50MG CX 60 COMP', 1, 0, 1, 435.00, 668.00, 0, 'GF89', 8, 8, '2025-11-07 07:13:21'),
(233, 'FARM', 'LOSARTAN + HCTZ BLUEPHARMA 100MG+25MG CX 30 COMP', 0, 0, 1, 501.00, 741.00, 0, 'GF90', 8, 8, '2024-04-27 17:46:54'),
(234, 'FARM', 'LOSARTAN + HCTZ BLUEPHARMA 50+12,5MG CX 60 COMP', 0, 0, 1, 632.00, 937.00, 0, 'GF91', 8, 8, '2024-04-27 17:46:54'),
(235, 'FARM', 'MONTELUCASTE BLUEPHARMA 10MG CX 28 COMP', 0, 0, 1, 734.00, 1087.00, 0, 'GF93', 8, 8, '2024-04-27 17:46:54'),
(236, 'FARM', 'MONTELUCASTE BLUEPHARMA 4MG CX 28 COMP', 0, 0, 1, 619.00, 916.00, 0, 'GF94', 8, 8, '2024-04-27 17:46:54'),
(237, 'FARM', 'MONTELUCASTE BLUEPHARMA 5MG CX 28 COMP', 0, 0, 1, 794.00, 1177.00, 0, 'GF95', 8, 8, '2024-04-27 17:46:54'),
(238, 'FARM', 'MOXIFLOXACINA BLUEPHARMA 400MG CX 7 COMP', 0, 0, 1, 117.00, 173.00, 0, 'GF96', 8, 8, '2024-04-27 17:46:54'),
(239, 'FARM', 'NIMESULIDA BLUEPHARMA100MG CX 20 COMP', 1, 0, 1, 1084.00, 86.50, 0, 'GF99', 8, 8, '2024-08-17 18:47:05'),
(240, 'FARM', 'PROLIF 250MG CX 20 CAP', 0, 0, 1, 880.00, 1304.00, 0, 'GF136', 8, 8, '2024-04-27 17:46:54'),
(241, 'FARM', 'THROMBOCID GEL 15MG/G 100GR', 3, 0, 1, 0.00, 1311.00, 0, '10/2027', 8, 8, '2024-12-05 18:15:13'),
(242, 'FARM', 'BEN-U-RON 75MG CX 10 SUP.', 0, 0, 1, 263.00, 389.00, 0, 'GF163', 8, 8, '2024-04-27 17:46:54'),
(244, 'FARM', 'IB-U-RON 20MG/ML SUSP. ORAL 200ML', 0, 0, 1, 222.00, 329.00, 0, 'GF164', 8, 8, '2024-04-27 17:46:54'),
(245, 'FARM', 'BEN-U-RON 1000MG CX 18 COMP', 0, 0, 1, 109.00, 161.00, 0, 'GF20', 8, 8, '2024-04-27 17:46:54'),
(246, 'FARM', 'BEN-U-RON 125 CX 10 SUP', 0, 0, 1, 289.00, 428.00, 0, 'GF21', 8, 8, '2024-04-27 17:46:54'),
(247, 'FARM', 'BEN-U-RON 500MG CX 20 CAPSULAS', 0, 0, 1, 0.00, 419.00, 0, 'GF23', 8, 8, '2024-07-28 08:40:08'),
(249, 'FARM', 'BEN-U-RON CAF 500+65MG CX 20 COMP', 8, 0, 1, 0.00, 179.00, 0, '31/07/2027', 8, 8, '2025-02-03 08:19:12'),
(250, 'FARM', 'BEN-U-RON 500MG CX 20 COMP', 0, 0, 1, 247.00, 328.00, 0, 'GF25', 8, 8, '2025-11-07 07:15:10'),
(252, 'FARM', 'IB-U-RON 600 CX 60 COMP', 0, 0, 1, 526.00, 779.00, 0, 'GF256', 8, 8, '2024-04-27 17:46:54'),
(253, 'FARM', 'TRAM-U-RON LP 100MG CAPS CX 20', 0, 0, 1, 217.00, 321.00, 0, 'GF257', 8, 8, '2024-04-27 17:46:54'),
(254, 'FARM', 'BEN-U-RON 250MG CX 10 SUP', 0, 0, 1, 260.00, 384.00, 0, 'GF26', 8, 8, '2024-04-27 17:46:54'),
(255, 'FARM', 'BEN-U-RON 500MG CX 10 SUP', 0, 0, 1, 292.00, 431.00, 0, 'GF27', 8, 8, '2024-04-27 17:46:54'),
(256, 'FARM', 'BEN-U-RON XAROPE 85ML PARACETAMOL', 0, 0, 1, 520.00, 770.00, 0, 'GF28', 8, 8, '2024-04-27 17:46:54'),
(257, 'FARM', 'BEN-U-RON PARACETAMOL 40MG/ML XAROPE 150ML', 0, 0, 1, 520.00, 770.00, 0, 'GF29', 8, 8, '2024-04-27 17:46:54'),
(259, 'FARM', 'DOL-U-RON FORTE 1000MG+60MG CX 18 COMP', 5, 0, 1, 0.00, 324.50, 0, 'GF54', 8, 8, '2025-01-29 01:29:14'),
(260, 'FARM', 'DOL-U-RON FORTE 500MG+30MG CX 20 CAPSULAS', 6, 0, 1, 0.00, 183.00, 0, '', 8, 8, '2025-01-20 07:26:46'),
(261, 'FARM', 'DUODIX 200MG+500MG CX 20 COMP', 0, 0, 1, 595.00, 882.00, 0, 'GF56', 8, 8, '2024-04-27 17:46:54'),
(262, 'FARM', 'IB-U-RON 400MG CX 20 COMP', 0, 0, 1, 372.00, 551.00, 0, 'GF74', 8, 8, '2024-04-27 17:46:54'),
(263, 'FARM', 'IB-U-RON 150MG SUP CX 10', 0, 0, 1, 351.00, 520.00, 0, 'GF76', 8, 8, '2024-04-27 17:46:54'),
(264, 'FARM', 'IB-U-RON 75MG CX 10 SUP', 0, 0, 1, 1085.00, 1607.00, 0, 'GF77', 8, 8, '2024-04-27 17:46:54'),
(265, 'FARM', 'IB-U-RON GEL MENTOL 50MG/G BISNAGA 100G', 0, 0, 1, 284.00, 420.00, 0, 'GF79', 8, 8, '2024-04-27 17:46:54'),
(266, 'FARM', 'ACARILBIAL SOL.CUTANEA 200ML', 0, 5, 1, 284.00, 412.00, 0, '30/11/2028', 8, 8, '2025-11-07 07:17:05'),
(268, 'FARM', 'OMPRANYT 20MG CX 28 CAP', 6, 0, 1, 0.00, 121.00, 0, '30/04/2026', 8, 8, '2024-12-11 18:21:53'),
(269, 'FARM', 'RANTUDIL 60 RETARD CX 60 CAP', 0, 0, 1, 1883.00, 312.00, 0, 'GF115', 8, 8, '2025-03-28 10:57:49'),
(270, 'FARM', 'RANTUDIL 90 RETARD CX 60 CAP', 6, 0, 1, 0.00, 458.00, 0, '31/05/2026', 8, 8, '2024-12-09 00:09:56'),
(272, 'FARM', 'REUMON GEL 50MG/G', 7, 0, 1, 413.00, 611.00, 0, '5/28', 8, 8, '2024-08-24 15:00:15'),
(274, 'FARM', 'RINIALER CX 20 COMP', 2, 0, 1, 0.00, 750.00, 0, '07/2025', 8, 8, '2025-11-07 07:18:40'),
(275, 'FARM', 'SEDOXIL 1MG CX 60 COMP', 10, 0, 1, 1474.00, 177.50, 0, '30/06/2027', 8, 8, '2025-02-02 11:56:49'),
(276, 'FARM', 'TRICEF 400MG CX 8 COMP', 0, 0, 1, 715.00, 1059.00, 0, 'GF147', 8, 8, '2024-04-27 17:46:54'),
(277, 'FARM', 'TRICEF SUSP ORAL 60ML', 0, 0, 1, 193.00, 285.00, 0, 'GF148', 8, 8, '2024-04-27 17:46:54'),
(280, 'FARM', 'URIPRIM 300MG CX 60 COMP', 6, 0, 1, 0.00, 96.00, 0, '11/2026', 8, 8, '2025-01-10 15:18:58'),
(281, 'FARM', 'UROFLOX 400MG CX 16 COMP', 0, 0, 1, 0.00, 369.00, 0, '01/2025', 8, 8, '2024-08-07 14:22:38'),
(284, 'FARM', 'VICOMBIL FERRUM, FRASCO DE 200ML', 0, 0, 1, 372.00, 551.00, 0, 'GF157', 8, 8, '2024-04-27 17:46:54'),
(285, 'FARM', 'VICOMBIL JUNIOR SUSP. FRASCO 200ML', 0, 0, 1, 0.00, 551.00, 0, '03/2025', 8, 8, '2025-01-18 01:28:51'),
(286, 'FARM', 'ADALAT CR 30 CX 28', 3, 0, 1, 0.00, 1162.00, 0, '31/10/25', 8, 8, '2024-08-07 14:03:53'),
(287, 'FARM', 'ASPIRINA BAYER 100MG CX 30 COMP', 0, 0, 1, 1246.00, 1845.00, 0, 'GF16', 8, 8, '2024-04-27 17:46:54'),
(288, 'FARM', 'AVELOX 400MG CX 5 COMP', 0, 0, 1, 2060.00, 3052.00, 0, 'GF250', 8, 8, '2024-04-27 17:46:54'),
(289, 'FARM', 'ANGELIC CX 28 COMP', 0, 0, 1, 531.00, 786.00, 0, 'GF251', 8, 8, '2024-04-27 17:46:54'),
(291, 'FARM', 'MICROGINON CX 21', 0, 0, 1, 492.00, 728.00, 0, 'GF259', 8, 8, '2024-04-27 17:46:54'),
(292, 'FARM', 'BEPANTHENE POMADA TUBO 30G', 0, 0, 1, 492.00, 722.00, 0, '02/2026', 8, 8, '2025-11-07 09:53:48'),
(293, 'FARM', 'BEPANTHENE CREME TUBO 30G', 1, 0, 1, 624.00, 1089.00, 0, '02/2026', 8, 8, '2025-11-07 09:53:28'),
(294, 'FARM', 'GINO-CANESTEN CREME VAGINAL 50G 10MG/G', 1, 0, 1, 0.00, 924.00, 0, '09/2025', 8, 8, '2024-11-30 07:25:53'),
(295, 'FARM', 'GINO-CANESTEN OVULO 500MG CX 1 COMP', 0, 0, 1, 127.61, 189.00, 0, 'GF71', 8, 8, '2024-04-27 17:46:54'),
(296, 'FARM', 'URTICUR (PROMETIZINA) 25MG 20COMP govind', 26, 20, 1, 60.00, 94.50, 0, '31/07/2026', 8, 8, '2025-06-04 08:29:58'),
(297, 'FARM', 'ESPECULO VAGINAL ROSCA VAGISPEC TAM.S 1UN', 0, 0, 1, 59.50, 54.00, 0, '12021000', 8, 8, '2024-04-27 17:46:54'),
(298, 'FARM', 'ESPECULO VAGINAL ROSCA VAGISPEC TAM,M 100UN', 0, 0, 1, 59.50, 54.00, 0, '12021001', 8, 8, '2024-04-27 17:46:54'),
(299, 'FARM', 'ESPECULO VAGINAL ROSCA VAGISPEC TAM.L 100UN', 0, 0, 1, 312.14, 463.00, 0, '12021002', 8, 8, '2024-04-27 17:46:54'),
(300, 'FARM', 'TENIVERME (FLUBENDAZOL) 20MG/ML SOL.ORAL 30ML', 0, 0, 1, 1459.00, 2163.00, 0, '2525491', 8, 8, '2024-04-27 17:46:54'),
(301, 'FARM', 'FENTANILO BASI 0,05MG/ML SOL IN 5ML 10AMP', 0, 0, 1, 387.00, 574.00, 0, '4419685', 8, 8, '2024-04-27 17:46:54'),
(302, 'FARM', 'FLUCLOXACILINA BASI 500MG 24CAPS', 0, 0, 1, 319.15, 473.00, 0, '4525192', 8, 8, '2024-04-27 17:46:54'),
(306, 'FARM', 'FUNGUR (TERBINAFINA) 250MG 28COMP', 0, 0, 1, 3372.70, 4999.00, 0, '5023130', 8, 8, '2024-04-27 17:46:54'),
(307, 'FARM', 'MORFINA BASI 10MG/ML SOL.INJ.CX.10 AMP', 0, 0, 1, 170.20, 252.00, 0, '5030622', 8, 8, '2024-04-27 17:46:54'),
(308, 'FARM', 'FLUCONAZOL BASI 150MG 2CAPS', 0, 0, 1, 0.00, 252.00, 0, '10/2025', 8, 8, '2024-12-13 03:43:01'),
(309, 'FARM', 'FLUCONAZOL BASI 200MG 7CAPS', 0, 0, 1, 152.58, 129.42, 0, '4/25', 8, 8, '2025-01-21 10:19:10'),
(311, 'FARM', 'BASIFLUX (BROMEXINA) 0,8MG/ML XAROPE 200ML 1FR', 20, 0, 1, 202.62, 275.00, 0, '5145743', 8, 8, '2025-03-28 01:08:01'),
(313, 'FARM', 'TERBUL (TIOCONAZOL) 10MG/G CREME 30G 1BISNAGA', 0, 0, 1, 0.00, 360.00, 0, '5247226', 8, 8, '2024-08-22 10:56:56'),
(316, 'FARM', 'CLORETO DE POTASSIO BASI 74,5MG/ML 10ML 10AMP', 0, 0, 1, 0.00, 1009.00, 0, '5354170', 8, 8, '2025-04-09 13:49:58'),
(317, 'FARM', 'INALGEX (ETOFENAMATO) 100MG/G GEL 100G 1 BISNAGA', 0, 0, 1, 119.43, 177.00, 0, '5355102', 8, 8, '2024-04-27 17:46:54'),
(318, 'FARM', 'CLOTRIMAZOL BASI SOL CUT 20ML 1FR', 26, 0, 1, 98.27, 146.00, 0, '5359708', 8, 8, '2025-03-28 16:23:56'),
(319, 'FARM', 'CLOTRIMAZOL BASI 10MG/G CREME 20G 1BISNAG', 0, 0, 1, 0.00, 146.00, 0, '28/02/25026', 8, 8, '2025-01-28 22:59:33'),
(320, 'FARM', 'BASIFLUX (BROMEXIN) 1,6MG/ML XAROPE 200ML 1FR', 11, 0, 1, 0.00, 256.00, 0, '5411566', 8, 8, '2025-03-28 06:29:06'),
(321, 'FARM', 'MYMUS (SIMETICONE) 66,6MG/ML 30ML', 0, 2, 1, 293.00, 434.00, 0, '5412374', 8, 8, '2025-06-19 15:00:13'),
(322, 'FARM', 'METFORMINA BASI 500MG 60COMP', 4, 2, 1, 10.00, 29.50, 0, '05/31/2027', 8, 8, '2025-06-12 12:06:09'),
(323, 'FARM', 'METFORMINA BASI 1000MG 60COMP', 0, 0, 1, 0.00, 42.00, 0, '10/24', 8, 8, '2024-06-29 09:31:24'),
(324, 'FARM', 'GLICERINA ADULTO 2000MG 12 SUSP', 0, 0, 1, 1481.00, 999.00, 0, '5558093', 8, 8, '2024-04-27 17:46:54'),
(325, 'FARM', 'PARACETAMOL BASI 500MG 1000COMP', 0, 0, 1, 74.25, 110.00, 0, '5564539', 8, 8, '2024-04-27 17:46:54'),
(326, 'FARM', 'METRONIZADOL BASI SOL.PERF.5MG/ML 100ML', 0, 0, 1, 224.95, 333.00, 0, '5587530', 8, 8, '2024-04-27 17:46:54'),
(327, 'FARM', 'TRIPLEXIL (BETAMET+CLOTR+GENTA) CREME 30G BISNAGA  novo', 2, 10, 1, 191.27, 283.00, 0, '31/10/2027', 8, 8, '2025-05-27 14:26:19'),
(328, 'FARM', 'TRIPLEXIL (BETAMET+CLOTR+GENTA) CREME 30G  Govind', 0, 10, 1, 200.00, 338.00, 0, '31/03/2028', 8, 8, '2025-07-08 10:05:48'),
(329, 'FARM', 'DABENZOL (METRONIZADOL) 125MG/5ML S ORAL 100ML', 0, 0, 1, 374.54, 515.00, 0, '', 8, 8, '2024-08-08 07:47:33'),
(330, 'FARM', 'DILACLAN (AMOXICILINA) 125MG/5ML PO ORAL 100ML 1FR', 0, 0, 1, 270.89, 303.00, 0, '', 8, 8, '2024-10-30 08:54:12'),
(333, 'FARM', 'VITASMA (SALBUTAMOL) 2MG/5ML XAROPE 100ML 1UN', 0, 0, 1, 148.03, 221.00, 0, '04/2026', 8, 8, '2024-10-18 21:40:21'),
(334, 'FARM', 'URTICUR (PROMETAZINA) 0.1% SOL ORAL 100ML 1FR', 26, 0, 1, 0.00, 219.00, 0, '31/07/2026', 8, 8, '2024-08-23 14:37:47'),
(335, 'FARM', 'DILACLAN (AMOXICILINA) 500MG 16CAPS', 3, 0, 1, 190.61, 283.00, 0, '', 8, 8, '2024-08-22 22:10:06'),
(338, 'FARM', 'METOCLOPRAMIDA BASI 5MG/ML SOL INJ 2ML 10AMPO', 0, 0, 1, 196.19, 291.00, 0, '', 8, 8, '2024-04-27 17:46:54'),
(339, 'FARM', 'CLARUS (DESLORATADINA) 0,5MG/ML SOL ORAL 150ML 1FR', 8, 0, 1, 0.00, 291.00, 0, '4/24', 8, 8, '2025-03-28 06:29:07'),
(340, 'FARM', 'STRYMIX (PREGABALINA) 75MG 56CAPS', 0, 0, 1, 2147.28, 3183.00, 0, '5636360', 8, 8, '2024-04-27 17:46:54'),
(341, 'FARM', 'STRYMIX (PREGABALINA) 300MG 56CAPS', 0, 0, 1, 342.11, 507.00, 0, '5636519', 8, 8, '2024-04-27 17:46:54'),
(342, 'FARM', 'FLUMINOC(ARTEMETER+LUMEFANTRINA) 20MG+120MG 24COMP', 0, 41, 2, 41.00, 126.75, 0, '5649058', 8, 8, '2025-06-03 14:19:15'),
(343, 'FARM', 'FUNGUR (TERBINAFINA) 10MG/G CREME 15G 1 UN', 1, 0, 1, 207.47, 288.00, 0, '5656889', 8, 8, '2025-01-28 03:44:11'),
(344, 'FARM', 'DIMIDON (IBUPROFENO) 20MG/ML SUSP ORAL 200ML 1FR', 0, 2, 1, 207.35, 308.00, 0, '5671367', 8, 8, '2025-10-11 14:07:53'),
(345, 'FARM', 'ENTIX (FUROSEMIDA) 40MG 60COMP', 0, 0, 1, 148.95, 221.00, 0, '5695036', 8, 8, '2024-04-27 17:46:54'),
(351, 'FARM', 'SORO FISIOLOGICO BASI 500ML 1FR', 16, 0, 1, 61.92, 86.00, 0, '5775044', 8, 8, '2025-03-28 06:29:07'),
(352, 'FARM', 'GLUCOSE 5% (Destrose   500ML)', 0, 3, 1, 99.00, 147.00, 0, '5775358', 8, 8, '2025-11-06 09:30:08'),
(353, 'FARM', 'ANTADAR (CEFOTAXIMA) 100MG PO SOL INJ 1UN', 0, 0, 1, 170.44, 253.00, 0, '5784244', 8, 8, '2024-04-27 17:46:54'),
(355, 'FARM', 'TOZEVITE 100ML 1FR', 0, 0, 1, 292.44, 441.00, 0, '6343830', 8, 8, '2024-04-27 17:46:54'),
(356, 'FARM', 'HIDROLACT CREME FACIAL 40G 1UN', 0, 0, 1, 0.00, 441.00, 0, '6611319', 8, 8, '2025-01-10 16:38:52'),
(357, 'FARM', 'HIDROLACT AVEIA E A.MILHO GEL BANHO 500ML 1UN', 0, 0, 1, 1133.74, 1710.00, 0, '6812321', 8, 8, '2024-04-27 17:46:54'),
(358, 'FARM', 'HIDROLACT EMOLIENTE CORPORAL 500ML 1UN', 0, 0, 1, 64.94, 96.00, 0, '6812339', 8, 8, '2024-04-27 17:46:54'),
(360, 'FARM', 'HIDROLACT CREME HIDRATANTE 1KG 1UN', 0, 0, 1, 193.14, 291.00, 0, '6983122', 8, 8, '2024-04-27 17:46:54'),
(361, 'FARM', 'CORVITE LIMAO 1000MG 20COMP EFERV 1 UN', 0, 0, 1, 0.00, 291.00, 0, '10/25', 8, 8, '2025-01-26 08:20:01'),
(362, 'FARM', 'CORVITE MULTIVITAMINAS KIDS 20COMP EFERV 1 UN', 0, 0, 1, 262.54, 399.00, 0, '7249995', 8, 8, '2024-04-27 17:46:54'),
(363, 'FARM', 'CORVITE MULTIVITAMINA+MINERAL 20COMP EFERV 1UN', 3, 0, 1, 262.52, 399.00, 0, '10/2025', 8, 8, '2024-08-22 22:10:07'),
(364, 'FARM', 'CORVITE ZINCO+VITAMINA C+ E COMP EFERV 1UN', 3, 0, 1, 0.00, 399.00, 0, '02/26', 8, 8, '2024-08-22 22:10:07'),
(366, 'FARM', 'COMPLEXO B BASI 100ML 1FR', 0, 0, 1, 237.44, 352.00, 0, '7354787', 8, 8, '2024-04-27 17:46:54'),
(367, 'FARM', 'REVIFORCE LIQUIDO (NOVA FORMULA) 250ML 1FR', 3, 0, 1, 0.00, 352.00, 0, '01/2026', 8, 8, '2025-02-03 07:17:05'),
(368, 'FARM', 'CORVITE 10% GOTAS ORAIS 20ML 1FR', 0, 0, 1, 199.06, 295.00, 0, '7397943', 8, 8, '2024-04-27 17:46:54'),
(369, 'FARM', 'FINATUX (CARBOCISTEINA) 50MG/ML XAROPE 200ML 1FR', 9, 0, 1, 199.50, 295.00, 0, '31/10/2026', 8, 8, '2025-01-28 22:06:54'),
(370, 'FARM', 'TENIVERME (FLUBENDAZOL) 100MG 6COMP', 0, 0, 1, 61.65, 91.00, 0, '9580407', 8, 8, '2024-04-27 17:46:54'),
(371, 'FARM', 'SUPOFEN (PARACETAMOL) 500MG 20 COMP', 12, 10, 1, 64.00, 94.50, 0, '9866608', 8, 8, '2025-08-07 13:54:52'),
(373, 'FARM', 'AGUA DE ROSAS FRASCO DE 200ML', 0, 0, 1, 157.00, 158.00, 0, 'GF08', 8, 8, '2024-04-27 17:46:54'),
(374, 'FARM', 'OLEO DE AMENDOAS  VERDE /MEDIS  60 ML', 0, 0, 1, 0.00, 250.00, 0, 'GF100', 8, 8, '2024-08-08 23:32:27'),
(375, 'FARM', 'OLEO DE AMENDOA DOCE FSC 250ML', 0, 0, 1, 420.00, 421.00, 0, 'GF103', 8, 8, '2024-04-27 17:46:54'),
(377, 'FARM', 'SYMBICORT TURBOHALER PO INAL. 320/9MCG 60 DOSES', 0, 0, 1, 3209.00, 4756.00, 0, 'GF261', 8, 8, '2024-04-27 17:46:54'),
(378, 'FARM', 'CRESTOR 10MG CX.60 COMP', 0, 0, 1, 2522.00, 3737.00, 0, 'GF262', 8, 8, '2024-04-27 17:46:54'),
(379, 'FARM', 'CRESTOR 20MG CX 30 COMP', 0, 0, 1, 3974.00, 5889.00, 0, 'GF263', 8, 8, '2024-04-27 17:46:54'),
(380, 'FARM', 'FORXIGA 10MG CX 28 COMP', 0, 0, 1, 673.00, 1000.00, 0, 'GF75', 8, 8, '2024-04-27 17:46:54'),
(381, 'FARM', 'NEXIUM 20MG CX 28 COMP', 0, 0, 1, 875.00, 1300.00, 0, 'GF97', 8, 8, '2024-04-27 17:46:54'),
(382, 'FARM', 'NEXIUM 40MG CX 28 COMP', 0, 0, 1, 220.00, 325.00, 0, 'GF98', 8, 8, '2024-04-27 17:46:54'),
(383, 'FARM', 'GLUCOPHAGE 500MG CX 50 COMP REVESTIDO', 0, 0, 1, 220.00, 325.00, 0, 'GF73', 8, 8, '2024-04-27 17:46:54'),
(385, 'FARM', 'ACEM-500 (Claritromicina 500mg), cxs x10 comp.', 1, NULL, 1, 0.00, 223.00, 0, '02/25', 8, 8, '2025-01-07 14:47:40'),
(386, 'FARM', 'ACETILCISTEINA AZEVEDOS 40mg/ml, Frs x 200ml', 4, 1, 1, 200.00, 577.00, 0, '06/2025', 8, 8, '2025-06-30 16:45:56'),
(387, 'FARM', 'ACETILCISTEINA TUSSLENE 600mg, Cxs 20 comp. Eferv', 3, 1, 1, 285.00, 640.00, 0, '01/2026', 8, 8, '2025-06-14 11:49:11'),
(388, 'FARM', 'ACIDO ALEDRONICO AZEVEDOS 70mgx 4 comp.', 0, NULL, 1, 424.00, 629.00, 0, 'AZEVED068', 8, 8, '2024-04-27 17:46:54'),
(389, 'FARM', 'AcUTIL Cxs 30 caps.', 0, NULL, 1, 1046.00, 1699.00, 0, 'ANGEH30158', 8, 8, '2024-04-27 17:46:54'),
(390, 'FARM', 'ADALGUR N cxs.x60 comp.', 0, NULL, 1, 861.00, 1275.00, 0, 'ANG000006', 8, 8, '2024-04-27 17:46:54'),
(391, 'FARM', 'ADESIVO ELASTIC BANDAGE BRANCO 2,5cmx4,5m', 0, NULL, 1, 139.00, 194.00, 0, 'SHE000163', 8, 8, '2024-04-27 17:46:54'),
(392, 'FARM', 'ADESIVO PAPER TAPE (ANTI-ALERGICO) 2,5cmx3m-1un.', 0, NULL, 1, 16.00, 22.00, 0, 'SHE000127', 8, 8, '2024-04-27 17:46:54'),
(393, 'FARM', 'AERO-OM COMPRIMIDOS,CXSx100 COMP', 13, 5, 1, 0.00, 113.00, 0, '02/27', 8, 8, '2025-08-12 11:22:16'),
(395, 'FARM', 'AFEBRYL tubo x16cp.eferv', 1, NULL, 1, 176.00, 262.00, 0, 'B90000044', 8, 8, '2024-07-02 12:31:12'),
(396, 'FARM', 'AFTASPRAY 30 MG/100 GR frs .x15ml', 0, NULL, 1, 1732.00, 2812.00, 0, 'FERRAZ007', 8, 8, '2024-04-27 17:46:54'),
(397, 'FARM', 'AFETUM GEL 240Mg/100 ORAL Frs.x15ml', 0, NULL, 1, 1688.00, 2741.00, 0, 'FERRAZ007', 8, 8, '2024-04-27 17:46:54'),
(398, 'FARM', 'AGUA OXIGENADA 3%,Frs x250 ml', 0, NULL, 1, 0.00, 96.00, 0, '03/26', 8, 8, '2025-01-28 23:39:30'),
(399, 'FARM', 'Agua Oxigenada 3% Frs.x500ml', 0, NULL, 1, 74.00, 103.00, 0, 'HIDR00006', 8, 8, '2024-04-27 17:46:54'),
(400, 'FARM', 'AIRALAMx24comp.', 0, NULL, 1, 430.00, 637.00, 0, 'AZEVEDO94', 8, 8, '2024-04-27 17:46:54'),
(403, 'FARM', 'Alcool Etilico 70%v/v,frsx250ml natur', 3, 10, 1, 132.00, 200.00, 0, '30/08/2028', 8, 8, '2025-07-07 12:22:26'),
(404, 'FARM', 'ALGODAO(COTTON WOOL)NON-STERILE X50g', 0, NULL, 1, 59.00, 96.00, 0, 'SHE000160', 8, 8, '2024-04-27 17:46:54'),
(405, 'FARM', 'ALHO SUPER S /ODOR X90 caps. ', 0, NULL, 1, 1233.00, 2002.00, 0, 'FJC000004', 8, 8, '2024-04-27 17:46:54'),
(406, 'FARM', 'ALICATE UNHAS  NR 49(ref.6730614)', 0, NULL, 1, 1938.00, 2948.00, 0, 'GESTAFAROO1', 8, 8, '2024-04-27 17:46:54'),
(407, 'FARM', 'ALPRAZOLAM PAZOLAM  0,5mgX60comp', 0, NULL, 1, 410.00, 608.00, 0, 'ATRAL0029', 8, 8, '2024-04-27 17:46:54'),
(409, 'FARM', 'AMBROXOL FLUIDOX,XAROPE X200ml', 1, NULL, 1, 658.00, 975.00, 0, '31/08/2028', 8, 8, '2025-01-28 05:25:02'),
(410, 'FARM', 'AMBROXOL TUSSILENE 6 mg/MILXAROPE X200 ml', 2, NULL, 1, 0.00, 406.00, 0, '31/08/26', 8, 8, '2025-01-27 02:10:12'),
(411, 'FARM', 'AMIFER IRON SYRUP Frs. X200ml', 2, NULL, 1, 0.00, 474.00, 0, '12/2024', 8, 8, '2024-12-12 08:51:44'),
(412, 'FARM', 'AMIFER FORTE  COMP 100/0,35 MG', 0, NULL, 1, 250.00, 123.33, 0, '10/2025', 8, 8, '2024-12-10 15:43:29'),
(413, 'FARM', 'AMIFER IV (sacarose  de ferro 20 mg/ml) emb X5 amp', 0, NULL, 1, 718.00, 1065.00, 0, 'DAFRA0059', 8, 8, '2024-04-27 17:46:54'),
(414, 'FARM', 'AMIFER  JUNIOR SOL .ORAL, Frs X 150 ml', 0, NULL, 1, 0.00, 370.00, 0, '11/2025', 8, 8, '2024-12-10 15:22:21'),
(415, 'FARM', 'AMIODARONA GP 200mg Cxs X60 comp', 0, NULL, 1, 906.00, 1343.00, 0, 'MEDINF060', 8, 8, '2024-04-27 17:46:54'),
(417, 'FARM', 'AMLODIPINA AZEVEDO 10mg -cxs X30 Comp', 0, NULL, 1, 394.00, 585.00, 0, 'AZEVEDO38', 8, 8, '2024-04-27 17:46:54'),
(418, 'FARM', 'ANACERVIX FORTE Cxs. X60 Comp', 0, NULL, 1, 942.00, 1397.00, 0, 'ANGE00007', 8, 8, '2024-04-27 17:46:54'),
(419, 'FARM', 'ANDARILHO COM RODAS DOBRAVEL E AJUSTAVEL,1 UN', 0, NULL, 1, 5691.00, 7967.00, 0, 'SHE000172', 8, 8, '2024-04-27 17:46:54'),
(420, 'FARM', 'ANDARILHO SEM RODAS DOBRAVEL E AJUSTAVEL,1 UN', 0, NULL, 1, 3478.00, 4869.00, 0, 'SHE000175', 8, 8, '2024-04-27 17:46:54'),
(422, 'FARM', 'ANTIBIOPHILUS 250mg X20 Capsulas', 3, NULL, 1, 0.00, 660.00, 0, '30/09/2025', 8, 8, '2025-01-25 13:51:22'),
(423, 'FARM', 'ANTIBIOPHILUS SAQUETAS X20 Saquetas', 30, NULL, 1, 0.00, 56.46, 0, '02/25', 8, 8, '2025-03-28 16:40:59'),
(424, 'FARM', 'ANTIPLAR 75mg Cxs x 40 comp.', 0, NULL, 1, 146.00, 217.00, 0, 'EMCURE022', 8, 8, '2024-04-27 17:46:54'),
(425, 'FARM', 'ASOMEX 2,5mg X 30 comp', 9, NULL, 1, 228.00, 112.66, 0, 'EMCURE002', 8, 8, '2024-05-20 11:19:11'),
(426, 'FARM', 'ASOMEX 5mg X 30 Comp.', 9, NULL, 1, 305.00, 150.66, 0, 'EMCURE003', 8, 8, '2024-05-20 11:19:46'),
(427, 'FARM', 'ASP 100mg Cxs x 30 Comp.', 0, NULL, 1, 213.00, 316.00, 0, 'MEDINF077', 8, 8, '2024-04-27 17:46:54'),
(428, 'FARM', 'ASP 100mg Cxs x 60 Comp.', 0, NULL, 1, 421.00, 623.00, 0, 'MEDINF070', 8, 8, '2024-04-27 17:46:54'),
(429, 'FARM', 'ANTENOLOL AZEVEDOS 1OOmg-Cxs X 56 Comp.', 0, NULL, 1, 488.00, 724.00, 0, 'AZEVEDO53', 8, 8, '2024-04-27 17:46:54'),
(430, 'FARM', 'ANTENOLOL AZEVEDOS 50 mg-Cxs X 56 comp.', 0, NULL, 1, 439.00, 650.00, 0, 'AZEVEDO52', 8, 8, '2024-04-27 17:46:54'),
(431, 'FARM', 'ATORVASTATINA AZEVEDOS 20 MG, Cxs X 28 Comp', 0, NULL, 1, 478.00, 708.00, 0, 'AZEVED150', 8, 8, '2024-04-27 17:46:54'),
(432, 'FARM', 'ATORVASTATINA AZEVEDOS 40 MG, CXS X 28', 0, NULL, 1, 611.00, 905.00, 0, 'AZEVEDO151', 8, 8, '2024-04-27 17:46:54'),
(433, '', 'AUGMENTIN 500/125 MG Cxs x14cp', 3, 1, 1, 200.00, 250.00, 0, 'GSK000044', 0, 0, '2025-06-13 15:40:58'),
(434, 'FARM', 'AULIN 100mg Cxs. X 20 Comp', 0, NULL, 1, 345.00, 512.00, 0, 'ANGEL3785', 8, 8, '2024-04-27 17:46:54'),
(436, 'FARM', 'AZITROMICINA ASTRAL 500mg X 3comp.', 0, NULL, 1, 217.00, 322.00, 0, 'ATRAL0023', 8, 8, '2024-04-27 17:46:54'),
(437, 'FARM', 'AZITROMICINA AZEV 500mg PO INJ-Emb x 10 frs', 7, NULL, 1, 2212.00, 144.66, 0, 'AZEVED168', 8, 8, '2024-10-18 08:29:44'),
(439, 'FARM', 'AZITROMICINA AZEVEDO 500MG CXS X 3 COMP.', 0, NULL, 1, 293.00, 145.66, 0, 'AZEVED124', 8, 8, '2024-06-28 10:20:16'),
(440, 'FARM', 'AZITROMICINA BALDACCI 500mg X 3 Comp', 0, NULL, 1, 327.00, 484.00, 0, 'O20000020', 8, 8, '2024-04-27 17:46:54'),
(441, 'FARM', 'BALFOLIC 400mcg-Ac.Folico,Emb x 120 cps', 0, NULL, 1, 780.00, 1156.00, 0, 'BALDAC0004', 8, 8, '2024-04-27 17:46:54'),
(442, 'FARM', 'BALPIC 1mg/g GEL-MELEATO demetindeno, bis X30mg', 0, NULL, 1, 583.00, 863.00, 0, 'BALDAC0005', 8, 8, '2024-04-27 17:46:54'),
(445, 'FARM', 'BARRAL CREME GORDO X 200g', 0, NULL, 1, 1106.00, 1796.00, 0, 'ANGEL2204', 8, 8, '2024-04-27 17:46:54'),
(446, 'FARM', 'BARRAL D. P. CREME EMOLIENTE REPARADOR  X 200ml', 0, NULL, 1, 1006.00, 1633.00, 0, 'ANGEL2213', 8, 8, '2024-04-27 17:46:54'),
(447, 'FARM', 'BARRAL  D.P. CREME MAOS X 75ML', 0, NULL, 1, 643.00, 1045.00, 0, 'ANG000054', 8, 8, '2024-04-27 17:46:54'),
(448, 'FARM', 'BARRAL STOP 24 X40g', 0, NULL, 1, 919.00, 1492.00, 0, 'ANGEL2215', 8, 8, '2024-04-27 17:46:54'),
(449, 'FARM', 'BENGALA AJUSTAVEL, 1 UN', 0, NULL, 1, 736.00, 1030.00, 0, 'SHE000176', 8, 8, '2024-04-27 17:46:54'),
(450, 'FARM', 'BENYLIN  4 FLU frs . X200ml', 11, 5, 1, 461.00, 683.00, 0, '30/09/2027', 8, 8, '2025-09-24 19:34:45'),
(451, 'FARM', 'BENYLIN ORIGINAL NIGHT COUGH ,FrsX200ml', 5, 5, 1, 456.00, 676.00, 0, '12/2026', 8, 8, '2025-09-24 19:41:11'),
(452, 'FARM', 'VDM KIT', 3, NULL, 1, 150.00, 210.00, 0, 'DVD2301', 9, 9, '2024-08-31 08:41:59'),
(454, 'FARM', 'SPAMOX500mg CAP.\"AMOXICILINA500mg\"', 0, NULL, 1, 240.00, 336.00, 0, 'MX479', 9, 9, '2024-04-27 17:46:54'),
(455, 'FARM', 'SPAMOX125mg Susp.\"Amoxicilina 125mg\"', 0, NULL, 1, 44.00, 61.60, 0, 'X01086', 9, 9, '2024-04-27 17:46:54'),
(456, 'FARM', 'SPAMOX250mg susp.\"AMOXICILINA SUSP\"', 0, NULL, 1, 60.00, 84.00, 0, 'XS121', 9, 9, '2024-04-27 17:46:54'),
(458, 'FARM', 'SPENIV500mg CP\" FENOXIMETILPENICILINA 500mg CP\"', 0, NULL, 1, 430.00, 602.00, 0, 'TP007', 9, 9, '2024-04-27 17:46:54'),
(459, 'FARM', 'SPENIV 125mg ou 5ml\"FENOXIMETILPENICILINA 125mg ou 5ml\"', 0, NULL, 1, 70.00, 98.00, 0, 'E0105', 9, 9, '2024-04-27 17:46:54'),
(460, 'FARM', 'SPENIV250mg ou 5ml\"FENOXIMETILPENICILINA 250 ou 5ml\"', 0, NULL, 1, 85.00, 119.00, 0, 'ES005', 9, 9, '2024-04-27 17:46:54'),
(461, 'FARM', 'ZOLETIN CP\" TINIDAZOLE 500mg CP\"', 0, NULL, 1, 170.00, 253.30, 0, 'T3104', 9, 9, '2024-04-27 17:46:54'),
(462, 'Selecione o prefixo', 'NORFLOPIN TZ', 1, NULL, 1, 0.00, 128.00, 0, '01/25', 9, 9, '2024-07-19 12:04:05'),
(463, 'FARM', 'ALMEX SUSP\" ALBENDAZOLE SUSP 200mg ou 5ml\"', 0, NULL, 1, 45.00, 71.60, 0, '2M00122', 9, 9, '2024-04-27 17:46:54'),
(464, 'FARM', 'CHLONFESHA 250mg\" CHLORAMPHENICOL CAP. 250mg\"', 0, NULL, 1, 300.00, 477.00, 0, 'WC032', 9, 9, '2024-04-27 17:46:54'),
(465, 'FARM', 'KETOGEN 200MG', 65, 5, 1, 0.00, 55.00, 0, '08/2027', 9, 9, '2025-08-08 06:26:04'),
(466, 'FARM', 'NALISHA TABS (Acido Nalidixico BP 500mg comp.)', 0, NULL, 1, 480.00, 763.20, 0, 'VT017-018 ', 9, 9, '2024-04-27 17:46:54'),
(468, 'FARM', 'GESIC ADL 200MG CP(IBUPROFENO 200MG CP)', 0, NULL, 1, 70.00, 104.29, 0, 'T32063', 10, 9, '2024-04-27 17:46:54'),
(469, 'FARM', 'PARA RAPIDO EXTRA(PARACETAMOL500MG,CAFFEINE 30MG) X', 0, 0, 1, 0.00, 14.00, 0, 'WT204', 10, 9, '2025-04-09 14:43:48'),
(471, 'FARM', 'PARA ACE CP(PARA 325MG,ACECLOFENAC 100MG)', 0, NULL, 1, 135.00, 201.20, 0, 'T3186', 10, 9, '2024-04-27 17:46:54'),
(472, 'FARM', 'PARACETAMOL CP(PARACETAMOL 500MG CP)', 0, NULL, 1, 65.00, 96.89, 0, '221166', 10, 9, '2024-04-27 17:46:54'),
(475, 'FARM', 'ROXISHA 20MG(PIROXICAM CAPS20MG)', 0, NULL, 1, 155.00, 246.50, 0, 'WC042', 10, 9, '2024-04-27 17:46:54'),
(476, 'FARM', 'SHIBUDAL CAPS', 171, 0, 1, 0.00, 55.00, 0, '05/2027', 10, 9, '2025-11-06 15:46:34'),
(477, 'FARM', 'EAZOL POM MULTIDORES', 0, NULL, 1, 25.00, 39.80, 0, 'SR1224', 10, 9, '2024-04-27 17:46:54'),
(478, 'FARM', 'EAZOL BALM(Balsamo a BASE DE PLANTA)', 0, NULL, 1, 30.00, 44.68, 0, 'SR0470', 10, 9, '2024-04-27 17:46:54'),
(480, 'FARM', 'KILL TOSS XAROPE(PARA TOSSES SECA+PEITORAL)', 0, NULL, 1, 75.00, 111.80, 0, '2305071', 11, 9, '2024-04-27 17:46:54'),
(481, 'FARM', 'KILL TOSSE PASTILHAS LARANJA(PARA TOSSES A BASE DE PLANTAS)', 0, NULL, 1, 185.00, 259.00, 0, 'SR3431', 11, 9, '2024-04-27 17:46:54'),
(482, 'FARM', 'KILL TOSSE PASTILHAS MIRTILO(PARA TOSSE A BASE DE PLANTAS)', 0, NULL, 1, 185.00, 259.00, 0, 'SR3433', 11, 9, '2024-04-27 17:46:54'),
(483, 'FARM', 'KILL TOSSE PASTILHAS MENTA(PARA TOSSE A BASE DE PLANTAS)', 0, NULL, 1, 185.00, 259.00, 0, 'SR3434', 11, 9, '2024-04-27 17:46:54'),
(484, 'FARM', 'KILL TOSSE PASTILHAS LIMAO(PARA TOSSE A BASE DE PLANTAS)', 0, 0, 1, 185.00, 15.00, 0, 'SR3433', 10, 9, '2025-11-08 17:10:53'),
(485, 'FARM', 'KUFHETU AZUL(PARA TRATAE TOSSE SECA)100ml', 0, NULL, 1, 65.00, 96.00, 0, 'EG23001', 11, 9, '2024-04-27 17:46:54'),
(486, 'FARM', 'KUFHETU CASTANHO(ANTI TUSSCO+REFORCOS IMUNOLOGICOS)100ml', 0, NULL, 1, 65.00, 96.90, 0, 'EF23005', 11, 9, '2024-04-27 17:46:54'),
(487, 'FARM', 'KUFHETU LARANJA(PARA TOSSE+REFORCOS IMUNOLOGICOS)100ml', 0, NULL, 1, 65.00, 96.90, 0, 'EH23001', 11, 9, '2024-04-27 17:46:54'),
(488, 'FARM', 'ALATROL PAEDIATRIC DROPS(CETIRIZINE HYDROCHLORIDE 205MG BP)15ml', 1, NULL, 1, 0.00, 53.00, 0, '3F01485', 11, 9, '2025-01-20 06:02:31'),
(489, 'FARM', 'DR.COLD CP(Para,cafeina,Fenilefrina Hid,Clorfenamina)', 0, NULL, 1, 225.00, 335.30, 0, 'AC35201', 11, 9, '2024-04-27 17:46:54'),
(490, 'FARM', 'COLRID CP(Para,cafeina,Fenilefrina Hid,Clorfenamina)', 0, 0, 1, 270.00, 315.00, 0, 'DCHZ2201', 11, 9, '2025-11-07 10:06:00'),
(491, 'FARM', 'MUCOLEX EXPECTORANT(AMBROXOL,SALBUTAMOL,GUAIFENESINA)100ml', 0, NULL, 1, 60.00, 84.00, 0, 'CJ23006', 11, 9, '2024-04-27 17:46:54'),
(492, 'FARM', 'EAZOL INHALOR(EAZOL INHALADOR)', 0, NULL, 1, 15.00, 22.40, 0, 'SR1227', 11, 9, '2024-04-27 17:46:54'),
(495, 'Selecione o prefixo', 'ARTEMETHER &LUMEFANTRINE (20/120MG)', 1, 5, 1, 120.00, 180.00, 0, '11/2026', 12, 9, '2025-10-22 07:37:09'),
(497, 'FARM', 'LEVOCOR FORTE SYP(TONICO DE FIGADO)200ml', 18, NULL, 1, 0.00, 154.00, 0, '03/2026', 13, 9, '2025-02-02 23:59:12'),
(498, 'FARM', 'EAZOLE ANTIACIDO(SUGAR FREE)(XAROPE ANTIACIDO)20ml', 0, NULL, 1, 100.00, 148.99, 0, 'SR3145', 13, 9, '2024-04-27 17:46:54'),
(499, 'FARM', 'ENTACYD SUGAR FREE SUSP(Oxi de alumino 175mg,hidro de mag.225mg)200ml', 40, NULL, 1, 0.00, 210.00, 0, '04/2026', 13, 9, '2025-03-28 22:37:53'),
(500, 'FARM', 'ENTACYD PLUS TABS ', 193, NULL, 1, 0.00, 25.00, 0, '06/26', 13, 9, '2025-03-28 15:21:30'),
(501, 'FARM', '', 0, NULL, 1, 75.00, 111.80, 0, 'GW63', 13, 9, '2024-04-27 17:46:54'),
(502, 'FARM', 'OMESK(OMEPRAZOLE CAP 20MG)', 0, NULL, 1, 140.00, 222.59, 0, 'OP22019', 13, 9, '2024-04-27 17:46:54'),
(503, 'FARM', 'SECLO 20 CAP(OMEPRAZOLE CAP 20MG)', 0, NULL, 1, 150.00, 238.50, 0, '2K00216', 13, 9, '2024-04-27 17:46:54'),
(504, 'FARM', 'VENTOSHA 4MG (SALBUTAMOL CP)', 539, NULL, 1, 0.00, 8.00, 0, '02/2025', 14, 9, '2025-03-28 21:58:00'),
(505, 'FARM', 'SULTOLIN INALADOR(SALBUTAMOL 100MCG/PUFF)', 0, NULL, 1, 120.00, 178.79, 0, '3F02281', 14, 9, '2024-04-27 17:46:54'),
(506, 'FARM', 'TRIBUL(TRIBULUS TERRESTRIS,SULFATO DE ZINCO)cp', 0, NULL, 1, 590.00, 879.10, 0, '3063', 15, 9, '2024-04-27 17:46:54'),
(507, 'FARM', 'LOBESE(PHASEOLUS VULGARIS)', 0, NULL, 1, 350.00, 521.49, 0, '3161', 15, 9, '2024-04-27 17:46:54'),
(508, 'FARM', 'AMCIVIT XYP(VIT C)100ml', 16, NULL, 1, 75.00, 113.00, 0, '05/2025', 15, 9, '2025-01-26 18:05:31'),
(509, 'FARM', 'MEGA 3(OLEO DE SALMAO 1000MG)cap.', 0, NULL, 1, 450.00, 670.00, 0, 'ME2323', 15, 9, '2024-04-27 17:46:54'),
(510, 'FARM', 'INFANT-D(Capsulas abertas com torcao)(VIT D3-400IU)', 0, NULL, 1, 450.00, 670.49, 0, 'VITD3', 15, 9, '2024-04-27 17:46:54');
INSERT INTO `produto` (`idproduto`, `prefico`, `nomeproduto`, `stock`, `stock_min`, `stocavel`, `preco_compra`, `preco`, `iva`, `codbar`, `grupo`, `familia`, `data`) VALUES
(511, 'FARM', 'SUPRAVIT CP(Forula Executiva de Multivitaminas e Minerais)', 0, NULL, 1, 510.00, 759.89, 0, '', 15, 9, '2024-04-27 17:46:54'),
(512, 'FARM', 'OZICAL CP(CALCIO 600MG +COLECALCIFEROL)', 0, NULL, 1, 415.00, 618.40, 0, '', 15, 9, '2024-04-27 17:46:54'),
(513, 'FARM', 'TRIOSHA CP(VIT B1,B6,B12)', 225, NULL, 1, 18.00, 48.00, 0, '07/2024', 15, 9, '2025-03-28 17:29:56'),
(514, 'FARM', 'SQUARE ZINC CP(ZINC SULFATE 200MG)', 0, NULL, 1, 150.00, 210.00, 0, '1CO3627', 15, 9, '2024-04-27 17:46:54'),
(515, 'FARM', 'VITAMAKS XAROPE(MULTI-VITMINA E MINERAIS XAROPE)100ml', 0, NULL, 1, 65.00, 96.90, 0, 'MV23009', 15, 9, '2024-04-27 17:46:54'),
(516, 'FARM', 'SIESTA(VALERIAN 2000MG)', 0, NULL, 1, 450.00, 715.50, 0, '', 15, 9, '2024-04-27 17:46:54'),
(518, 'FARM', 'ACCUQUIK TEST TESTE RAPIDO DE GRAVIDEZ', 11, NULL, 1, 35.00, 55.00, 0, '09/2026', 22, 9, '2025-02-03 07:26:56'),
(519, 'FARM', 'ALGODAO 500GMS Algodao hidrofilo', 0, NULL, 1, 130.00, 206.70, 0, '', 22, 9, '2024-04-27 17:46:54'),
(520, 'FARM', 'HEMMYGO CAPS(Basa de ervas para pilhas internas&externa 1*30)', 0, NULL, 1, 145.00, 230.60, 0, 'EP22001', 16, 9, '2024-04-27 17:46:54'),
(521, 'FARM', 'SHADID CRÈME VAGINAL-BASI LAB(CLOTRIMAZOL 10MG/G)', 0, NULL, 1, 280.00, 417.18, 0, '', 16, 9, '2024-04-27 17:46:54'),
(522, 'FARM', 'DANAZOL 200MG CAP', 0, NULL, 1, 750.00, 1192.49, 0, 'WC005', 16, 9, '2024-04-27 17:46:54'),
(523, 'FARM', 'MYGRA 100MG(SIldenafil 100mg comp.)', 0, NULL, 1, 60.00, 90.00, 0, 'SC21003', 16, 9, '2024-07-07 07:14:15'),
(524, 'FARM', 'PLAN INTIMO(LEVONORGESTREL 1.5MG)', 0, 0, 1, 50.00, 75.00, 0, 'NRO1346C', 16, 9, '2025-11-06 14:27:09'),
(526, 'FARM', 'MICROLENYN INTIMO 30+(LEVONORGESTREL 0.15,ETINILESTRADIOL)', 0, NULL, 1, 105.00, 156.49, 0, '', 16, 9, '2024-04-27 17:46:54'),
(527, 'FARM', 'CANI-MAKS V2(CLOTRIMAZOLE CRÈME VAGINAL 2% p/p)', 0, NULL, 1, 85.00, 126.70, 0, 'CN22001', 16, 9, '2024-04-27 17:46:54'),
(528, 'FARM', 'HEMMYGO CREAM(PARA ALIVIO DE HEMORROIDAS)', 0, NULL, 1, 125.00, 175.00, 0, 'E023001', 17, 9, '2024-04-27 17:46:54'),
(529, 'FARM', 'ANTISEPTIC(HAND SANITIZER)50ml', 0, NULL, 1, 90.00, 143.10, 0, '', 17, 9, '2024-04-27 17:46:54'),
(530, 'FARM', 'ANTISEPTIC - HEMANI 70% 1 L(HAND SANITIZER)1000ml', 0, NULL, 1, 800.00, 1271.99, 0, 'MO1L AHS', 17, 9, '2024-04-27 17:46:54'),
(531, 'FARM', 'ANTISEPTIC WITH NATURAL BEADS(HAND SANITIZER)250ml', 0, NULL, 1, 200.00, 318.00, 0, '250AHS20', 17, 9, '2024-04-27 17:46:54'),
(533, 'FARM', 'DERM X(Econazol+Triamcinolona+Gentamicina crème)', 0, NULL, 1, 80.00, 119.20, 0, 'DY21006', 17, 9, '2024-04-27 17:46:54'),
(535, 'FARM', 'KETOSHA CREME(CETOCONAZOL 2% W/W)', 95, NULL, 1, 80.00, 113.00, 0, '11/2026', 17, 9, '2025-02-02 20:48:24'),
(536, 'FARM', 'DERMASOL 0.05%(PROPIONATO DE CLOBETASOL)', 0, NULL, 1, 62.00, 92.39, 0, '3G01641', 17, 9, '2024-04-27 17:46:54'),
(537, 'FARM', 'DESINFECTA ALCOOL 70% 250ml', 0, NULL, 1, 105.00, 167.00, 0, '', 17, 9, '2024-04-27 17:46:54'),
(538, 'FARM', 'HAND SANITIZER 165ml', 0, NULL, 1, 110.00, 174.90, 0, '', 17, 9, '2024-04-27 17:46:54'),
(541, 'FARM', 'NIFESHA 20MG(NIFEDIPINA 20MG)', 0, NULL, 1, 145.00, 230.60, 0, '', 18, 9, '2024-04-27 17:46:54'),
(542, 'FARM', 'AMILOSHA-H (Amilorio 5MG)', 381, NULL, 1, 0.00, 32.00, 0, '8/2027', 18, 9, '2025-03-28 10:05:42'),
(543, 'FARM', 'GLYSHA CP(GLIBENCLAMIDA 5MG)', 0, NULL, 1, 25.00, 39.80, 0, 'VT287', 18, 9, '2024-04-27 17:46:54'),
(544, 'FARM', 'CLORANFENICOL POM OFTALMICA(CLORANFENICOL POM OFT BP1% w/w)', 0, NULL, 1, 10.00, 15.90, 0, 'H1001', 20, 9, '2024-04-27 17:46:54'),
(545, 'FARM', 'BILOCOR5', 0, NULL, 1, 119.00, 176.00, 0, '', 18, 10, '2024-04-27 17:46:54'),
(546, 'FARM', 'SOLA  GEL', 0, NULL, 1, 0.00, 297.00, 0, '', 17, 8, '2025-01-08 01:00:16'),
(549, 'FARM', 'Lauroderme Sabonete 100g', 3, NULL, 1, 0.00, 720.00, 0, '30/09/2026', 17, 12, '2025-01-20 08:52:07'),
(550, 'FARM', 'Chupeta physio silicone azul 6-16 meses', 1, NULL, 1, 0.00, 295.00, 0, '', 22, 12, '2024-05-01 21:22:46'),
(551, 'FARM', 'Chupeta physio silicone Rosa 6-16 meses', 1, NULL, 1, 0.00, 320.00, 0, '', 22, 12, '2024-05-01 21:23:47'),
(552, 'FARM', 'Vaselina pura basi', 0, NULL, 1, 0.00, 96.00, 0, '', 17, 12, '2024-06-05 05:51:54'),
(553, 'FARM', 'Oleo de Amendoa Medis 60ml', 0, NULL, 1, 0.00, 250.00, 0, '31/03/', 17, 12, '2025-02-02 18:24:34'),
(554, 'FARM', 'Oleo de Amendoa FG campos 60ml', 0, NULL, 1, 0.00, 306.00, 0, '', 17, 12, '2024-06-06 05:07:29'),
(555, 'FARM', 'OLEO DE AMENDOAS DOCES FG campos 60ml', 2, NULL, 1, 0.00, 306.00, 0, '', 17, 12, '2024-05-24 01:37:17'),
(557, 'FARM', 'ACE-Konazole shampoo 100ml', 4, 5, 1, 0.00, 204.00, 0, '', 17, 15, '2025-11-07 09:00:00'),
(558, 'FARM', 'Bio-oil 125ml', 2, NULL, 1, 0.00, 670.00, 0, '', 17, 10, '2025-01-22 03:25:28'),
(559, 'FARM', 'Hidrolat creme facial 40g', 2, NULL, 1, 0.00, 441.00, 0, '', 17, 8, '2024-05-01 21:32:56'),
(560, 'FARM', 'Halibut Derma plus creme 30g', 3, 1, 1, 600.00, 744.00, 0, '30/04/2027', 17, 12, '2025-06-11 09:07:09'),
(561, 'FARM', 'Lauroderme liquido 150ml', 3, NULL, 1, 0.00, 703.00, 0, '', 17, 12, '2024-05-01 21:36:08'),
(562, 'FARM', 'Lauroderme creme 100g', 0, NULL, 1, 0.00, 874.00, 0, '', 17, 12, '2025-01-24 12:33:04'),
(563, 'FARM', 'Lauroderme po 50g', 0, NULL, 1, 0.00, 468.00, 0, '', 17, 12, '2024-08-19 22:01:57'),
(564, 'FARM', 'Lauroderme po 100g', 0, NULL, 1, 0.00, 671.00, 0, '', 17, 12, '2025-01-14 08:35:47'),
(565, 'FARM', 'OBESYL CHA 100g', 0, 1, 1, 0.00, 623.00, 0, '01/30/2028', 21, 12, '2025-04-21 16:21:32'),
(566, 'FARM', 'SABONETE DE ENXOFRE', 0, NULL, 1, 0.00, 297.00, 0, '12/2025', 17, 12, '2024-05-22 08:08:17'),
(567, 'FARM', 'SABONETE DE ALCATRAO X 90 G', 1, NULL, 1, 0.00, 276.00, 0, '12/20230', 17, 12, '2024-08-22 20:26:43'),
(568, 'FARM', 'SABONETE STOP ACNE 90g', 1, NULL, 1, 0.00, 715.00, 0, '12/2030', 17, 12, '2024-05-20 13:48:32'),
(569, 'FARM', 'STOP ACNE Capsulas 60+30', 0, NULL, 1, 0.00, 2026.00, 0, '12/2025', 17, 12, '2024-08-09 00:25:50'),
(570, 'FARM', 'Lauroderme pasta 100g', 5, NULL, 1, 0.00, 886.00, 0, '31-/03/2026', 17, 12, '2024-08-23 01:39:15'),
(571, 'FARM', 'Lauroderme pasta  50g', 1, NULL, 1, 0.00, 708.00, 0, '31/12/2028', 17, 12, '2024-12-10 08:58:45'),
(574, 'FARM', 'Aqueos cream 500g', 0, NULL, 1, 0.00, 850.00, 0, '', 17, 10, '2024-08-26 09:27:55'),
(575, 'FARM', 'Camphor creme 500g', 0, NULL, 1, 0.00, 200.00, 0, '', 17, 10, '2024-06-07 07:24:51'),
(576, 'FARM', 'Sabusol sabonete 100g', 0, NULL, 1, 0.00, 88.00, 0, '', 17, 8, '2024-05-11 08:36:24'),
(577, 'FARM', 'SABONETE DE GLICERINA VERDE', 0, NULL, 1, 0.00, 270.00, 0, '', 17, 10, '2024-06-23 08:41:16'),
(578, 'FARM', 'SABONETE DE GLICERINA TRANSPARENTE', 0, NULL, 1, 0.00, 270.00, 0, '', 17, 10, '2024-06-07 11:41:03'),
(579, 'FARM', 'SABONETE DE GLICERINA AMARELO', 2, NULL, 1, 0.00, 270.00, 0, '', 17, 10, '2024-08-26 08:56:53'),
(580, 'FARM', 'SABONETE DE GLICERINA VERMELHO', 3, NULL, 1, 0.00, 270.00, 0, '', 17, 10, '2024-08-08 02:38:59'),
(581, 'FARM', 'ACEBACT', 15, 0, 1, 0.00, 101.00, 0, '30/09/2026', 17, 13, '2025-11-07 07:55:27'),
(584, 'FARM', 'Escova pierrot azul 2-8 anos', 1, NULL, 1, 0.00, 190.00, 0, '', 21, 10, '2024-05-01 22:05:49'),
(585, 'FARM', 'Escova PIERROT ADULTO ROSA', 0, NULL, 1, 0.00, 245.00, 0, '12/2045', 21, 10, '2024-08-24 13:15:33'),
(586, 'FARM', 'Gel dental pierrot  piwy 75ml', 0, NULL, 1, 0.00, 340.00, 0, '', 22, 10, '2024-05-11 22:55:09'),
(587, 'FARM', 'CETAFOR ceftriaxone 1000mg IV', 13, NULL, 1, 0.00, 231.00, 0, '31/05/2027', 23, 12, '2025-03-28 09:24:04'),
(588, 'FARM', 'CETAFOR ceftriaxone+lidocaina 1000mg IM', 8, NULL, 1, 0.00, 242.00, 0, '08/2025', 23, 12, '2024-11-15 09:32:20'),
(589, 'FARM', 'GALCORT 100mg INJECTAVEL', 0, NULL, 1, 0.00, 58.00, 0, '03/25', 23, 8, '2025-01-21 10:03:23'),
(590, 'FARM', 'Diclofenac injectavel 75mg/3ml', 5, 5, 1, 10.00, 24.00, 0, '01/26', 23, 13, '2025-05-08 08:54:13'),
(591, 'FARM', 'PRIMACORT -100 INJECTAVEL', 1, NULL, 1, 0.00, 131.00, 0, '10/25', 23, 12, '2024-11-15 11:01:34'),
(592, 'FARM', 'SISTEMA DE SORO', 7, 5, 1, 21.00, 26.00, 0, '07/2028', 23, 15, '2025-09-03 16:58:36'),
(593, 'FARM', 'ANABEL WIPES', 0, NULL, 1, 0.00, 175.00, 0, '11/28', 17, 11, '2024-10-17 17:15:14'),
(594, 'FARM', 'LUVAS DE EXAMINACAO HI-CARE tamanho M', 0, NULL, 1, 0.00, 790.00, 0, '05/26', 21, 12, '2024-08-15 12:58:33'),
(598, 'FARM', 'GEN CARE LUVAS DE LATEX M', 13, NULL, 1, 0.00, 5.22, 0, '10/26', 22, 8, '2025-01-28 04:53:42'),
(599, 'FARM', 'KANAMICINA 2G injectavel', 49, NULL, 1, 0.00, 138.00, 0, '12/25', 23, 14, '2025-01-30 03:30:51'),
(600, 'FARM', 'Soro glicose 5% 1000 ml balao fresenios kabi', 0, NULL, 1, 0.00, 178.80, 0, '', 23, 14, '2025-01-22 02:19:51'),
(601, 'FARM', 'Soro Fisiologico 0.9% 1000ml BASI', 6, 4, 1, 100.00, 207.00, 0, '30/11/2026', 23, 8, '2025-06-13 06:37:53'),
(603, 'FARM', 'Soro glicose 5% 500 ml balao fresenios kabi', 5, NULL, 1, 0.00, 147.51, 0, '', 23, 14, '2024-08-17 09:04:54'),
(604, 'FARM', 'Soro Glicose 5% 500ml balao BASI', 18, NULL, 1, 0.00, 92.00, 0, '', 23, 8, '2025-03-28 06:59:20'),
(605, 'FARM', 'I-WASH 100ml', 26, NULL, 1, 0.00, 134.00, 0, '08/2024', 16, 15, '2025-03-28 22:16:01'),
(606, 'FARM', 'NYTATIN-V nistatina comprimido vaginais 100000 U', 0, NULL, 1, 0.00, 103.00, 0, '', 16, 9, '2024-05-09 04:27:45'),
(607, 'FARM', 'TANTUM PROTECT MENTA FRESCA  250ml', 0, NULL, 1, 0.00, 250.00, 0, '11/25', 21, 12, '2025-01-28 04:23:52'),
(608, 'FARM', 'NALBIX CLOTRIMAZOL 100MG CP VAGINAL', 3, NULL, 1, 0.00, 312.00, 0, '30/09/2025', 16, 12, '2025-02-03 14:09:55'),
(609, 'FARM', 'DAFNEGIL NIFURATEL+NISTATINA OVULOS', 1, NULL, 1, 0.00, 1019.00, 0, '9/25', 16, 12, '2024-05-20 16:25:16'),
(610, 'FARM', 'VEGAMAX 50 mg', 40, 2, 1, 80.00, 108.50, 0, '30/11/2026', 18, 12, '2025-07-08 13:28:25'),
(611, 'FARM', 'VEGAMAX 100 mg', 38, 5, 1, 10.00, 77.00, 0, '06/24', 18, 12, '2025-07-08 13:29:17'),
(612, 'FARM', 'DZIRE GEL SAQUETA', 9, 35, 1, 0.00, 26.00, 0, '11/24', 18, 15, '2025-04-30 11:19:02'),
(613, 'FARM', 'DZIRE SILDENAFIL 100 MG CP', 262, 10, 1, 0.00, 82.00, 0, '30/04/2027', 18, 15, '2025-11-07 06:58:00'),
(614, 'FARM', 'AEROMAX NASAL', 2, NULL, 1, 0.00, 1405.00, 0, '07/25', 14, 12, '2024-08-19 23:33:32'),
(615, 'FARM', 'SOFO FISIOLOGICO NASAL 30 ML', 30, NULL, 1, 0.00, 100.00, 0, '', 14, 10, '2024-05-01 23:41:11'),
(616, 'FARM', 'SORO FISIOLOGICO NASAL 60 ML', 0, NULL, 1, 0.00, 115.00, 0, '12/2025', 14, 10, '2024-08-25 17:07:01'),
(617, 'FARM', 'FRAMOPTIC-D GOTAS OFT/AURI 10 ML', 5, NULL, 1, 0.00, 457.00, 0, '30/04/2027', 20, 12, '2025-01-25 13:51:22'),
(618, 'FARM', 'KET VISION COLIRIO', 0, NULL, 1, 0.00, 71.00, 0, '11/25', 20, 15, '2024-08-18 22:53:30'),
(619, 'FARM', 'CELL  VISION', 28, NULL, 1, 0.00, 80.00, 0, '11/25', 20, 15, '2025-03-27 09:53:55'),
(620, 'FARM', 'TOBRASON VISION COLIRIO', 106, NULL, 1, 0.00, 102.00, 0, '12/25', 20, 15, '2025-03-28 01:58:37'),
(621, 'FARM', 'ACE-PEN 500', 93, NULL, 1, 0.00, 63.00, 0, '', 9, 15, '2025-01-25 22:18:45'),
(622, 'FARM', 'TOPGYL 250 CP', 32, 0, 1, 0.00, 12.00, 0, '10/26', 9, 15, '2025-04-12 11:23:12'),
(623, 'FARM', 'ACEPHENICOL 250 MG CAPSULAS', 45, NULL, 1, 0.00, 68.00, 0, '12/25', 0, 15, '2025-01-31 02:07:50'),
(624, 'FARM', 'NEOFENICOL 250 MG CAPSULAS', 6, NULL, 1, 0.00, 44.44, 0, '06/2025', 9, 11, '2025-01-20 15:12:35'),
(625, 'Selecione o prefixo', 'NEONALDIX ACIDO NALDIXICO 500MG CP', 400, NULL, 1, 0.00, 81.51, 0, '07/25', 0, 12, '2025-01-31 16:01:01'),
(626, 'FARM', 'FASTRIM KIT 1COMBI KIT Gvind', 28, 7, 1, 2.00, 296.00, 0, '11/2025', 9, 15, '2025-07-16 12:55:09'),
(628, 'FARM', 'QUININOR-M 200MG  CP', 94, NULL, 1, 0.00, 54.00, 0, '30/10/2027', 9, 15, '2025-03-28 21:57:37'),
(629, 'FARM', 'BETRIM-480 CP', 8, NULL, 1, 0.00, 20.50, 0, '', 9, 12, '2025-02-01 01:20:40'),
(630, 'FARM', 'COTRIM 480 MG', 3, NULL, 1, 0.00, 20.07, 0, '', 9, 11, '2024-12-12 11:07:23'),
(631, 'FARM', 'ACETRIM CTZ 480 CP', 120, 0, 1, 0.00, 20.00, 0, '30/04//2027', 9, 15, '2025-11-07 08:44:55'),
(632, 'FARM', 'FUNGIZOL FLUCONAZOL 200 CP', 40, NULL, 1, 0.00, 40.00, 0, '31/07/2026', 9, 13, '2025-01-31 19:35:56'),
(633, 'FARM', 'NEODOX  DOXICICLINA 100 MG CAP', 0, NULL, 1, 0.00, 29.00, 0, '31/01/2026', 9, 11, '2024-11-30 01:27:39'),
(634, 'FARM', 'ERYKO  500 ERITROMECINA CP', 183, NULL, 1, 0.00, 94.00, 0, '03/2027', 9, 14, '2025-02-03 00:42:49'),
(635, 'FARM', 'ENRINOCIN ERITROMECINA 500MG CP', 73, NULL, 1, 0.00, 108.00, 0, '', 9, 15, '2025-02-03 14:01:11'),
(636, 'FARM', 'COTTON WOOL ALGODAO 50G ', 0, NULL, 1, 0.00, 96.00, 0, '', 22, 12, '2024-07-28 10:56:10'),
(637, 'FARM', 'ELASTIC ADHESIV BANDAGE 15CMX4.5 M LIGADURA ELASTICA', 2, NULL, 1, 0.00, 444.00, 0, '11/2025', 21, 14, '2025-03-27 19:36:29'),
(638, 'FARM', 'ELASTIC ADHESIV BANDAGE 10CMX4.5 M LIGADURA ELASTICA', 1, NULL, 1, 0.00, 263.00, 0, '11/25', 22, 14, '2025-01-11 01:32:02'),
(639, 'FARM', 'ELASTIC ADHESIV BANDAGE 7.5CMX4.5 M LIGADURA ELASTICA', 1, NULL, 1, 0.00, 228.00, 0, '11/2025', 21, 14, '2025-03-27 14:22:20'),
(640, 'FARM', 'ELASTIC ADHESIV BANDAGE 5CMX4.5 M LIGADURA ELASTICA', 4, NULL, 1, 0.00, 504.00, 0, '11/25', 22, 14, '2024-10-30 00:40:11'),
(641, 'FARM', 'BENZOSOL BENZOATO DE BENZILO 25%  100 ML', 1, NULL, 1, 0.00, 136.00, 0, '', 17, 15, '2025-01-23 20:22:29'),
(642, 'FARM', 'IODOPOVIDONA 10% SOLUCAO 125 ML', 16, NULL, 1, 0.00, 460.00, 0, '', 17, 12, '2025-01-30 23:39:39'),
(643, 'FARM', 'KCI PHARMA SOLUCAO DE VIOLETA 1%W/V 25 ML', 35, NULL, 1, 0.00, 77.06, 0, '', 17, 11, '2025-01-31 16:01:01'),
(644, 'FARM', 'LUVAS CIRURGICAS A+M GLOVES PAR', 25, NULL, 1, 0.00, 36.00, 0, '', 21, 14, '2025-03-28 14:45:26'),
(645, 'FARM', 'LUVAS DE EXAMINACAO HEALTHEASE 1OO  TAMANHO M', 0, 2, 1, 0.00, 621.00, 0, '30/06/2026', 21, 11, '2025-05-10 06:48:41'),
(646, 'FARM', 'LUVAS DE EXAMINACAO TAMANHO L', 0, NULL, 1, 0.00, 727.00, 0, '', 22, 14, '2025-01-16 22:31:39'),
(647, 'FARM', 'MASCARAS HEALTHESEASE', 13, NULL, 1, 0.00, 10.00, 0, '', 21, 11, '2024-08-17 02:06:53'),
(648, 'FARM', 'LUVAS CIRURGICAS COM PO 8', 6, NULL, 1, 0.00, 37.00, 0, '', 22, 8, '2024-07-27 11:09:14'),
(649, 'FARM', 'ABSORBENT COTTON 50G BASTOS VIEGAS ', 0, NULL, 1, 0.00, 94.00, 0, '10/26', 22, 8, '2024-10-19 14:44:58'),
(650, 'FARM', 'COMPRESS DE GAUSES 100X1OOML PECAS HELTHEASE', 0, NULL, 1, 0.00, 48.00, 0, '', 21, 11, '2024-07-06 12:12:05'),
(651, 'FARM', 'ALGODAO HIDROFILO HEATHEASE', 8, NULL, 1, 0.00, 80.00, 0, '', 21, 11, '2024-07-25 12:07:36'),
(652, 'FARM', 'BIOEARTH LABORATORIES 40G 100MMX100MM', 2, NULL, 1, 0.00, 179.00, 0, '', 21, 12, '2025-01-30 15:17:30'),
(653, 'FARM', 'ALGODAO HIDROFILO 2OOG GENERICS', 0, NULL, 1, 0.00, 210.00, 0, '', 22, 8, '2024-08-29 19:01:36'),
(654, 'FARM', 'ALGODAO HIDROFILO 1OOG  GENERIC', 0, NULL, 1, 0.00, 136.00, 0, '', 22, 8, '2025-03-27 03:48:15'),
(655, 'FARM', 'BECOSHEL XAROPE 100ML', 47, NULL, 1, 0.00, 147.00, 0, '02/2026', 15, 14, '2025-01-20 06:28:17'),
(656, 'FARM', 'ACE-C 500 VITAMINA C COMP CP', 13, NULL, 1, 0.00, 34.00, 0, '09/2025', 15, 13, '2025-02-01 02:22:44'),
(657, 'FARM', 'CYPROPLEX XAROPE 200 ML', 29, NULL, 1, 0.00, 163.00, 0, '01/2027', 15, 13, '2025-03-27 04:43:38'),
(658, 'FARM', 'ACEVIT XAROPE 200 ML', 229, NULL, 1, 0.00, 148.00, 0, '09/2025', 15, 13, '2025-03-28 21:42:12'),
(659, 'FARM', 'HEMOVIT XAROPE 225 ML ', 90, NULL, 1, 0.00, 186.00, 0, '', 15, 11, '2025-02-03 08:52:17'),
(660, 'FARM', 'MOMVIT PREGNANCY 60 CP', 2, NULL, 1, 0.00, 112.00, 0, '', 15, 11, '2024-11-03 02:39:42'),
(661, 'FARM', 'KAP KAP BALSAMO HEALTHESE', 50, NULL, 1, 0.00, 63.00, 0, '', 10, 11, '2025-03-28 06:40:37'),
(662, 'FARM', 'MYPRODOL XAROPE 100 ML', 24, NULL, 1, 0.00, 154.00, 0, '05/2025', 10, 12, '2025-01-27 06:09:50'),
(663, 'FARM', 'BALSAMO analgesico 50 G BASI  61,1/MG', 0, 2, 1, 200.00, 348.00, 0, '02/2027', 22, 8, '2025-05-22 05:55:50'),
(664, 'FARM', 'BALSAMO ANALGESICO BASI61,1 20G', 0, 5, 1, 130.70, 194.00, 0, '09/26', 10, 8, '2025-08-15 10:43:21'),
(665, 'FARM', 'DICLOFENAC AZEVEDO GEL 10 mg/g 100 G', 10, 2, 1, 400.00, 536.00, 0, '05/25', 10, 12, '2025-05-28 13:32:54'),
(667, 'FARM', 'DAGESIL BASI 10 100G GEL', 13, NULL, 1, 0.00, 289.00, 0, '6/2026', 10, 8, '2025-02-02 22:02:45'),
(668, 'FARM', 'BALSAMO ANALGESICO 40 gestafarma Creme', 5, NULL, 1, 0.00, 330.00, 0, '05/2027', 10, 12, '2024-12-08 12:50:34'),
(669, 'FARM', 'BENYLIN WITH CODEINE Frs. X1OOml', 40, NULL, 1, 0.00, 511.00, 0, '8/25', 11, 12, '2025-02-03 17:12:57'),
(671, 'FARM', 'PANADO caixa 1x12 CP', 0, NULL, 1, 0.00, 99.00, 0, '09/2026', 9, 12, '2025-01-05 01:24:26'),
(672, 'FARM', 'COLDRIL XAROPE 1OOML', 125, NULL, 1, 0.00, 239.00, 0, '08/2027', 11, 14, '2025-03-28 14:16:42'),
(673, 'FARM', 'POVIGEN IODOPOVIDONA SOLUCAO 150 5%P/V', 41, NULL, 1, 0.00, 97.00, 0, '09/2026', 17, 8, '2025-03-28 16:29:16'),
(674, 'FARM', 'MAQUINA DE PRESSAO ARTERIAL AMARELA', 0, NULL, 1, 0.00, 2310.00, 0, '', 21, 8, '2024-08-28 05:51:50'),
(675, 'FARM', 'MAQUINA DE PRESSAO ARTERIAL CINZA', 2, NULL, 1, 0.00, 2384.00, 0, '', 21, 8, '2025-01-20 07:55:56'),
(677, 'FARM', 'TOSSEQUE 2OOML', 6, NULL, 1, 0.00, 442.00, 0, '5/26', 0, 12, '2025-01-27 09:40:24'),
(678, 'FARM', 'POLLENTYME  XAROPE 100ML', 0, NULL, 1, 0.00, 318.00, 0, '9/26', 14, 10, '2025-01-20 16:27:25'),
(679, '', 'TUSSILENE 2MG/ML SOLUCAO ORAl', 0, 0, 1, 0.00, 437.00, 0, '5/26', 0, 0, '2025-04-01 10:43:44'),
(680, 'FARM', 'DESLORATADINA AZEVEDOS 0,5mg/ml solucao oral', 0, NULL, 1, 0.00, 469.00, 0, '5/25', 11, 12, '2025-01-29 16:12:25'),
(681, 'FARM', 'MENTOCAINA R 20 PASTILHAS MENTOL', 5, 5, 1, 209.00, 158.00, 0, '05/2027', 10, 12, '2025-09-25 12:41:06'),
(682, 'FARM', 'EFFERFLU C COLD EFLU', 0, NULL, 1, 0.00, 265.00, 0, '07/2026', 11, 10, '2024-06-16 08:14:02'),
(684, 'FARM', 'PARCIDO ANTIACIDO SUSP 200ML', 20, NULL, 1, 0.00, 134.00, 0, '', 13, 8, '2024-05-02 20:49:57'),
(685, 'FARM', 'PARACIDO ANTIACIDO SUSP 200ML', 1, NULL, 1, 0.00, 134.00, 0, '11/2025', 13, 8, '2024-09-01 06:34:47'),
(686, 'FARM', 'NALGEN-S NALDIXIC ACID ORAL SUSP BP100ML', 0, NULL, 1, 0.00, 127.00, 0, '12/2025', 9, 8, '2025-01-30 12:14:17'),
(689, 'FARM', 'POVIGEN POMADA 20g', 94, 20, 1, 28.00, 48.00, 0, '09/2027', 17, 8, '2025-05-05 08:00:04'),
(690, 'FARM', 'ESTETOSCOPIO NORMAL', 2, NULL, 1, 0.00, 1341.00, 0, '', 22, 8, '2024-05-02 21:22:41'),
(691, 'FARM', 'ESTETOSCOPIO MEDIO', 0, NULL, 1, 0.00, 1192.00, 0, '', 22, 8, '2024-07-03 10:38:12'),
(692, 'FARM', 'BIOLECTRA CALCIO  500mg', 0, NULL, 1, 0.00, 59.60, 0, '05/2024', 21, 12, '2025-02-01 15:05:45'),
(693, 'FARM', 'BIOLECTRA MAGNESIUM 243 mg', 1, NULL, 1, 0.00, 73.90, 0, '03/2026', 21, 12, '2024-12-10 18:35:13'),
(694, 'FARM', 'BIO - RITMO', 0, NULL, 1, 0.00, 82.30, 0, '03/2025', 22, 12, '2025-01-20 06:02:28'),
(695, 'FARM', '0PTIMUS suplemento alimenatr', 6, NULL, 1, 0.00, 357.00, 0, '', 21, 0, '2024-05-03 11:41:24'),
(697, 'FARM', 'ZINPLEX JUNIOR SYRUP 200ML', 0, NULL, 1, 0.00, 464.00, 0, '30/04/2025', 21, 12, '2025-01-20 15:30:57'),
(698, 'Selecione o prefixo', 'ZINPLEX JUNIOR VITAMINC &VITAMIN D3', 6, NULL, 1, 0.00, 464.00, 0, '31/04/2025', 15, 12, '2025-03-28 09:16:10'),
(699, 'FARM', 'BILOBAN  40mg', 2, NULL, 1, 0.00, 313.00, 0, '', 22, 12, '2024-05-03 11:52:24'),
(700, 'FARM', 'SPERMOTREND CAPSULAS 450 m', 2, 2, 1, 562.00, 1597.00, 0, '30/08/2028', 15, 12, '2025-07-03 14:51:55'),
(701, 'FARM', 'DICLOFENAC GEL 20g BP 1.0% W/W', 44, NULL, 1, 0.00, 51.00, 0, '08/2026', 17, 12, '2025-02-03 09:01:59'),
(702, 'FARM', 'HISTAGEN Comp. 4mg CLORFENIRAMINE', 12, 0, 1, 0.00, 10.00, 0, '10/26', 11, 8, '2025-11-07 09:15:08'),
(703, 'FARM', 'Amoxicilina Capsula BP 250 Mg', 21, NULL, 1, 0.00, 26.82, 0, '04/26', 9, 8, '2024-06-01 06:29:46'),
(704, 'FARM', 'MOXMOD 500 CAP', 0, 20, 1, 12.00, 37.50, 0, '06/25', 9, 8, '2025-06-03 14:35:30'),
(707, 'FARM', 'Clavugen 625 amoxicilina e clavulanato de potassio', 0, NULL, 1, 0.00, 205.00, 0, '', 9, 8, '2025-01-11 07:13:53'),
(708, 'FARM', 'M0D\'S RELIEF FD Cp', 47, NULL, 1, 0.00, 23.00, 0, '', 9, 8, '2024-05-10 11:24:25'),
(709, 'FARM', 'MODS RELIEF FD CP', 3, 50, 1, 23.00, 33.00, 0, '31/10/27', 9, 8, '2025-10-11 13:19:38'),
(710, 'FARM', 'STETHOSCOPE', 1, NULL, 1, 0.00, 1192.00, 0, '', 21, 8, '2024-05-03 14:24:49'),
(711, 'FARM', 'STETHOSCOPE YUWELL', 1, NULL, 1, 0.00, 1788.00, 0, '', 21, 8, '2024-05-03 14:26:36'),
(714, 'FARM', 'ACENAC 50 comprimido diclofenac ', 8, 0, 1, 0.00, 10.00, 0, '30/03/2027', 10, 13, '2025-11-07 08:11:16'),
(715, 'FARM', 'ARTEMETHER E LUMEFANTRINE (METADE)', 0, NULL, 1, 0.00, 90.00, 0, '06/2026', 12, 9, '2024-05-22 11:17:34'),
(716, 'FARM', 'CYPROPLEX Plus-MULTIVITAMINA', 139, 50, 1, 80.00, 119.00, 0, '01/2026', 15, 15, '2025-07-16 12:32:16'),
(717, 'FARM', 'SKDERM CREME 30g', 285, 0, 1, 0.00, 100.00, 0, '06/2026', 17, 9, '2025-11-06 12:17:51'),
(718, 'FARM', 'TOPGYL -SUSPENSAO 125mg/5ml , 100ML', 114, 5, 1, 0.00, 79.00, 0, '30/01/2027', 9, 13, '2025-11-07 08:29:09'),
(719, 'FARM', 'COLICAID GOTAS INFANTIS 15 ML', 116, NULL, 1, 0.00, 67.00, 0, '12/24', 11, 9, '2025-03-27 09:06:52'),
(720, 'FARM', 'AGUA DIGESTIVA - GRIPE WATER GvD', 16, 10, 1, 80.00, 118.00, 0, '0925', 11, 0, '2025-06-27 14:02:32'),
(721, 'FARM', 'PARA RAPIDO TABS', 2126, NULL, 1, 0.00, 8.00, 0, '02/26', 9, 9, '2025-03-28 23:55:44'),
(722, 'FARM', 'ACEFEN 400 IBUPROFENO CP', 43, 20, 1, 10.00, 20.00, 0, '31/05/2027', 9, 13, '2025-11-06 12:21:35'),
(724, 'FARM', 'NEOMETACIN', 12, NULL, 1, 0.00, 18.00, 0, '', 10, 11, '2024-06-15 11:23:43'),
(725, 'FARM', 'ROYALS GRIPE WATER', 5, NULL, 1, 0.00, 113.00, 0, '07/25', 11, 9, '2025-02-03 07:26:56'),
(726, 'FARM', 'KOFLYN MORANGO', 166, 0, 1, 0.00, 142.00, 0, '08/2027', 14, 14, '2025-11-07 10:19:18'),
(727, 'FARM', 'KOFLYN ANANAS', 196, NULL, 1, 0.00, 155.00, 0, '08/2027', 0, 14, '2025-03-28 22:01:14'),
(728, 'FARM', 'PANADO STRAWBERRY XAROPE (morango)', 20, NULL, 1, 0.00, 283.00, 0, '31/03/2026', 9, 12, '2025-01-25 13:51:23'),
(729, 'FARM', 'PARACETAMOL ALGIK 32MG/ML', 5, NULL, 1, 0.00, 375.00, 0, '12/2024', 9, 12, '2025-01-20 08:52:08'),
(730, 'FARM', 'IBUPROFENO ALGIK 20 MG/ML', 0, NULL, 1, 0.00, 384.00, 0, '04/2026', 10, 12, '2025-01-20 05:26:09'),
(731, 'FARM', 'MUCOLYN PEDIATRICO', 255, NULL, 1, 0.00, 186.00, 0, '08/2027', 14, 14, '2025-03-28 20:41:16'),
(732, 'Selecione o prefixo', 'MUCOLYN ADULTO XAROPE', 157, NULL, 1, 0.00, 186.00, 0, '08/2027', 14, 14, '2025-03-28 03:01:09'),
(733, 'FARM', 'MULTIVITAMINA BASI', 12, NULL, 1, 0.00, 231.00, 0, '03/2026', 15, 8, '2025-01-05 13:06:05'),
(734, 'FARM', 'COMPLEXO B XAROPE BASI', 0, NULL, 1, 0.00, 186.00, 0, '03/2026', 15, 8, '2024-06-12 07:00:45'),
(735, 'FARM', 'OROFER XAROPE', 8, NULL, 1, 0.00, 158.00, 0, '', 15, 10, '2024-12-09 10:44:17'),
(736, 'FARM', 'CLOTRIMAZOLE BASI creme 10mg/g', 8, NULL, 1, 0.00, 146.00, 0, '', 17, 8, '2025-03-28 22:42:54'),
(737, 'FARM', 'NEOFAGE', 1, NULL, 1, 0.00, 23.00, 0, '5/26', 19, 11, '2024-12-13 03:20:38'),
(738, 'FARM', 'DERMOSONE S POMADA 15g', 2, 10, 1, 170.00, 79.00, 0, '28/02/2026', 17, 8, '2025-11-07 09:08:27'),
(739, 'FARM', 'CLOTRI-DENK 1% CREMW', 0, NULL, 1, 0.00, 170.00, 0, '08/26', 17, 9, '2024-05-22 09:31:23'),
(740, 'FARM', 'EKISEM', 23, NULL, 1, 0.00, 221.00, 0, '', 17, 8, '2024-09-05 07:20:37'),
(741, 'FARM', 'Healthease ANTI - HAEMORRHOIDAL CREME 30 G', 1, NULL, 1, 0.00, 107.00, 0, '', 17, 11, '2024-12-10 00:55:13'),
(742, 'FARM', 'HEMOFISSURAL PASTA X 20g', 1, 1, 1, 500.00, 609.00, 0, '01/30/2026', 17, 12, '2025-05-03 07:51:52'),
(743, 'FARM', 'DUOSKIN Creme Tub.X 15g', 0, NULL, 1, 0.00, 560.00, 0, '', 17, 12, '2025-01-07 00:06:46'),
(744, 'FARM', 'DERMICIDE Acido Fusidico creme BP 2%w/w', 42, NULL, 1, 0.00, 132.00, 0, '31/08/2027', 17, 15, '2025-02-01 11:12:54'),
(745, 'FARM', 'VENOSMIL', 0, NULL, 1, 0.00, 1173.00, 0, '', 17, 12, '2025-01-07 03:10:24'),
(746, 'FARM', 'SULFADIAZINA DE PRATA 1%CREME, TUBO X 30GR', 9, 5, 1, 187.00, 277.00, 0, '02/2026', 17, 12, '2025-09-25 12:23:10'),
(747, 'FARM', 'ZUDICORT CREME 15G', 19, NULL, 1, 0.00, 156.00, 0, '', 0, 11, '2024-11-30 01:27:39'),
(748, 'FARM', 'TIABENDAZOL POMADA 5% 30G', 0, NULL, 1, 0.00, 499.00, 0, '', 17, 0, '2025-01-26 02:08:16'),
(749, 'FARM', 'CANDIDERM CREME', 51, NULL, 1, 0.00, 119.00, 0, '', 17, 11, '2025-01-31 16:01:01'),
(750, 'FARM', 'CANDID-B CREME', 44, NULL, 1, 0.00, 97.00, 0, '', 17, 11, '2025-01-14 21:00:00'),
(755, 'FARM', 'ACEPEN 125 SUSP', 1, 0, 1, 0.00, 89.00, 0, '08/2025', 0, 13, '2025-11-07 08:33:07'),
(756, 'FARM', 'ACEPEN 250, 100ML  SUSP', 8, 0, 1, 0.00, 120.00, 0, '10/2025', 9, 13, '2025-11-07 08:37:10'),
(758, 'FARM', 'CALADIN  Calamina  100ML', 5, NULL, 1, 79.00, 118.00, 0, '11/2026', 0, 8, '2025-02-03 08:15:22'),
(759, 'FARM', 'MULTIGEN-S XAROPE 100 ML', 4, NULL, 1, 0.00, 82.00, 0, '06/2025', 15, 8, '2025-01-20 15:27:34'),
(760, 'FARM', 'PHENIREX clorofenamina CP ', 39, 10, 1, 5.00, 10.00, 0, '31/07/2026', 10, 13, '2025-11-10 13:59:13'),
(761, 'FARM', 'REQUILITY SRO oral', 0, NULL, 1, 0.00, 17.00, 0, '10/2026', 13, 15, '2025-01-30 04:54:00'),
(762, 'FARM', 'TOSSIL MENTA', 579, NULL, 1, 0.00, 3.00, 0, '31/07/27', 14, 8, '2025-03-28 23:54:08'),
(763, 'FARM', 'ACEMOL-S 125  SUSPENSAO ', 0, NULL, 1, 0.00, 68.00, 0, '07/2026', 9, 8, '2025-01-07 15:25:36'),
(764, 'FARM', 'CURAMOL', 47, NULL, 1, 0.00, 66.00, 0, '', 10, 14, '2025-03-28 18:57:29'),
(767, 'FARM', 'MUCOCLEAR ADULTO', 20, NULL, 1, 0.00, 133.02, 0, '', 0, 15, '2024-12-06 00:53:13'),
(768, 'FARM', 'PROMETAZINA SOLUCAO ORAL', 8, NULL, 1, 0.00, 134.00, 0, '08/2026', 13, 15, '2025-01-30 10:09:49'),
(769, 'FARM', 'URTICUR XAROPE 0,1 100ML', 0, NULL, 1, 0.00, 219.00, 0, '', 13, 8, '2024-05-17 02:27:57'),
(770, 'FARM', 'STOPAYNE XAROPE 100ML ', 0, NULL, 1, 0.00, 179.00, 0, '06/2025', 13, 12, '2025-01-11 13:47:22'),
(771, 'FARM', 'GENPRAZOL', 17, NULL, 1, 0.00, 22.00, 0, '', 22, 8, '2024-09-04 03:03:52'),
(772, 'FARM', 'SULFATO DE ZINCO COMP 20mg', 198, NULL, 1, 0.00, 48.87, 0, '10/25', 15, 14, '2025-03-28 18:42:54'),
(773, 'FARM', 'AZICURE 200,AZITROMICINA 200 MG/5ML,FRS 30 ML', 2, NULL, 1, 0.00, 317.00, 0, '07/25', 22, 12, '2024-08-24 17:09:15'),
(774, 'FARM', 'NEOBENDEX 1x6', 0, NULL, 1, 0.00, 11.00, 0, '05/2026', 13, 11, '2025-01-30 12:27:00'),
(775, 'FARM', 'Z-BEN ALBENDAZOL ORAL10 ML', 5, NULL, 1, 0.00, 45.00, 0, '05/25', 13, 11, '2025-01-20 19:43:06'),
(776, 'Selecione o prefixo', 'ALBENDAZOLE 400MG', 2, NULL, 1, 0.00, 23.00, 0, '06/2026', 13, 11, '2025-01-22 01:24:38'),
(777, 'Selecione o prefixo', 'AMOX125 ACE PO SUSP', 0, 0, 1, 0.00, 66.00, 0, '8/25', 10, 8, '2025-11-07 08:31:37'),
(778, 'FARM', 'MODBEN ALBENDAZOL 400MG', 0, NULL, 1, 0.00, 12.00, 0, '10/2025', 13, 10, '2024-08-28 00:25:12'),
(779, 'FARM', 'ACETRIM-S PAEDIATRIC CO-TRIMOXAZOL SUSPESAO ORAL', 0, 5, 1, 0.00, 74.00, 0, '06/2026', 14, 15, '2025-09-17 10:24:44'),
(781, 'FARM', 'AMOXILINA 250 xarope seco 100ML', 57, NULL, 1, 0.00, 119.00, 0, '', 13, 15, '2024-07-14 07:36:41'),
(782, 'FARM', 'BUTAMOL XAROPE', 3, NULL, 1, 0.00, 92.38, 0, '05/25', 14, 14, '2024-11-01 15:15:18'),
(783, 'FARM', 'NOVOLUX BISACODYL 5mg', 31, NULL, 1, 0.00, 2.00, 0, '05/2025', 13, 11, '2025-01-26 19:23:58'),
(784, 'FARM', 'FLUCLOXACILINA AZEVEDO 500 MG CAPSULAS', 0, NULL, 1, 0.00, 223.60, 0, '', 21, 12, '2024-08-17 23:05:24'),
(785, 'FARM', 'HISTAGEN -SUSP 100ML ', 0, 5, 1, 0.00, 104.00, 0, '30/08/2026', 11, 8, '2025-11-07 09:18:51'),
(786, 'FARM', 'TRIAZOL FLUCONAZOL 150 MG', 0, NULL, 1, 0.00, 238.00, 0, '', 9, 12, '2024-11-30 01:27:38'),
(789, 'FARM', 'AMOXI-DENK 500', 11, NULL, 1, 0.00, 237.00, 0, '09/2026', 9, 16, '2025-01-30 22:58:02'),
(790, 'FARM', 'CORVITE VITAMINA C 20 ML', 7, NULL, 1, 0.00, 194.00, 0, '09/2025', 15, 8, '2024-11-30 06:36:59'),
(791, 'FARM', 'C0-ARTESINE ', 2, NULL, 1, 0.00, 729.00, 0, '', 0, 12, '2024-05-07 14:54:29'),
(792, 'FARM', 'CIPRONAT 500 MG CP', 60, NULL, 1, 0.00, 19.00, 0, '31/01/25', 21, 12, '2024-12-13 09:42:37'),
(793, 'FARM', 'P-ZONE Comp. 5MG', 66, NULL, 1, 0.00, 29.00, 0, '06/25', 10, 14, '2024-11-03 02:34:50'),
(794, 'FARM', 'SALBUTAMOL COMP. 4 MG', 0, NULL, 1, 0.00, 7.45, 0, '11/24', 18, 14, '2024-06-23 12:38:36'),
(795, 'FARM', 'SALBUTAMOL TABLET B.P.4.MG', 0, NULL, 1, 0.00, 9.50, 0, '06/2025', 18, 14, '2025-01-21 13:33:35'),
(796, 'FARM', 'PAUSE-500 Acido tranexamico comprimido BP', 48, NULL, 1, 0.00, 162.33, 0, '30/09/2025', 21, 12, '2025-02-03 12:11:40'),
(797, 'FARM', 'SALBUREST S', 1, 0, 1, 60.00, 90.00, 0, '05/2026', 18, 15, '2025-11-10 14:18:47'),
(798, 'FARM', 'NICOF 100ML', 114, NULL, 1, 0.00, 78.00, 0, '06/2026', 0, 14, '2025-03-28 05:08:32'),
(799, 'FARM', 'COARTEM 20MG/120MG AZEVEDOS', 0, NULL, 1, 0.00, 145.00, 0, '08/2025', 12, 12, '2024-05-30 04:08:47'),
(800, 'EMPRE', 'ACERIDE 5mg COMP', 37, NULL, 1, 0.00, 13.45, 0, '11/2025', 13, 15, '2025-01-12 12:02:54'),
(801, 'FARM', 'DESCONTRAN 500mg/2mg,CxSX30 CPS', 28, NULL, 1, 0.00, 155.00, 0, '06/2026', 9, 8, '2025-03-28 11:37:48'),
(802, 'FARM', 'CINON FORTE', 0, NULL, 1, 0.00, 85.00, 0, '', 9, 12, '2025-01-31 20:27:45'),
(803, 'FARM', 'SNIP,CXS 20 COMP', 32, NULL, 1, 0.00, 103.00, 0, '11/2027', 11, 12, '2024-08-03 12:47:36'),
(804, 'FARM', 'VOMIDRINE 50mg Cxs X 20 comp', 20, 5, 1, 0.00, 116.00, 0, '30/06/2026', 13, 12, '2025-08-08 11:56:22'),
(805, 'FARM', 'VOMIDRINE Dimenidrinato 50mg DIRECT', 5, NULL, 1, 0.00, 598.00, 0, '30/06/2026', 13, 12, '2025-01-25 13:51:23'),
(806, 'FARM', 'TRIFENE 100mg/5ml suspensao oral FrX200ml', 3, NULL, 1, 0.00, 437.00, 0, '04/2028', 14, 12, '2025-01-11 00:41:37'),
(807, 'FARM', 'VIFEX XAROPE FrX100ml', 8, NULL, 1, 0.00, 87.00, 0, '30/06/26', 14, 12, '2025-03-27 23:10:58'),
(808, 'FARM', 'PEDIFEN Xarope Frs.x 100ml', 3, NULL, 1, 0.00, 278.00, 0, '03/2026', 14, 12, '2025-01-14 07:18:01'),
(809, 'FARM', 'PAROL 250mg/5ml susp.Frs.x', 1, 5, 1, 164.00, 243.00, 0, '31/10/2027', 10, 12, '2025-10-25 16:48:25'),
(810, 'FARM', 'PARAMOLAN susp.Oral frs.X200ML', 2, 5, 1, 270.00, 401.00, 0, '31/01/2028', 10, 12, '2025-09-24 19:04:37'),
(811, 'Selecione o prefixo', 'DICLOFORTE CP', 171, NULL, 1, 0.00, 18.00, 0, '02/2027', 10, 12, '2024-11-29 23:51:24'),
(812, 'FARM', 'DICLOFENAC 50mg-Cxs 10X10 com', 8, NULL, 1, 0.00, 8.80, 0, '', 10, 12, '2024-06-28 08:47:29'),
(813, 'Selecione o prefixo', 'AGUA OXIGENADA 3%,Frs x250 ml    MEDIS', 9, NULL, 1, 0.00, 96.00, 0, '31/07/2027', 22, 12, '2025-02-02 21:16:04'),
(814, 'FARM', 'ZARVITE 20mg TADALAFIL', 2, 5, 1, 500.00, 681.00, 0, '30/11/2026', 16, 8, '2025-07-08 09:54:05'),
(815, 'Selecione o prefixo', 'CETOCONAZOL 200MG CP', 47, NULL, 1, 0.00, 55.10, 0, '', 21, 8, '2024-11-22 16:11:26'),
(816, 'FARM', 'CLAVAMOX DT 875mg/125mg', 0, NULL, 1, 0.00, 296.70, 0, '', 9, 8, '2025-03-28 17:42:04'),
(817, 'FARM', 'PEPSAMAR 240 MG CP', 1, NULL, 1, 0.00, 59.66, 0, '', 21, 12, '2024-08-24 15:57:55'),
(820, 'FARM', 'ACEPEN 500 tablet', 0, 100, 1, 50.00, 63.00, 0, '10/25', 21, 15, '2025-06-13 10:19:42'),
(821, 'FARM', 'AMOXI 250 XAROPE', 14, NULL, 1, 0.00, 119.00, 0, '10/25', 9, 15, '2024-07-19 05:55:44'),
(822, 'Selecione o prefixo', 'OROFER CAPSULAS', 32, NULL, 1, 0.00, 41.00, 0, '28/02/2026', 15, 12, '2025-01-12 07:47:58'),
(823, 'FARM', 'GLUC0SE 5% BASI 500ML', 25, NULL, 1, 0.00, 92.00, 0, '', 22, 8, '2025-03-28 06:29:07'),
(824, 'FARM', 'PANADO PEPERMENT XAROPE (verde)', 9, NULL, 1, 0.00, 383.00, 0, '31/03/2026', 9, 12, '2025-01-27 18:17:05'),
(825, 'FARM', 'FOLINEED 5MG', 48, NULL, 1, 0.00, 5.91, 0, '', 22, 15, '2025-01-24 13:41:45'),
(826, 'FARM', 'HEALTHEASE LUVAS TAMANHO (M)', 242, NULL, 1, 0.00, 4.47, 0, '', 21, 8, '2025-03-28 23:03:28'),
(827, 'FARM', 'LATEX EXAMINATION  GLOVES (LARGE)', 99, NULL, 1, 0.00, 7.26, 0, '', 22, 8, '2024-08-16 05:33:09'),
(828, 'FARM', 'LATEX EXAMINATION  GL)', 200, NULL, 1, 0.00, 7.26, 0, '', 21, 0, '2024-05-19 17:04:48'),
(829, 'FARM', 'MUCOCLEAR INFANTIL', 0, 20, 3, 0.00, 109.00, 0, '31/10/2027', 11, 14, '2025-04-30 09:36:22'),
(830, 'FARM', 'DAPROFEN', 49, NULL, 1, 0.00, 82.00, 0, '', 22, 12, '2025-01-26 05:08:20'),
(831, 'FARM', 'HERMOFISSURAL PASTA CUTANEA 20G', 2, NULL, 1, 0.00, 609.00, 0, '', 17, 12, '2024-05-19 17:17:24'),
(832, 'FARM', 'VENASMILGEL 20MG/G', 1, NULL, 1, 0.00, 1173.00, 0, '', 17, 12, '2024-05-19 17:18:38'),
(833, 'FARM', 'CIPROFLOXACINA AZEVEDOS 500MG/', 0, NULL, 1, 0.00, 325.00, 0, '', 22, 12, '2025-01-30 15:15:17'),
(834, 'FARM', 'AMOXICILINA +ACIDO CLAVULANICO BLUEPHARMA', 74, NULL, 1, 0.00, 34.00, 0, '', 9, 8, '2024-05-19 17:41:41'),
(835, 'FARM', 'COMPRAL PAIN TABLETS', 0, NULL, 1, 0.00, 33.25, 0, '', 9, 12, '2025-01-22 22:20:46'),
(836, 'Selecione o prefixo', 'GASTROTAB 500MG', 2, 0, 1, 0.00, 10.00, 0, '320/06/2026', 21, 15, '2025-11-07 08:21:34'),
(837, 'FARM', 'AMUZOLE AMITRIPLILINA ', 1, NULL, 1, 0.00, 21.00, 0, '', 22, 12, '2024-05-24 12:08:17'),
(838, 'FARM', 'PANADO LAMINA', 26, NULL, 1, 0.00, 98.30, 0, '', 0, 12, '2025-01-26 17:40:41'),
(839, 'FARM', 'METHYLDOPA 250', 0, NULL, 1, 0.00, 99.83, 0, '', 21, 12, '2024-11-01 15:17:27'),
(840, 'FARM', 'SYNDOL 18', 4, NULL, 1, 0.00, 131.00, 0, '05/2026', 22, 15, '2025-01-25 16:56:50'),
(841, 'Selecione o prefixo', 'AMINOBRON', 0, NULL, 1, 0.00, 13.32, 0, '', 21, 15, '2024-05-30 02:38:41'),
(842, 'FARM', 'FUROMIDA ', 0, NULL, 1, 0.00, 13.33, 0, '', 0, 15, '2024-06-16 07:25:43'),
(843, 'FARM', 'SYNDOL 10', 28, NULL, 1, 0.00, 76.00, 0, '', 22, 12, '2025-03-27 09:02:11'),
(844, 'FARM', 'HEALTHEASE MOMVIT', 5, NULL, 1, 0.00, 112.00, 0, '', 22, 13, '2024-05-20 08:34:45'),
(845, 'FARM', 'HEALTHEASE COTTON WOOL 50G', 3, NULL, 1, 0.00, 80.00, 0, '', 21, 11, '2024-07-23 04:25:14'),
(846, 'FARM', 'LIGADURA DE GASE ABSORVENTE 10CMX3M', 23, NULL, 1, 0.00, 11.83, 0, '', 22, 8, '2025-03-28 16:29:16'),
(847, 'FARM', 'PLASTERMAN GASE 10X 4,5M', 12, NULL, 1, 0.00, 0.00, 0, '', 22, 11, '2024-05-20 09:00:07'),
(849, 'FARM', 'CARPLEXIL  hidrocortisona creme 30g', 0, 5, 1, 148.00, 218.00, 0, '28/02/2026', 17, 11, '2025-05-06 10:12:30'),
(850, 'FARM', 'HERPIRAX 400G', 0, 1, 1, 0.00, 222.00, 0, '31/01/2027', 21, 13, '2025-11-10 12:05:02'),
(851, 'FARM', 'CLOTRIMAZOL BASI 10 MG/ML SOLUCAO CUTANEA', 2, NULL, 1, 0.00, 177.00, 0, '', 17, 8, '2024-11-22 16:16:56'),
(852, 'FARM', 'NALBIX SOLUCAO CUTANEA  10 MG/ML', 4, NULL, 1, 0.00, 301.00, 0, '', 17, 13, '2025-03-28 10:01:10'),
(853, 'FARM', 'CANDID LOTION 20 ML - CLOTRIMAZOL', 18, NULL, 1, 0.00, 60.00, 0, '31/05/2026', 17, 13, '2025-01-24 19:33:56'),
(854, 'FARM', 'TRICEF 20 MG/ML PO PARA SOLUCAO ORAL', 2, 1, 1, 400.00, 650.00, 0, '12/30/2025', 13, 12, '2025-05-30 09:39:52'),
(855, 'FARM', 'CEFOLAC DRY SYRUP 100 MG/5 ML', 1, NULL, 1, 0.00, 592.00, 0, '', 13, 12, '2025-01-22 16:24:58'),
(856, 'FARM', 'CEFITON 100MG /5ML', 2, NULL, 1, 0.00, 826.00, 0, '', 13, 14, '2025-01-17 14:39:39'),
(857, 'FARM', 'CO-ARTESIANE 120 ML ', 1, NULL, 1, 0.00, 729.00, 0, '', 12, 12, '2024-06-06 07:11:05'),
(858, 'FARM', 'SPIROLON 25', 12, NULL, 1, 0.00, 146.02, 0, '', 13, 12, '2025-01-23 09:17:23'),
(859, 'FARM', 'PAROL 500MG COMP', 40, 5, 1, 101.00, 50.00, 0, '31/05/2029', 21, 12, '2025-09-25 12:46:56'),
(860, 'FARM', 'BEN-U-RON 500 MG', 4, NULL, 1, 0.00, 209.50, 0, '', 21, 13, '2024-05-20 09:35:36'),
(861, 'FARM', 'CELEBIB 200MG', 16, NULL, 1, 0.00, 142.33, 0, '', 22, 12, '2025-01-31 21:32:23'),
(862, 'FARM', 'PARACETAMOL AZEVEDOS 500MG', 13, 5, 1, 116.00, 86.00, 0, '30/04/2027', 9, 12, '2025-09-24 17:17:06'),
(863, 'FARM', 'MIGRETIL 1G+100MG+0,1MG.4OOMG', 0, 5, 1, 117.00, 173.00, 0, '31/08/2026', 9, 12, '2025-05-06 09:34:17'),
(864, 'FARM', 'STETHOSCOPE BRANCO VERDE', 2, NULL, 1, 0.00, 1341.00, 0, '', 22, 8, '2024-05-20 09:50:17'),
(865, 'FARM', 'STETHOSCOPE CINZA', 1, NULL, 1, 0.00, 1192.00, 0, '', 22, 8, '2024-05-20 09:51:40'),
(866, 'FARM', 'STETHOSCOPE BRANCO ', 1, NULL, 1, 0.00, 1788.00, 0, '', 21, 8, '2024-05-20 09:52:37'),
(867, 'FARM', 'OLEO DE AMENDOAS  DOCE TRANSPARENTE', 0, NULL, 1, 0.00, 306.00, 0, '', 13, 12, '2024-06-07 11:41:04'),
(868, 'FARM', 'Escova pierrot amarelo 2-8 anos', 1, NULL, 1, 0.00, 235.00, 0, '', 22, 14, '2024-05-20 10:01:15'),
(870, 'FARM', 'EDULCOR ADOCANTE 100 C', 1, NULL, 1, 0.00, 280.00, 0, '', 22, 10, '2024-05-20 10:12:18'),
(871, 'FARM', 'EDULCOR ADOCANTE 300 CP', 1, NULL, 1, 0.00, 500.00, 0, '', 22, 10, '2024-05-20 10:13:35'),
(872, 'FARM', 'PANADO FRASCO 500MG X24', 42, NULL, 1, 0.00, 103.00, 0, '02/2027', 9, 12, '2025-03-27 16:21:41'),
(873, 'FARM', 'SANKO PARACETAMOL 500 MG', 29, 5, 1, 0.00, 8.68, 0, '', 9, 12, '2025-08-08 11:15:05'),
(874, 'FARM', 'CALGEN-D', 8, NULL, 1, 0.00, 148.66, 0, '', 9, 12, '2025-01-14 14:21:37'),
(875, 'FARM', 'PARACETAMOL  GP -MG 1000MG', 0, NULL, 1, 0.00, 101.50, 0, '', 9, 12, '2024-12-10 22:05:17'),
(876, 'FARM', 'TRIFENE 200 CP', 0, NULL, 1, 0.00, 137.00, 0, '', 9, 12, '2024-11-19 18:06:49'),
(877, 'FARM', 'RHINO PAROL CP ', 0, NULL, 1, 0.00, 130.50, 0, '', 22, 8, '2024-06-12 11:18:01'),
(878, 'FARM', 'FOLCIL COMPRIMIDO DOXIDO FOLICO', 2, NULL, 1, 0.00, 78.66, 0, '', 21, 12, '2024-06-19 10:09:44'),
(879, 'FARM', 'PROXICAN  /CAPSULA USP 20 MG', 3, NULL, 1, 0.00, 24.00, 0, '', 22, 10, '2024-05-20 10:27:49'),
(880, 'FARM', 'LOPEREX 2MG', 0, NULL, 1, 0.00, 18.00, 0, '30/12/2026', 22, 14, '2025-01-08 03:25:12'),
(881, 'FARM', 'METOCLAPRAMIDA ', 35, 50, 1, 8.00, 12.00, 0, '30/11/2026', 22, 12, '2025-04-30 08:56:55'),
(883, 'FARM', 'REMOTIL 10 MG', 6, NULL, 1, 0.00, 251.00, 0, '', 21, 12, '2025-02-01 12:03:07'),
(885, 'FARM', 'ACEDINE 150 MG', 0, NULL, 1, 0.00, 23.93, 0, '', 22, 12, '2024-06-19 12:20:35'),
(886, 'FARM', 'SPERMOTREND CAPSULAS 450 mG', 1, NULL, 1, 0.00, 1597.00, 0, '', 21, 13, '2025-01-31 16:35:55'),
(887, 'FARM', 'BELOBAN 40 MG', 3, NULL, 1, 0.00, 312.66, 0, '', 22, 13, '2024-05-20 10:41:14'),
(890, 'FARM', 'CLAVAMOX 125 MG/31,25MG/5ML', 0, NULL, 1, 339.00, 502.00, 0, '30/04/2025', 21, 12, '2025-01-20 05:48:17'),
(891, 'FARM', 'CLAVAMOX 250 MG/62,5MG/5ML', 0, NULL, 1, 0.00, 887.00, 0, '', 21, 12, '2024-06-28 10:58:53'),
(892, 'FARM', 'AMDOCAL 5', 5, NULL, 1, 0.00, 25.00, 0, '', 22, 15, '2025-02-02 15:48:18'),
(893, 'FARM', 'ACECARDIA 30 MG', 9, 50, 1, 30.00, 48.00, 0, '30/09/26', 22, 13, '2025-04-30 11:14:36'),
(894, 'FARM', 'URIPRIM 100 MG', 0, NULL, 1, 0.00, 47.50, 0, '', 21, 14, '2024-07-24 12:09:13'),
(895, 'FARM', 'LISINOPRIL BLUEPHARMA 20 MG', 4, NULL, 1, 0.00, 127.00, 0, '', 22, 13, '2025-02-02 17:52:13'),
(896, 'Selecione o prefixo', 'FRUSEMIX 40', 0, 10, 1, 0.00, 15.00, 0, '30/04/2026', 22, 14, '2025-10-11 14:29:08'),
(897, 'FARM', 'SYTOPYN', 64, NULL, 1, 0.00, 32.70, 0, '', 21, 14, '2024-07-05 06:28:10'),
(898, 'FARM', 'OMEPRAZOL 20 MG AZEVEDOS (7 caps)', 13, NULL, 1, 0.00, 143.87, 0, '06/2025', 13, 12, '2025-02-03 14:29:47'),
(899, 'FARM', 'Gaviscon Duefet saqueta ', 4, NULL, 1, 0.00, 51.75, 0, '05/25', 13, 12, '2025-01-29 11:14:28'),
(900, 'FARM', 'Gaviscon susp. saqueta ', 32, NULL, 1, 0.00, 48.25, 0, '8/24', 13, 12, '2025-01-31 06:58:56'),
(901, 'FARM', 'PROMETAZINA 25 mg cp ACE', 3, NULL, 1, 0.00, 10.64, 0, '1/26', 13, 15, '2024-08-20 01:06:05'),
(902, 'FARM', 'GAVISCON COMPRIMIDOS', 2, NULL, 1, 0.00, 136.33, 0, '8/24', 13, 12, '2024-06-18 10:51:01'),
(903, 'FARM', 'ERGAGIN METHYLEGONOVITE', 64, NULL, 1, 0.00, 33.00, 0, '06/25', 18, 14, '2025-02-03 09:01:27'),
(904, 'FARM', 'BROT 500 MG COMP', 0, NULL, 1, 0.00, 55.00, 0, '07/24', 13, 12, '2024-07-21 09:02:20'),
(905, 'FARM', 'NIMED GEL 30/G 100 G', 1, NULL, 1, 0.00, 1403.00, 0, '05/2027', 10, 12, '2024-08-17 18:20:05'),
(906, 'FARM', 'PENSO RAPIDO Plasterman ', -1, NULL, 1, 0.00, 2.00, 0, '08/2028', 22, 11, '2024-08-09 06:37:33'),
(907, 'FARM', 'Penso Rapido PVC bandage ', 77, NULL, 1, 0.00, 2.00, 0, '10/2027', 22, 8, '2025-03-28 22:36:51'),
(908, 'FARM', 'STETHOSCOPE DELUXE', 1, NULL, 1, 0.00, 421.00, 0, '', 18, 12, '2024-06-05 04:30:30'),
(909, 'FARM', 'ACEPHENICOL SUSPENSAO ORAL', 23, NULL, 1, 0.00, 122.00, 0, '11/2026', 9, 15, '2025-02-01 11:17:23'),
(910, 'FARM', 'ERINOCIN 500 CP', 94, NULL, 1, 0.00, 108.00, 0, '08/2026', 9, 13, '2025-03-28 10:18:08'),
(911, 'FARM', 'DAWA-FLOX Fluocloxacilina Suspensao', 37, NULL, 1, 0.00, 142.00, 0, '08/2025', 9, 14, '2025-03-28 00:54:05'),
(912, 'FARM', 'GEN CARE LUVAS DE LATEX L', 78, NULL, 1, 0.00, 5.22, 0, '10/26', 22, 8, '2024-11-02 22:18:46'),
(913, 'FARM', 'ACEFEN -S  IBUPROFENO SUSP', 74, 0, 1, 0.00, 75.00, 0, '08/26', 10, 15, '2025-11-07 08:19:57'),
(914, 'FARM', 'AERO-OM GOTAS FRS x25ml', 0, 1, 1, 600.00, 628.00, 0, '02/26', 13, 12, '2025-08-12 07:22:36'),
(915, 'FARM', 'VERRUFILM 167mg/g SOL / CUT', 1, NULL, 1, 0.00, 557.00, 0, '12/24', 17, 12, '2024-05-20 14:39:15'),
(917, 'FARM', 'CANDID-B LOCAO 15 ML', 22, NULL, 1, 0.00, 82.00, 0, '04/25', 9, 11, '2025-01-29 08:52:46'),
(918, 'FARM', 'ALCOOL ETILICO 96% 250 ML', 0, 10, 1, 132.00, 177.00, 0, '11/28', 21, 12, '2025-11-07 07:25:48'),
(919, 'FARM', 'ABSORBENT COTTON  GAUZE BANDAGE 7.5X4M', 54, NULL, 1, 0.00, 14.64, 0, '11/26', 22, 14, '2024-05-20 14:52:03'),
(920, 'FARM', 'ABSORBENT COTTON  GAUZE BANDAGE 40X4M', 52, NULL, 1, 0.00, 17.45, 0, '11/26', 22, 14, '2025-01-11 08:50:39'),
(921, 'FARM', 'ABSORBENT COTTON  GAUZE BANDAGE 15X4M', 0, NULL, 1, 0.00, 24.99, 0, '11/26', 22, 14, '2024-06-16 13:01:27'),
(922, 'FARM', 'GENTAMICINA INJECTAVEL 80 mg/2ml', 5, 0, 1, 0.00, 244.00, 0, '04/26', 23, 9, '2025-11-07 07:06:43'),
(923, 'FARM', 'NIFELAT R 20mg CP', 0, NULL, 1, 0.00, 52.15, 0, '07/2026', 18, 14, '2025-01-28 11:08:35'),
(924, 'FARM', 'DECOMIT 100 PG 200 TIRAS', 0, NULL, 1, 0.00, 289.00, 0, '04/2025', 14, 11, '2024-07-20 12:41:53'),
(925, 'FARM', 'VONTOMAC 100 BOMBA 200 DOSES', 0, NULL, 1, 0.00, 347.00, 0, '07/2025', 14, 11, '2024-06-04 11:46:35'),
(926, 'FARM', 'POLLENTYME COMP ', 9, NULL, 1, 0.00, 139.66, 0, '06/2025', 11, 16, '2024-08-20 16:15:45'),
(927, 'FARM', 'CEREGUMIL AMPOLAS BEBIVEIS ', 20, NULL, 1, 0.00, 39.10, 0, '06/2024', 21, 12, '2025-01-21 15:51:23'),
(928, 'FARM', 'MAGNORAL AMPOLAS BEBIVEIS', 20, NULL, 1, 0.00, 36.25, 0, '09/2025', 22, 12, '2024-05-20 15:38:50'),
(929, 'FARM', 'GINSACTIV ENERGIE', 0, NULL, 1, 0.00, 75.75, 0, '04/2025', 22, 12, '2025-01-19 14:41:19'),
(930, 'FARM', 'CEFIMED 400 MG CP', 0, NULL, 1, 0.00, 84.50, 0, '02/2026', 9, 12, '2024-06-25 08:34:23'),
(931, 'FARM', 'ACETRIP 10', 27, 0, 1, 0.00, 14.00, 0, '05/25', 22, 13, '2025-11-07 07:53:19'),
(932, 'FARM', 'TRIPSUN 25', 8, NULL, 1, 0.00, 14.40, 0, '05/25', 22, 15, '2024-08-02 11:27:59'),
(933, 'FARM', 'FLUCOXACILINA AZEVEDOS 500 MG', 6, NULL, 1, 0.00, 219.33, 0, '09/25', 22, 12, '2024-05-20 15:58:26'),
(934, 'FARM', 'SORO FISIOLOGICO NASAL 30ML', 11, NULL, 1, 0.00, 100.00, 0, '02/26', 14, 14, '2025-01-31 03:51:36'),
(935, 'FARM', 'SINOMARIN', 10, NULL, 1, 0.00, 58.27, 0, '09/25', 22, 12, '2024-12-09 15:36:58'),
(936, 'FARM', 'FLUIDOX 30 MG COMP ', 2, NULL, 1, 0.00, 245.00, 0, '08/2025', 14, 12, '2025-01-29 04:49:32'),
(937, 'FARM', 'HERBAL LOZENGES LEMON HEALTHEASE', 77, 0, 1, 0.00, 20.00, 0, '07/2026', 14, 11, '2025-11-07 13:12:51'),
(938, 'FARM', 'HERBAL TUMERIC PASTILHAS HEALTHEASE', 44, 0, 1, 0.00, 20.00, 0, '31/07/2026', 14, 11, '2025-11-07 13:13:17'),
(939, 'FARM', 'CORENZA - C ', 0, NULL, 1, 0.00, 299.00, 0, '5/26', 14, 11, '2024-07-14 09:28:42'),
(940, 'FARM', 'WHITEFIELD ACE POMADA 30 G', 38, NULL, 1, 0.00, 82.00, 0, '05/2025', 17, 15, '2025-03-28 17:23:39'),
(941, 'FARM', 'GENLYTE SRO SAQUETA', 305, NULL, 1, 0.00, 17.00, 0, '004/2026', 22, 8, '2025-01-14 22:29:20'),
(942, 'FARM', 'ESPEN 200 CP', 3, NULL, 1, 0.00, 9.30, 0, '07/2024', 10, 12, '2024-07-18 11:42:19'),
(943, 'FARM', 'TESTE DE GRAVIDEZ HCG', 92, NULL, 1, 0.00, 60.00, 0, '19/02/2027', 22, 8, '2025-03-28 19:01:47'),
(944, 'FARM', 'MODFEN 100mg', 19, 5, 1, 9.00, 14.00, 0, '30/09/2027', 10, 8, '2025-10-11 14:28:31'),
(945, 'FARM', 'VITAVIN B1..B2..B3...B4...B5...B', 0, 5, 1, 0.00, 14.00, 0, '30/04/2027', 15, 8, '2025-08-11 11:45:13'),
(946, 'Selecione o prefixo', 'HERPIRAX 2OOG', 30, 0, 1, 0.00, 134.00, 0, '06/2026', 22, 13, '2025-11-10 12:06:10'),
(948, 'FARM', 'IPRED OFTALMICA 5 ML', 2, NULL, 1, 0.00, 112.00, 0, '8/26', 20, 8, '2024-08-26 11:46:04'),
(949, 'FARM', 'KETOCONAZOLE 200 MG CP ', 5, 5, 1, 366.00, 55.00, 0, '09/2027', 9, 8, '2025-10-14 09:08:30'),
(950, 'FARM', 'WHITEFIELD AC. SAL.+AC. BENZ. POMADA', 280, 5, 1, 70.00, 72.00, 0, '11/2026', 9, 8, '2025-11-07 10:02:21'),
(951, 'FARM', 'KANAMYCIN INJ 2MG Govind', 21, 20, 1, 110.00, 164.00, 0, '30/10/2026', 9, 8, '2025-05-27 14:55:51'),
(952, 'FARM', 'WATER FOR INJECTION basi', 88, 20, 1, 15.00, 22.00, 0, '30/09/2027', 22, 8, '2025-05-27 12:49:33'),
(953, 'FARM', 'TOSSIL LARANJA', 577, NULL, 1, 0.00, 3.00, 0, '31/03/2027', 14, 8, '2025-03-28 22:22:44'),
(954, 'FARM', 'FLUSIN C XAROPE', 1, NULL, 1, 0.00, 369.00, 0, '', 15, 8, '2024-06-25 11:38:32'),
(955, 'FARM', 'FOLICIL BIAL 5MG govind', 39, 10, 10, 52.50, 78.00, 0, '30/04/2027', 15, 8, '2025-06-03 14:15:51'),
(956, 'FARM', 'FOLIFER CP ', 0, 12, 10, 73.00, 107.00, 0, '28/02/2028', 18, 8, '2025-11-07 13:44:25'),
(957, 'FARM', 'DICLOFENAC GEL BLUEPHARMA ', 2, NULL, 1, 0.00, 361.00, 0, '', 10, 8, '2025-01-09 00:23:26'),
(958, 'FARM', 'BERMOLEX 600 MGCP', 0, NULL, 1, 0.00, 52.90, 0, '31/03/2025', 9, 12, '2024-08-23 09:15:11'),
(959, 'FARM', 'HALIBUT ORIGINAL POMADA 50 G', 0, 10, 1, 200.00, 417.00, 0, '31/10/2026', 17, 12, '2025-07-04 13:57:22'),
(960, 'FARM', 'PROTON 20 MG', 13, NULL, 1, 0.00, 115.00, 0, '', 13, 12, '2025-03-27 19:46:08'),
(961, 'FARM', 'TRIFENE 600 CP', 0, NULL, 1, 0.00, 105.00, 0, '', 10, 12, '2024-05-31 08:28:27'),
(962, 'FARM', 'COARTEM 2X6 DISPERSIVEL (120+20) MG', 8, NULL, 1, 0.00, 145.00, 0, '', 9, 12, '2025-01-11 09:42:38'),
(963, 'FARM', 'TINTURA DE IODO A 1%, 30ML', 29, NULL, 1, 0.00, 134.00, 0, '', 17, 12, '2025-02-03 06:19:36'),
(964, 'FARM', 'AERO - OM DIARREICO 1X12, CP 2MG', 1, NULL, 1, 0.00, 340.00, 0, '', 13, 12, '2024-05-24 01:52:46'),
(965, 'FARM', 'ALIVIADOR (500+50) MG CP', 230, 100, 1, 8.00, 10.00, 0, '31/08/2027', 10, 15, '2025-11-07 08:12:20'),
(966, 'FARM', 'GRISEOSHA 500 MG CP', 90, NULL, 1, 0.00, 85.00, 0, '01/2027', 9, 9, '2025-03-28 03:43:15'),
(967, 'FARM', 'COLDAFLU PLUS DR MEYER', 692, NULL, 1, 0.00, 18.00, 0, '', 11, 11, '2025-03-28 23:30:58'),
(968, 'FARM', 'COMYCETIN EAR DROPS 10 ML', 0, NULL, 1, 0.00, 70.00, 0, '10/26', 9, 11, '2024-06-22 03:03:38'),
(969, 'FARM', 'GRIPE WATER 150 ML', 106, 100, 1, 100.00, 136.00, 0, '07/2025', 13, 11, '2025-07-28 13:20:04'),
(970, 'FARM', 'Healthease WHITEFIELD OINTMENT 30 G', 1, NULL, 1, 0.00, 92.00, 0, '', 17, 11, '2025-01-21 17:58:36'),
(971, 'FARM', 'HERBAL LOZENGES GINGER HONEY HEALTHEASE', 87, NULL, 1, 0.00, 18.00, 0, '31/03/2027', 14, 11, '2025-03-28 10:59:27'),
(972, 'FARM', 'KETO TABS', 229, NULL, 1, 0.00, 70.00, 0, '', 9, 11, '2024-08-26 11:24:45'),
(973, 'FARM', 'NEOCID TABS 5X4', 46, NULL, 1, 0.00, 86.00, 0, '', 13, 11, '2025-03-27 18:50:46'),
(974, 'FARM', 'NEOCLARITY ', 2, NULL, 1, 0.00, 107.00, 0, '', 11, 11, '2024-12-14 05:01:37'),
(975, 'FARM', 'NEOPOVIDINA POMADA', 0, NULL, 1, 0.00, 54.00, 0, '', 9, 11, '2024-06-01 08:36:08'),
(976, 'FARM', 'NEOPOVIDINA SOLUCAO', 0, NULL, 1, 0.00, 109.00, 0, '', 17, 11, '2024-07-25 12:07:36'),
(977, 'FARM', 'NETON CP', 47, NULL, 1, 0.00, 9.00, 0, '', 11, 11, '2024-07-24 04:52:54'),
(978, 'FARM', 'NEOVENT 2 MG CP', 1, NULL, 1, 0.00, 9.00, 0, '', 14, 11, '2024-06-21 04:53:53'),
(979, 'FARM', 'NEOTON CP', 325, NULL, 1, 0.00, 9.00, 0, '', 11, 11, '2025-03-28 22:58:10'),
(980, 'FARM', 'NEOZOL CP', 3, NULL, 1, 0.00, 17.00, 0, '', 9, 11, '2024-06-01 13:09:27'),
(981, 'FARM', 'BECOVITA TABS', 0, NULL, 1, 0.00, 11.00, 0, '', 15, 11, '2024-09-01 20:53:44'),
(982, 'FARM', 'ZUDIC CREAM', 50, NULL, 1, 0.00, 200.00, 0, '', 17, 11, '2025-01-31 16:01:01'),
(983, 'FARM', 'OVA MIT CLOMIFENO 50 MG 1X10', 1, 0, 1, 295.00, 440.00, 0, '07/2028', 16, 14, '2025-11-07 10:17:27'),
(984, 'FARM', 'MUCOASTHALIN', 204, NULL, 1, 0.00, 164.00, 0, '03/2024', 14, 14, '2025-03-28 15:13:32'),
(985, 'FARM', 'ANEROID', 2, NULL, 1, 0.00, 2098.00, 0, '', 22, 8, '2024-08-08 07:51:27'),
(986, 'FARM', 'BENZOCAB LOCAO DE BENZYL BENZOATO', 9, NULL, 1, 0.00, 133.00, 0, '', 17, 8, '2024-08-16 17:02:09'),
(987, 'FARM', 'CLOTRIMAZOL CREME VAGINAL 10 MG', 1, NULL, 1, 0.00, 456.00, 0, '', 16, 8, '2025-01-28 18:31:53'),
(988, 'FARM', 'SORO LACTATO DE RINGER 500 ML BASI BALAO', 3, NULL, 1, 0.00, 86.00, 0, '', 23, 8, '2025-01-28 17:08:16'),
(989, 'FARM', 'IBUPROFENO Azevedos 600 MG ', 0, 54, 1, 420.00, 104.00, 0, '31/05/2027', 10, 12, '2025-09-25 09:48:43'),
(990, 'FARM', 'IBUPROFENO AZEVEDOS 400 MG CP 1X20 CP', 2, NULL, 1, 79.00, 117.00, 0, 'POR CARTELA', 0, 12, '2024-06-25 13:04:52'),
(991, 'FARM', 'IRBESARTAN + HIDROCLORTIZIDA 150/12.5 MG CP', 1, NULL, 1, 540.00, 801.00, 0, '', 18, 12, '2024-06-02 00:56:22'),
(992, 'FARM', 'NALBIX CREME TUBO 20 G', 1, 5, 1, 136.00, 201.00, 0, '31/07/2027', 9, 12, '2025-08-08 11:54:34'),
(993, 'FARM', 'TRAMADOL Azevedos 50 MG CAP', 6, NULL, 1, 0.00, 165.00, 0, '31/03/2027', 10, 12, '2025-02-02 12:04:06'),
(994, 'FARM', 'PARACETAMOL AZEVEDOS 500 MG CP', 0, 10, 1, 115.00, 86.00, 0, '30/04/2027', 10, 12, '2025-09-24 17:18:38'),
(995, 'FARM', 'METFORMINA AZEVEDOS 1000 MG CP', 0, NULL, 1, 0.00, 172.16, 0, '', 19, 12, '2025-01-15 01:39:27'),
(996, 'FARM', 'METFORMINA AZEVEDOS 500 MG CP', 3, NULL, 1, 0.00, 84.83, 0, '', 19, 12, '2025-03-27 10:14:07'),
(998, 'FARM', 'SABONETE DE ALCATRAO 90 G', 3, NULL, 1, 0.00, 286.00, 0, '', 17, 12, '2024-06-02 01:21:51'),
(999, 'FARM', 'SYNTOPINE CARBAMAZEPINA 200 MG', 96, NULL, 1, 0.00, 63.00, 0, '', 21, 12, '2025-03-28 10:16:55'),
(1000, 'FARM', 'FORTALINE PLUS 500 MG CAPS', 2, NULL, 1, 0.00, 311.00, 0, '', 15, 12, '2024-12-05 15:48:00'),
(1001, 'FARM', 'SILDENAFIL AZEVEDOS 50 MG ', 10, NULL, 1, 0.00, 250.50, 0, 'POR COMP', 18, 12, '2024-11-21 22:44:19'),
(1002, 'FARM', 'SILDENAFIL AZEVEDOS 100 MG CP', 5, NULL, 1, 0.00, 296.25, 0, 'POR COMP', 18, 12, '2024-10-30 01:58:06'),
(1003, 'FARM', 'IBUPROFENO AZEVEDOS 400 MG COMP 1X60 CP', 2, 5, 1, 209.00, 52.00, 0, '28/02/2027', 10, 12, '2025-09-25 10:23:13'),
(1004, 'FARM', 'TRIFENE 400 MG COMP 1X60', 108, 5, 1, 331.00, 82.00, 0, '30/04/2029', 10, 12, '2025-09-26 12:36:04'),
(1005, 'FARM', 'ERYTHROSHA', 0, NULL, 1, 0.00, 68.00, 0, '01/2027', 9, 9, '2024-08-14 09:58:53'),
(1006, 'FARM', 'MEDICAO DE PESO', 999567, NULL, 1, 0.00, 5.00, 0, '', 21, 0, '2025-02-03 08:55:26'),
(1007, 'FARM', 'MEDICAO DE PRESSAO ARTERIAL', 999802, NULL, 1, 0.00, 15.00, 0, 'SERVICO', 22, 0, '2025-02-02 22:57:58'),
(1009, 'FARM', 'MOXMOD 500 CAP 10X10 ', 1127, 50, 1, 30.00, 37.50, 0, '30/06/2025', 9, 8, '2025-06-12 11:32:31'),
(1010, 'FARM', 'CEDOR PARACETAMOL 500 COMP ', 336, 50, 1, 5.00, 7.00, 0, '09/2027', 10, 8, '2025-10-11 13:43:14'),
(1011, 'FARM', 'VASELINA PURA BASI GEL', 9, NULL, 1, 0.00, 89.00, 0, '', 17, 8, '2025-01-28 04:46:27'),
(1013, 'FARM', 'DABENZOL ', 11, NULL, 1, 0.00, 91.00, 0, '31/08/2026', 9, 8, '2025-03-28 15:41:33'),
(1014, 'FARM', 'CLOTRIMAZOL CREME VAGINAL BLUEPHARMA 10MG/50 GVDs', 10, 5, 1, 291.00, 431.00, 0, '28/02/2027', 16, 8, '2025-06-12 12:43:06'),
(1015, 'FARM', 'MULETAS TAMANHO L (CADA)', 0, NULL, 1, 0.00, 1565.00, 16, '', 21, 8, '2025-01-16 20:17:33'),
(1017, 'FARM', 'MULETAS TAMANHO SMALL S (UNIDADE)', 2, NULL, 1, 0.00, 1379.00, 16, '', 22, 8, '2024-06-11 01:27:24');
INSERT INTO `produto` (`idproduto`, `prefico`, `nomeproduto`, `stock`, `stock_min`, `stocavel`, `preco_compra`, `preco`, `iva`, `codbar`, `grupo`, `familia`, `data`) VALUES
(1018, 'FARM', 'CANADIANA MEDIA (CADA)', 2, NULL, 1, 0.00, 1341.00, 16, '', 22, 8, '2025-01-04 06:42:31'),
(1019, 'FARM', 'CANADIANA SMALL (CADA)', 2, NULL, 1, 0.00, 1192.00, 16, '', 22, 8, '2024-06-11 01:31:20'),
(1020, 'FARM', 'SUPORTE DO BRACO PARA MULETA', 2, NULL, 1, 0.00, 298.00, 16, '', 22, 8, '2024-06-11 01:32:16'),
(1021, 'FARM', 'SUPORTE DE PUNHO PARA MULETAS', 2, NULL, 1, 0.00, 224.00, 16, '', 22, 8, '2024-06-11 01:32:56'),
(1022, 'FARM', 'SUPORTE DE BAIXO PARA MULETA', 2, NULL, 1, 0.00, 112.00, 16, '', 21, 8, '2024-06-11 01:33:41'),
(1024, 'FARM', 'OPTIMOL 0,5 por cento colirio x 5ml', 0, 1, 1, 200.00, 250.00, 0, '30/11/2026', 20, 12, '2025-06-13 16:02:29'),
(1025, 'FARM', 'OPTOMOMYCIN D-COLIRIOX5ML(2-8)', 0, NULL, 1, 0.00, 286.00, 0, '30/04/2027', 20, 12, '2024-08-26 12:15:54'),
(1026, 'FARM', 'TATUM MENTA FRESCA X 250ML', 5, NULL, 1, 0.00, 250.00, 16, '31/12/2030', 22, 12, '2024-08-03 13:01:43'),
(1028, 'FARM', 'TATUM VERDE PASTILHA MEL E LARANJA', 4, NULL, 1, 0.00, 225.50, 0, '31/10/26', 11, 12, '2024-06-12 04:14:59'),
(1031, 'FARM', 'BIOLECTRA CALCIU  500mg', 96, NULL, 1, 0.00, 59.60, 16, '30/04/2026', 15, 12, '2025-02-01 15:12:34'),
(1032, 'FARM', 'DICLOFENAC AZEVEDO GEL', 4, 5, 1, 365.00, 541.00, 0, '31/07/2027', 17, 12, '2025-09-24 15:21:44'),
(1033, 'FARM', 'HERMES MULTIVIT X 20 COMP.eferv', 0, NULL, 1, 0.00, 450.00, 0, '30/11/2024', 15, 12, '2024-07-05 11:28:45'),
(1034, 'FARM', 'MENTOCAINA  ANTI-INFLAM,CXS', 0, NULL, 1, 0.00, 250.00, 0, '20/10/25', 11, 12, '2024-07-23 08:22:58'),
(1035, 'FARM', 'MENTOCAINA MEL E LIMAO CXS 24 PASTILHAS', 0, NULL, 1, 0.00, 230.00, 0, '', 11, 12, '2024-07-18 04:05:26'),
(1036, 'FARM', 'SINOMARIM INFANTIL Frx100ml+25ml', 2, NULL, 1, 0.00, 977.00, 16, '31/05/2026', 11, 12, '2024-06-12 09:31:47'),
(1037, 'FARM', 'SINOMARIN ADULTO X125ml', 1, NULL, 1, 0.00, 1049.00, 16, '31/10/25', 11, 12, '2024-06-12 10:19:08'),
(1038, 'FARM', 'LAXODAL 7,5mg Gotas FrsX 30ml', 0, NULL, 1, 0.00, 671.00, 0, '31/08/2026', 13, 12, '2025-01-07 03:52:53'),
(1039, 'FARM', 'PEDIFEN 400mg Cxs.X 30 Comp', 17, NULL, 1, 0.00, 77.30, 0, '31/03/2027', 10, 12, '2025-01-28 00:42:52'),
(1040, 'FARM', 'SERETAIDE INALADOR 25/250X120', 1, NULL, 1, 0.00, 2296.00, 0, '30/09/2025', 14, 12, '2024-06-12 13:11:20'),
(1044, 'FARM', 'ESPEN 400', 6, NULL, 1, 0.00, 20.00, 0, '30/11/2026', 10, 12, '2025-01-14 07:24:32'),
(1045, 'FARM', 'FENOXIMETILPENICILINA', 372, NULL, 1, 0.00, 67.20, 0, '31/01/2026', 9, 12, '2025-03-28 04:36:18'),
(1046, 'FARM', 'MILPOL SYRUP frsX100ML PARACETAMOL', 2, NULL, 1, 0.00, 76.00, 0, '31/12/26', 10, 12, '2024-11-20 22:29:38'),
(1047, 'FARM', 'NADICLOX 2% pomada bisnagax15g', 5, NULL, 1, 0.00, 374.00, 0, '31/10/26', 0, 12, '2025-01-21 16:21:00'),
(1049, 'FARM', 'MICOLD SYRUP', 0, NULL, 1, 0.00, 60.00, 0, '30/11/2026', 11, 12, '2024-10-18 04:24:45'),
(1050, 'FARM', 'COMPRAL', 0, NULL, 1, 0.00, 33.25, 0, '', 10, 12, '2025-01-23 23:44:26'),
(1051, 'FARM', 'BURN DRESSING-HI CARE BRAND 10X10CM', 4, NULL, 1, 0.00, 178.00, 0, '31/05/2025', 22, 12, '2025-01-26 17:06:08'),
(1052, 'FARM', 'FRESHEN BISACODIL', 15, NULL, 1, 0.00, 86.00, 0, '31/12/2025', 11, 12, '2025-03-28 16:20:51'),
(1054, 'FARM', 'VENTILAN-INALADOR 100 microgramas', 0, NULL, 1, 0.00, 389.00, 0, '30/11/2024', 14, 12, '2024-06-25 06:28:55'),
(1061, 'Selecione o prefixo', 'ZINPLEX JUNIOR BERRY(sugar free)', 1, NULL, 1, 0.00, 544.00, 16, '31/05/2025', 15, 12, '2024-12-11 12:38:57'),
(1062, 'FARM', 'ZINPLEX JUNIOR SYRUP(CREAM SODA)', 1, NULL, 1, 0.00, 544.00, 16, '30/04/2025', 15, 12, '2024-12-11 09:56:36'),
(1063, 'FARM', 'CELEBIB 100 MG 1X30 cap', 0, NULL, 1, 0.00, 99.00, 0, '31/08/2025', 0, 12, '2025-02-01 16:01:05'),
(1065, 'FARM', 'ADCO-IBUPROFEN', 0, NULL, 1, 0.00, 71.50, 0, '30/04/2025', 10, 12, '2025-01-26 23:38:22'),
(1066, 'FARM', 'OBESYL CHA x100g', 1, 2, 1, 122.00, 623.00, 16, '01/30/2028', 13, 12, '2025-04-21 16:17:26'),
(1067, 'FARM', 'SABUTAR SABONETE DE ENXOFRE', 33, NULL, 1, 0.00, 179.00, 0, '06/2026', 17, 8, '2025-01-20 10:56:46'),
(1069, 'FARM', 'TRICEF 400Mg', 7, NULL, 1, 0.00, 279.00, 0, '', 9, 8, '2024-08-14 18:45:49'),
(1070, 'FARM', 'TRICEF  ORAL 60ML', 0, NULL, 1, 0.00, 1103.00, 0, '', 9, 8, '2024-06-21 03:47:53'),
(1071, 'FARM', 'FOLICIL BIAL', 0, NULL, 1, 0.00, 80.50, 0, '', 15, 8, '2024-06-21 03:46:45'),
(1073, 'FARM', 'TUSSILENE COMP', 0, NULL, 1, 0.00, 423.00, 0, '', 11, 12, '2024-07-23 10:03:40'),
(1074, 'FARM', 'SILVERKANT', 8, NULL, 1, 0.00, 90.00, 0, '', 0, 9, '2024-07-23 11:05:17'),
(1075, 'FARM', 'PARA RAPIDO EXTRA', 512, NULL, 1, 0.00, 14.00, 0, '2027/04', 0, 9, '2025-03-28 21:38:15'),
(1076, 'FARM', 'NYSTATIN SUSPESAO', 46, NULL, 1, 0.00, 173.00, 0, '07/2025', 9, 9, '2025-03-28 21:57:05'),
(1077, 'FARM', 'HIDROKANT CREAM', 18, NULL, 1, 0.00, 165.00, 0, '', 17, 9, '2024-10-19 23:22:32'),
(1079, 'FARM', 'BETASHA-N', 53, NULL, 1, 0.00, 90.00, 0, '2026/06', 17, 9, '2025-02-03 14:21:14'),
(1080, 'FARM', 'DR.KOLD', 4, NULL, 1, 0.00, 13.00, 0, '', 11, 9, '2024-07-02 13:28:41'),
(1081, 'FARM', 'PINNAFLAM SP', 301, NULL, 1, 0.00, 68.00, 0, '', 10, 9, '2024-08-22 05:58:38'),
(1082, 'FARM', 'GO-GEL', 35, NULL, 1, 0.00, 150.00, 0, '08/2028', 0, 16, '2025-03-28 08:52:59'),
(1083, 'FARM', 'NOVA POWER', 178, NULL, 1, 0.00, 120.00, 0, '03/2026', 15, 9, '2025-03-28 22:01:41'),
(1085, 'FARM', 'MENTHOSIL COUGH LOZENGES-LEMON', 0, NULL, 1, 0.00, 55.00, 0, '9/25', 14, 8, '2025-01-03 17:44:12'),
(1087, 'FARM', 'ASPIRINA 500MG GENSPRIN', 124, NULL, 1, 0.00, 15.00, 0, '28/02/2026', 10, 8, '2025-03-28 05:35:15'),
(1088, 'FARM', 'CANDIGEN CLOTRIMAZOLE CREAM 1% 20g', 48, 50, 1, 32.00, 48.00, 0, '30/11/2026', 17, 8, '2025-11-07 09:12:45'),
(1089, 'FARM', 'PARALOGO', 1851, 100, 1, 6.50, 10.00, 0, '31/10/2028', 10, 8, '2025-10-11 14:42:26'),
(1090, 'FARM', 'DILACLAN AMOXICILINA  PO SUSPENSAO 250MG/100 ML', 1, NULL, 1, 0.00, 402.00, 0, '31/03/2026', 9, 8, '2024-08-14 12:58:52'),
(1091, 'FARM', 'DILACLAN AMOXICILINA 125/5ML PO ORAL', 0, NULL, 1, 0.00, 227.00, 0, '30/06/2026', 9, 8, '2024-07-09 03:50:22'),
(1095, 'FARM', 'PARACETAMOL BLUEPHARMA 1000MG', 0, NULL, 1, 0.00, 76.00, 0, '30/04/2025', 10, 8, '2025-01-20 06:02:29'),
(1096, 'FARM', 'PARACETAMOL BLUEPHARMA 500MG', 15, NULL, 1, 0.00, 58.00, 0, '31/03/2026', 10, 8, '2025-02-03 09:58:53'),
(1098, 'FARM', 'BRUGEN 400 COMP Ibuprofeno', 76, 100, 1, 9.50, 15.00, 0, '31/10/2027', 10, 0, '2025-08-11 16:49:28'),
(1099, 'FARM', 'VICOMBIL JUNIOR suplemento alimentar multivitaminico', 1, NULL, 1, 0.00, 564.00, 0, '31/05/2025', 15, 8, '2025-01-20 12:30:37'),
(1101, 'FARM', 'MOMEZ', 0, NULL, 1, 0.00, 16.09, 0, '', 22, 8, '2024-11-03 03:05:13'),
(1102, 'FARM', 'NEXIUM', 8, NULL, 1, 0.00, 250.00, 0, 'O8/2024', 13, 8, '2024-07-02 10:01:27'),
(1103, 'FARM', 'ETORICOXIB BLUEPHARMA 60MG CX20', 3, NULL, 1, 0.00, 412.00, 0, '31/03/2026', 22, 8, '2025-01-22 19:27:00'),
(1104, 'FARM', 'MONTELUCASTE BLUEPHARMA  10MG', 2, 2, 1, 0.00, 465.00, 0, '30/11/2026', 14, 8, '2025-04-30 16:09:49'),
(1105, 'FARM', 'NIMESULIDA BLUEPHARMA', 15, NULL, 1, 0.00, 87.00, 0, '31/01/2026', 0, 8, '2025-02-03 06:09:24'),
(1106, 'FARM', 'SULTRIMIX(COTRIMOXAZOL)240MG/5ML', 16, NULL, 1, 0.00, 258.00, 0, '30/11/2025', 14, 8, '2025-02-01 05:20:49'),
(1108, 'FARM', 'AGUA DE CAMOMILA  FRASCO 200', 1, NULL, 1, 0.00, 437.00, 0, '', 17, 8, '2025-01-04 13:17:10'),
(1109, 'FARM', 'OLEO DE AMENDO DOCE 250ML', 1, NULL, 1, 0.00, 621.00, 0, '31/10/2025', 17, 8, '2025-03-27 22:31:29'),
(1110, 'FARM', 'LORATADINA BASI 10 MG', 0, NULL, 1, 0.00, 226.00, 0, '31/10/25', 11, 8, '2025-01-20 05:40:35'),
(1111, 'FARM', 'AMOXICILINA+ACIDO CLAVULANICO BLUEPHARMA 500+125', 0, NULL, 1, 0.00, 529.00, 0, '31/07/2025', 9, 8, '2024-11-22 16:46:59'),
(1113, 'FARM', 'CETIRIZINA BLUEPHARMA', 3, NULL, 1, 0.00, 106.00, 0, '30/04/2026', 11, 8, '2025-01-23 06:56:19'),
(1114, 'FARM', 'DABENZOL 250 MG COMP 1X20', 1, NULL, 1, 0.00, 91.00, 0, '31/08/2026', 9, 8, '2025-01-09 23:28:28'),
(1115, 'FARM', 'ACETILCISTEINA BLUEPHARMA 600MG', 3, NULL, 1, 0.00, 385.00, 0, '31/07/2026', 11, 8, '2025-01-09 20:01:08'),
(1116, 'FARM', 'TOCELIV 2MG/ML XAROPE 200ML ', 7, 5, 1, 266.00, 393.00, 0, '31/03/2026', 14, 8, '2025-10-11 14:05:57'),
(1117, 'FARM', 'SORO FISIOLOGICO BASI 0.9 100ML', 12, NULL, 1, 0.00, 207.00, 0, '30/04/2028', 23, 8, '2025-03-28 06:29:06'),
(1119, 'FARM', 'CLAVAMOX 500', 8, NULL, 1, 0.00, 287.00, 0, '07/2025', 10, 8, '2024-07-03 08:49:43'),
(1120, 'FARM', 'ASPIRINA BAYER 1000 CX 30COMP', 1, NULL, 1, 0.00, 361.00, 0, '30/04/2028', 10, 8, '2025-01-16 19:35:32'),
(1121, 'FARM', 'VICOMBIL 100/.SUSPENSAO FRASCO 250 ML', 12, NULL, 1, 0.00, 644.00, 0, '30/09/2024', 15, 8, '2024-08-29 23:03:06'),
(1123, 'FARM', 'VICOMBIL FERRUM FRASCO  DE 250 ML', 0, NULL, 1, 0.00, 708.00, 0, '30/09/2024', 15, 8, '2024-07-03 17:28:13'),
(1124, 'FARM', 'RINIALER SOLUCAO ORAL', 2, NULL, 1, 0.00, 576.00, 16, '', 0, 8, '2024-07-03 17:29:24'),
(1128, 'FARM', 'CORENZA', 13, NULL, 1, 0.00, 305.00, 0, '31/05/2026', 10, 8, '2025-01-27 11:14:26'),
(1129, 'FARM', 'TRIFENE 400 MG COMP 1X60 COMP.', 3, 5, 1, 0.00, 82.00, 0, '30/04/2028', 10, 8, '2025-08-08 11:52:08'),
(1130, 'Selecione o prefixo', 'SYNTOPRIM 480MG CXs.', 79, NULL, 1, 0.00, 33.00, 0, '30/11/2026', 14, 12, '2024-07-05 12:06:17'),
(1131, 'FARM', 'STOPITCH CREME', 3, NULL, 1, 0.00, 135.00, 0, '31/05/2025', 17, 12, '2024-08-15 01:25:35'),
(1135, 'FARM', 'AMBROXOL TUSSILENE 6MG/ML XAROPE 200ML', 5, NULL, 1, 0.00, 406.00, 0, '30/11/2026', 14, 12, '2025-03-28 20:41:16'),
(1136, 'FARM', 'DESLORATADINA AZEVEDO  SOL.ORAL FRSX 150', 1, NULL, 1, 0.00, 499.00, 0, '31/05/2025', 0, 12, '2025-02-02 11:29:20'),
(1138, 'FARM', 'ALGIK IBUPROFENO XAROPE 200ML', 6, NULL, 1, 0.00, 384.00, 0, '04/2026', 10, 12, '2025-03-28 20:41:16'),
(1139, 'FARM', 'NALBIX CREME TUBO 20 G XX', 10, 1, 1, 135.00, 201.00, 0, '31/07/2029', 17, 12, '2025-09-25 05:16:42'),
(1140, 'FARM', 'TAKS(diclofenac) 50mg', 25, NULL, 1, 0.00, 50.60, 0, '31/07/2027', 10, 12, '2025-02-03 09:35:17'),
(1141, 'FARM', 'OMEPRAZOL 20 MG AZEVEDOS 20MG CXS X 56 CAPS', 34, NULL, 1, 0.00, 155.00, 0, '30/06/2025', 13, 12, '2025-03-28 05:02:53'),
(1144, 'FARM', 'MACROLIN. AZITROMICINA AZEVEDO', 0, 5, 1, 80.00, 80.00, 0, '31/08/2025', 16, 8, '2025-04-18 12:52:17'),
(1146, 'FARM', 'CICLOVIRAL 800 MG COMP', 1384, NULL, 1, 0.00, 3.00, 0, '', 9, 12, '2024-07-04 20:21:28'),
(1147, 'FARM', 'CICLOVIRAL 5% CREME BISNAGA', 2, NULL, 1, 0.00, 271.00, 0, '', 9, 12, '2024-10-31 22:31:13'),
(1148, 'FARM', 'PARAMOLAN 500 MG COMP', 3, 10, 1, 40.00, 74.00, 0, '09/2027', 10, 12, '2025-09-02 11:25:00'),
(1149, 'FARM', 'TRICOVIVAX 2%  1OO ML', 1, NULL, 1, 0.00, 1382.00, 0, '', 17, 12, '2024-07-04 20:27:30'),
(1151, 'FARM', 'MEDOFED COMPOUND 1OO ML', 0, NULL, 1, 0.00, 301.00, 0, '', 10, 12, '2024-10-31 02:19:14'),
(1152, 'FARM', 'CIPROMED 500 MG COMP', 8, 5, 1, 0.00, 44.00, 0, 'ML-141', 9, 12, '2025-08-08 12:08:22'),
(1153, 'FARM', 'GASTRALGIN SUSP 200 ML', 0, 5, 1, 0.00, 95.00, 0, '31/12/2027', 13, 12, '2025-08-08 12:01:11'),
(1154, 'FARM', 'MILOXY 500 CAPS', 5, NULL, 1, 0.00, 48.40, 0, '', 9, 12, '2024-08-28 12:33:45'),
(1155, 'FARM', 'MILZOR 500 MG COMP', 15, NULL, 1, 0.00, 13.50, 0, '31/05/2026', 10, 12, '2025-01-27 12:04:20'),
(1156, 'FARM', 'ML-METRO 250 MG COMP', 70, NULL, 1, 0.00, 15.00, 0, '', 9, 12, '2024-08-28 17:22:55'),
(1157, 'FARM', 'COARTEM 80+480 COMP 1X6', 0, NULL, 1, 0.00, 329.00, 0, '', 9, 12, '2024-10-29 19:51:35'),
(1158, '', 'STREPSILS MEL E LIMAO ', 3, 0, 1, 0.00, 207.00, 0, '', 0, 0, '2025-04-01 10:22:34'),
(1159, 'FARM', 'CEREGUMIL PEDIATRICO 250 ML', 1, NULL, 1, 0.00, 2717.00, 16, '', 15, 12, '2024-07-04 21:05:21'),
(1160, 'FARM', 'CEREGUMIL XAROPE 200 ML', 2, NULL, 1, 0.00, 1907.00, 16, '', 15, 12, '2024-07-04 21:06:32'),
(1161, 'FARM', 'PANKREOFLAT COMP 60', 2, NULL, 1, 0.00, 210.00, 0, '', 13, 12, '2025-02-01 18:05:02'),
(1163, 'FARM', 'TASECTAN 500MG CAPS', 31, NULL, 1, 0.00, 49.00, 16, '31/01/2029', 13, 12, '2025-01-24 00:40:09'),
(1165, 'FARM', 'G-ZOLE', 3, NULL, 1, 0.00, 45.00, 0, '30/11/2026', 13, 8, '2025-01-27 23:53:10'),
(1166, 'FARM', 'VICOMBIL FERRUM FRASCO  DE 200ML', 2, NULL, 1, 0.00, 708.00, 0, '30/09/2024', 15, 8, '2024-07-05 07:04:29'),
(1169, 'FARM', 'VICOMBIL CX 60 COMPRIMIDO ', 8, 5, 1, 326.00, 81.00, 0, '30/04/2027', 15, 12, '2025-10-07 12:09:26'),
(1170, 'FARM', 'GEN-Z SULFATO DE ZINCO', 30, NULL, 1, 0.00, 44.00, 0, '31/01/2027', 15, 8, '2025-01-24 18:34:24'),
(1171, 'FARM', 'GLIVERA SABONETE', 2, NULL, 1, 0.00, 119.00, 0, '30/06/2026', 17, 8, '2024-11-30 04:16:48'),
(1172, 'FARM', 'ANTI-ACNE SABONETE', 7, 10, 1, 59.00, 90.00, 0, '30/06/26', 17, 8, '2025-06-12 10:47:42'),
(1173, 'FARM', 'ADESIVOS ZINC OXIDE 7.5 CMX5M-10UN', 1, 5, 1, 90.00, 149.00, 0, '21/03/2029', 21, 0, '2025-08-09 14:31:23'),
(1174, 'FARM', 'ALCOOL SPRAY', 0, NULL, 1, 0.00, 120.00, 0, '', 17, 0, '2024-11-30 07:37:05'),
(1175, 'FARM', 'ALCOOL GEL   500 ML', 0, NULL, 1, 0.00, 409.00, 0, '', 17, 0, '2025-03-28 11:03:14'),
(1176, 'FARM', 'ALGODAO EM BOLAS-100UN', 0, 1, 1, 78.00, 117.00, 0, '30/04/2028', 17, 0, '2025-05-06 10:04:21'),
(1177, 'FARM', 'ALOPERIDOL', 0, NULL, 1, 0.00, 3000.00, 0, '', 0, 0, '2024-08-14 21:21:02'),
(1178, 'FARM', 'BARRETES DESCARTAVEL', 19, 10, 1, 0.00, 8.00, 0, '10/20/2030', 0, 0, '2025-04-24 16:02:04'),
(1179, 'FARM', 'FITAS PARA GLUCOMETROS  SENSOLITE NOVA', 3, NULL, 1, 0.00, 1584.00, 0, '', 22, 0, '2024-07-13 05:35:00'),
(1180, 'FARM', 'FRALDAS PARA ADULTO-XL', 2, 0, 1, 0.00, 413.00, 0, '', 22, 0, '2025-11-07 09:52:21'),
(1181, 'FARM', 'GLUCOMETRO-SENSOLITE NOVA', 1, NULL, 1, 0.00, 6353.00, 0, '', 21, 0, '2024-07-13 05:45:33'),
(1182, 'FARM', 'LATERNA TIPO CANETA PARA OBSERVACAO', 1, NULL, 1, 0.00, 548.00, 0, '', 0, 0, '2024-08-15 12:49:51'),
(1183, 'Selecione o prefixo', 'LUVAS CIRURGICAS NO 7,5', 0, NULL, 1, 0.00, 29.30, 0, '', 22, 0, '2025-01-14 19:02:21'),
(1184, 'Selecione o prefixo', 'LUVAS DE LIMPEZA', 2, NULL, 1, 0.00, 209.11, 0, '', 0, 0, '2024-07-13 06:03:01'),
(1185, 'FARM', 'MASCARAS FACE MASK', 5, 10, 1, 0.00, 10.00, 0, '2029', 22, 0, '2025-08-11 10:07:36'),
(1186, 'FARM', 'MASCARAS KN 95', 30, 5, 1, 20.00, 37.60, 0, '30/08/2028', 22, 0, '2025-07-28 05:48:26'),
(1187, 'FARM', 'OCULOS DE  PROTECAO', 2, NULL, 1, 0.00, 200.00, 0, '', 21, 0, '2025-03-28 14:45:26'),
(1188, 'FARM', 'RESGUARDO DESCARTAVEL', 50, NULL, 1, 0.00, 99.80, 0, '', 0, 0, '2024-07-13 06:32:19'),
(1189, 'EMPRE', 'SERINGAS DE INSULINA  1 ML', 11, 10, 1, 0.00, 8.00, 0, '', 0, 0, '2025-04-18 08:29:27'),
(1190, 'FARM', 'TERMOMETRO DE MERCURIO GROSSO', 0, NULL, 1, 0.00, 199.80, 0, '', 0, 0, '2024-12-09 04:51:04'),
(1191, 'FARM', 'TERMOMETRO DIGITAL', 1, NULL, 1, 0.00, 207.20, 0, '', 21, 0, '2025-01-12 05:26:03'),
(1192, 'FARM', 'THEOFIX-100 DS XAROPR', 9, NULL, 1, 0.00, 281.00, 0, '12/16', 9, 14, '2024-08-21 00:09:01'),
(1193, 'FARM', 'TEOGRA 1OO (SIDELNALFIL 100 MG)', 46, NULL, 1, 0.00, 59.00, 0, '', 16, 14, '2025-01-30 14:57:17'),
(1194, 'FARM', 'THEOGRA -50(SIDELNALFIL 50 MG)', 49, NULL, 1, 0.00, 55.00, 0, '10/26', 16, 14, '2025-03-28 10:51:07'),
(1195, 'FARM', 'THEOFIX-400  TAB 400 MG', 38, NULL, 1, 0.00, 29.00, 0, '', 9, 14, '2025-02-02 19:41:16'),
(1196, 'FARM', 'THEOCLAV-1000 (AMOXI, POTASSIO CP)', 0, NULL, 1, 0.00, 470.00, 0, '11/25', 9, 14, '2024-08-22 16:33:37'),
(1197, 'FARM', 'THEOCLAV-625 (AMOXI, CLAVULANATE POTTASSIOCP)', 3, NULL, 1, 0.00, 366.00, 0, '', 9, 14, '2024-07-25 12:13:29'),
(1200, 'FARM', 'THEOCLAV 156.25(AMOXI, E POTASS,) xarope', 3, NULL, 1, 0.00, 147.00, 0, '11/2025', 9, 14, '2024-08-09 17:25:20'),
(1201, 'FARM', 'THEOCLAV 228.50.E CLAVUNATE SUSP.ORAL', 3, NULL, 1, 0.00, 202.00, 0, '11/2025', 14, 14, '2024-08-17 05:50:41'),
(1202, 'FARM', 'TRIM-CONTRIMOXAZOL', 239, NULL, 1, 0.00, 20.10, 0, '07/2027', 9, 14, '2025-03-28 23:20:49'),
(1203, 'FARM', 'LUMIER 80/480', 10, NULL, 1, 0.00, 296.00, 0, '', 12, 14, '2025-02-02 20:54:34'),
(1204, 'FARM', 'ARTEMETER 20MG+LUMIFENTRIN A 120MG', 4, NULL, 1, 0.00, 180.00, 0, '11/2026', 12, 14, '2025-03-27 14:27:15'),
(1205, 'FARM', 'RELAX-BISACODIL', 114, NULL, 1, 0.00, 9.00, 0, '06/2025', 0, 14, '2025-03-28 10:29:23'),
(1206, 'FARM', 'PROMETHAZINE  HIYDROCHLORIDE', 143, NULL, 1, 0.00, 6.00, 0, '11/2024', 13, 14, '2024-11-30 00:49:07'),
(1207, 'FARM', 'MOVILON-25..INDOMETACINA', 1238, 0, 1, 0.00, 30.00, 0, '02/26', 9, 14, '2025-11-06 12:32:15'),
(1208, 'FARM', 'KODOX(DOXYCILINA CAPS.100MG)', 531, NULL, 1, 0.00, 32.00, 0, '08/2027', 9, 14, '2025-03-28 23:00:49'),
(1209, 'FARM', 'MS.TELLME-Teste de Gravidez', 95, 0, 1, 0.00, 100.00, 0, '12/2025', 16, 14, '2025-11-07 13:21:33'),
(1210, 'FARM', 'IBUPAR SUSPENSION 60ML(IBUPROFENO+PARACETAMOL)', 114, NULL, 1, 0.00, 97.00, 0, '06/2026', 10, 14, '2025-03-28 15:38:03'),
(1211, 'FARM', 'SPIRALON-25', 10, NULL, 1, 0.00, 147.00, 0, '05/2028', 9, 14, '2024-07-18 05:22:36'),
(1212, 'FARM', 'LIGADURA DE GAZE -10CMX4M ROLL (12s)', 100, NULL, 1, 0.00, 18.00, 0, '11/2026', 17, 14, '2025-01-23 09:17:23'),
(1213, 'FARM', 'LIGADURA DE GAZE-15CM X4M ROLL', 0, NULL, 1, 0.00, 25.00, 0, '11/2026', 17, 14, '2024-09-01 01:37:06'),
(1214, 'FARM', 'LIGADURA DE GAZE-7.5CMX4 ROLL', 4, NULL, 1, 0.00, 15.00, 0, '11/2026', 17, 14, '2025-03-28 15:55:23'),
(1215, 'FARM', 'BATAS DE  ISOLAMENTO (NAO TECIDO)', 8, NULL, 1, 0.00, 230.00, 0, '', 22, 14, '2024-07-18 08:16:42'),
(1217, 'FARM', 'SHOES COVER', 7, 20, 1, 7.00, 9.00, 0, '11/2027', 0, 14, '2025-06-07 13:38:18'),
(1219, 'FARM', 'RABIES ', 1, NULL, 1, 0.00, 2082.00, 0, '', 0, 17, '2024-08-31 10:18:45'),
(1220, 'FARM', 'GENVIR -400', 37, 30, 1, 54.00, 80.00, 0, '05/2026', 22, 8, '2025-06-12 11:09:16'),
(1221, 'FARM', 'MELIOZOL 500 CX.10 OVULOS', 3, NULL, 1, 0.00, 452.00, 0, '04/26', 16, 8, '2024-12-13 09:28:21'),
(1223, 'FARM', 'Cicloviral Aciclovir 800 mg', 50, NULL, 1, 0.00, 277.00, 0, '', 9, 8, '2024-07-21 11:21:34'),
(1225, 'FARM', 'myprodol capsulas', 156, NULL, 1, 0.00, 47.00, 0, '01/26', 21, 12, '2025-03-28 08:52:59'),
(1226, 'FARM', 'CIPRO-SODA 60g', 10, NULL, 1, 0.00, 185.00, 0, '10/25', 13, 12, '2024-07-24 13:03:23'),
(1227, 'FARM', 'DILINCT DRY COUGHT SYRUP', 5, 0, 1, 0.00, 84.00, 0, '11/25', 0, 12, '2025-11-07 10:05:11'),
(1228, 'FARM', 'HACTOSEC 6MG/ML XAROPE 150 ML', 0, NULL, 1, 0.00, 407.00, 0, '02/2025', 22, 12, '2025-01-12 15:09:04'),
(1229, 'Selecione o prefixo', 'CITRO-SODA', 6, NULL, 1, 0.00, 185.00, 0, '', 0, 12, '2025-01-24 12:53:13'),
(1230, 'Selecione o prefixo', 'TANTUM VERDE ', 1, NULL, 1, 0.00, 451.00, 0, '', 0, 13, '2024-07-25 05:39:12'),
(1233, 'FARM', 'LAURODERME PO X 100G XX', 0, 1, 1, 0.00, 646.00, 0, '31/12/2028', 17, 12, '2025-04-08 09:04:50'),
(1238, 'FARM', 'Artigo de Teste', 1, NULL, 1, 0.00, 5.00, 16, '0', 9, 10, '2024-07-27 09:50:28'),
(1239, 'FARM', 'SALBUTAMOL TABLET B.P.2MG', 13, NULL, 1, 0.00, 5.90, 0, '06/2025', 9, 8, '2025-01-22 13:16:09'),
(1243, 'FARM', 'TATUM VERDE NEBULIZADOR', 1, NULL, 1, 0.00, 590.00, 0, '31/03/2027', 14, 12, '2025-01-21 03:25:57'),
(1244, 'FARM', 'TATUM VERDE PASTILHAS ORIGINAIS CXS 20', 5, NULL, 1, 0.00, 401.00, 0, '31/07/2028', 14, 12, '2024-08-03 13:39:36'),
(1246, 'Selecione o prefixo', 'MBEN-S', 0, 0, 1, 0.00, 67.00, 0, '31/05/2026', 10, 8, '2025-11-07 08:26:54'),
(1247, 'FARM', 'ACE-DOX CAPS', 2, 0, 1, 0.00, 37.00, 0, '31/10/26', 9, 8, '2025-11-07 08:46:12'),
(1248, 'FARM', 'INSTALON', 2, 0, 1, 0.00, 158.00, 0, '30/11/2026', 17, 8, '2025-11-07 06:51:54'),
(1249, 'FARM', 'ACE-FORTE CAPS', 0, 50, 1, 0.00, 45.00, 0, '30/06/2027', 0, 8, '2025-11-07 08:18:54'),
(1250, 'FARM', 'ACE-CYCLINE POMADA OFT', 5, 50, 1, 28.00, 38.00, 0, '31/12/2026', 0, 8, '2025-05-02 08:14:32'),
(1251, 'FARM', 'TOSSIL XAROPE', 0, NULL, 1, 0.00, 67.00, 0, '30/09/2026', 14, 8, '2025-01-03 22:40:13'),
(1252, 'FARM', 'GLYCOGEN XAROPE', 0, NULL, 1, 0.00, 97.00, 0, '31/03/2026', 14, 8, '2024-08-29 20:49:38'),
(1254, 'FARM', 'Tantum protect  250ml cor verde', 4, NULL, 1, 0.00, 250.00, 16, '31/12/2030', 13, 8, '2024-08-23 01:39:15'),
(1255, 'FARM', 'TATUM PROTECT MENTA FRESCA', 4, NULL, 1, 0.00, 250.00, 16, '31/12/2030', 13, 8, '2024-10-29 23:33:14'),
(1256, 'FARM', 'TOSSEQUE XAROPE', 9, NULL, 1, 0.00, 446.00, 0, '31/10/26', 14, 12, '2024-12-13 11:50:47'),
(1257, 'FARM', 'Tantum protect  250ml', 1, NULL, 1, 0.00, 250.00, 16, '31/12/2030', 14, 12, '2024-08-19 15:43:54'),
(1260, 'FARM', 'FASTRIM KIT 1COMBI KIT', 8, NULL, 1, 0.00, 343.00, 0, '30/09/2026', 9, 8, '2025-03-28 05:02:53'),
(1261, 'FARM', 'TRAMADOL BASI INJECTAVEL', 0, NULL, 1, 0.00, 48.00, 0, '31/05/2026', 23, 8, '2024-08-22 03:44:29'),
(1262, 'EMPRE', 'NEURO B SOL.INJECTAVEL', 14, NULL, 1, 0.00, 295.00, 0, '28/02/2026', 23, 8, '2025-01-28 15:59:47'),
(1263, 'FARM', 'HIDROCORTISONA BLUEPHARMA', 0, 5, 1, 233.00, 344.00, 0, '31/12/2026', 17, 8, '2025-10-07 12:11:55'),
(1264, 'FARM', 'TRAN-U-RON-LP 100MG CAPSX20', 3, NULL, 1, 0.00, 390.00, 0, '04/26', 10, 8, '2024-09-04 16:39:33'),
(1265, 'FARM', 'IB.U.RON-ibuprofeno', 21, NULL, 1, 0.00, 52.00, 0, '31/01/2028', 10, 8, '2024-10-18 06:03:26'),
(1266, 'FARM', 'aciclovir BLUEPHARMA 50MG creme', 0, 5, 1, 100.00, 195.00, 0, '31/05/2026', 17, 8, '2025-06-12 11:26:55'),
(1268, 'FARM', 'PEN-G FENOXIMETIL PENICELINA COMP. 500MG', 0, NULL, 1, 0.00, 82.00, 0, '30/09/2025', 9, 8, '2024-10-18 02:00:31'),
(1270, 'Selecione o prefixo', 'DIACOL 27MG/15ML XAROPE GVD', 2, 2, 1, 280.00, 316.00, 0, '30/06/2028', 14, 12, '2025-06-12 12:19:34'),
(1271, 'FARM', 'DIACOL 27MG/15ML - XAROPE', 2, NULL, 1, 0.00, 320.00, 0, '', 14, 12, '2024-08-16 14:21:31'),
(1273, 'FARM', 'TOPGYL SUSP', 0, NULL, 1, 0.00, 82.00, 0, '', 9, 8, '2025-01-20 06:18:13'),
(1274, 'FARM', 'QUININOR-M 200MG Comp', 0, 5, 1, 0.00, 56.00, 0, '02/2027', 9, 8, '2025-11-07 09:01:11'),
(1275, 'FARM', 'FURUNBAO', 4, NULL, 1, 0.00, 2000.00, 16, '', 22, 16, '2024-08-14 08:37:44'),
(1276, 'FARM', 'AMOXICLAV-DENK 500/62.5', 3, NULL, 1, 0.00, 276.50, 0, '', 0, 16, '2025-01-29 21:17:28'),
(1277, 'FARM', 'AMOXI-DENK 500 CP', 7, NULL, 1, 0.00, 474.00, 0, '', 0, 16, '2024-12-10 11:43:18'),
(1278, 'FARM', 'FLUCONA-DENK 100 MG CAPS', 10, NULL, 1, 0.00, 34.40, 0, '', 9, 16, '2025-01-20 11:10:04'),
(1279, 'FARM', 'FLUCONA-DENK 150 MG 1CP', 0, NULL, 1, 0.00, 140.00, 0, '', 9, 16, '2025-01-03 08:12:34'),
(1280, 'FARM', 'CIPRO-DENK 500 CP', 63, NULL, 1, 0.00, 32.90, 0, '', 10, 16, '2025-03-28 18:20:13'),
(1281, 'FARM', 'SUPIDON SUSP ORAL FR.100ML', 2, 5, 1, 200.00, 378.00, 0, '31/07/2027', 10, 16, '2025-04-17 07:54:04'),
(1282, 'FARM', 'AGUA PARA INJECTAVEL BASI', 150, NULL, 1, 0.00, 22.00, 0, '31/10/2026', 22, 16, '2025-02-02 18:59:25'),
(1283, 'FARM', 'OMEPRAZOL BASI 20 MG CAPS', 1, NULL, 1, 0.00, 48.25, 0, '', 13, 16, '2025-01-22 01:37:08'),
(1284, 'FARM', 'METROLE - METRONIDAZOL  500 MG COMP ORAL', 498, NULL, 1, 0.00, 56.00, 0, '31/08/2027', 9, 16, '2025-02-02 20:19:12'),
(1285, 'FARM', 'PREDOL - PREDNISOLONA 20 MG ', 460, NULL, 1, 0.00, 60.00, 0, '', 11, 16, '2025-03-29 00:01:29'),
(1286, 'FARM', 'CANDICORT POMADA 30 GR', 10, NULL, 1, 0.00, 425.00, 0, '', 17, 16, '2025-02-02 18:59:25'),
(1287, 'FARM', 'DECADRON NASAL 20 ML', 1, NULL, 1, 0.00, 325.00, 0, '', 11, 16, '2025-01-31 14:22:19'),
(1288, 'FARM', 'PREDNISOLONA SOLUCAO ORAL', 0, NULL, 1, 0.00, 524.00, 0, '', 11, 16, '2024-08-18 13:04:53'),
(1293, 'FARM', 'SWOT CREME REPELENTE', 4, NULL, 1, 0.00, 550.00, 0, '', 17, 16, '2025-03-28 10:48:46'),
(1296, 'FARM', 'EFFERFLU C  CP', 12, NULL, 1, 0.00, 272.00, 0, '', 11, 16, '2025-03-27 09:06:52'),
(1300, 'FARM', 'TEXA  ALLERGYC 10 MG', 3, 0, 1, 0.00, 110.00, 0, '0', 11, 16, '2025-04-09 13:42:56'),
(1301, 'FARM', 'EFFERFLU C JUNIOR IMMUNE BOOSTER', 3, NULL, 1, 0.00, 600.00, 0, '', 11, 16, '2024-08-14 09:52:56'),
(1302, 'FARM', 'TEXA XAROPE CETIRIZINA 100 ML', 9, NULL, 1, 0.00, 350.00, 0, '', 11, 16, '2025-03-28 17:11:19'),
(1303, 'FARM', 'VICKS VAPORUB 40GR', 26, NULL, 1, 0.00, 150.00, 16, '', 11, 16, '2025-02-03 04:22:25'),
(1304, 'FARM', 'CEREBRUM STUDENT CAP', 1, NULL, 1, 0.00, 3400.00, 16, '', 15, 16, '2024-08-14 10:05:20'),
(1305, 'FARM', 'CHA TRIFAST', 0, NULL, 1, 0.00, 700.00, 16, '', 13, 16, '2024-08-16 19:02:40'),
(1306, 'FARM', 'AGUA DE ROSAS VELVET 200 ML', 9, NULL, 1, 0.00, 230.00, 16, '', 17, 16, '2025-03-27 03:46:56'),
(1307, 'FARM', 'AGUA OXIGENADA 3% 10 VOL,Frs x500 ML', 0, 5, 1, 82.00, 115.00, 0, '31/07/2027', 22, 16, '2025-09-24 15:45:00'),
(1308, 'FARM', 'ALCOOL ETILICO 70%v/v,250ml NATUR ', 23, NULL, 1, 0.00, 200.00, 16, '', 21, 16, '2025-02-03 08:37:57'),
(1309, 'FARM', 'ALCOOL ETILICO 96%; 250ml NATUR', 6, NULL, 1, 0.00, 200.00, 16, '', 21, 16, '2025-02-03 05:04:03'),
(1310, 'FARM', 'BICARBONATO DE SODIO 30 GR', 1, 5, 1, 110.00, 160.00, 0, '31/07/2027', 21, 16, '2025-11-07 06:50:25'),
(1311, 'FARM', 'GREEN MUTI 14 GR', 24, 5, 1, 59.00, 90.00, 0, '31/12/2027', 14, 16, '2025-07-07 12:03:32'),
(1312, 'FARM', 'MENTHOL CAMPHOR 14 GR ', 24, NULL, 1, 0.00, 75.00, 16, '', 11, 16, '2025-02-02 23:02:28'),
(1313, 'FARM', 'HOT MUTI 14 GR', 25, NULL, 1, 0.00, 100.00, 16, '', 11, 16, '2025-02-02 18:59:26'),
(1314, 'FARM', 'RISEGRA 100 ', 0, 10, 1, 30.00, 45.00, 0, '31/07/2027', 18, 16, '2025-07-07 12:13:32'),
(1315, 'FARM', 'RISEGRA 50', 1, 10, 1, 18.00, 27.00, 0, '30/11)2026', 18, 16, '2025-07-07 12:14:20'),
(1316, 'FARM', 'ZILANTIX SUSP AZITROMICINA', 20, NULL, 1, 0.00, 657.00, 0, '', 9, 16, '2024-12-11 01:19:32'),
(1317, 'FARM', 'TANTUM VERDE PASTILHAS 3+2,5 MG', 12, NULL, 1, 0.00, 200.50, 0, '', 11, 16, '2024-12-10 01:54:40'),
(1318, 'FARM', 'ANEROID KIT JD', 3, NULL, 1, 0.00, 2215.00, 16, '', 22, 0, '2025-01-13 16:47:22'),
(1319, 'FARM', 'RELOGIO DE BOLSO', 5, NULL, 1, 0.00, 360.53, 16, '', 22, 0, '2024-08-14 20:52:01'),
(1320, 'FARM', 'TESTE DE MALARIA JUSCHECK UNI', 14, NULL, 1, 0.00, 119.00, 0, '', 21, 0, '2025-03-28 14:45:27'),
(1321, 'FARM', 'TESTE DE HIV SINGLE-1 S', 7, NULL, 1, 0.00, 419.00, 0, '31/07/2027', 21, 0, '2025-01-30 14:30:21'),
(1322, 'FARM', 'GESSO 10X3M UNI', 37, NULL, 1, 0.00, 140.00, 0, '', 22, 0, '2024-09-05 10:42:49'),
(1323, 'FARM', 'AVENTAL DESCARTAVEL', 466, NULL, 1, 0.00, 13.00, 16, '', 22, 0, '2025-03-28 14:45:26'),
(1324, 'FARM', 'TANTUM VERDE  SOLUCAO ORAL 240 ML', 0, NULL, 1, 0.00, 540.00, 0, '', 14, 12, '2024-08-15 06:54:55'),
(1325, 'FARM', 'MASCARA S CIRURGICAS DESCARTAVEL', 59, 10, 1, 0.00, 8.00, 0, '19/01/2026', 0, 15, '2025-04-16 07:59:55'),
(1326, 'FARM', 'ACEDINE 150 MG COMP', 17, NULL, 1, 0.00, 26.40, 0, '', 13, 8, '2025-01-31 12:06:13'),
(1327, 'FARM', 'DICLOGEN 50 COMP', 4, 50, 1, 5.00, 7.50, 0, '30/09/2026', 10, 8, '2025-05-06 08:53:05'),
(1328, 'FARM', 'SERINGAS 5 ML', 288, 10, 1, 5.00, 8.00, 0, '31/05/2028', 22, 8, '2025-07-25 12:41:43'),
(1329, 'FARM', 'ERINOCIN SUSP 100 ML 250/5ML', 38, NULL, 1, 0.00, 212.00, 0, '', 9, 8, '2025-02-02 22:10:31'),
(1330, 'FARM', 'MUCOCLEAR INFANTIL XAROPE', 0, NULL, 1, 88.00, 131.00, 0, '31/07/2026', 14, 8, '2024-12-06 11:33:57'),
(1331, 'FARM', 'MUCOCLEAR ADULTO XAROPE', 19, 0, 1, 0.00, 140.00, 0, '', 0, 8, '2025-11-07 08:07:40'),
(1332, 'FARM', 'ACEVIT XAROPE 200 ML .', 0, NULL, 1, 0.00, 163.00, 0, '', 15, 8, '2024-11-21 08:56:02'),
(1333, 'FARM', 'ACETRIM-SUSPENSAO ORAL 100 ML', 28, NULL, 1, 0.00, 74.00, 0, '31/07/2027', 9, 8, '2025-02-02 19:55:03'),
(1334, 'FARM', 'ERYTHOGEN 250 MG 100ML', 1, NULL, 1, 0.00, 149.00, 0, '', 9, 8, '2025-01-16 17:24:56'),
(1335, 'FARM', 'ERYTHOGEN 125 MG 100ML', 0, NULL, 1, 0.00, 112.00, 0, '', 9, 8, '2025-01-12 01:59:12'),
(1336, 'FARM', 'HALIBUT POMADA ORIGINAL', 0, 10, 1, 200.00, 417.00, 0, '31/10/2026', 17, 12, '2025-07-04 13:58:38'),
(1337, 'FARM', 'DEXTROMETORFANO TUSSILENE 6MG/ML', 7, NULL, 1, 0.00, 432.00, 0, '31/05/2026', 14, 12, '2025-03-28 20:18:07'),
(1338, 'FARM', 'TATUM VERDE SOLUCAO ORAL Frs.x2941 240ml', 3, NULL, 1, 0.00, 540.00, 0, '30/09/2027', 10, 12, '2024-09-06 20:52:11'),
(1339, 'FARM', 'ben.u.ron supositorio 1000', 10, NULL, 1, 0.00, 45.00, 0, '12/2027', 10, 8, '2024-08-20 23:36:08'),
(1340, 'FARM', 'CORVITE CALCIO + D3 20COMP EFERV 1UN', 3, 2, 1, 217.00, 328.00, 0, '28/02/2026', 15, 8, '2025-07-08 09:50:05'),
(1341, 'FARM', 'OLEOBAN CREME BEBE 25% 200 G', 0, NULL, 1, 0.00, 1500.00, 0, '', 17, 8, '2025-01-11 20:24:49'),
(1343, 'FARM', 'OLEOBAN CREME DIARIO SKIN FIRST 200 G', 0, NULL, 1, 0.00, 1344.00, 0, '', 17, 12, '2024-12-10 22:35:27'),
(1344, 'FARM', 'OLEOBAN CREME BEBE FRASCO 450 GR', 1, NULL, 1, 0.00, 2692.00, 0, '', 17, 12, '2024-08-22 19:06:29'),
(1345, 'FARM', 'OLEOBAN CREME DIARIO ( 200+25%)', 2, NULL, 1, 0.00, 1328.00, 0, '31/02/2027', 17, 12, '2025-01-05 09:14:13'),
(1346, 'FARM', 'OLEOBAN CREME BEBE 200G', 0, NULL, 1, 0.00, 1267.00, 0, '', 17, 12, '2024-11-30 05:50:39'),
(1347, 'FARM', 'OPTIMUS COMP', 22, 1, 1, 0.00, 250.00, 0, '31/07/2027', 15, 12, '2025-06-13 16:03:20'),
(1348, 'FARM', 'SABONETE DE ENXOFRE 90 G', 4, NULL, 1, 0.00, 325.00, 0, '', 17, 12, '2025-01-15 00:42:43'),
(1349, 'FARM', 'SABONETE DE ALCATRAO', 8, NULL, 1, 0.00, 299.00, 0, '', 17, 12, '2024-08-23 01:39:16'),
(1350, 'FARM', 'SABONETE DE GLICERINA TRANSPARENTE PLUS', 1, 5, 1, 0.00, 280.00, 0, '', 17, 16, '2025-04-26 17:08:29'),
(1351, 'FARM', 'SABONETE DE GLICERINA AMARELO PLUS', 8, NULL, 1, 0.00, 280.00, 0, '', 17, 16, '2025-01-03 23:11:19'),
(1352, 'FARM', 'SABONETE DE GLICERINA VERDE PLUS', 10, NULL, 1, 0.00, 280.00, 0, '', 17, 16, '2024-08-22 20:36:11'),
(1353, 'FARM', 'SABONETE DE GLICERINA VERMELHO PLUS', 7, NULL, 1, 0.00, 280.00, 0, '', 17, 16, '2025-02-02 18:59:26'),
(1354, 'FARM', 'PIERROT BRANQUEADOR DENTIFRICO 75 ML', 4, NULL, 1, 0.00, 445.00, 0, '', 21, 16, '2025-02-02 18:59:26'),
(1355, 'FARM', 'PIERROT TOTAL CARE 6 IN 1 DE 500 ML', 2, NULL, 1, 0.00, 540.00, 0, '', 22, 16, '2024-08-22 21:29:09'),
(1356, 'FARM', 'EFFERFLU C IMMUNE BOOSTER', 3, NULL, 1, 0.00, 750.00, 0, '', 15, 16, '2024-08-22 21:31:36'),
(1357, 'FARM', 'EFFERFLU C PLUS IMMUNE BOOSTER', 3, NULL, 1, 0.00, 1000.00, 0, '', 15, 16, '2024-08-22 21:36:22'),
(1358, 'FARM', 'VITASMA XAROPE', 1, NULL, 1, 0.00, 225.00, 0, '', 14, 8, '2024-09-01 00:27:38'),
(1359, 'FARM', 'ADALAT CR 30 X28', 1, NULL, 1, 0.00, 1152.00, 0, '', 18, 8, '2024-08-28 17:23:38'),
(1361, 'FARM', 'PREDNACE 5 COMP', 30, NULL, 1, 0.00, 13.00, 0, '31/07/2027', 11, 13, '2025-02-03 17:49:16'),
(1362, 'EMPRE', 'ERINOCIN 500 MG COMP', 27, NULL, 1, 0.00, 87.80, 0, '', 9, 15, '2024-12-10 16:37:56'),
(1363, 'FARM', 'INSTALON 100 ML', 9, NULL, 1, 0.00, 106.00, 0, '31/01/2027', 17, 8, '2025-01-28 21:58:42'),
(1364, 'FARM', 'REQUILYTE SAQUETA', 89, NULL, 1, 0.00, 18.00, 0, '', 22, 13, '2025-03-28 23:11:46'),
(1365, 'FARM', 'PROMETAZINA SOL ORAL 100 ML', 16, NULL, 1, 0.00, 147.00, 0, '', 13, 13, '2025-01-05 03:44:03'),
(1368, 'FARM', 'CLOTRIMAZOL CREME BLUEPHARMA 10MG/ 20 G', 0, NULL, 1, 0.00, 165.00, 0, '', 9, 8, '2025-01-22 01:37:09'),
(1369, 'EMPRE', 'CLOTRIMAZOL CREME VAGINAL BLUEPHARMA 10MG/G 50 GOVINDG', 2, 3, 1, 0.00, 431.00, 0, 'VB', 16, 8, '2025-05-06 09:57:58'),
(1370, 'FARM', 'GEN C ACIDO ASCORBICO 500 MG', 22, NULL, 1, 0.00, 45.00, 0, '', 15, 8, '2024-11-15 22:53:49'),
(1371, 'FARM', 'PEN G FENOXIMETIL PENICILINA CP', 60, NULL, 1, 0.00, 82.00, 0, '', 9, 8, '2024-10-30 14:27:45'),
(1372, 'FARM', 'PENINCILINA BENZATINICA 2.4 MG INJ', 0, NULL, 1, 0.00, 82.00, 0, '', 9, 8, '2025-01-22 09:05:54'),
(1373, 'FARM', 'PEPSAMAR COMP ', 2, NULL, 1, 0.00, 68.00, 0, '', 13, 12, '2024-09-04 01:48:18'),
(1374, '', 'TANTUM VERDE PASTILHAS LIMAO', 2, 0, 1, 0.00, 244.00, 0, '', 0, 0, '2025-04-01 10:32:24'),
(1375, '', 'TANTUM VERDE PASTILHAS MEL LARANJA', 2, 0, 1, 0.00, 244.00, 0, '', 0, 0, '2025-04-01 10:36:55'),
(1376, '', 'TANTUM VERDE PASTILHAS MENTA ', 1, 0, 1, 0.00, 244.00, 0, '', 0, 0, '2025-04-01 10:37:39'),
(1377, 'FARM', 'OPTOMYCIN - D COLIRIO', 0, NULL, 1, 0.00, 278.00, 0, '', 20, 12, '2025-01-26 11:21:01'),
(1379, 'Selecione o prefixo', 'ACETILCISTEINA TUSSILENE 600 MG COMP EFERV', 2, NULL, 1, 0.00, 433.00, 0, '', 14, 12, '2024-08-23 00:17:32'),
(1380, 'FARM', 'AIRALAM X 24 COMP', 2, NULL, 1, 0.00, 632.00, 0, '', 9, 11, '2025-01-21 10:23:08'),
(1381, 'FARM', 'AMLODIPINA AZEVEDOS 10 MG X 30 COMP', 0, NULL, 1, 0.00, 195.66, 0, '', 18, 12, '2025-01-12 11:35:59'),
(1382, 'FARM', 'MENTOCAINA ANTI-INFLAM,CXS', 2, NULL, 1, 0.00, 246.50, 0, '', 11, 12, '2024-11-19 12:32:57'),
(1384, 'FARM', 'Lauroderme pasta 50g', 5, NULL, 1, 0.00, 702.00, 0, '', 17, 12, '2025-01-25 13:51:22'),
(1386, 'FARM', 'SABONETE BABA DE CARACOL 140 G', 1, NULL, 1, 0.00, 681.00, 0, '', 17, 12, '2024-08-23 00:46:44'),
(1387, 'FARM', 'SABONETE DE ARGILA E ALGAS', 1, NULL, 1, 0.00, 516.00, 0, '', 17, 12, '2025-01-10 16:38:52'),
(1390, 'FARM', 'METHYLDOPA 1X10 COMP', 0, NULL, 1, 0.00, 92.00, 0, '', 18, 12, '2024-12-08 16:42:15'),
(1393, '', 'ISOTRETINOINA OROTEX 10 MG ', 1, 0, 1, 0.00, 392.00, 0, '', 0, 0, '2025-04-01 12:53:19'),
(1395, 'FARM', 'NAPROXENO 500 MG COMP', 54, NULL, 1, 0.00, 188.16, 0, '', 10, 12, '2024-10-31 09:23:57'),
(1397, 'FARM', 'PARAMOLAN XAROPE', 2, NULL, 1, 0.00, 396.00, 0, '28/02/2028', 10, 12, '2025-03-28 20:41:16'),
(1400, 'FARM', 'DICLOFORTE COMP', 49, 100, 1, 12.80, 18.90, 0, '31/07/2027', 10, 12, '2025-07-10 10:18:00'),
(1403, 'FARM', 'DICLOKREN STRONG', 119, NULL, 1, 0.00, 135.00, 0, '05/2027', 17, 9, '2025-03-28 03:06:18'),
(1404, 'FARM', 'SANTRO SILDENAFIL', 58, NULL, 1, 0.00, 63.00, 0, '31/07/2026', 16, 8, '2025-02-02 19:41:17'),
(1405, 'FARM', 'BRUGEN-S IBUPROFENO SUSPENCAO ORAL', 34, 2, 1, 55.00, 82.00, 0, '30/11/2025', 10, 8, '2025-07-09 09:45:03'),
(1406, 'FARM', 'GENTAMICINA GOTAS 10 ML L-GENTA', 52, NULL, 1, 60.00, 89.00, 0, '30/07/2025', 20, 8, '2025-03-28 23:03:45'),
(1407, 'FARM', 'TESTE DE GRAVIDEZ 25 \'S', 0, NULL, 1, 40.00, 60.00, 0, '', 16, 8, '2024-09-02 22:02:07'),
(1408, 'FARM', 'AMOXI+AC.CLAV500MG+125MG COMP 2X10 CLAVUGEN', 0, 2, 1, 100.00, 187.00, 0, '31/03/2025', 9, 8, '2025-05-14 07:41:18'),
(1409, 'FARM', 'AMOXI+AC.CLAVULANICO 312,5 MG SUSP-CLAVUGEN DS', 50, NULL, 1, 185.00, 276.00, 0, '30/11/2025', 9, 8, '2025-03-28 06:29:06'),
(1410, 'FARM', 'ZINPLEX  B-COMPLEX', 0, NULL, 1, 0.00, 424.00, 0, '31/09/2026', 15, 12, '2025-02-02 19:16:04'),
(1411, 'PART', 'CHAIR MAN', 5, NULL, 1, 0.00, 10000.00, 0, '', 22, 17, '2024-08-25 02:16:57'),
(1412, 'FARM', 'ACEPEN- 125 suspencao', 1, NULL, 1, 0.00, 111.00, 0, '31/102025', 9, 8, '2025-01-21 17:38:26'),
(1413, 'FARM', 'PAROL 250mg/5ml susp.Frs.novo', 16, NULL, 1, 0.00, 240.00, 0, '', 10, 12, '2025-02-01 17:40:25'),
(1414, 'FARM', 'ADCO-LINCTOPEN XAROPE 100 ML', 5, NULL, 1, 0.00, 160.00, 0, '', 14, 12, '2025-02-01 02:46:06'),
(1415, 'FARM', 'OPTOMICYN COLIRIO 10 ML', 0, NULL, 1, 0.00, 142.00, 0, '31/11/2026', 9, 12, '2025-01-27 09:56:03'),
(1417, 'FARM', 'MILMALEATE 4MG', 14, NULL, 1, 0.00, 9.00, 0, '', 11, 12, '2024-11-15 13:49:28'),
(1418, 'FARM', 'MYCIMAC 500', 119, NULL, 1, 0.00, 142.00, 0, '', 9, 12, '2025-01-20 14:41:02'),
(1420, 'FARM', 'VICKS VAPORUB natur', 21, 10, 1, 99.00, 150.00, 0, '31/12/2027', 9, 16, '2025-07-07 11:54:20'),
(1421, 'Selecione o prefixo', 'HOT MUTI 14G', 2, NULL, 1, 0.00, 100.00, 0, '', 10, 16, '2024-12-08 18:37:29'),
(1422, 'FARM', 'MENTHOL CAMPHOR natur', 0, 10, 1, 49.00, 75.00, 0, '31/12/2027', 0, 16, '2025-07-07 12:26:59'),
(1423, 'FARM', 'COFLIX SYRUP 100ml', 87, 0, 1, 81.50, 130.00, 0, '30/04/2027', 14, 11, '2025-11-07 13:19:38'),
(1424, 'FARM', 'NEOTOS COUGH SYRUP 100ml', 65, 2, 1, 80.00, 116.00, 0, '30/04/2027', 14, 11, '2025-08-16 11:45:13'),
(1425, 'FARM', 'IBUCODAL CAPS', 4, NULL, 1, 0.00, 53.00, 0, '', 10, 11, '2025-01-09 10:00:39'),
(1426, 'FARM', 'NEOCIPRO TABS 10X10', 181, NULL, 1, 0.00, 57.00, 0, '', 9, 11, '2025-03-28 23:15:37'),
(1427, 'FARM', 'ZITHROCARE 500', 0, NULL, 1, 0.00, 30.00, 0, '31/12/2026', 14, 11, '2024-12-08 03:59:22'),
(1428, 'FARM', 'NEOVENT-4 10X10', 0, NULL, 1, 0.00, 10.00, 0, '31/12/2023', 14, 11, '2024-12-08 18:13:35'),
(1429, 'FARM', 'HERBAL RAPIDGESIC CREAM', 34, 5, 1, 50.00, 91.00, 0, '331/10/2026', 10, 11, '2025-05-26 06:28:46'),
(1430, 'FARM', 'NEODOL TABS 10X10', 25, NULL, 1, 0.00, 12.00, 0, '31/05/2026', 9, 11, '2024-11-20 03:31:52'),
(1431, 'FARM', 'TOSLIX-M SYRUP', 93, NULL, 1, 0.00, 126.00, 0, '31/04/2027', 14, 11, '2025-03-28 20:09:14'),
(1432, 'FARM', 'AMBOTEN Ambroxol,cloridarat', 105, NULL, 1, 0.00, 146.00, 0, '', 14, 11, '2025-01-10 07:38:10'),
(1433, 'FARM', 'METROGEN SUSPENSAO', 4, NULL, 1, 0.00, 75.00, 0, '', 9, 8, '2025-03-27 02:57:36'),
(1434, 'FARM', 'FLUGEN S 100 ML', 97, NULL, 1, 59.00, 88.00, 0, '28/02/2027', 11, 8, '2025-03-28 11:03:54'),
(1435, 'FARM', 'MUCOSHAN carbocisteina  5ml', 49, NULL, 1, 0.00, 108.00, 0, '02/2027', 13, 9, '2024-11-02 04:45:54'),
(1436, 'FARM', 'SHAN-B-PLEX XRP vitamina B COMPLEX Xrp', 37, NULL, 1, 0.00, 98.00, 0, '02/2026', 15, 9, '2025-02-01 02:45:47'),
(1437, 'FARM', 'SHANIGYL metronidazol oral 5ml', 97, NULL, 1, 0.00, 75.00, 0, '02/2027', 9, 9, '2025-03-28 15:25:47'),
(1438, 'FARM', 'SHANIDRYL Diphenhydramina', 96, 0, 1, 0.00, 75.00, 0, '02/2027', 14, 9, '2025-11-07 10:05:39'),
(1439, 'FARM', 'SHAN-B TABS vitamina B complex', 690, NULL, 1, 0.00, 10.20, 0, '02/2026', 15, 9, '2025-03-28 18:37:01'),
(1440, 'FARM', 'MODCYCLINE(TETRACICLINA POMADA OFTAMICA)', 8, NULL, 1, 0.00, 67.00, 0, '01/11/2026', 20, 8, '2024-12-12 11:26:35'),
(1441, 'FARM', 'MOCET EYE DROPS(clorafenicol colirio)', 21, 20, 1, 47.00, 70.00, 0, '01/05/26', 20, 8, '2025-10-11 13:50:09'),
(1442, 'FARM', 'GENTLEY EYE DROPS(gentamicina colirio)', 1, NULL, 1, 0.00, 105.00, 0, '01/03/207', 20, 8, '2025-01-22 10:45:47'),
(1443, 'FARM', 'ACTIFEEL', 0, NULL, 1, 0.00, 107.00, 0, '30/04/2027', 10, 8, '2024-11-22 09:13:46'),
(1444, 'FARM', 'FOURTS B CAPS 3X10', 73, 10, 1, 15.00, 42.30, 0, '30/09/2025', 15, 8, '2025-06-19 15:04:42'),
(1445, 'FARM', 'MOM IRON', 10, NULL, 1, 0.00, 189.00, 0, '30/03/2027', 15, 8, '2025-02-02 19:16:05'),
(1446, 'FARM', 'MOXMOD 250CPS(AMOXICILINA)', 495, NULL, 1, 0.00, 29.00, 0, '28/02/2027', 9, 8, '2025-03-28 21:52:16'),
(1447, 'FARM', 'REDIN PN-XAROPE', 8, NULL, 1, 0.00, 177.00, 0, '31/01/26', 15, 8, '2025-01-31 15:15:41'),
(1448, 'FARM', 'IBUGEN', 4, NULL, 1, 0.00, 48.00, 0, '31/01/2027', 10, 8, '2025-02-01 17:24:22'),
(1449, 'FARM', 'PYLOOCAIN ANTI HEMOROIDS OINTMENT', 6, NULL, 1, 0.00, 112.00, 0, '30/01/2027', 17, 8, '2025-02-02 00:35:50'),
(1450, 'FARM', 'NYGEN-V,creme vaginal', 0, NULL, 1, 0.00, 103.00, 0, '31/01/2027', 16, 8, '2024-11-29 22:55:07'),
(1451, 'FARM', 'ALERJON,COLIRIO SOLUCAO 10ML', 1, NULL, 1, 0.00, 697.00, 0, '31/07/2026', 20, 8, '2025-01-12 01:32:07'),
(1452, 'FARM', 'TEDOL,CREME 30G', 3, 0, 1, 0.00, 517.00, 0, '31/10/2026', 17, 8, '2025-11-07 06:50:54'),
(1453, 'FARM', 'GENCEF O  (cefixime tablets 400mg', 71, NULL, 1, 0.00, 25.30, 0, '31/03/2027', 9, 8, '2025-02-03 12:21:23'),
(1454, 'FARM', 'CLOTRISAN  V6 clotrimazol vaginal comprimidos BP', 0, 10, 1, 64.00, 95.00, 0, '30/09/25', 16, 8, '2025-07-16 12:36:36'),
(1455, 'FARM', 'GENCITRIN  cetrizine 10 mg', 7, NULL, 1, 11.00, 16.40, 0, '30/09/2026', 11, 8, '2025-02-03 15:18:28'),
(1456, 'FARM', 'ALUGEN HIDROXIDO DE ALUMINIO 500MG', 15, NULL, 1, 0.00, 13.30, 0, '30/09/2026', 13, 8, '2025-03-28 06:48:11'),
(1457, 'FARM', 'AZITROMICINA 500 BASI', 6, NULL, 1, 0.00, 86.00, 0, '', 9, 8, '2025-03-27 12:52:02'),
(1458, 'FARM', 'ACEJET 5 ML SERINGAS', 38, NULL, 1, 0.00, 6.00, 0, '31/05/2028', 22, 8, '2025-03-27 21:43:23'),
(1459, 'FARM', 'PENICILINA BENZATINICA 2,4 MEGA INJECTAVEL', 4, NULL, 1, 0.00, 82.00, 0, '31/10/2025', 23, 8, '2025-01-29 13:26:04'),
(1460, 'FARM', 'LIDOCAINE INJECTION 2%', 1, NULL, 1, 0.00, 266.00, 0, '30/11/2025', 10, 13, '2025-02-01 17:21:45'),
(1461, '', 'SORTEX TESTE DE GRAVIDEZ', 2, 0, 1, 0.00, 60.00, 0, '19/02/2027', 0, 0, '2025-04-01 15:02:36'),
(1462, 'FARM', 'GENSONE PREDNISOLONE 5MG', 9, NULL, 1, 0.00, 14.80, 0, '31/12/2026', 11, 8, '2025-01-13 13:46:03'),
(1463, 'FARM', 'GENTOCIL COLIRIO SOLUCAO 5ML', 0, 0, 1, 0.00, 494.00, 0, '31/03/2029', 20, 8, '2025-11-07 06:50:05'),
(1464, 'FARM', 'MEOCIL,POMADA OFTALMICA 5G', 1, NULL, 1, 0.00, 614.00, 0, '28/02/2027', 20, 8, '2024-12-12 15:34:57'),
(1465, 'FARM', 'RONIC,COLIRIO SOLUCAO 5ML', 0, 5, 1, 400.00, 611.00, 0, '30/04/2027', 20, 8, '2025-10-09 15:09:45'),
(1466, 'FARM', 'OTTODUO GOTAS AURICULARES SOLUCAO', 3, NULL, 1, 0.00, 636.00, 0, '31/01/2026', 20, 8, '2025-01-26 03:53:53'),
(1467, 'FARM', 'PANDERMIL POMADA 30MG', 0, 2, 1, 389.00, 569.00, 0, '31/10/2026', 17, 8, '2025-05-06 11:21:53'),
(1468, 'FARM', 'PSODERMIL POMADA 30G', 0, NULL, 1, 0.00, 419.00, 0, '31/O3/2027', 17, 8, '2024-12-03 17:39:25'),
(1469, 'FARM', 'LASSADERMIL,POMADA 25G', 2, NULL, 1, 0.00, 688.00, 0, '30/11/26', 17, 8, '2025-01-25 21:10:51'),
(1470, 'FARM', 'MULETAS MEDIOS M (UNIDADE)', 0, NULL, 1, 0.00, 1426.00, 0, '', 0, 8, '2025-01-31 02:17:51'),
(1471, 'FARM', 'SINVASTATINA BALDACCI', 9, NULL, 1, 0.00, 170.00, 0, '12/2026', 18, 12, '2024-11-19 18:48:54'),
(1472, 'FARM', 'PENICILIN PROCAINE INJ 3 MEGAS ', 0, NULL, 1, 0.00, 104.00, 0, '28/02/2026', 9, 8, '2025-03-27 07:04:57'),
(1473, 'FARM', 'ERECTA 50 MG', 14, NULL, 1, 0.00, 68.00, 0, '', 16, 9, '2025-01-13 14:57:49'),
(1474, 'Selecione o prefixo', 'DICLODOR  (DICLOFENAC) 75MG /3ML CX', 0, 5, 1, 10.00, 20.00, 16, '02/2027', 0, 0, '2025-05-23 09:17:35'),
(1476, 'FARM', 'AMOX-, 10X10 CAPS', 9, NULL, 1, 0.00, 38.20, 0, '30/11/2025', 9, 8, '2024-12-12 05:15:27'),
(1477, 'FARM', 'LISINOPRIL ECAPRIL 20MG', 0, NULL, 1, 0.00, 183.50, 0, '31/12/2025', 18, 12, '2025-02-01 03:15:57'),
(1478, 'FARM', 'NOVAFLOX', 7, NULL, 1, 0.00, 372.00, 0, '31/03/2027', 20, 12, '2025-01-28 14:20:00'),
(1479, 'FARM', 'KETACONAZOLE CREAM CREAM 10G DERM KETA ', 40, NULL, 1, 0.00, 45.00, 0, '30/06/2027', 9, 8, '2025-03-28 06:29:06'),
(1480, 'FARM', 'ACEMOL,10X10 COMP', 212, 100, 1, 4.50, 10.00, 0, '31/05/2027', 10, 13, '2025-11-06 12:26:41'),
(1481, 'FARM', 'ACECARDIA 30 MG. COMP', 13, NULL, 1, 0.00, 53.20, 0, '31/07/2027', 18, 13, '2025-03-28 22:11:45'),
(1482, 'Selecione o prefixo', 'BUSCOCINE 10MG,COMP', 0, NULL, 1, 0.00, 56.00, 16, '', 0, 0, '2024-12-05 21:48:27'),
(1484, 'FARM', 'LOPERIX 2MG , CAPS 10X10', 225, 5, 1, 0.00, 15.00, 0, '31/12/2026', 13, 13, '2025-11-07 07:59:13'),
(1485, 'FARM', 'HIDROCLORETO DE PROMETAZINA 25MG BP,COMP. 10X10', 85, 0, 1, 0.00, 17.00, 0, '31/03/2027', 13, 15, '2025-11-07 08:02:45'),
(1486, 'FARM', 'PREDNACE 20.COMP 10X10', 0, 0, 1, 0.00, 67.00, 0, '31/07/2027', 14, 13, '2025-11-07 08:15:18'),
(1494, 'FARM', 'ACECARB 200 MG 10X10', 0, 0, 1, 0.00, 48.00, 0, '31/12/2026', 10, 13, '2025-11-07 07:52:03'),
(1496, 'FARM', 'AZICIN-500 ,1X3 COMP', 1, 0, 1, 0.00, 30.00, 0, '28/02/2027', 9, 13, '2025-11-07 08:44:01'),
(1497, 'FARM', 'ERINOCIN 250, 100 ML', 9, 0, 1, 0.00, 222.00, 0, '30/06/2027', 9, 13, '2025-11-07 08:10:16'),
(1500, 'FARM', 'ACEFULVIN-500,10X10', 31, 5, 1, 45.00, 97.50, 0, '30/06/2027', 22, 13, '2025-05-02 14:09:42'),
(1501, 'FARM', 'AMOX-250, 100 ML SUSP', 1, 10, 1, 50.00, 89.00, 0, '31/10/205', 9, 15, '2025-11-07 08:34:51'),
(1502, 'FARM', 'ACEPEN- 500 10X10', 39, 50, 1, 50.00, 63.00, 0, '30/11/2025', 9, 13, '2025-11-07 08:40:50'),
(1503, 'FARM', 'ACE-C  500..3X10 ', 79, NULL, 1, 0.00, 39.40, 0, '31/05/2026', 15, 13, '2025-01-22 11:37:41'),
(1504, 'FARM', 'PHIENIREX-S ,SUSPESAO ORAL ,100', 0, NULL, 1, 0.00, 97.00, 0, '30/06/2027', 14, 13, '2024-12-05 23:40:55'),
(1506, 'FARM', 'PHENIREX-S ,SUSPESAO ORAL ,100', 2, NULL, 1, 0.00, 97.00, 0, '30/06/2027', 11, 13, '2025-02-02 20:07:28'),
(1507, 'FARM', 'ACE S CALAMINE', 27, NULL, 1, 0.00, 119.00, 0, '28/02/2027', 17, 13, '2025-03-28 18:57:09'),
(1509, 'FARM', 'POVINANZ, 10%', 0, NULL, 1, 0.00, 67.00, 0, '30/*04/2026', 17, 15, '2025-01-26 22:54:04'),
(1510, 'FARM', 'CEFTRIOXONA SODICA 1G +WFI INJ', 2, 0, 1, 0.00, 95.00, 0, '31/03/2026', 21, 13, '2025-11-07 07:02:26'),
(1511, 'FARM', 'NISTA-P ,100  000 UI, ', 13, NULL, 1, 0.00, 119.00, 0, '30/04/2026', 16, 13, '2025-02-03 12:21:23'),
(1512, 'FARM', 'SEFIMEX DT 400', 35, NULL, 1, 0.00, 29.00, 0, '31/05/2026', 9, 13, '2025-01-31 20:22:02'),
(1513, 'FARM', 'SEFIMEX DS ,SUSP ,100', 7, NULL, 1, 0.00, 363.00, 0, '31/05/2026', 9, 13, '2024-12-06 00:53:13'),
(1514, 'FARM', 'ACECLAV-457, SAQUETA', 240, NULL, 1, 0.00, 25.00, 0, '30/04/2026', 9, 13, '2024-12-06 00:53:13'),
(1515, 'FARM', 'ACECLAV-228.5, SAQUETA', 168, NULL, 1, 0.00, 17.00, 0, '30/04/2026', 9, 13, '2024-12-06 12:44:57'),
(1516, 'FARM', 'BACISEPT,30MG', 24, NULL, 1, 0.00, 133.02, 0, '30/06/23027', 17, 13, '2025-03-27 02:22:32'),
(1517, 'FARM', 'NAYCOLD', 21, 100, 1, 10.00, 20.00, 0, '31/08/2027', 14, 13, '2025-11-07 08:06:54'),
(1518, 'FARM', 'LUVAS DE EXAMINACAO      M', 0, 1, 1, 420.00, 621.00, 0, '30/042026', 22, 13, '2025-05-10 06:54:08'),
(1519, 'FARM', 'LUVAS DE EXAMINACAO      L', 0, 2, 1, 420.00, 621.00, 0, '30/042026', 0, 13, '2025-05-10 06:53:24'),
(1520, 'FARM', 'SABONETE OLEOBAN DIARIO', 10, NULL, 1, 0.00, 569.00, 0, '30/11/2026', 17, 12, '2025-01-04 05:14:31'),
(1521, 'FARM', 'MIGRETIL CX 20 COMP novo lote', 5, 5, 1, 100.00, 358.00, 0, '30/02/2027', 10, 8, '2025-11-07 07:23:57'),
(1522, 'FARM', 'TESTE DE GRAVIDEZ', 7, NULL, 1, 0.00, 60.00, 0, '19/02/2027', 16, 8, '2025-01-05 10:37:56'),
(1523, 'FARM', 'TETRACICLINA POMADA OFTAMICA 1% 5G', 17, NULL, 1, 0.00, 40.00, 0, '04/06/2027', 20, 8, '2025-01-30 22:08:32'),
(1524, 'FARM', 'ROLO DE ALGODAO 500 G', 2, NULL, 1, 0.00, 402.00, 0, '31/07/2028', 22, 8, '2024-12-10 20:39:57'),
(1525, 'FARM', 'PARALOGO FORTE TABLETS', 412, NULL, 1, 0.00, 14.80, 0, '31/07/2027', 10, 8, '2025-03-28 23:44:55'),
(1526, 'FARM', 'AZITHROGEN 200 MG /5 ML15ML XAROPE', 9, NULL, 1, 0.00, 73.00, 0, '30/04/2026', 9, 0, '2025-03-28 20:41:16'),
(1527, 'FARM', 'ADESIVO DE ZINCO- ROLO 5CMXM', 4, NULL, 1, 0.00, 113.00, 0, '31/03/2029', 22, 8, '2025-03-27 20:02:58'),
(1528, 'FARM', 'ORIGIDINE (IODOPOVIDONA) 10% 125 ML', 16, NULL, 1, 0.00, 234.00, 0, '31/07/2027', 17, 8, '2025-01-22 03:10:09'),
(1529, 'FARM', 'DOXYDERMA 100MG CX ,20 CP', 4, NULL, 1, 0.00, 234.70, 0, '30/11/2025', 9, 8, '2024-12-10 21:01:58'),
(1531, 'FARM', 'SORO FISIOLOGICO QI 60 ML (GOTAS NASAIS)', 39, NULL, 1, 0.00, 74.00, 0, '30/06/2026', 14, 8, '2025-01-26 20:43:02'),
(1532, 'FARM', 'OFRIVID EYES DROPS ', 6, NULL, 1, 0.00, 89.00, 0, '30/04/2027', 20, 8, '2025-03-28 02:01:14'),
(1533, 'FARM', 'FUSIDIC ACID CREAM 2% ', 1, NULL, 1, 0.00, 148.00, 0, '31/1/2026', 21, 8, '2025-01-22 12:14:15'),
(1534, 'FARM', 'DERMOSONE BETAMETASONA CREME 0.05% 30G', 0, 10, 1, 50.00, 82.00, 0, '30/11/2026', 17, 8, '2025-11-07 09:22:01'),
(1535, 'FARM', 'BRUGEN 200', 263, NULL, 1, 0.00, 12.00, 0, '30/01/2026', 10, 8, '2025-03-28 21:52:17'),
(1536, 'FARM', 'AMPICILINA SODIUM INJ', 28, NULL, 1, 0.00, 82.00, 0, '31/12/2025', 9, 8, '2025-03-27 04:53:32'),
(1537, 'FARM', 'GENVIR  ACICLOVIR COMP 200MG 10X10', 5, 20, 1, 33.00, 49.00, 0, '31/05/2026', 13, 8, '2025-06-12 11:10:05'),
(1538, 'FARM', 'DAXAVAL', 3, NULL, 1, 0.00, 996.00, 0, '02/02/2026', 20, 8, '2024-12-10 21:01:59'),
(1539, 'FARM', 'PROMETAZINA  BASI 25MG/ ML SOL INJ 2 ML 10 AMPOLAS', 30, NULL, 1, 0.00, 36.00, 0, '30/10/2026', 23, 8, '2025-01-25 01:06:13'),
(1548, 'FARM', 'AMINOGEM (aminofilina ) 100mg ', 146, 0, 1, 0.00, 15.00, 0, '', 18, 8, '2025-11-07 09:04:28'),
(1549, 'FARM', 'ACEPEN_125 xarope', 0, NULL, 1, 0.00, 122.00, 0, '', 9, 8, '2025-01-03 08:00:05'),
(1550, 'FARM', 'METHYLDOPA COMP. 250 MG 10X10', 47, NULL, 1, 0.00, 99.80, 0, '02/2028', 18, 14, '2025-01-29 16:59:37'),
(1551, 'FARM', 'LUMITER DT (ARTEMTHR 20MG/ LUMIFTRN 120 MG COMP 1X6)', 1, 10, 1, 140.00, 100.00, 0, '04/2027', 12, 14, '2025-11-07 10:14:22'),
(1554, 'FARM', 'AMYN 125 MG', 9, NULL, 1, 0.00, 82.00, 0, '03/2027', 9, 14, '2025-01-29 14:44:18'),
(1555, 'FARM', 'FERICAL12 (IRON E ZINC, VITAMIN B COMPLEX', 47, NULL, 1, 0.00, 142.00, 0, '12/2025', 0, 14, '2025-02-02 09:58:50'),
(1556, 'FARM', 'MULTON FORTE ', 23, NULL, 1, 0.00, 12.00, 0, '12/2026', 15, 14, '2025-01-30 11:36:26'),
(1557, 'FARM', 'PIRODOXINE 50 MG TAB - 10X10', 448, NULL, 1, 0.00, 39.40, 0, '10/2025', 21, 14, '2025-02-02 14:45:15'),
(1558, 'FARM', 'DAWA -CPM', 190, NULL, 1, 0.00, 68.00, 0, '02/2027', 14, 13, '2025-03-28 23:44:31'),
(1559, 'EMPRE', 'AMYN 250 MG', 87, NULL, 1, 0.00, 93.00, 0, '', 9, 14, '2025-03-28 22:16:22'),
(1560, 'FARM', 'sinvastatina bluepharma 20 mg', 12, NULL, 1, 0.00, 126.00, 0, '10/2026', 18, 8, '2025-01-22 10:43:33'),
(1561, 'FARM', 'TERMOMETRO DE MERCURIO ', 2, NULL, 1, 0.00, 179.00, 0, '', 21, 8, '2025-03-28 14:45:27'),
(1562, 'FARM', 'DISPOGEM  insulina 1 ML', 147, NULL, 1, 0.00, 8.00, 0, '04/05/2028', 21, 8, '2025-02-03 02:29:39'),
(1563, 'FARM', 'PARAMOLAN SUPOSITORIO 250 MG', 0, NULL, 1, 0.00, 37.00, 0, '30/10/2026', 21, 12, '2025-01-30 06:16:17'),
(1564, 'FARM', 'OPTIMOL 0.5%', 3, NULL, 1, 0.00, 407.00, 0, '31/07/2027', 20, 12, '2024-12-13 08:31:31'),
(1566, 'FARM', 'ORASPAS', 2, NULL, 1, 0.00, 103.00, 0, '10/2027', 21, 12, '2025-03-28 05:39:49'),
(1567, 'FARM', 'DICLOFENAC AZEVEDOS', 6, NULL, 1, 0.00, 536.00, 0, '', 17, 12, '2025-01-25 20:31:01'),
(1568, 'FARM', 'SORO FISIOLOGICO QI 60 ML (GOTAS NASAIS) AZEVEDOS', 2, NULL, 1, 0.00, 92.00, 0, '30/04/2026', 0, 12, '2025-02-02 19:44:15'),
(1569, 'FARM', 'CICLOVIRAL  400MG ', 15, NULL, 1, 0.00, 180.00, 0, '30/04/2026', 22, 12, '2024-12-13 09:18:08'),
(1573, 'FARM', 'VITAMIX TABS 10X10', 563, 0, 1, 0.00, 20.00, 0, '31/12/2025', 15, 11, '2025-11-06 12:28:04'),
(1574, 'FARM', 'CANDID MOUTH PAINT 15 ML', 3, NULL, 1, 0.00, 77.06, 0, '30/04/2026', 17, 11, '2025-03-28 20:51:24'),
(1575, 'FARM', 'HERBALS COUGH LOZENGES ORANGE', 0, NULL, 1, 0.00, 18.00, 0, '31/07/2026', 21, 11, '2025-01-24 14:01:11'),
(1576, 'FARM', 'HERBALS COUGH LOZENGES Menta', 87, 0, 1, 0.00, 20.00, 0, '31/03/2027', 21, 11, '2025-11-07 13:14:37'),
(1577, 'FARM', 'HERBALS COUGH LOZENGE FRAMBOESA MORANGO', 90, 0, 1, 0.00, 20.00, 0, '31/03/2027', 22, 11, '2025-11-07 13:19:14'),
(1578, 'FARM', 'PARACETAMOL AZEVEDOS    1000 MG', 0, 5, 1, 85.00, 108.00, 0, '05/2025', 22, 12, '2025-07-03 14:59:49'),
(1579, 'FARM', 'silvandex creme', 30, 10, 1, 50.00, 77.00, 0, '30/06/2027', 21, 8, '2025-07-16 12:29:52'),
(1580, 'EMPRE', 'TASECTAN', 2, NULL, 1, 0.00, 46.00, 0, '01/2025', 13, 12, '2025-01-12 18:26:00'),
(1581, 'FARM', 'PREDNIOCIL', 0, NULL, 1, 0.00, 538.00, 0, '04/2029', 20, 8, '2025-01-28 14:53:37'),
(1582, 'FARM', 'BIOLECTRA CALCIO ', 28, NULL, 1, 0.00, 61.00, 0, '31/06/2026', 22, 0, '2025-03-27 10:13:19'),
(1584, 'FARM', 'PEDIFEN Xarope Frs.x 100ml XX', 0, 0, 1, 0.00, 271.00, 0, '30/11/2026', 10, 12, '2025-04-08 09:21:35'),
(1587, 'FARM', 'BENYLIN 4 FLU', 1, NULL, 1, 0.00, 676.00, 0, '31/06/2026', 11, 12, '2025-01-18 22:06:46'),
(1588, 'FARM', 'BIOLECTRA  MAGNESIO CAP', 0, NULL, 1, 0.00, 483.00, 0, '31/10/2026', 0, 12, '2025-01-05 09:09:19'),
(1593, 'FARM', 'FLURIFEN', 0, NULL, 1, 0.00, 306.00, 0, '01/2026', 22, 12, '2025-01-12 14:44:55'),
(1594, 'FARM', 'DUOSKIN CREME', 0, NULL, 1, 0.00, 464.00, 0, '30/04//2026', 0, 12, '2025-01-05 09:12:51'),
(1598, 'FARM', 'HYDROWN CREAM', 85, NULL, 1, 0.00, 165.00, 0, '08/2027', 17, 9, '2025-03-28 18:03:25'),
(1599, 'FARM', 'BETASHA-S ', 17, NULL, 1, 0.00, 90.00, 0, '05/2027', 17, 9, '2025-01-31 01:32:43');
INSERT INTO `produto` (`idproduto`, `prefico`, `nomeproduto`, `stock`, `stock_min`, `stocavel`, `preco_compra`, `preco`, `iva`, `codbar`, `grupo`, `familia`, `data`) VALUES
(1600, 'FARM', 'COFISHA ', 55, NULL, 1, 0.00, 94.00, 0, '04/2027', 14, 9, '2025-03-28 04:59:10'),
(1601, 'FARM', 'AMOXICILINA+ACIDO CLAVULANICO BLUEPHARMA', 1, NULL, 1, 0.00, 264.50, 0, '06/2025', 9, 8, '2025-01-22 09:36:31'),
(1602, 'FARM', 'STOPDOR (PARACETAMOL) xarope', 85, 10, 1, 108.00, 159.00, 0, '30/11/2026', 10, 8, '2025-05-27 14:46:50'),
(1603, 'FARM', 'COTRIGEN S  SUSP 100ML', 0, 5, 1, 0.00, 82.00, 0, '31/01/2027', 9, 8, '2025-11-07 09:21:38'),
(1604, 'FARM', 'COTRIGEM XAROPE', 23, NULL, 1, 0.00, 82.00, 0, '31/01/2027', 9, 8, '2025-03-28 22:27:21'),
(1606, 'FARM', 'LUMITER DT (ARTEMTHR 20MG+ LUMFENTRINA120 MG) COMP( 1X24)', 49, NULL, 1, 0.00, 179.00, 0, '', 12, 14, '2025-03-28 21:46:39'),
(1608, 'Selecione o prefixo', 'COLDRIL CAPS 500mg+30.0mg+2.0mg-12x20s', 252, 10, 1, 100.00, 243.00, 0, '8/30/2027', 14, 14, '2025-11-07 10:10:31'),
(1609, 'FARM', 'ACARIBIAL SOL..CUT', 3, NULL, 1, 0.00, 401.00, 0, '30/04/2029', 17, 8, '2025-03-27 15:03:16'),
(1610, 'FARM', 'CLOPIDOGREL BLUEPHARMA 75 MGCX28 COMP[', 6, NULL, 1, 0.00, 398.50, 0, '03/2026', 18, 8, '2025-01-14 22:29:20'),
(1611, 'FARM', 'ENSER(ACIDO FOLIUCO) 5MG 60 COMP', 89, NULL, 1, 0.00, 64.00, 0, '30/05/2026', 15, 8, '2025-03-28 10:08:45'),
(1612, 'FARM', 'ANSER (ACIDO FOLICO) 5MG 60CP', 0, NULL, 1, 0.00, 64.00, 0, '', 15, 8, '2025-01-14 21:55:05'),
(1613, 'FARM', 'GLUCOSE 5% BASI 1000  MLT.H', 16, NULL, 1, 0.00, 164.00, 0, '31/08/2029', 21, 8, '2025-03-28 06:29:07'),
(1614, 'FARM', 'CASTOR OIL 100 ML', 0, NULL, 1, 0.00, 177.00, 0, '', 15, 8, '2025-01-14 22:04:48'),
(1615, 'FARM', 'METROGEN CP 250', 0, NULL, 1, 0.00, 13.00, 0, '', 9, 8, '2025-01-14 22:06:39'),
(1616, 'FARM', 'STROM 50MG TRAMADOL 10X10', 46, NULL, 1, 0.00, 149.00, 0, '30/06/2026', 10, 8, '2025-03-28 14:37:51'),
(1617, 'FARM', 'AMLODIPINA BASI 10 MG 60 COMP', 29, NULL, 1, 0.00, 94.00, 0, '28/02/2028', 0, 8, '2025-01-24 10:40:54'),
(1618, 'FARM', 'DIMIDON(IBUPROFENO) 400MG CX .20CP', 12, NULL, 1, 0.00, 106.00, 0, '28/02/2028', 10, 8, '2025-03-27 05:11:01'),
(1619, 'FARM', 'ACE- FORTE PLUS, CAPS 10X2', 0, 50, 1, 0.00, 92.00, 0, '30/09/2027', 0, 13, '2025-09-16 16:25:33'),
(1620, 'FARM', 'ACE-FORTE PLUS, CAPS 10X2', 38, 10, 1, 72.00, 92.00, 0, '30/04/2027', 0, 13, '2025-11-07 08:19:24'),
(1621, 'FARM', 'MUCOCLEAR , 30 MG COMP.1X10', 14, NULL, 1, 0.00, 25.00, 0, '31/02/2026', 11, 13, '2025-02-03 05:44:08'),
(1622, 'FARM', 'DERMATRIX - CG , 30G 1 S ', 97, NULL, 1, 0.00, 82.00, 0, '31/07/2027', 0, 13, '2025-02-03 04:05:31'),
(1623, 'FARM', 'POVINANZ , 5% ,20G', 79, NULL, 1, 0.00, 67.00, 0, '30/04/2027', 20, 13, '2025-02-03 15:21:37'),
(1624, 'FARM', 'FLUCLOCIN 500 , 1X 10, CAPS', 0, NULL, 1, 0.00, 130.00, 0, '', 9, 13, '2025-02-03 09:39:03'),
(1625, 'FARM', 'ACENAC-SR-100, CAPS.10X1', 27, NULL, 1, 0.00, 33.00, 0, '30/04/2026', 0, 13, '2025-02-02 13:20:27'),
(1627, 'FARM', 'TADASIL 5,1X4 COMP ', 4, NULL, 1, 0.00, 48.00, 0, '31/10/2026', 0, 13, '2025-01-19 04:23:53'),
(1628, 'FARM', 'TADASIL 10, 1X4', 2, NULL, 1, 0.00, 67.00, 0, '30/04/2026', 16, 13, '2025-02-03 10:07:40'),
(1629, 'FARM', 'TADASIL 20, 1X4', 0, NULL, 1, 0.00, 141.00, 0, '28/02/2026', 16, 13, '2025-02-03 10:07:40'),
(1630, 'FARM', 'ACENAC GEL 30G, GEL', 14, NULL, 1, 0.00, 89.00, 0, '31/07/2027', 0, 13, '2025-01-26 14:40:13'),
(1631, 'FARM', 'NACL VISION , SOLUCAO OFT', 9, NULL, 1, 0.00, 89.00, 0, '30/09/2026', 20, 13, '2025-02-01 10:41:17'),
(1632, 'FARM', 'PREDNOLVISION SOL OFT ', 2, 2, 1, 0.00, 114.00, 0, '31/05/2026', 0, 13, '2025-11-07 07:54:04'),
(1634, 'FARM', 'CANDIGEN-BG CREAM 30 GRAM', 58, NULL, 1, 0.00, 94.00, 0, '11/2025', 0, 8, '2025-03-28 09:55:49'),
(1635, 'FARM', 'NEOGLOVES  TAMANHO GRANDE', 2, NULL, 1, 0.00, 447.00, 0, '', 22, 8, '2025-03-28 11:03:14'),
(1636, 'FARM', 'NEOGLOVES  TAMANHO  MEDIO', 0, NULL, 1, 0.00, 447.00, 0, '', 0, 8, '2025-02-02 23:11:32'),
(1637, 'FARM', 'HIDROCIL FILAC', 3, NULL, 1, 0.00, 703.00, 0, '30/09/2027', 20, 8, '2025-03-28 06:29:08'),
(1638, 'FARM', 'MACROLYN NOVO LOTE', 0, NULL, 1, 0.00, 80.00, 0, '31/01/2027', 9, 12, '2025-01-20 08:35:03'),
(1643, 'FARM', 'GENMOX CAPS 500', 1, NULL, 1, 0.00, 33.00, 0, '29/07/2027', 9, 8, '2025-01-25 04:47:27'),
(1644, 'FARM', 'VOMIPAR', 74, NULL, 1, 0.00, 98.00, 0, '2027/02', 21, 9, '2025-03-28 21:42:13'),
(1645, 'FARM', 'SHANYSTIN TABS', 25, NULL, 1, 0.00, 90.00, 0, '', 16, 9, '2025-03-27 15:59:30'),
(1646, 'FARM', 'BURNKUL-30GR', 25, NULL, 1, 0.00, 98.00, 0, '', 21, 9, '2025-03-28 20:53:51'),
(1647, 'FARM', 'APETISHA XAROPE', 131, NULL, 1, 0.00, 98.00, 0, '', 15, 9, '2025-03-28 22:16:50'),
(1648, 'FARM', 'CEFEXIMA NEOCEF', 5, NULL, 1, 0.00, 1365.00, 0, '', 9, 12, '2025-01-25 13:51:24'),
(1649, 'FARM', 'CLARITROMICINA BLU 500 MG', 0, 0, 1, 0.00, 950.00, 0, '062026', 9, 8, '2025-11-07 07:36:12'),
(1658, 'FARM', 'TRIMSRO', 1, NULL, 1, 0.00, 350.00, 0, '', 21, 8, '2025-01-22 10:29:52'),
(1659, 'FARM', 'AGUA OXIGENADA ORIGINPHARMA', 4, NULL, 1, 0.00, 80.00, 0, '31/07/2027', 0, 8, '2025-02-03 08:37:57'),
(1660, 'FARM', 'TOUCA DESCARTAVEL 100', 185, NULL, 1, 0.00, 4.00, 0, '31/07/2027', 22, 8, '2025-01-26 04:29:03'),
(1661, 'FARM', 'RONIC ', 0, NULL, 1, 0.00, 514.00, 0, '31/07/2027', 20, 8, '2025-01-30 11:48:36'),
(1662, 'FARM', 'MULTONE  XAROPE', 9, NULL, 1, 0.00, 132.00, 0, '12/2025', 15, 14, '2025-02-01 03:40:23'),
(1663, 'FARM', 'DERMAZINE CREME (sulfadiazina de prata', 10, NULL, 1, 0.00, 105.00, 0, '09/2027', 17, 14, '2025-03-27 07:04:58'),
(1666, 'FARM', 'THEOXONE INJECTION (cefriaxona  1gm)', 20, NULL, 1, 0.00, 93.00, 0, '08/2027', 9, 14, '2025-01-23 09:17:23'),
(1667, 'FARM', 'IBUPAR TABLET ', 103, 0, 1, 0.00, 98.00, 0, '02/2027', 10, 14, '2025-11-07 10:12:47'),
(1668, 'FARM', 'DACOLD', 3, NULL, 1, 0.00, 57.00, 0, '02/2027', 14, 14, '2025-03-27 03:49:22'),
(1669, 'FARM', 'TETRACYCLINE OFT', 1, NULL, 1, 0.00, 57.00, 0, '03/2027', 20, 14, '2025-01-30 14:27:12'),
(1670, 'FARM', 'HAERMVITAL PLUS,COM 10X10', 13, NULL, 1, 0.00, 15.00, 0, '', 15, 15, '2025-03-28 07:31:11'),
(1671, 'FARM', 'ALGODAO 100G Ace heal', 8, 5, 1, 0.00, 82.00, 0, '04/30/2029', 22, 15, '2025-05-12 06:52:48'),
(1672, 'FARM', 'LBEN-400, 1 COMP', 454, 5, 1, 8.20, 13.00, 0, '31/12/2027', 13, 15, '2025-08-15 07:42:30'),
(1673, 'FARM', 'SALBURET-S , 2MG /5ML, 100ML', 4, 5, 1, 60.00, 89.00, 0, '12/2027', 17, 13, '2025-11-07 08:05:15'),
(1674, 'FARM', 'TOCA CIRURGICAS DESCARTAVEL', 56, NULL, 1, 0.00, 6.00, 0, '', 22, 13, '2025-01-29 12:54:59'),
(1675, 'FARM', 'ORACIP', 22, NULL, 1, 0.00, 68.00, 0, '', 20, 13, '2025-02-02 08:03:21'),
(1676, 'FARM', 'NEOGLOVE MEDIO METADE ', 83, NULL, 1, 0.00, 4.47, 0, '', 21, 8, '2025-03-27 21:43:23'),
(1677, 'FARM', 'BETAPYN CXS X 18 COM', 53, NULL, 1, 0.00, 96.00, 0, '', 10, 12, '2025-02-01 17:45:52'),
(1678, 'FARM', 'LAURODERME PASTA X 100g', 3, NULL, 1, 0.00, 885.00, 0, '', 17, 12, '2025-01-25 13:51:22'),
(1679, 'FARM', 'CLARITROMICINA AZEVEDOS 500mg', 0, NULL, 1, 0.00, 538.00, 0, '', 9, 12, '2025-02-01 06:04:20'),
(1680, 'FARM', 'CIPROFLOXACINA AZEVEDOS 500MG', 24, NULL, 1, 0.00, 41.00, 0, '', 9, 12, '2025-02-03 09:08:26'),
(1681, 'FARM', 'TRIFENE XAROPE NOVO LOTE1', 7, NULL, 1, 0.00, 457.00, 0, '31/01/2029', 10, 12, '2025-01-25 13:51:23'),
(1682, 'FARM', 'ADCO LOPERAMIDA', 29, NULL, 1, 0.00, 19.00, 0, '30/09/2025', 22, 12, '2025-01-28 23:03:58'),
(1683, 'FARM', 'PANADO 500 MG - CXS X 24 COMP', 5, NULL, 1, 0.00, 199.00, 0, '', 10, 12, '2025-01-25 13:51:23'),
(1684, 'FARM', 'CITRO-SODA X60 G', 5, NULL, 1, 0.00, 183.00, 0, '', 13, 12, '2025-01-25 13:51:23'),
(1686, 'FARM', 'MENTOCAINA  R NOVO LOTE', 8, 5, 1, 113.00, 240.00, 0, '30/11/2026', 14, 12, '2025-07-03 14:27:40'),
(1687, 'FARM', 'MENTOCAINA  ANTI-INFLAMATORIO NOVO LOTE 1', 20, NULL, 1, 0.00, 247.00, 0, '', 10, 12, '2025-01-25 13:51:24'),
(1688, 'FARM', 'MENTOCAINA  MEL E LIMAO NOVO LOTE', 10, NULL, 1, 0.00, 228.00, 0, '31/08/2027', 10, 12, '2025-01-25 13:51:24'),
(1689, 'FARM', 'TADALAFIL 10 MG AZEVEDO', 8, 1, 1, 50.00, 100.00, 16, '30/08/2028', 16, 12, '2025-06-19 10:38:18'),
(1690, 'FARM', 'SILDENAFIL 50 MG', 8, NULL, 1, 0.00, 258.00, 0, '', 16, 12, '2025-01-25 13:51:24'),
(1691, 'FARM', 'PANVITOL ; MIND POWER', 35, NULL, 1, 0.00, 42.00, 0, '', 21, 12, '2025-02-01 23:53:31'),
(1693, 'FARM', 'HERMES MULTIVIT', 2, NULL, 1, 0.00, 600.00, 0, '', 22, 12, '2025-01-25 13:51:24'),
(1694, 'Selecione o prefixo', 'STREPSILS LARANJA VIT C', 2, NULL, 1, 0.00, 104.00, 0, '', 21, 0, '2025-01-26 05:45:07'),
(1695, 'FARM', 'STUDENT CEREBRUM', 30, NULL, 1, 0.00, 113.30, 0, '', 22, 12, '2025-01-25 14:21:29'),
(1696, 'FARM', 'METROGEN CP 250 NOVO LOTE', 1992, NULL, 1, 0.00, 13.00, 0, '31/12/2027', 9, 8, '2025-03-28 22:37:37'),
(1697, 'FARM', 'ALGODAO 50GM', 9, NULL, 1, 0.00, 71.00, 0, '30/06/2028', 21, 8, '2025-03-28 16:29:16'),
(1699, 'FARM', 'STOPDOR (PARACETAMOL) 500 MG 20 COMP', 15, NULL, 1, 0.00, 40.00, 0, '30/11/2025', 10, 8, '2025-03-28 23:31:51'),
(1702, 'FARM', 'FLUMINOC (ARTEMETER+LUMEFA) 20MG+120 MG 24 COMP', 15, NULL, 1, 0.00, 127.00, 0, '', 21, 8, '2025-03-28 16:39:54'),
(1703, 'Selecione o prefixo', 'FACE MAK 50S', 0, NULL, 1, 0.00, 0.00, 16, '', 0, 0, '2025-01-28 04:39:05'),
(1705, 'FARM', 'QITOP REPELENTE P/ MOSQITO', 3, NULL, 1, 0.00, 548.00, 0, '', 22, 8, '2025-01-28 04:46:27'),
(1706, 'FARM', 'DIFISAL (DICLOFENAC)', 366, NULL, 1, 0.00, 7.30, 0, '', 10, 0, '2025-03-28 22:49:27'),
(1707, 'FARM', 'BREN 400', 897, NULL, 1, 0.00, 17.80, 0, '', 10, 14, '2025-03-28 22:55:42'),
(1708, 'FARM', 'AGUA BIDESTILADA', 0, NULL, 1, 0.00, 0.00, 0, '', 0, 14, '2025-01-30 03:27:25'),
(1709, 'FARM', 'NORMNIL-10 ML', 3, NULL, 1, 0.00, 38.00, 0, '', 22, 14, '2025-02-01 10:59:55'),
(1710, 'FARM', 'POWER GESIC', 53, NULL, 1, 0.00, 9.00, 0, '06/2026', 10, 9, '2025-02-02 00:11:07'),
(1711, 'FARM', 'VENTOSHA XAROPE', 78, NULL, 1, 0.00, 90.00, 0, '', 21, 9, '2025-03-28 16:12:30'),
(1712, 'FARM', 'NEOTON SYRUP', 92, 5, 1, 0.00, 125.00, 0, '01/2028', 21, 9, '2025-08-16 11:47:27'),
(1713, 'FARM', 'DEXWIN EYES', 16, NULL, 1, 0.00, 70.00, 0, '', 0, 11, '2025-03-28 20:56:53'),
(1714, 'FARM', 'APETIMAX  XAROPE', 27, NULL, 1, 0.00, 119.00, 0, '', 22, 11, '2025-03-28 10:26:21'),
(1715, 'FARM', 'BURNEX ', 44, NULL, 1, 0.00, 101.00, 0, '', 21, 11, '2025-02-02 06:01:58'),
(1716, 'Selecione o prefixo', 'ZITHROCARE 200 15 ML', 34, NULL, 1, 0.00, 82.00, 0, '', 22, 11, '2025-03-28 15:36:32'),
(1718, 'FARM', 'CANDICORT CREME X 30G', 4, NULL, 1, 0.00, 358.00, 0, '30/01/2026', 22, 16, '2025-03-27 14:37:35'),
(1719, 'FARM', 'NOVACORT CREME X 30G', 7, NULL, 1, 0.00, 407.00, 0, '31/10/2026', 22, 16, '2025-02-02 18:59:25'),
(1720, 'FARM', 'NOVACORT POMADA X 30G', 10, NULL, 1, 0.00, 600.00, 0, '31/10/2026', 17, 16, '2025-02-02 18:59:25'),
(1721, 'FARM', 'FLAGASS GOTAS (SIMETICONE) 75 ML/ML FR X 10 ML', 4, NULL, 1, 0.00, 175.00, 0, '30/01/2026', 22, 16, '2025-03-28 04:39:38'),
(1723, 'FARM', 'PREDNISOLONA SOLUCAO ORAL FR X 60ML ', 5, NULL, 1, 0.00, 655.00, 0, '30/11/2026', 22, 16, '2025-02-02 18:59:25'),
(1724, 'FARM', 'CLOTRI-DENK', 29, NULL, 1, 0.00, 172.00, 0, '30/03/2027', 21, 16, '2025-02-02 22:54:10'),
(1725, 'FARM', 'GLICERINA PLUS NATURAL NOVO LOTE', 10, NULL, 1, 0.00, 280.00, 0, '', 17, 16, '2025-02-02 18:59:25'),
(1726, 'FARM', 'GLICERINA AMARELO NOVO LOTE', 5, NULL, 1, 0.00, 280.00, 0, '30/10/2025', 0, 16, '2025-02-02 18:59:25'),
(1727, 'FARM', 'GLICERINA VERDE NOVO LOTE', 5, NULL, 1, 0.00, 280.00, 0, '30/10/2025', 21, 16, '2025-02-02 18:59:25'),
(1728, 'FARM', 'AGUA OXIGENADA NOVO LOTE ', 13, NULL, 1, 0.00, 120.00, 0, '28/02/2027', 0, 16, '2025-03-27 02:25:19'),
(1729, 'FARM', 'BETAMETASONA BASI NOVO LOTE', 14, NULL, 1, 0.00, 230.00, 0, '30/09/2026', 17, 8, '2025-03-28 13:34:34'),
(1730, 'FARM', 'RINGER COM LACTATO BASI 1000ML T.H', 5, NULL, 1, 0.00, 170.00, 0, '31/08/2027', 21, 8, '2025-03-28 06:29:07'),
(1731, 'EMPRE', 'SERINGAS 3 PARTES COM AGULHA', 294, NULL, 1, 0.00, 9.00, 0, '25/08/2027', 22, 8, '2025-03-28 21:38:16'),
(1732, 'FARM', 'HIDROCIL PENSOLAC', 3, NULL, 1, 0.00, 967.00, 0, '30/04/2027', 20, 8, '2025-03-28 06:29:08'),
(1733, 'FARM', 'D3 ACTIVE DENK', 5, NULL, 1, 0.00, 198.20, 0, '30/4/2026', 15, 8, '2025-03-28 06:33:08'),
(1734, 'Selecione o prefixo', 'Fins Sanitario(Alcool Elitico', 22, NULL, 1, 0.00, 200.00, 0, '', 0, 0, '2025-03-28 09:47:23'),
(1735, 'FARM', 'ALGIK PARACETAMOL NOVO LOTE', 14, NULL, 1, 0.00, 299.00, 0, '', 10, 12, '2025-03-28 15:49:00'),
(83246, 'FARM', 'LISOMUCIN XAROPE', 0, 3, 1, 500.00, 636.00, 0, '31/03/2026', 21, 12, '2025-05-28 12:08:28'),
(83247, 'Selecione o prefixo', 'PANTOPRAZOL BLUEP 20MG', 0, 0, 0, 0.00, 196.00, 0, '', 0, 8, '2025-04-01 12:07:00'),
(83248, 'EMPRE', 'ISOTRETINOINA OROTEX 20MG ', 0, 0, 0, 0.00, 648.00, 0, '', 21, 0, '2025-04-01 12:55:39'),
(83249, 'FARM', 'CO-ARINATE FDC JUNIOR', NULL, 1, 1, 0.00, 316.00, 0, '0', 8, 12, '2025-04-08 09:52:05'),
(83250, 'FARM', 'TESTE', NULL, 0, 1, 0.00, 1.00, 0, '0', 12, 11, '2025-04-08 09:52:24'),
(83251, 'FARM', 'TRAMADOL AZEVEDO 50MG-6XS X20 CAPS XX', NULL, 1, 1, 0.00, 164.50, 0, '0', 8, 12, '2025-04-12 16:27:45'),
(83252, 'FARM', 'CO-ARINATE FDC ADULTO', NULL, 5, 1, 261.00, 439.00, 0, '30/11/2026', 8, 12, '2025-09-24 20:17:47'),
(83253, 'FARM', 'ALGODAO 5OOG HIDROFILO X', NULL, 0, 1, 0.00, 400.00, 0, '0', 8, 15, '2025-04-08 10:51:56'),
(83254, 'FARM', 'ALGODAO 100G HIDROFILO X', NULL, 0, 1, 0.00, 89.00, 0, '0', 8, 15, '2025-04-08 10:56:39'),
(83255, 'FARM', 'ACEFEN 400MG', NULL, 0, 1, 1.00, 15.00, 0, '1', 10, 15, '2025-04-08 13:25:28'),
(83256, 'FARM', 'RELOGIO ', NULL, 0, 1, 1.00, 419.00, 0, '1', 9, 12, '2025-04-08 15:23:22'),
(83257, 'FARM', 'DEXOVAL 0', NULL, 0, 1, 0.00, 689.00, 0, '0', 8, 14, '2025-04-09 09:53:51'),
(83258, 'FARM', 'L GEUTA', NULL, 0, 1, 0.00, 89.00, 0, '0', 8, 15, '2025-04-09 09:56:28'),
(83259, 'FARM', 'JOBRASON VISION', NULL, 0, 1, 0.00, 102.00, 0, '0', 8, 13, '2025-04-09 09:58:50'),
(83260, 'FARM', 'AERO-OM 42MG', NULL, 0, 1, 0.00, 110.00, 0, '0', 8, 13, '2025-04-09 10:16:38'),
(83261, 'FARM', 'BUSCOCINE INJECTAVEL', NULL, 0, 1, 0.00, 47.00, 0, '0', 8, 12, '2025-04-09 10:20:27'),
(83262, 'EMPRE', 'PREDNACE INJECTAVEL', NULL, 0, 1, 0.00, 296.00, 16, '0', 8, 13, '2025-04-09 10:21:17'),
(83263, 'FARM', 'HAEMVITAL 20 XX', NULL, 0, 1, 0.00, 243.00, 0, '0', 8, 13, '2025-04-09 10:25:38'),
(83264, 'EMPRE', 'ACETRIM 480 INFUSAO INTRAVENOSA', NULL, 0, 1, 0.00, 162.00, 0, '0', 8, 13, '2025-04-09 10:28:37'),
(83265, 'FARM', 'DESCONTRAN', NULL, 0, 1, 0.00, 232.00, 0, '0', 8, 12, '2025-04-09 10:32:13'),
(83266, 'FARM', 'CEFIXINA', NULL, 0, 1, 0.00, 1365.00, 0, '0', 8, 12, '2025-04-09 10:36:19'),
(83267, 'FARM', 'LUMITER', NULL, 0, 1, 0.00, 296.00, 0, '0', 8, 12, '2025-04-09 10:40:00'),
(83268, 'FARM', 'LASARTAN+HIDROCLORATAZIDE BLUEPHARM', NULL, 0, 1, 0.00, 45.00, 0, '0', 8, 12, '2025-04-09 10:43:52'),
(83269, 'EMPRE', 'SERINGAS 10 ML', NULL, 2, 1, 0.00, 9.00, 0, '0', 8, 13, '2025-04-09 10:51:03'),
(83270, 'FARM', 'KANAMECINA INJETAVEL X', NULL, 0, 1, 0.00, 164.00, 0, '0', 8, 12, '2025-04-09 10:58:09'),
(83271, 'FARM', 'OLEOBAN BEBE 450G', NULL, 0, 1, 0.00, 2692.00, 0, '0', 8, 13, '2025-04-09 11:03:11'),
(83272, 'FARM', 'OLEOBAN BEBE 250G', NULL, 0, 1, 0.00, 1328.00, 0, '0', 8, 12, '2025-04-09 11:05:22'),
(83273, 'FARM', 'PO JHONSONS', NULL, 0, 1, 0.00, 170.00, 0, '0', 8, 13, '2025-04-09 11:21:52'),
(83274, 'EMPRE', 'LAURODERME PASTA CUTANEA', NULL, 0, 1, 0.00, 933.00, 0, '0', 8, 13, '2025-04-09 11:27:15'),
(83275, 'FARM', 'OLEO DE AMENDOAS DOCES ALIFAN', NULL, 0, 1, 0.00, 536.00, 0, '0', 9, 12, '2025-04-09 11:29:45'),
(83276, 'FARM', 'SABONETE SABUSOL SULF', NULL, 0, 1, 0.00, 103.00, 0, '0', 8, 15, '2025-04-09 11:32:22'),
(83277, 'FARM', 'STREPSILS', NULL, 0, 1, 0.00, 413.00, 0, '0', 8, 13, '2025-04-09 11:38:15'),
(83278, 'EMPRE', 'MENTOCAINA MEL', NULL, 0, 1, 0.00, 456.00, 0, '0', 8, 13, '2025-04-09 11:46:41'),
(83279, 'Selecione o prefixo', 'MENTOCAINA ANTI-INFLAM X', NULL, 4, 1, 230.00, 247.00, 0, '10/30/2025', 8, 12, '2025-04-29 08:17:40'),
(83280, 'EMPRE', 'NEOGUARD LDPE APRON CARENTAL', NULL, 0, 1, 0.00, 16.00, 0, '0', 8, 13, '2025-04-09 11:57:12'),
(83281, 'FARM', 'CLORETO DE POTASSIO CONCENTRADO INJ', NULL, 0, 1, 0.00, 201.00, 0, '0', 21, 12, '2025-04-09 13:53:32'),
(83282, 'FARM', 'BIOB 12', NULL, 0, 1, 0.00, 119.00, 0, '0', 8, 13, '2025-04-09 13:56:54'),
(83283, 'FARM', 'VITAMINA K1', NULL, 0, 1, 0.00, 135.00, 0, '0', 8, 12, '2025-04-09 13:59:00'),
(83284, 'FARM', 'ACE ALIVIADOR', NULL, 2, 1, 1.00, 175.00, 0, '1', 10, 13, '2025-04-12 12:43:43'),
(83285, 'FARM', 'ALUGEL XAROPE 200 ML', NULL, 10, 0, 90.00, 134.00, 0, '31/08/2027', 13, 8, '2025-04-16 15:25:02'),
(83286, 'FARM', 'COMPRESSAS DE GAZE ESTERILIZADAS', NULL, 10, 0, 7.20, 11.00, 0, '21/08/2027', 22, 8, '2025-04-16 15:42:46'),
(83287, 'FARM', 'COMPRESSAS DE GAZE NAO ESTERILIZADAS', NULL, 10, 1, 2.00, 3.00, 0, '220/47/2028', 21, 8, '2025-04-16 15:48:24'),
(83288, 'FARM', 'PENSO  RAPIDO DE TECIDO', NULL, 20, 1, 1.00, 8.00, 0, '09/07/2027', 22, 8, '2025-04-16 15:56:44'),
(83289, 'FARM', 'SUPIDON SUSP ', NULL, 5, 1, 12.00, 378.00, 0, '10/30/2025', 10, 8, '2025-04-17 07:57:30'),
(83290, 'FARM', 'CHLORFENIRAMINA MAL.COM', NULL, 100, 0, 6.80, 10.10, 0, '09302027', 14, 14, '2025-04-17 14:29:56'),
(83291, 'FARM', 'BILOCOR 5 mg', NULL, 6, 0, 40.00, 59.00, 0, '02/28/2027', 18, 16, '2025-04-21 09:33:57'),
(83292, 'FARM', 'BILOCOR 10mg cxsx30 comp', NULL, 10, 0, 48.00, 70.00, 0, '09/302026', 18, 16, '2025-04-21 09:38:50'),
(83293, 'FARM', 'NERILON 100mg+200mg+0.2mg', NULL, 10, 0, 105.00, 155.00, 0, '31/07/2027', 15, 16, '2025-04-21 09:44:21'),
(83294, 'FARM', 'ENAP 10MG Maleato de enaprl', NULL, 10, 0, 79.00, 117.00, 0, '03/302027', 18, 16, '2025-04-21 09:50:59'),
(83295, 'FARM', 'ENAP 20MG Maleato de enaprl', NULL, 0, 0, 111.00, 165.00, 0, '03/302027', 18, 16, '2025-04-21 09:55:54'),
(83296, 'FARM', 'FEDALOC 30mg SR ', NULL, 6, 0, 136.00, 201.00, 0, '05/30/2027', 18, 16, '2025-04-21 10:02:49'),
(83297, 'FARM', 'ZASVENIL (lisinopril) 20mg-cxs x60 cps', NULL, 10, 1, 54.50, 80.60, 0, '30/09/2028', 18, 16, '2025-08-08 10:35:43'),
(83298, 'FARM', 'METACYCLINE', NULL, 50, 0, 22.00, 33.00, 0, '30/09/2027', 20, 8, '2025-04-24 08:17:30'),
(83301, 'FARM', 'DICLOFENAC SODIO 75mg/3ml,  injectavel', NULL, 10, 1, 15.00, 24.00, 0, '30/06/2027', 10, 15, '2025-04-30 08:41:23'),
(83302, 'FARM', 'ACEFIRST', NULL, 100, 0, 1.00, 2.00, 0, '30/04/2029', 22, 15, '2025-04-30 08:53:53'),
(83303, 'FARM', 'BENZYLPENICILIN  INJ', NULL, 10, 1, 65.00, 97.00, 0, '31/08/2026', 9, 15, '2025-04-30 09:21:43'),
(83304, 'FARM', 'CELL VISION NOVO LOTE', NULL, 25, 0, 62.00, 92.00, 0, '30/11/2026', 20, 15, '2025-04-30 09:48:55'),
(83305, 'FARM', 'LBEN 200MG/5ML,10ML', NULL, 30, 0, 32.00, 48.00, 0, '30/11/2027', 13, 15, '2025-11-07 08:30:11'),
(83306, 'FARM', 'COMPRESSAS DE GAZE ESTERILIZADAS 5cmx5cmx8', NULL, 20, 0, 6.00, 9.00, 0, '30/04/2029', 22, 13, '2025-04-30 10:04:54'),
(83307, 'FARM', 'COMPRESSAS DE GAZE ESTERILIZADAS 7.5x7.5', NULL, 20, 0, 9.00, 11.60, 0, '30/04/2029', 21, 13, '2025-04-30 10:09:08'),
(83308, 'FARM', 'COMPRESSA DE GAZE NAO ESTERIS', NULL, 3, 0, 70.00, 104.00, 0, '30/04/2029', 22, 13, '2025-04-30 10:18:07'),
(83309, 'FARM', 'COMPRESSAS DE GAZE NAO ESTERILIZADAS 7.5CMX7.5CM', NULL, 3, 0, 90.00, 134.00, 0, '30/04/2029', 21, 13, '2025-04-30 10:21:20'),
(83310, 'FARM', 'SERINGAS DE ACE   10 ML', NULL, 200, 0, 5.00, 7.00, 0, '31/08/2027', 22, 13, '2025-04-30 10:25:29'),
(83311, 'FARM', 'SERINGAS 5ML ACE', NULL, 100, 0, 3.00, 5.00, 0, '31/08/2027', 21, 13, '2025-04-30 10:28:32'),
(83312, 'EMPRE', 'LIGADURAS DE GAZE 7.5 CMX4', NULL, 24, 0, 8.70, 12.00, 0, '31/03/2029', 22, 13, '2025-10-22 10:16:32'),
(83314, 'FARM', 'LIGADURAS DE GAZE 10 CMX4', NULL, 15, 0, 14.00, 17.25, 0, '31/03/2029', 22, 15, '2025-04-30 10:38:59'),
(83315, 'FARM', 'GAZE PARAFINADO ACE', NULL, 10, 0, 160.00, 237.00, 0, '30/04/2029', 22, 13, '2025-04-30 10:41:32'),
(83317, 'FARM', 'AMOX-500 CAPS', NULL, 100, 1, 26.00, 39.00, 0, '30/09/2026', 22, 13, '2025-11-07 08:42:48'),
(83318, 'FARM', 'FRUSIMEX INJECTAVEL', NULL, 5, 0, 18.00, 27.00, 0, '31/10/2026', 23, 13, '2025-04-30 10:57:45'),
(83319, 'FARM', 'AMINOBRON INJECTAVEL', NULL, 5, 0, 72.00, 107.00, 0, '30/11/2026', 23, 15, '2025-04-30 11:01:56'),
(83320, 'FARM', 'ANATRIX HEAVY INJECTAVEL', NULL, 5, 0, 130.00, 188.00, 0, '30/11/2026', 23, 13, '2025-04-30 11:05:30'),
(83321, 'FARM', 'TRIXOCEFT INJ', NULL, 1, 0, 136.00, 202.00, 0, '30/11/2026', 23, 15, '2025-04-30 11:08:30'),
(83322, 'FARM', 'AGUA ESTERILIZADA INJECTAVEL-ACE', NULL, 30, 0, 10.00, 15.00, 0, '1024265', 23, 15, '2025-04-30 12:03:52'),
(83323, 'Selecione o prefixo', 'ACEJET 1ML SERINGA DESCARTAVEL', NULL, 100, 0, 5.00, 9.00, 0, 'MZ220421', 23, 13, '2025-04-30 12:17:33'),
(83324, 'FARM', 'Acnegel_b 15 gm', NULL, 10, 1, 49.00, 73.00, 0, '31/01/2026', 10, 8, '2025-05-06 08:42:32'),
(83325, 'FARM', 'Malacura Ds', NULL, 5, 1, 35.00, 52.00, 0, '30/06/2026', 22, 8, '2025-05-06 08:48:51'),
(83326, 'FARM', 'LOPERAMIDA  2MG 10X10 CPS GOVIND', NULL, 20, 1, 14.00, 21.00, 0, '30/01/2026', 21, 8, '2025-11-07 09:45:20'),
(83327, 'FARM', 'LOPERAMIDA  2MG 10X10 GOVIND', NULL, 20, 0, 14.00, 20.90, 0, '30/01/2026', 21, 8, '2025-05-06 09:05:38'),
(83333, 'FARM', 'FRUSEMIDE', NULL, 10, 1, 9.00, 15.00, 0, '31/01/2027', 22, 8, '2025-11-07 09:26:45'),
(83335, 'FARM', 'GENTAMICIN+DEXAMETHASONE /EYES DROPS', NULL, 25, 1, 80.00, 119.00, 0, '31/05/2026', 20, 8, '2025-05-06 09:18:16'),
(83336, 'FARM', 'GENSONE PREDNISOLONE 20MG', NULL, 10, 1, 41.00, 61.10, 0, '30/10/2027', 21, 8, '2025-05-06 09:22:29'),
(83337, 'FARM', 'AXILLARY CRUTCH MULETAS NOVO', NULL, 1, 1, 1500.00, 1118.00, 0, '30/10/2027', 22, 8, '2025-05-06 10:32:02'),
(83338, 'FARM', 'OLEO DE AMENDOAS DOCE GOVIND', NULL, 3, 0, 139.00, 208.00, 0, '03/31/2026', 22, 8, '2025-05-06 11:08:39'),
(83339, 'FARM', 'PANDERMIL  CREME  30MG', NULL, 3, 1, 345.00, 509.00, 0, '30/04/2027', 21, 8, '2025-10-11 14:41:28'),
(83340, 'FARM', 'ROLOS DE ALGODAO ABSORVENTE', NULL, 2, 0, 50.00, 71.00, 0, '06/30/2026', 22, 8, '2025-05-16 08:49:56'),
(83341, 'FARM', 'COMPRESSA DE GAZE ESTERILO 5X5', NULL, 10, 0, 5.00, 9.00, 0, '04/30/2029', 22, 15, '2025-05-17 13:00:16'),
(83342, 'FARM', 'Clorocil soluÃ§Ã£o oft', NULL, 5, 1, 327.21, 485.00, 0, '30/09/2027', 20, 8, '2025-05-22 14:51:20'),
(83343, 'FARM', 'Ãgua para injeÃ§Ã£o 5ml novo lote', NULL, 30, 1, 5.00, 10.00, 0, '30/08/2028', 23, 8, '2025-05-27 14:32:37'),
(83344, 'FARM', 'AGUA INJECTAVEL', NULL, 30, 1, 5.00, 7.50, 0, '30/08/2028', 23, 8, '2025-06-12 11:41:57'),
(83345, 'FARM', 'CANADIANA  MEDIA CADA GVD', NULL, 1, 1, 1000.00, 1341.00, 0, '12/302027', 22, 8, '2025-05-31 08:05:14'),
(83346, 'FARM', 'Cedor Novo lote1', NULL, 200, 1, 4.50, 6.70, 0, '12/31/2027', 10, 8, '2025-08-05 09:29:23'),
(83347, 'FARM', 'DICLODOR GEL', NULL, 4, 1, 162.00, 232.00, 0, '30/04/2026', 10, 8, '2025-06-12 10:53:01'),
(83348, 'FARM', 'Gendox', NULL, 50, 1, 25.00, 38.00, 0, '28/02/2027', 9, 8, '2025-10-11 13:28:57'),
(83349, 'FARM', 'FOURTB', NULL, 10, 1, 30.00, 42.30, 0, '30/11/2026', 15, 8, '2025-06-23 10:23:23'),
(83350, 'PART', 'ImpressÃ£o de Banner 100cmx60cm', NULL, 1000, 1, 0.00, 0.00, 0, '', 22, 15, '2025-06-22 12:25:32'),
(83351, 'PART', 'ProjecÃ§Ã£o de Layout 100cmx60cm', NULL, 100, 0, 1000.00, 1000.00, 0, '', 21, 13, '2025-06-22 12:31:01'),
(83352, 'FARM', 'Tansulosina bluep 400mg 30caps', NULL, 5, 1, 150.00, 205.60, 0, '30/08/2028', 22, 8, '2025-06-24 15:13:32'),
(83353, 'FARM', 'Amizol', NULL, 30, 1, 15.00, 22.50, 0, '28/02/2027', 18, 8, '2025-06-24 15:16:38'),
(83354, 'FARM', 'SUPOFEN xarope', NULL, 10, 1, 210.00, 310.00, 0, '31/12/2026', 10, 8, '2025-06-27 14:08:42'),
(83356, 'FARM', 'SINOT CLAV', NULL, 5, 1, 379.00, 562.00, 0, '28/02/2027', 9, 8, '2025-07-03 14:09:48'),
(83357, 'FARM', 'Clotri-denk novo lote', NULL, 10, 1, 123.00, 182.00, 0, '30/10/2027', 17, 10, '2025-07-07 11:57:34'),
(83358, 'FARM', 'Cipro-denk 500mg', NULL, 10, 1, 25.00, 36.00, 0, '06/30/2026', 9, 10, '2025-07-07 12:10:47'),
(83359, 'FARM', 'Letrozole Denk 2.3', NULL, 6, 1, 500.00, 685.50, 0, '31/12/2027', 9, 16, '2025-07-07 12:34:43'),
(83360, 'FARM', 'G-zole comprimodos', NULL, 50, 1, 10.00, 15.00, 0, '28/12/2026', 21, 8, '2025-07-08 09:28:08'),
(83361, 'FARM', 'Amoxicilina+Ac CLAV basi', NULL, 10, 1, 58.00, 131.00, 0, '30/09/2025', 9, 8, '2025-07-08 10:00:23'),
(83362, 'FARM', 'Areal 500 comprimidos', NULL, 10, 1, 80.30, 119.00, 0, '30/11/2026', 9, 12, '2025-07-10 10:08:53'),
(83363, 'FARM', 'Atral', NULL, 10, 1, 80.30, 119.00, 0, '30/11/2026', 9, 12, '2025-07-10 10:10:45'),
(83364, 'FARM', 'Azicure 500 comprimidos', NULL, 10, 1, 24.00, 34.00, 0, '28/02/2027', 9, 12, '2025-07-10 10:14:26'),
(83365, 'FARM', 'PRIMECIP 500mg', NULL, 40, 1, 33.00, 50.00, 0, '31/10/2027', 9, 8, '2025-11-07 08:23:11'),
(83366, 'FARM', 'Benzathine benzylpenicilin', NULL, 10, 1, 50.00, 82.00, 0, '28/02/2028', 9, 8, '2025-07-16 13:27:11'),
(83367, 'FARM', 'TOSSIL LIMÃƒO ', NULL, 50, 1, 2.00, 3.00, 0, '31/05/202', 11, 8, '2025-07-16 13:10:24'),
(83368, 'FARM', 'Acemol xarope novo lote', NULL, 10, 1, 40.00, 61.00, 0, '30/08/2028', 10, 8, '2025-07-25 12:15:31'),
(83369, 'FARM', 'Azicin-S, 15 ML', NULL, 5, 1, 50.00, 74.00, 0, '30/11/2026', 9, 8, '2025-11-06 10:17:35'),
(83370, 'FARM', 'TRUST,1S ', NULL, 5, 1, 25.00, 60.00, 0, '30/11/2026', 22, 8, '2025-11-07 13:11:55'),
(83371, 'FARM', 'Ronic, colÃ­rio 5ml novo lote', NULL, 1, 1, 412.00, 611.00, 0, '28/02/2027', 9, 8, '2025-07-25 12:38:01'),
(83372, 'FARM', 'Ligadura de gaze 12.3cmx5m-12un', NULL, 15, 1, 15.00, 20.00, 0, '30/08/2028', 21, 8, '2025-07-25 12:56:24'),
(83373, 'FARM', 'MÃ¡scara km 95', NULL, 5, 0, 10.00, 39.00, 0, '28/02/2027', 21, 8, '2025-07-25 12:58:46'),
(83374, 'FARM', 'Cetriaxoba sÃ³dica, 1g+wfi inj', NULL, 10, 1, 25.00, 95.00, 0, '28/02/2027', 9, 8, '2025-07-25 13:26:40'),
(83375, 'FARM', 'Laxaque 5mg', NULL, 10, 1, 10.00, 15.00, 0, '30/11/2026', 21, 8, '2025-11-07 08:03:12'),
(83376, 'FARM', 'Mistura', NULL, 50, 1, 15.00, 25.80, 0, '28/02/2027', 21, 8, '2025-07-25 13:53:50'),
(83377, 'PART', 'Heiniker ', NULL, 5, 1, 550.00, 600.00, 0, '', 21, 11, '2025-07-28 15:49:56'),
(83378, 'FARM', 'Clotrisan Cream BP 1/w/W 20G', NULL, 10, 0, 48.00, 60.00, 0, 'NL-MNB', 9, 8, '2025-08-06 06:51:43'),
(83379, 'FARM', 'Seringa descartave N CARe', NULL, 10, 0, 486.00, 9.00, 0, '31/07/2026', 23, 8, '2025-08-13 08:58:06'),
(83380, 'Selecione o prefixo', 'SERINGA DESCARTAVEL NCARE', NULL, 10, 0, 486.00, 8.00, 0, '31/07/2026', 23, 8, '2025-08-06 08:26:43'),
(83381, 'FARM', 'Neofulvin Tabs 10Ã—19', NULL, 5, 0, 98.00, 107.44, 0, '31/01/2026', 9, 11, '2025-08-10 15:20:05'),
(83382, 'FARM', 'ACECLAV-625,2X10 COMP', NULL, 5, 1, 100.00, 183.00, 0, '04/30/2029', 9, 8, '2025-11-07 08:39:34'),
(83383, 'FARM', 'SERINGA NCARE 10ML', NULL, 10, 0, 5.00, 9.00, 0, '30/08/2-27', 23, 8, '2025-08-13 09:10:21'),
(83384, 'FARM', 'COMPRESSA DE GAZE ESTERILIZADA 10CMX10CM', NULL, 5, 0, 200.00, 537.00, 0, '04/2029', 21, 15, '2025-11-07 09:50:51'),
(83385, 'FARM', 'CALAMINE', NULL, 109, 0, 0.00, 122.00, 0, '10/2027', 17, 15, '2025-08-14 11:12:37'),
(83386, 'FARM', 'DIACOL 27MG/200ML XAROPE', NULL, 5, 0, 0.00, 338.00, 0, '09/2028', 14, 8, '2025-11-07 07:21:19'),
(83387, 'FARM', 'ACEMOL SUSPENCAO 125', NULL, 5, 0, 0.00, 68.00, 0, '08/2027', 14, 8, '2025-08-16 12:16:39'),
(83388, 'FARM', 'COMPRESSA DE GAZE PARAFINADA BP', NULL, 5, 0, 0.00, 237.00, 0, '04/09', 17, 13, '2025-08-16 12:41:48'),
(83389, 'FARM', 'OMEPRAZOL BLUEPHARMA 20MG', NULL, 5, 0, 335.00, 62.00, 0, '30/10/2026', 13, 8, '2025-08-18 10:55:25'),
(83391, 'FARM', 'INDOMOD INDOMENTACINA', NULL, 5, 0, 141.60, 22.00, 0, '31/O1/28', 9, 8, '2025-08-18 11:13:05'),
(83392, 'FARM', 'CLOTRIMAZOL BASI 10MG', NULL, 5, 0, 0.00, 520.00, 0, '01/26', 9, 8, '2025-08-19 11:18:44'),
(83393, 'FARM', 'PREDNOL VISION', NULL, 5, 0, 0.00, 114.00, 0, '01/2027', 20, 8, '2025-08-19 14:40:07'),
(83394, 'FARM', 'DICL0FENAC DICLODOR', NULL, 5, 0, 0.00, 20.00, 0, '11/26', 23, 8, '2025-08-20 10:24:58'),
(83397, 'FARM', 'CEFRIAXONA INJECCAO 1000MG/FRASCO', NULL, 0, 0, 0.00, 95.00, 0, '01/2027', 23, 13, '2025-08-25 16:18:04'),
(83398, 'FARM', 'ligadura de gaze NCARE 10cmx5cm', NULL, 5, 0, 144.00, 18.00, 0, '03/2029', 17, 8, '2025-08-27 16:04:13'),
(83399, 'FARM', 'Acarilbial', NULL, 5, 0, 279.00, 412.00, 0, '240463', 17, 8, '2025-08-28 15:38:16'),
(83400, 'FARM', 'Texa Allergy', NULL, 5, 0, 0.00, 350.00, 0, 'O3/2027', 11, 16, '2025-09-03 17:08:01'),
(83402, 'FARM', 'SALBURET-S , XAROPE', NULL, 5, 0, 0.00, 89.00, 0, '12/2027', 14, 8, '2025-09-04 16:53:41'),
(83403, 'FARM', 'DAGESIL GEL', NULL, 5, 0, 0.00, 233.00, 0, '05/2027', 17, 8, '2025-09-10 13:38:31'),
(83404, 'FARM', 'CANOIO-B', NULL, 5, 0, 0.00, 97.00, 0, '05/2027', 9, 8, '2025-09-10 14:47:37'),
(83405, 'FARM', 'GRIPE WATER BIO MATRIX', NULL, 5, 0, 80.00, 119.00, 0, '28/02/2028', 13, 8, '2025-09-11 15:12:01'),
(83406, 'FARM', 'ACEJET SERINGA 10ML', NULL, 5, 0, 440.00, 7.00, 0, '28/02/2030', 23, 8, '2025-09-11 15:50:00'),
(83407, 'FARM', 'REQUILYTE, 30X1X22 G,PO', NULL, 5, 0, 300.00, 15.00, 0, '31/01/2027', 13, 8, '2025-09-11 16:44:01'),
(83408, 'FARM', 'ACE WHITFIELD pomada 30mg', NULL, 5, 0, 70.00, 82.00, 0, '11/2026', 17, 8, '2025-09-12 06:40:00'),
(83409, 'FARM', 'Clotrimazol creme bluepharma ', NULL, 5, 0, 70.00, 180.00, 0, '01/2027', 17, 8, '2025-09-12 06:42:46'),
(83410, 'FARM', ' Water for injection 5ml', NULL, 5, 0, 5.00, 7.50, 0, '08/2028', 23, 8, '2025-09-12 06:46:31'),
(83412, 'FARM', 'MASCARA KN95 COM VALVULA', NULL, 5, 0, 252.00, 38.00, 0, '08/2029', 14, 8, '2025-09-12 12:08:22'),
(83413, 'FARM', 'LIGADURA 12.5CMX5M', NULL, 5, 0, 192.00, 18.00, 0, '06/2027', 17, 8, '2025-09-12 12:18:20'),
(83414, 'FARM', 'AGUA PARA INJECAO 10ML', NULL, 5, 0, 10.00, 15.00, 0, '31/08/2029', 23, 8, '2025-09-12 12:42:23'),
(83415, 'FARM', 'LUMITER( Artemeter 20MG+LUMIFENTRiNA 120mg', NULL, 5, 0, 60.00, 120.00, 0, '08/2027', 12, 14, '2025-09-12 16:25:32'),
(83416, 'FARM', 'Metoprix 10', NULL, 5, 0, 74.00, 11.00, 0, '30/04/2027', 13, 8, '2025-09-13 06:26:39'),
(83418, 'FARM', 'FITA ADESIVA 5CMX5', NULL, 5, 0, 760.00, 104.00, 0, '05/2029', 17, 8, '2025-09-13 09:59:26'),
(83419, 'FARM', 'ACE-CyCLINE CÃ¡psulas', NULL, 5, 0, 320.00, 48.00, 0, '07/27', 9, 8, '2025-09-13 10:35:28'),
(83420, 'FARM', 'FITA ADESIVA 2.5CMX5', NULL, 5, 0, 45.00, 67.00, 0, '02/2030', 17, 8, '2025-09-13 10:55:56'),
(83421, 'FARM', 'FITA ADESIVA 7.5X5', NULL, 5, 0, 110.00, 163.00, 0, '05/2029', 17, 8, '2025-09-13 11:00:11'),
(83422, 'FARM', 'I-WASH', NULL, 5, 0, 100.00, 148.00, 0, '03/2027', 16, 8, '2025-09-16 10:36:18'),
(83424, 'FARM', 'AZMANIL 100MG Aminofil', NULL, 5, 0, 16.53, 18.00, 0, '11/20216', 14, 8, '2025-09-24 10:27:13'),
(83427, 'FARM', 'Coartem BebÃ©', NULL, 5, 0, 97.00, 144.00, 0, '31/03/2027', 12, 12, '2025-09-24 20:04:46'),
(83429, 'FARM', 'NCARE SERINGA 5ML', NULL, 5, 0, 0.00, 8.00, 0, '20230831`', 23, 8, '2025-09-25 11:15:10'),
(83431, 'FARM', 'TRIFENE 400MGX20COMP', NULL, 5, 0, 185.00, 138.00, 0, 'ECR', 10, 12, '2025-09-26 12:48:40'),
(83433, 'FARM', 'ZINPLEX', NULL, 5, 0, 0.00, 532.00, 0, '08/2026', 15, 12, '2025-10-07 11:54:21'),
(83435, 'FARM', 'PANADO MORANGO', NULL, 5, 0, 0.00, 150.00, 0, '11/2025', 10, 8, '2025-10-09 09:07:08'),
(83437, 'FARM', 'ACE - Conazol comp ', NULL, 10, 1, 37.00, 55.00, 0, '', 9, 8, '2025-10-11 13:33:19'),
(83438, 'FARM', 'CLAVAMOX 250 MG+62,5MG/100ML susp AMOX+AC.CLAVULA', NULL, 2, 1, 660.00, 945.00, 0, '', 9, 8, '2025-11-07 07:19:49'),
(83439, 'FARM', 'PARALOGO FORTE comp', NULL, 50, 1, 10.00, 15.00, 0, '', 10, 8, '2025-10-11 13:42:32'),
(83440, 'FARM', 'Fosfato de sÃ³dio de dexametasona + neomicina colirio', NULL, 10, 1, 390.00, 580.00, 0, '', 9, 8, '2025-10-11 13:48:55'),
(83441, 'FARM', 'Paracetamol Basi 500 MG 20 comp', NULL, 5, 1, 40.00, 58.00, 0, '', 10, 8, '2025-10-11 14:04:41'),
(83442, 'FARM', 'DIMIDON (IBUPROFENO) 40MG/ML SUSP ORAL ', NULL, 2, 1, 244.00, 361.00, 0, '', 10, 8, '2025-10-11 14:09:17'),
(83443, 'FARM', 'FORMIMET 500 MG CP', NULL, 10, 1, 11.00, 20.00, 0, '', 19, 8, '2025-11-11 11:12:34'),
(83444, 'FARM', 'CEFOTAMINA 1 G INJ 10 ML', NULL, 5, 1, 200.00, 296.00, 0, '', 9, 8, '2025-10-11 14:35:55'),
(83445, 'FARM', 'Valprote ', NULL, 1, 1, 237.00, 352.00, 16, '', 21, 8, '2025-10-11 14:37:19'),
(83446, 'FARM', 'Gentamicina inj 80 mg/2 ml', NULL, 5, 1, 17.00, 25.00, 0, '', 9, 8, '2025-10-11 14:44:53'),
(83448, 'FARM', 'SERINGAS DE 10 ML ACEJET', NULL, 0, 0, 440.00, 9.00, 0, '02/2030', 23, 8, '2025-10-14 16:45:10'),
(83449, 'FARM', 'GENCEF-1G', NULL, 5, 0, 0.00, 103.00, 0, '05/2027', 23, 8, '2025-10-16 10:50:02'),
(83450, 'FARM', 'NS CLORETO DE SODIO. INFUSION 0.9%-500ML', NULL, 5, 1, 99.00, 147.00, 0, '60640143', 13, 8, '2025-11-06 09:33:13'),
(83451, 'FARM', 'Balsamo Analgesico basi 61.1mg', NULL, 5, 0, 234.68, 348.00, 0, 'P1015', 10, 8, '2025-10-17 15:16:39'),
(83452, 'FARM', 'Balsamo Analgesico basi 61.1mg/g pomada 20g', NULL, 5, 0, 150.08, 222.00, 0, '30/09/2026', 10, 8, '2025-10-17 15:21:44'),
(83453, 'FARM', 'IBUPRUFEN DENK', NULL, 5, 0, 173.50, 94.00, 0, '5', 10, 8, '2025-10-17 16:08:58'),
(83455, 'FARM', 'GINO-CANESTEN Creme Vaginal', NULL, 5, 0, 868.00, 1286.00, 0, '31/01/2028', 9, 8, '2025-10-18 09:58:07'),
(83456, 'FARM', 'AZITROMICINA BLUEPHARMA 500MG', NULL, 5, 0, 219.00, 107.67, 0, '10/2026', 9, 8, '2025-10-18 10:23:58'),
(83458, 'FARM', 'MODCL0X CLOXACILINA 500MG CAPS', NULL, 5, 0, 429.00, 64.00, 0, '03/2027', 17, 8, '2025-10-22 06:13:36'),
(83459, 'FARM', 'IMPETINE GEL 20 MG', NULL, 5, 0, 667.95, 959.00, 0, '10/2026', 17, 8, '2025-10-22 06:37:35'),
(83460, 'FARM', 'tansulosina', NULL, 0, 0, 0.00, 617.00, 0, '31/07/2027', 16, 8, '2025-10-23 10:29:52'),
(83462, 'Selecione o prefixo', 'AMYN 500(Amoxicicilna cps 500mg 10x10)', NULL, 0, 1, 295.00, 50.00, 16, '', 9, 14, '2025-11-06 07:51:55'),
(83463, 'Selecione o prefixo', 'LOKIT(Omeprazole 20mg 10x10)', NULL, 0, 1, 120.00, 20.00, 16, '', 21, 14, '2025-11-06 08:39:20'),
(83464, 'Selecione o prefixo', 'VELORIN-100(Atenolol COMP 100mg )10x10', NULL, 0, 1, 450.00, 50.00, 16, '#####', 21, 14, '2025-11-07 10:12:20'),
(83465, 'Selecione o prefixo', 'AMILORIDE COMP 5mg (10X10)', NULL, 0, 1, 145.00, 22.00, 16, '', 21, 14, '2025-11-06 08:46:30'),
(83466, 'Selecione o prefixo', 'BIOSCINE (Buscopan COMP 10mg 2X5X10)', NULL, 3, 1, 778.00, 156.00, 16, '', 0, 14, '2025-11-06 08:53:55'),
(83467, 'Selecione o prefixo', 'UMITOL 200 (CARBAMAZEPINA COMP.200mg-10X10)', NULL, 3, 1, 315.00, 47.00, 16, '', 0, 14, '2025-11-06 08:56:47'),
(83468, 'Selecione o prefixo', 'CIAVITA (Complexo B Tiamina+Riboflavina+Ni)10x10', NULL, 3, 1, 49.00, 10.00, 16, '##', 0, 0, '2025-11-06 09:01:56'),
(83469, 'Selecione o prefixo', 'LUMITER 80/480(ARTIMRTER 80mg E LUMEFANTRINA 480Mg COMP 1X6', NULL, 3, 1, 198.00, 295.00, 16, '', 21, 14, '2025-11-06 09:06:03'),
(83470, 'Selecione o prefixo', 'MILOREX (Amilor 5mg +HYDRO.CHLORTH 50mg) 3X10', NULL, 5, 1, 395.00, 200.00, 16, '', 21, 14, '2025-11-06 09:10:59'),
(83471, 'Selecione o prefixo', 'NUBEND-400(Albendazol COMP 400mg ', NULL, 3, 1, 14.00, 21.00, 16, '', 0, 14, '2025-11-07 10:16:21'),
(83472, 'Selecione o prefixo', 'SPIROSTAL (ESPIROLACTONA 25mg Tablets 10X11', NULL, 3, 1, 345.00, 52.00, 16, '', 0, 14, '2025-11-06 09:19:40'),
(83473, 'Selecione o prefixo', 'glucose 5% (destrose 1000 ML)', NULL, 3, 1, 120.00, 178.00, 16, '02/2027', 21, 14, '2025-11-06 09:25:21'),
(83476, 'Selecione o prefixo', 'NS CLORETO DE SODIO. INFUSION 0.9% 1000ML', NULL, 3, 1, 110.00, 163.00, 16, '', 21, 14, '2025-11-06 09:36:07'),
(83477, 'Selecione o prefixo', 'FUNGINIL-K (KETOCONAZOL CREME 20gm )', NULL, 3, 1, 64.00, 95.00, 16, '', 21, 14, '2025-11-06 09:39:21'),
(83478, 'Selecione o prefixo', 'CEFIXINME FOR ORAL SUSP 100MG/5ML', NULL, 3, 0, 110.00, 164.00, 16, '', 21, 14, '2025-11-07 09:38:23'),
(83479, 'Selecione o prefixo', 'KETACONAZOLE CREM 10G DERM KETA ', NULL, 0, 1, 30.00, 45.00, 16, '', 22, 14, '2025-11-06 09:44:17'),
(83480, 'Selecione o prefixo', 'LIGADURA DE GAZE 10M X10cm12', NULL, 0, 0, 235.00, 350.00, 16, '', 21, 14, '2025-11-06 09:48:19'),
(83481, 'Selecione o prefixo', 'METROGEN-S  200mg/5ML SUSP 100ML ', NULL, 0, 0, 50.00, 75.00, 16, '', 0, 14, '2025-11-06 09:53:15'),
(83482, 'Selecione o prefixo', 'KAMASSUTRA EXTRA FINA', NULL, 3, 1, 45.00, 67.00, 16, '', 22, 8, '2025-11-06 09:56:49'),
(83483, 'Selecione o prefixo', 'ESFIGMOMANOMETRO ANEROIDE 1S ', NULL, 0, 1, 1400.00, 2069.00, 16, '', 22, 8, '2025-11-06 09:59:12'),
(83484, 'Selecione o prefixo', 'DOMIGEN 10 MG TABLETS 10X10', NULL, 3, 1, 99.00, 15.00, 16, '', 22, 8, '2025-11-06 10:05:07'),
(83485, 'Selecione o prefixo', 'CIPROFLOXACINA INJ 200mgX100ML', NULL, 0, 1, 90.00, 134.00, 16, '', 21, 8, '2025-11-07 09:10:36'),
(83486, 'Selecione o prefixo', 'ACEDIXIC, 500mg 10x10 COMP', NULL, 3, 1, 490.00, 73.00, 16, '', 21, 8, '2025-11-06 10:10:54'),
(83487, 'Selecione o prefixo', 'Q-CLAR 500, 1X10 COMP', NULL, 0, 1, 185.00, 274.00, 16, '', 21, 8, '2025-11-07 09:01:42'),
(83488, 'Selecione o prefixo', 'ACECLAV-228.5,SUSP . 100ml', NULL, 0, 1, 239.00, 354.00, 16, '', 0, 8, '2025-11-06 10:19:48'),
(83489, 'Selecione o prefixo', 'ACEMOL-S 120mg/5ML 100ML', NULL, 0, 1, 41.00, 69.00, 16, '', 0, 8, '2025-11-07 09:55:45'),
(83490, 'Selecione o prefixo', 'PROMETAZINA SOLUCAO ORAL 6,25mg/5ml, 100ml', NULL, 0, 1, 90.00, 134.00, 16, '', 21, 8, '2025-11-06 10:25:57'),
(83491, 'Selecione o prefixo', 'ACEPRAZOLE 20mg 10X10 CAPS ', NULL, 3, 1, 90.00, 15.00, 16, '', 22, 8, '2025-11-06 10:29:32'),
(83492, 'Selecione o prefixo', 'METOCLOPRAMIDA 5MG ,COMP . 10X10', NULL, 0, 1, 90.00, 15.00, 16, '', 0, 8, '2025-11-06 10:32:33'),
(83493, 'Selecione o prefixo', 'BROMOVAM , 4 mg  5 ML  SUSP . 100ML ', NULL, 0, 1, 42.00, 63.00, 16, '', 22, 8, '2025-11-06 10:34:41'),
(83494, 'Selecione o prefixo', 'ACERIDE -H 10X10 COMP ', NULL, 0, 1, 175.00, 26.00, 16, '', 0, 8, '2025-11-07 08:09:47'),
(83495, 'Selecione o prefixo', 'AMLOTRIX-5 10X10 COMP', NULL, 0, 1, 73.00, 11.00, 16, '', 0, 8, '2025-11-07 07:45:06'),
(83496, 'Selecione o prefixo', 'ANATEC 10 10X10 COMP', NULL, 0, 1, 91.00, 135.00, 16, '', 0, 8, '2025-11-06 10:42:09'),
(83497, 'Selecione o prefixo', 'DAOMIDE 5mg COMP 10X10 ', NULL, 5, 1, 85.00, 13.00, 16, '', 0, 8, '2025-11-07 07:50:51'),
(83498, 'Selecione o prefixo', 'ANTIEP-300 CPS 3X10', NULL, 2, 1, 275.00, 407.00, 16, '', 0, 8, '2025-11-07 07:52:35'),
(83499, 'Selecione o prefixo', 'ACE S CALAMINE 100 ML LOCAO 1S', NULL, 3, 1, 82.00, 122.00, 16, '', 0, 8, '2025-11-07 07:57:08'),
(83500, 'Selecione o prefixo', 'ECTODINE (IOdopovidona) 100mg /ML SOL 125ML 1 FR', NULL, 0, 1, 314.00, 467.00, 16, '', 0, 8, '2025-11-07 07:58:09'),
(83502, 'Selecione o prefixo', 'BEN-U-RON XAROPE 85 ML PARACETAMOL', NULL, 0, 1, 327.00, 484.00, 16, '', 0, 8, '2025-11-06 10:56:32'),
(83503, 'Selecione o prefixo', 'IB-U-RON 40MG/ML SUSP ORAL 150 ML', NULL, 0, 1, 464.00, 687.00, 16, '', 0, 8, '2025-11-06 10:58:35'),
(83504, 'Selecione o prefixo', 'REUMON GEL 100G', NULL, 0, 1, 428.00, 634.00, 16, '', 0, 8, '2025-11-06 11:01:16'),
(83505, 'Selecione o prefixo', 'URIPRIM 100 MG CX 60 COMP', NULL, 0, 1, 232.00, 344.00, 16, '', 0, 8, '2025-11-06 11:03:39'),
(83506, 'Select prefix', 'FOLICIL CX 60COMP', NULL, 0, 1, 336.00, 498.00, 16, '', 0, 8, '2025-11-06 11:08:33'),
(83507, 'Selecione o prefixo', 'CLINAC SOLUCAO CUTANIA 100 ML ', NULL, 0, 1, 323.15, 479.00, 16, '', 0, 8, '2025-11-06 11:10:52'),
(83508, 'Selecione o prefixo', 'CALCIUM ACTIVE DENK 500MG', NULL, 0, 1, 470.10, 709.00, 16, '', 0, 8, '2025-11-07 07:26:56'),
(83509, 'Selecione o prefixo', 'IBUPROFEN DENK 400-100 COMP ', NULL, 0, 1, 654.37, 939.00, 16, '', 0, 8, '2025-11-06 11:16:51'),
(83510, 'Selecione o prefixo', 'METFORMIN DENK 500-100 COMP', NULL, 0, 1, 375.53, 557.00, 16, '', 0, 8, '2025-11-06 11:18:41'),
(83511, 'Selecione o prefixo', 'CLEARBACT CREAM ', NULL, 0, 1, 54.00, 81.00, 16, '', 0, 9, '2025-11-06 11:20:46'),
(83512, 'Selecione o prefixo', 'HEAMOFORTE 200ML ', NULL, 0, 1, 80.00, 128.00, 16, '', 0, 9, '2025-11-07 10:02:51'),
(83513, 'Selecione o prefixo', 'MICOSHA CREAM 15GR', NULL, 0, 1, 40.00, 60.00, 16, '', 0, 9, '2025-11-06 11:25:35'),
(83514, 'Selecione o prefixo', 'ISTALON 250ML ', NULL, 0, 1, 110.00, 158.00, 16, '', 0, 8, '2025-11-06 11:27:24'),
(83515, 'Selecione o prefixo', 'TESTE MALARIA PF/PAN (HRP PLDH)1 S', NULL, 0, 1, 90.00, 134.00, 16, '', 0, 8, '2025-11-06 11:30:05'),
(83516, 'Selecione o prefixo', 'FRUSEMIX 10mg/ML INJ. 10X2 ML ', NULL, 0, 1, 180.00, 267.00, 16, '', 0, 8, '2025-11-06 11:32:07'),
(83517, 'Selecione o prefixo', 'NEOCID PINEAPLE SUSP 160ML ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:35:17'),
(83518, 'Selecione o prefixo', 'NEOCID  STRAWBERRY SUSP 160 ML', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:37:31'),
(83519, 'Selecione o prefixo', 'NEOCLAV 312.5 SUSP (100ml)', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:39:23'),
(83520, 'Selecione o prefixo', 'SILAGRA- 100mg TABS(10x1x4)', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:42:18'),
(83521, 'Selecione o prefixo', 'AZMASOL HFA INHALER', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:46:12'),
(83522, 'Selecione o prefixo', 'DOLAPYN TABS 10X10', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:49:49'),
(83523, 'Selecione o prefixo', 'H/E HALBENDAZOLE-400 ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:51:52'),
(83524, 'Selecione o prefixo', 'OMASTIN 150 CAPS ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:53:23'),
(83525, 'Selecione o prefixo', 'NEODOPA TABS 10X10', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:54:51'),
(83526, 'Selecione o prefixo', 'NEOCLAV 625 TABS  (6X3)', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:56:45'),
(83527, 'Selecione o prefixo', 'TEZOLE CREME / BISNAGA 10g ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 11:58:09'),
(83528, 'Selecione o prefixo', 'MICOZOLE PLUS CREME ( MICONAZOL)', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:00:13'),
(83529, 'Selecione o prefixo', 'GENTIAN VIOLET 25 ML ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:02:05'),
(83530, 'Selecione o prefixo', 'NEOFENAC GEL 25G ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:03:26'),
(83531, 'Selecione o prefixo', 'H/E VOOMA 100 COMP ( 1X4 ) 100 mg', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:04:53'),
(83532, 'Selecione o prefixo', 'ZINC OXIDE PLASTER 10cmX5M (1X6)', NULL, 3, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:07:03'),
(83533, 'Selecione o prefixo', 'COMYCETIN EYE DROPS 10ml ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:09:34'),
(83534, 'Selecione o prefixo', 'OPTOPRED EYE DROPS 5ml ', NULL, 0, 1, 0.00, 0.00, 16, '', 0, 11, '2025-11-06 12:11:06'),
(83535, 'Selecione o prefixo', 'SEKURE INTIMO ', NULL, 0, 1, 27.50, 60.00, 16, '', 0, 0, '2025-11-06 14:24:05'),
(83536, 'Selecione o prefixo', 'FIESTA BANANA ', NULL, 0, 1, 400.00, 25.00, 16, '', 21, 0, '2025-11-06 14:31:08'),
(83537, 'Selecione o prefixo', 'FIESTA MENTA ', NULL, 0, 1, 400.00, 25.00, 16, '', 21, 0, '2025-11-06 14:35:16'),
(83538, 'Selecione o prefixo', 'FIESTA CAFE ', NULL, 0, 1, 400.00, 25.00, 16, '', 22, 0, '2025-11-06 14:40:41'),
(83539, 'Selecione o prefixo', 'FIESTA UVA ', NULL, 0, 1, 400.00, 25.00, 16, '', 21, 0, '2025-11-06 14:42:27'),
(83540, 'Selecione o prefixo', 'PRUDENCE MORANGO', NULL, 0, 1, 275.00, 20.00, 16, '', 21, 0, '2025-11-06 14:45:38'),
(83541, 'Selecione o prefixo', 'PRUDENCE CLASSICO', NULL, 0, 1, 275.00, 20.00, 16, '', 0, 0, '2025-11-06 14:48:04'),
(83542, 'Selecione o prefixo', 'PRUDENCE NICE ', NULL, 0, 1, 275.00, 20.00, 16, '', 21, 0, '2025-11-06 14:49:13'),
(83543, 'Selecione o prefixo', 'PRUDENCE SENSUAL ', NULL, 0, 1, 275.00, 20.00, 16, '', 22, 0, '2025-11-06 14:50:16'),
(83544, 'Selecione o prefixo', 'INTIMO SEGURO ', NULL, 0, 1, 250.00, 550.00, 16, '', 21, 0, '2025-11-06 14:55:37'),
(83545, 'Select prefix', 'PRUDENCE MARACUJA ', NULL, 0, 1, 275.00, 20.00, 16, '', 0, 0, '2025-11-06 15:00:22'),
(83546, 'Selecione o prefixo', 'GEL LUBRIFICANTE (PRUDENCE) sem aroma', NULL, 0, 1, 400.00, 30.00, 16, '', 0, 0, '2025-11-06 15:05:25'),
(83547, 'Selecione o prefixo', 'GEL LUBRIFICANTE PRUDENCE (MORANGO)', NULL, 0, 1, 400.00, 30.00, 16, '', 0, 0, '2025-11-06 15:08:12'),
(83548, 'Selecione o prefixo', 'ALPRAZOLAM BLUEPHARMA 0.5mg', NULL, 0, 1, 326.00, 81.00, 16, '', 0, 8, '2025-11-06 15:20:11'),
(83549, 'Selecione o prefixo', 'ALPRAZOLAM 1mg', NULL, 0, 1, 325.00, 55.00, 16, '', 22, 8, '2025-11-06 15:19:05'),
(83550, 'Selecione o prefixo', 'ANTI ACNE SABONETE ', NULL, 0, 1, 59.00, 89.00, 16, '', 21, 8, '2025-11-07 07:10:27'),
(83551, 'Selecione o prefixo', 'ENATEC 10X10 COMP', NULL, 0, 1, 91.00, 14.00, 16, '', 21, 8, '2025-11-07 07:50:05'),
(83552, 'Selecione o prefixo', 'MBEN 100mg, COMP 2X6', NULL, 0, 1, 100.00, 10.00, 16, '', 0, 8, '2025-11-07 08:53:03'),
(83553, 'Selecione o prefixo', 'AMOX-500, 10X10', NULL, 100, 1, 260.00, 385.00, 16, '', 9, 8, '2025-11-10 10:34:23');

-- --------------------------------------------------------

--
-- Table structure for table `rc_fact`
--

CREATE TABLE `rc_fact` (
  `id` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `serie` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `id_rc` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rc_fact`
--

INSERT INTO `rc_fact` (`id`, `factura`, `valor`, `iva`, `total`, `serie`, `cliente`, `user`, `id_rc`, `data`) VALUES
(1, 2, 100.00, 0.00, 100.00, 2025, 1, 22, 1, '2025-11-24 20:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `rc_fact_temp`
--

CREATE TABLE `rc_fact_temp` (
  `id` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `serie` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rc_fact_temp`
--

INSERT INTO `rc_fact_temp` (`id`, `factura`, `valor`, `iva`, `total`, `serie`, `cliente`, `user`, `data`) VALUES
(1, 7, 10.00, 0.00, 10.00, 2025, 1, 1, '2025-11-24 08:45:03');

-- --------------------------------------------------------

--
-- Table structure for table `rc_faturas_temp_recepcao`
--

CREATE TABLE `rc_faturas_temp_recepcao` (
  `id` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recibo`
--

CREATE TABLE `recibo` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `cliente` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `recibo`
--

INSERT INTO `recibo` (`id`, `n_doc`, `descricao`, `valor`, `modo`, `serie`, `cliente`, `user`, `data`) VALUES
(1, 1, '2025-11-24 22:22:38', 100.00, 'Numerario', '2025', 1, 22, '2025-11-24 20:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `recibo_factura_recepcao`
--

CREATE TABLE `recibo_factura_recepcao` (
  `id` int(11) NOT NULL,
  `recibo_id` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recibo_recepcao`
--

CREATE TABLE `recibo_recepcao` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisicao_externa`
--

CREATE TABLE `requisicao_externa` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `fornecedor` int(11) NOT NULL,
  `sector` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `requisicao_externa`
--

INSERT INTO `requisicao_externa` (`id`, `n_doc`, `descricao`, `fornecedor`, `sector`, `solicitante`, `serie`, `motivo`, `user`, `data`) VALUES
(1, 1, '2025-11-10 07:47:48', 2, 9, '', 2025, '', 22, '2025-11-10 07:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `requisicao_interna`
--

CREATE TABLE `requisicao_interna` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sector` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `motivo` varchar(20000) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `requisicao_interna`
--

INSERT INTO `requisicao_interna` (`id`, `n_doc`, `descricao`, `sector`, `solicitante`, `serie`, `motivo`, `user`, `data`) VALUES
(1, 1, '2025-11-07 16:42:55', 9, '', 2025, '', 24, '2025-11-07 06:42:55'),
(2, 2, '2025-11-08 19:40:54', 9, '', 2025, '', 22, '2025-11-08 09:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `re_artigos`
--

CREATE TABLE `re_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `re` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `re_artigos`
--

INSERT INTO `re_artigos` (`id`, `artigo`, `qtd`, `re`, `user`, `data`) VALUES
(1, 9, 10, 1, 22, '2025-11-10 07:47:48'),
(2, 6, 5, 1, 22, '2025-11-10 07:47:48'),
(3, 8, 6, 1, 22, '2025-11-10 07:47:48'),
(4, 24, 5, 1, 22, '2025-11-10 07:47:48'),
(5, 23, 5, 1, 22, '2025-11-10 07:47:48'),
(6, 22, 4, 1, 22, '2025-11-10 07:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `re_artigos_temp`
--

CREATE TABLE `re_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `re_artigos_temp`
--

INSERT INTO `re_artigos_temp` (`id`, `artigo`, `qtd`, `user`, `data`) VALUES
(1, 4, 1, 24, '2025-11-07 06:43:12'),
(2, 6, 1, 24, '2025-11-07 06:43:12'),
(3, 7, 1, 24, '2025-11-07 06:43:13'),
(4, 9, 1, 24, '2025-11-07 06:43:13');

-- --------------------------------------------------------

--
-- Table structure for table `ri_artigos`
--

CREATE TABLE `ri_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `ri` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ri_artigos`
--

INSERT INTO `ri_artigos` (`id`, `artigo`, `qtd`, `ri`, `user`, `data`) VALUES
(1, 4, 1, 1, 24, '2025-11-07 06:42:55'),
(2, 8, 1, 1, 24, '2025-11-07 06:42:55'),
(3, 16, 1, 1, 24, '2025-11-07 06:42:55'),
(4, 20, 1, 1, 24, '2025-11-07 06:42:55'),
(5, 23, 1, 1, 24, '2025-11-07 06:42:55'),
(6, 16, 3, 2, 22, '2025-11-08 09:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `ri_artigos_temp`
--

CREATE TABLE `ri_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saida_caixa`
--

CREATE TABLE `saida_caixa` (
  `id` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `caixa` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saida_stock`
--

CREATE TABLE `saida_stock` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `serie` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `motivo` text NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `responsavel` varchar(255) NOT NULL,
  `qtdrequisicao` int(11) DEFAULT NULL,
  `qtdrequisicaoexterna` int(11) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sector`
--

INSERT INTO `sector` (`id`, `nome`, `responsavel`, `qtdrequisicao`, `qtdrequisicaoexterna`, `data`) VALUES
(9, 'Farmacia', 'Estevao Licussa', NULL, NULL, '2024-03-18 15:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `serie_factura`
--

CREATE TABLE `serie_factura` (
  `id` int(11) NOT NULL,
  `ano_fiscal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `serie_factura`
--

INSERT INTO `serie_factura` (`id`, `ano_fiscal`) VALUES
(1, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `servicos_clinica`
--

CREATE TABLE `servicos_clinica` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `categoria` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servicos_clinica`
--

INSERT INTO `servicos_clinica` (`id`, `codigo`, `nome`, `descricao`, `preco`, `categoria`, `ativo`, `data_criacao`, `usuario_criacao`) VALUES
(1, 'CONS-GERAL', 'Consulta Geral', 'Consulta médica geral', 1200.00, 'Consulta', 1, '2025-11-21 12:41:45', NULL),
(2, 'CONS-ESPEC', 'Consulta Especializada', 'Consulta com especialista', 2000.00, 'Consulta', 1, '2025-11-21 12:41:45', NULL),
(3, 'EXAME-SANGUE', 'Exame de Sangue', 'Análise de sangue completa', 1500.00, 'Exame', 1, '2025-11-21 12:41:45', NULL),
(4, 'EXAME-URINA', 'Exame de Urina', 'Análise de urina', 800.00, 'Exame', 1, '2025-11-21 12:41:45', NULL),
(5, 'PROC-CURATIVO', 'Curativo', 'Aplicação de curativo', 500.00, 'Procedimento', 1, '2025-11-21 12:41:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ss_artigos`
--

CREATE TABLE `ss_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `ss` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ss_artigos_temp`
--

CREATE TABLE `ss_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ss_artigos_temp`
--

INSERT INTO `ss_artigos_temp` (`id`, `artigo`, `qtd`, `user`, `data`) VALUES
(1, 1463, 1, 22, '2025-11-05 08:21:49');

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `quantidade_inicial` int(11) DEFAULT NULL,
  `lote` varchar(50) DEFAULT NULL,
  `prazo` date DEFAULT NULL,
  `estado` enum('ativo','inativo') DEFAULT 'ativo',
  `data_entrada` varchar(255) DEFAULT NULL,
  `origem` varchar(50) DEFAULT NULL COMMENT 'Origem do estoque (entrada_direta, transferencia_armazem, etc)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `produto_id`, `quantidade`, `quantidade_inicial`, `lote`, `prazo`, `estado`, `data_entrada`, `origem`) VALUES
(1, 232, 2, 2, 'GF89', '0000-00-00', 'ativo', NULL, NULL),
(2, 195, 10, 10, 'GF89', '2026-03-05', 'ativo', NULL, NULL),
(3, 266, 10, 10, 'GF89', '2026-03-05', 'ativo', NULL, NULL),
(4, 274, 5, 5, 'GF89', '2026-03-05', 'ativo', NULL, NULL),
(5, 83438, 5, 5, 'GF89', '2026-03-05', 'ativo', NULL, NULL),
(6, 1270, 10, 10, 'GF52', '2028-03-01', 'ativo', NULL, NULL),
(7, 956, 10, 10, 'GF69', '2028-02-28', 'ativo', NULL, NULL),
(8, 1521, 10, 10, 'GF92', '2027-01-30', 'ativo', NULL, NULL),
(9, 1465, 10, 10, '323232691', '2027-11-30', 'ativo', NULL, NULL),
(10, 918, 10, 10, 'ARI72', '2029-10-31', 'ativo', NULL, NULL),
(11, 83453, 10, 10, 'DE-100070', '2028-03-31', 'ativo', NULL, NULL),
(12, 1463, 10, 10, 'GCS001', '2029-05-30', 'ativo', NULL, NULL),
(13, 1310, 10, 10, '24268', '2027-09-30', 'ativo', NULL, NULL),
(14, 83339, 10, 10, '2404028', '2027-04-30', 'ativo', NULL, NULL),
(15, 1452, 10, 10, '2412060', '2027-06-30', 'ativo', NULL, NULL),
(16, 1463, 10, 10, '2405070', '2029-05-30', 'ativo', NULL, NULL),
(17, 641, 10, 10, 'E0140238', '2027-10-31', 'ativo', NULL, NULL),
(18, 716, 9, 10, 'AEL24017A', '2026-09-30', 'ativo', NULL, NULL),
(19, 657, 10, 10, 'ABS24001A', '2027-01-31', 'ativo', NULL, NULL),
(20, 1503, 10, 10, 'AAR24004', '2026-09-30', 'ativo', NULL, NULL),
(21, 83302, 8, 10, 'MZ250305', '2030-02-28', 'ativo', NULL, NULL),
(22, 83370, 9, 10, 'MZ240901', '2027-08-30', 'ativo', NULL, NULL),
(23, 613, 10, 10, 'AEC25002A', '2027-12-31', 'ativo', NULL, NULL),
(24, 612, 10, 10, '1085', '2027-11-30', 'ativo', NULL, NULL),
(25, 1459, 50, 50, '2502010', '2028-02-28', 'ativo', NULL, NULL),
(26, 47, 50, 50, 'MC25025', '2027-01-31', 'ativo', NULL, NULL),
(27, 922, 20, 20, 'ML24785', '2027-10-31', 'ativo', NULL, NULL),
(28, 83414, 50, 50, '1024265', '2029-08-31', 'ativo', NULL, NULL),
(29, 1172, 10, 10, 'C183', '2027-02-28', 'ativo', NULL, NULL),
(30, 1220, 10, 10, 'HF4003,HF4004', '2027-06-30', 'ativo', NULL, NULL),
(31, 4, 4, 10, '202402', '2029-02-28', 'ativo', NULL, NULL),
(32, 8, 1, 10, 'MZ240501', '2029-04-30', 'ativo', NULL, NULL),
(33, 1481, 100, 100, 'ADJ25002A', '2027-12-31', 'ativo', NULL, NULL),
(34, 896, 10, 10, 'ACG24008', '2027-11-30', 'ativo', NULL, NULL),
(35, 83443, 10, 100, 'ADA23006', '2026-05-31', 'ativo', NULL, NULL),
(36, 1494, 10, 10, 'ABD24001', '2026-12-31', 'ativo', NULL, NULL),
(37, 1494, 10, 10, 'ABD24001', '2026-12-31', 'ativo', NULL, NULL),
(38, 931, 10, 10, 'AFR25002A', '2027-12-31', 'ativo', NULL, NULL),
(39, 1632, 10, 10, 'N25B18', '2027-01-31', 'ativo', NULL, NULL),
(40, 1250, 10, 10, '4G05', '2026-06-30', 'ativo', NULL, NULL),
(41, 1250, 10, 10, '4G05', '2026-06-30', 'ativo', NULL, NULL),
(42, 83378, 10, 10, '15E0150042', '2028-02-28', 'ativo', NULL, NULL),
(43, 581, 10, 10, '0130128', '2026-11-30', 'ativo', NULL, NULL),
(44, 1507, 10, 10, 'E0140254', '2027-10-31', 'ativo', NULL, NULL),
(45, 744, 10, 10, 'E014015A', '2027-12-31', 'ativo', NULL, NULL),
(46, 1622, 10, 10, 'E0150001', '2027-12-31', 'ativo', NULL, NULL),
(47, 1484, 10, 10, 'AEV24007', '2027-10-31', 'ativo', NULL, NULL),
(48, 1482, 10, 10, 'ACL24008A', '2027-10-31', 'ativo', NULL, NULL),
(49, 1482, 100, 100, 'ACL24008A', '2027-10-31', 'ativo', NULL, NULL),
(50, 1485, 100, 100, 'ADW24004', '2027-03-31', 'ativo', NULL, NULL),
(51, 83375, 100, 100, 'ACH24005', '2027-11-30', 'ativo', NULL, NULL),
(52, 760, 100, 100, 'ABG24009', '2027-09-30', 'ativo', NULL, NULL),
(53, 1673, 10, 10, 'AEA25001', '2027-12-31', 'ativo', NULL, NULL),
(54, 1517, 6, 10, 'AAB25002', '2027-12-31', 'ativo', NULL, NULL),
(55, 1331, 10, 10, 'AAL24005A', '2027-10-31', 'ativo', NULL, NULL),
(56, 1506, 10, 10, 'AHQ24004', '2027-03-06', 'ativo', NULL, NULL),
(57, 800, 10, 10, 'AFQ25002', '2028-01-30', 'ativo', NULL, NULL),
(58, 1497, 10, 10, 'AGV25001', '2028-01-31', 'ativo', NULL, NULL),
(59, 714, 98, 100, 'ACI24016A', '2027-06-30', 'ativo', NULL, NULL),
(60, 965, 93, 100, 'ABW25004', '2027-12-31', 'ativo', NULL, NULL),
(61, 722, 6, 10, 'ACQ24021', '2027-10-31', 'ativo', NULL, NULL),
(62, 1480, 96, 100, 'AAA24044', '2027-10-31', 'ativo', NULL, NULL),
(63, 1361, 10, 10, 'ADT25001', '2027-11-30', 'ativo', NULL, NULL),
(64, 1249, 10, 10, 'AKN25001', '2028-03-31', 'ativo', NULL, NULL),
(65, 1620, 10, 10, 'AKM2429', '2027-09-30', 'ativo', NULL, NULL),
(66, 913, 10, 10, 'ACO24007', '2027-10-31', 'ativo', NULL, NULL),
(67, 763, 11, 11, 'AAD25001', '2028-01-31', 'ativo', NULL, NULL),
(68, 83387, 10, 10, 'AAD25001', '2028-01-31', 'ativo', NULL, NULL),
(69, 1630, 10, 10, 'E01150023', '2028-01-31', 'ativo', NULL, NULL),
(70, 836, 100, 100, 'ABE25001', '2027-12-30', 'ativo', NULL, NULL),
(71, 1260, 10, 10, 'ABP24002', '2026-06-30', 'ativo', NULL, NULL),
(72, 622, 10, 10, 'AHO24008', '2027-10-31', 'ativo', NULL, NULL),
(73, 83365, 100, 100, 'ABH24006', '2027-05-31', 'ativo', NULL, NULL),
(74, 1512, 10, 10, 'CBDXD4004', '2026-05-31', 'ativo', NULL, NULL),
(75, 83369, 10, 10, 'AEQ25001', '2027-02-28', 'ativo', NULL, NULL),
(76, 909, 10, 10, 'ABF25001A', '2026-10-31', 'ativo', NULL, NULL),
(77, 1246, 10, 10, 'ACX25004A', '2028-03-31', 'ativo', NULL, NULL),
(78, 1333, 10, 10, 'ADP24019A', '2027-07-31', 'ativo', NULL, NULL),
(79, 718, 10, 10, 'ADD24007', '2027-08-31', 'ativo', NULL, NULL),
(80, 83305, 10, 10, 'AAF24003A', '2027-11-30', 'ativo', NULL, NULL),
(81, 777, 10, 10, 'BX028', '2026-07-31', 'ativo', NULL, NULL),
(82, 755, 10, 10, 'PB026', '2026-07-31', 'ativo', NULL, NULL),
(83, 1501, 10, 10, 'B25071', '2027-01-31', 'ativo', NULL, NULL),
(84, 756, 10, 10, 'D25075', '2027-01-31', 'ativo', NULL, NULL),
(85, 83382, 10, 10, '24022801', '2026-11-30', 'ativo', NULL, NULL),
(86, 1502, 110, 110, 'PA053', '2026-07-31', 'ativo', NULL, NULL),
(87, 1277, 10, 10, 'AA076', '2026-09-30', 'ativo', NULL, NULL),
(88, 1672, 10, 10, 'AAG25001A', '2027-12-31', 'ativo', NULL, NULL),
(89, 1496, 10, 10, 'AAZ24020A', '2027-10-31', 'ativo', NULL, NULL),
(90, 631, 98, 100, 'ABQ24004A', '2027-07-31', 'ativo', NULL, NULL),
(91, 632, 10, 10, 'ALB25001A', '2028-01-31', 'ativo', NULL, NULL),
(92, 1247, 100, 100, 'ABY25001A', '2028-02-28', 'ativo', NULL, NULL),
(93, 1246, 5, 5, 'ACY25001A', '2028-02-28', 'ativo', NULL, NULL),
(94, 1362, 10, 10, 'ACC23015A', '2026-09-30', 'ativo', NULL, NULL),
(95, 1362, 10, 10, 'ACC23015A', '2026-09-30', 'ativo', NULL, NULL),
(96, 946, 10, 10, 'AAE25001A', '2027-12-31', 'ativo', NULL, NULL),
(97, 850, 10, 10, 'AAS25001A', '2028-01-31', 'ativo', NULL, NULL),
(98, 623, 100, 100, 'AFS24003A', '2027-05-31', 'ativo', NULL, NULL),
(99, 557, 5, 5, 'E0150004', '2027-12-31', 'ativo', NULL, NULL),
(100, 1274, 100, 100, 'AJZ24004A', '2027-10-31', 'ativo', NULL, NULL),
(101, 1454, 10, 10, 'ABO25001', '2028-01-31', 'ativo', NULL, NULL),
(102, 9, 10, 10, 'AOE37', '0202-09-30', 'ativo', NULL, NULL),
(103, 10, 8, 10, 'WG24456', '2027-05-30', 'ativo', NULL, NULL),
(104, 1548, 10, 10, 'BA4006', '2027-09-30', 'ativo', NULL, NULL),
(105, 15, 10, 10, 'GPN', '2027-07-31', 'ativo', NULL, NULL),
(106, 16, 10, 10, 'ATF10', '2027-09-30', 'ativo', NULL, NULL),
(107, 738, 10, 10, 'HA001', '2027-06-30', 'ativo', NULL, NULL),
(108, 758, 10, 10, 'QB24021,QB24022', '2027-06-30', 'ativo', NULL, NULL),
(109, 20, 9, 10, 'WR2501', '2027-12-31', 'ativo', NULL, NULL),
(110, 1614, 10, 10, 'WR2501', '2027-12-31', 'ativo', NULL, NULL),
(111, 200, 10, 10, 'CPC132,133,134', '2027-05-31', 'ativo', NULL, NULL),
(112, 23, 10, 10, 'BJ4006', '2027-04-30', 'ativo', NULL, NULL),
(113, 202, 10, 10, 'CD24007', '2027-06-30', 'ativo', NULL, NULL),
(114, 26, 10, 10, 'NZ4007,NZ4008', '2027-06-30', 'ativo', NULL, NULL),
(115, 29, 10, 10, 'M0895TOM0898', '2027-07-31', 'ativo', NULL, NULL),
(116, 702, 10, 10, '2419570L001', '2027-09-30', 'ativo', NULL, NULL),
(117, 1603, 10, 10, 'L2434', '2027-01-31', 'ativo', NULL, NULL),
(118, 1604, 10, 10, 'L2434', '2027-01-31', 'ativo', NULL, NULL),
(119, 1604, 10, 10, 'L2434', '2027-01-31', 'ativo', NULL, NULL),
(120, 1534, 10, 10, 'CB250032,CVB25004', '2028-03-31', 'ativo', NULL, NULL),
(121, 1089, 10, 10, 'PGL97PGL101', '2028-10-31', 'ativo', NULL, NULL),
(122, 83348, 10, 10, 'L2677TOL2680', '2027-02-28', 'ativo', NULL, NULL),
(123, 41, 10, 10, 'S2520021', '2026-12-31', 'ativo', NULL, NULL),
(124, 1334, 10, 10, 'AM4001,AM4002,AM4003', '2027-01-31', 'ativo', NULL, NULL),
(125, 83333, 10, 10, 'L2498TOL2501', '2027-01-31', 'ativo', NULL, NULL),
(126, 83333, 10, 10, 'L2498TOL2501', '2027-01-31', 'ativo', NULL, NULL),
(127, 42, 100, 100, '24189470T005L,241894', '2027-03-30', 'ativo', NULL, NULL),
(128, 1434, 10, 10, '2415226L001', '2027-02-28', 'ativo', NULL, NULL),
(129, 1406, 10, 10, 'CN4054,CN4055', '2027-02-28', 'ativo', NULL, NULL),
(130, 47, 10, 10, '13425Z060', '2028-01-31', 'ativo', NULL, NULL),
(131, 83449, 10, 10, '13425Z060', '2028-01-31', 'ativo', NULL, NULL),
(132, 1370, 10, 10, 'DLC4TC011,DLC4TC012', '2027-09-30', 'ativo', NULL, NULL),
(133, 48, 10, 10, 'GV014,GV015', '2026-07-31', 'ativo', NULL, NULL),
(134, 51, 10, 10, '0G010,0G011', '2026-07-31', 'ativo', NULL, NULL),
(135, 52, 10, 10, 'D25161', '2027-03-31', 'ativo', NULL, NULL),
(136, 1098, 10, 10, 'L2348', '2027-10-31', 'ativo', NULL, NULL),
(137, 951, 50, 50, '243041026', '2027-04-30', 'ativo', NULL, NULL),
(138, 81, 10, 10, 'WH24027', '2027-01-31', 'ativo', NULL, NULL),
(139, 83326, 10, 10, 'DLR24HG001,G002,G3', '2027-06-30', 'ativo', NULL, NULL),
(140, 89, 10, 10, '122110', '2027-05-31', 'ativo', NULL, NULL),
(141, 1615, 10, 10, 'MTR54,55,56,57,58,5', '2027-04-30', 'ativo', NULL, NULL),
(142, 1076, 10, 10, 'EF4003,EF4004,EF4005', '2027-08-31', 'ativo', NULL, NULL),
(143, 104, 9, 10, 'WS2511', '2027-12-31', 'ativo', NULL, NULL),
(144, 1010, 10, 10, 'PET858,859,860,861', '2027-12-31', 'ativo', NULL, NULL),
(145, 673, 10, 10, 'XE4I16', '2027-08-31', 'ativo', NULL, NULL),
(146, 1524, 10, 10, '20230717', '2028-07-31', 'ativo', NULL, NULL),
(147, 120, 10, 10, 'CK24007', '2027-06-30', 'ativo', NULL, NULL),
(148, 950, 5, 5, 'GA24003', '2027-06-30', 'ativo', NULL, NULL),
(149, 144, 10, 10, '240442', '2027-03-31', 'ativo', NULL, NULL),
(150, 179, 2, 2, '241066', '2027-05-30', 'ativo', NULL, NULL),
(151, 180, 2, 2, '230373', '2026-03-31', 'ativo', NULL, NULL),
(152, 182, 5, 5, '2310181', '2026-06-30', 'ativo', NULL, NULL),
(153, 192, 10, 10, 'M1455', '2026-08-31', 'ativo', NULL, NULL),
(154, 193, 10, 10, 'M1459', '2026-08-30', 'ativo', NULL, NULL),
(155, 199, 10, 10, '2318178', '2027-10-31', 'ativo', NULL, NULL),
(156, 200, 10, 10, '240719', '2027-04-30', 'ativo', NULL, NULL),
(157, 1649, 5, 5, '240972', '2027-05-30', 'ativo', NULL, NULL),
(158, 203, 5, 5, 'N1498', '2027-06-30', 'ativo', NULL, NULL),
(159, 224, 3, 3, '240739', '2027-04-30', 'ativo', NULL, NULL),
(160, 223, 3, 3, '240772', '2027-04-30', 'ativo', NULL, NULL),
(161, 225, 3, 3, 'BB0164B', '2027-02-28', 'ativo', NULL, NULL),
(162, 991, 3, 3, 'AK1234H', '2026-11-30', 'ativo', NULL, NULL),
(163, 230, 3, 3, '242229', '2026-11-30', 'ativo', NULL, NULL),
(164, 717, 88, 90, 'BE2411-40', '2027-07-30', 'ativo', NULL, NULL),
(165, 476, 8, 10, 'DC010-20', '2028-02-28', 'ativo', NULL, NULL),
(166, 1083, 50, 50, 'DT065-85', '2027-01-30', 'ativo', NULL, NULL),
(167, 1647, 19, 20, 'DL-014-15', '2028-02-28', 'ativo', NULL, NULL),
(168, 721, 100, 100, 'BT85-97', '2026-07-30', 'ativo', NULL, NULL),
(169, 1403, 10, 10, 'D35003', '2028-01-30', 'ativo', NULL, NULL),
(170, 1079, 10, 10, 'CE144-47', '2026-08-30', 'ativo', NULL, NULL),
(171, 1599, 10, 10, 'DE055-60', '2028-02-28', 'ativo', NULL, NULL),
(172, 950, 10, 10, 'CE103-108', '2027-06-30', 'ativo', NULL, NULL),
(173, 484, 40, 40, 'SR4489', '2027-08-30', 'ativo', NULL, NULL),
(174, 480, 10, 10, '2408155-160', '2027-07-30', 'ativo', NULL, NULL),
(175, 1438, 10, 10, 'NL4087', '2027-02-28', 'ativo', NULL, NULL),
(176, 490, 10, 10, 'BCHZ2401', '2027-01-30', 'ativo', NULL, NULL),
(177, 469, 80, 80, 'CT110', '2027-04-30', 'ativo', NULL, NULL),
(178, 1424, 10, 10, 'D14325001', '2028-01-31', 'ativo', NULL, NULL),
(179, 659, 12, 12, 'B19725001', '2027-12-31', 'ativo', NULL, NULL),
(180, 1431, 10, 10, 'D17225001', '2028-01-31', 'ativo', NULL, NULL),
(181, 1714, 11, 12, 'D12025001', '2028-01-31', 'ativo', NULL, NULL),
(182, 719, 15, 15, '010P2416X', '2025-12-26', 'ativo', NULL, NULL),
(183, 1712, 10, 10, 'D14125001', '2028-01-31', 'ativo', NULL, NULL),
(184, 1573, 7, 10, '24GO26', '2026-06-30', 'ativo', NULL, NULL),
(185, 973, 10, 10, '24MK136', '2027-10-31', 'ativo', NULL, NULL),
(186, 937, 5, 5, 'HELL111', '2027-03-31', 'ativo', NULL, NULL),
(187, 1576, 25, 25, 'HEML111', '2027-03-31', 'ativo', NULL, NULL),
(188, 1577, 24, 25, 'HERL110', '2027-03-31', 'ativo', NULL, NULL),
(189, 938, 47, 50, 'HETL111', '2027-03-31', 'ativo', NULL, NULL),
(190, 741, 12, 12, 'D26824001', '2027-01-31', 'ativo', NULL, NULL),
(191, 750, 10, 10, '10241431', '2027-05-31', 'ativo', NULL, NULL),
(192, 749, 10, 10, '10241310', '2027-04-30', 'ativo', NULL, NULL),
(193, 1266, 10, 10, '085G24', '2027-07-31', 'ativo', NULL, NULL),
(194, 1715, 10, 10, 'D10125001', '2027-12-31', 'ativo', NULL, NULL),
(195, 1179, 2, 2, 'XD10EJ73E', '2027-01-07', 'ativo', NULL, NULL),
(196, 1191, 1, 1, '#######', '2028-12-31', 'ativo', NULL, NULL),
(197, 975, 10, 10, 'D10525001', '2027-12-31', 'ativo', NULL, NULL),
(198, 976, 10, 10, 'D10325001', '2028-01-31', 'ativo', NULL, NULL),
(199, 364, 10, 10, 'NFPL41010', '2027-10-31', 'ativo', NULL, NULL),
(200, 842, 10, 10, '24G011', '2027-06-30', 'ativo', NULL, NULL),
(201, 981, 10, 10, '24K031', '2026-10-31', 'ativo', NULL, NULL),
(202, 593, 10, 10, '20250227', '2030-02-26', 'ativo', NULL, NULL),
(203, 1173, 2, 2, '#######', '2030-03-03', 'ativo', NULL, NULL),
(204, 1423, 8, 10, 'D04225001', '2027-12-31', 'ativo', NULL, NULL),
(205, 968, 10, 10, 'CYH002', '2028-01-31', 'ativo', NULL, NULL),
(206, 83420, 2, 2, 'MZ250318', '2030-02-28', 'ativo', NULL, NULL),
(207, 54, 24, 24, '24104001', '2029-03-30', 'ativo', NULL, NULL),
(208, 58, 24, 24, '24044033', '2029-03-31', 'ativo', NULL, NULL),
(209, 60, 24, 24, '24014003', '2028-12-30', 'ativo', NULL, NULL),
(210, 31, 1, 1, '20240816', '2026-08-21', 'ativo', NULL, NULL),
(211, 1180, 1, 1, 'MZ250121', '2026-12-31', 'ativo', NULL, NULL),
(212, 293, 2, 2, 'GP039BE', '2028-02-28', 'ativo', NULL, NULL),
(213, 292, 2, 2, 'GP02U9P', '2026-07-31', 'ativo', NULL, NULL),
(214, 1328, 1, 1, 'MZ240903', '2027-08-31', 'ativo', NULL, NULL),
(215, 83448, 100, 100, 'MZ240903', '2027-08-31', 'ativo', NULL, NULL),
(216, 1608, 20, 20, '2410086', '2027-09-30', 'ativo', NULL, NULL),
(217, 1668, 100, 100, '2410131', '2027-09-30', 'ativo', NULL, NULL),
(218, 1207, 99, 100, 'U24C0165A', '2027-09-30', 'ativo', NULL, NULL),
(219, 1667, 10, 10, '2403194A', '2027-02-28', 'ativo', NULL, NULL),
(220, 1551, 10, 10, 'NAD2410C', '2026-12-30', 'ativo', NULL, NULL),
(221, 1606, 11, 11, 'NA24391B', '2027-01-30', 'ativo', NULL, NULL),
(222, 923, 50, 50, '116257', '2027-04-30', 'ativo', NULL, NULL),
(223, 983, 10, 10, '120476', '0000-00-00', 'ativo', NULL, NULL),
(224, 655, 10, 10, '230755', '2026-06-30', 'ativo', NULL, NULL),
(225, 672, 1, 1, '2409049,2409050,2410130A', '2027-08-30', 'ativo', NULL, NULL),
(226, 1210, 9, 10, '2408098,2408096', '2027-07-30', 'ativo', NULL, NULL),
(227, 731, 10, 10, '2411100', '2027-10-23', 'ativo', NULL, NULL),
(228, 727, 10, 10, '2409047', '2027-08-30', 'ativo', NULL, NULL),
(229, 726, 10, 10, '2408076,2408077,2407248A', '2027-06-30', 'ativo', NULL, NULL),
(230, 911, 10, 10, '2407248A', '2027-06-30', 'ativo', NULL, NULL),
(231, 1663, 10, 10, '2410095/96/97,2410174/175', '2027-09-30', 'ativo', NULL, NULL),
(232, 1663, 10, 10, '2410095/96/97,2410174/175', '2027-09-30', 'ativo', NULL, NULL),
(233, 1663, 10, 10, '2410095/96/97,2410174/175', '2027-09-30', 'ativo', NULL, NULL),
(234, 1209, 79, 80, 'HCG24080032,24080033', '2026-08-30', 'ativo', NULL, NULL),
(235, 592, 50, 50, '25014', '2030-03-30', 'ativo', NULL, NULL),
(236, 83462, 100, 100, 'S366524038TO,S33524040', '2027-03-30', 'ativo', NULL, NULL),
(237, 83463, 200, 200, 'K053240762', '2027-10-30', 'ativo', NULL, NULL),
(238, 83464, 50, 50, '118328', '2029-07-30', 'ativo', NULL, NULL),
(239, 83465, 50, 50, 'T5149', '2028-02-03', 'ativo', NULL, NULL),
(240, 83466, 100, 100, '24150302', '2027-09-30', 'ativo', NULL, NULL),
(241, 83467, 98, 100, 'U24T1302A', '2024-09-30', 'ativo', NULL, NULL),
(242, 83468, 100, 100, '24159001', '2026-09-30', 'ativo', NULL, NULL),
(243, 83469, 10, 10, 'MAC2402A', '2027-04-30', 'ativo', NULL, NULL),
(244, 83470, 30, 30, '123239/240/241/242', '2030-04-30', 'ativo', NULL, NULL),
(245, 83471, 200, 200, 'K720240052', '2027-03-30', 'ativo', NULL, NULL),
(246, 83472, 100, 100, 'mo-5073', '2026-10-30', 'ativo', NULL, NULL),
(247, 83473, 15, 15, '82SI106806', '2026-08-30', 'ativo', NULL, NULL),
(248, 83473, 15, 15, '82SI106806', '2026-08-30', 'ativo', NULL, NULL),
(249, 83473, 15, 15, '82SI106806', '2026-08-30', 'ativo', NULL, NULL),
(250, 352, 10, 10, '82SH106602 ', '2026-07-30', 'ativo', NULL, NULL),
(251, 83450, 10, 10, '82TG204601', '2027-06-30', 'ativo', NULL, NULL),
(252, 83476, 15, 15, '82SI104801,02,03,04,05', '0026-08-20', 'ativo', NULL, NULL),
(253, 83477, 10, 10, 'EK0204A', '2027-11-30', 'ativo', NULL, NULL),
(254, 83478, 10, 10, '4814Z001', '2027-02-28', 'ativo', NULL, NULL),
(255, 83479, 10, 10, 'CN2402', '0000-00-00', 'ativo', NULL, NULL),
(256, 83480, 5, 5, '202403', '2026-05-04', 'ativo', NULL, NULL),
(257, 83481, 10, 10, 'L2191/L2192', '2026-09-30', 'ativo', NULL, NULL),
(258, 83482, 24, 24, '25014052', '2029-12-31', 'ativo', NULL, NULL),
(259, 83483, 1, 1, 'MZ221017', '2035-03-03', 'ativo', NULL, NULL),
(260, 83484, 100, 100, 'FB4008', '2027-04-30', 'ativo', NULL, NULL),
(261, 83485, 10, 10, '26440113', '2027-02-28', 'ativo', NULL, NULL),
(262, 83486, 100, 100, 'ADI24008A', '2027-09-30', 'ativo', NULL, NULL),
(263, 83487, 10, 10, 'ABJ24002A', '2027-04-30', 'ativo', NULL, NULL),
(264, 83488, 10, 10, '2402180-1', '2026-10-31', 'ativo', NULL, NULL),
(265, 83489, 10, 10, 'AFL24003A', '2027-01-31', 'ativo', NULL, NULL),
(266, 83490, 5, 5, 'ADX23004A', '2026-08-31', 'ativo', NULL, NULL),
(267, 83491, 100, 100, 'ACJ25001A', '2027-12-31', 'ativo', NULL, NULL),
(268, 83492, 100, 100, 'ADC24002A', '2027-01-31', 'ativo', NULL, NULL),
(269, 83493, 8, 10, 'ABA23007', '2026-10-31', 'ativo', NULL, NULL),
(270, 83495, 10, 10, 'AAM24003A', '2027-02-28', 'ativo', NULL, NULL),
(271, 83496, 10, 10, 'AHL24001A', '2027-04-30', 'ativo', NULL, NULL),
(272, 83497, 100, 100, 'AEJ25003A', '2028-03-31', 'ativo', NULL, NULL),
(273, 83498, 10, 10, 'A-109002', '2026-08-31', 'ativo', NULL, NULL),
(274, 83499, 10, 10, 'E0140254', '2027-10-31', 'ativo', NULL, NULL),
(275, 83500, 10, 10, '1010', '2028-01-31', 'ativo', NULL, NULL),
(276, 249, 10, 10, '632F241', '2028-02-28', 'ativo', NULL, NULL),
(277, 256, 1, 1, '303E24MU', '2027-04-30', 'ativo', NULL, NULL),
(278, 83502, 10, 10, '303E24MU', '2027-04-30', 'ativo', NULL, NULL),
(279, 83503, 10, 10, '4Z098', '2027-01-31', 'ativo', NULL, NULL),
(280, 83504, 10, 10, '240750', '2029-12-31', 'ativo', NULL, NULL),
(281, 83505, 10, 10, '230695', '2028-10-31', 'ativo', NULL, NULL),
(282, 83506, 10, 10, '250042', '2028-02-28', 'ativo', NULL, NULL),
(283, 83507, 10, 10, '2405053', '2028-05-31', 'ativo', NULL, NULL),
(284, 83508, 10, 10, '29465', '2027-02-28', 'ativo', NULL, NULL),
(285, 83509, 10, 10, '5758', '2028-03-31', 'ativo', NULL, NULL),
(286, 83510, 10, 10, '29174', '2026-11-30', 'ativo', NULL, NULL),
(287, 83511, 10, 10, 'C23124', '2028-01-31', 'ativo', NULL, NULL),
(288, 83512, 10, 10, '1962-19666', '2026-02-28', 'ativo', NULL, NULL),
(289, 83513, 10, 10, 'BEC-056', '2026-07-30', 'ativo', NULL, NULL),
(290, 83514, 10, 10, 'E0130129', '2026-11-30', 'ativo', NULL, NULL),
(291, 83515, 10, 10, 'NZ240902', '2027-08-30', 'ativo', NULL, NULL),
(292, 83516, 2, 2, 'ML24783', '2026-10-31', 'ativo', NULL, NULL),
(293, 83517, 10, 10, 'D20424001', '2027-07-31', 'ativo', NULL, NULL),
(294, 83518, 10, 10, 'D20324001', '2027-07-31', 'ativo', NULL, NULL),
(295, 83519, 12, 12, '24353002', '2026-03-31', 'ativo', NULL, NULL),
(296, 83521, 12, 12, '20402033', '2026-07-31', 'ativo', NULL, NULL),
(297, 83522, 10, 10, '24B007', '2027-04-30', 'ativo', NULL, NULL),
(298, 83523, 20, 20, '23G081', '2026-06-30', 'ativo', NULL, NULL),
(299, 83524, 10, 10, '11301013', '2025-12-31', 'ativo', NULL, NULL),
(300, 83525, 10, 10, '23MI017', '2026-08-31', 'ativo', NULL, NULL),
(301, 83526, 10, 10, '24443001', '2026-04-30', 'ativo', NULL, NULL),
(302, 83527, 10, 10, '083G24', '2029-07-31', 'ativo', NULL, NULL),
(303, 83528, 20, 20, 'SS528', '2026-02-28', 'ativo', NULL, NULL),
(304, 83529, 1, 1, 'GO1K12', '2027-03-31', 'ativo', NULL, NULL),
(305, 83530, 10, 10, 'D752402', '2027-08-31', 'ativo', NULL, NULL),
(306, 83531, 10, 10, '24K105', '2027-10-31', 'ativo', NULL, NULL),
(307, 83532, 2, 2, 'D04225001', '2027-12-31', 'ativo', NULL, NULL),
(308, 83533, 10, 10, 'CYH002', '2028-01-31', 'ativo', NULL, NULL),
(309, 83534, 10, 10, 'EK36', '2026-12-31', 'ativo', NULL, NULL),
(310, 526, 60, 60, '4197036', '2029-09-03', 'ativo', NULL, NULL),
(311, 83535, 40, 40, 'NR01873A', '2026-09-30', 'ativo', NULL, NULL),
(312, 524, 40, 40, 'NR02280A', '2029-03-30', 'ativo', NULL, NULL),
(313, 83536, 96, 96, '2402082', '2029-01-30', 'ativo', NULL, NULL),
(314, 83537, 96, 96, '2402081', '2029-01-30', 'ativo', NULL, NULL),
(315, 83538, 96, 96, '24028801', '2029-01-30', 'ativo', NULL, NULL),
(316, 83539, 96, 96, '2402083', '2029-01-30', 'ativo', NULL, NULL),
(317, 83540, 48, 48, 'CRI24277', '2024-08-19', 'ativo', NULL, NULL),
(318, 83541, 48, 48, 'CSM24281', '2029-01-30', 'ativo', NULL, NULL),
(319, 83542, 48, 48, 'CSM24287', '2029-01-30', 'ativo', NULL, NULL),
(320, 83543, 48, 48, 'CDO24284', '2029-01-30', 'ativo', NULL, NULL),
(321, 83544, 10, 10, 'NR02337A', '2027-10-30', 'ativo', NULL, NULL),
(322, 83545, 48, 48, 'CRI24376', '2029-09-30', 'ativo', NULL, NULL),
(323, 83546, 40, 40, '230327', '2026-02-28', 'ativo', NULL, NULL),
(324, 83547, 40, 40, '230328', '2026-02-28', 'ativo', NULL, NULL),
(325, 83548, 12, 12, 'L241066', '2027-05-30', 'ativo', NULL, NULL),
(326, 83549, 12, 12, 'L230373', '2026-03-30', 'ativo', NULL, NULL),
(327, 83551, 100, 100, 'AHL24001A', '2027-04-30', 'ativo', NULL, NULL),
(328, 83552, 100, 100, 'ACY25001A', '2028-02-28', 'ativo', NULL, NULL),
(329, 1088, 10, 10, 'DC24007', '2027-06-30', 'ativo', NULL, NULL),
(330, 785, 10, 10, '2419570L001', '2027-09-30', 'ativo', NULL, NULL),
(331, 83467, 100, 100, 'U24T1302A', '2027-10-30', 'ativo', NULL, NULL),
(332, 1517, 290, 290, 'AAB25003A', '2027-12-30', 'ativo', NULL, NULL),
(333, 250, 17, 19, '632F241', '2028-02-28', 'ativo', NULL, NULL),
(334, 250, 1, 1, '632F241', '2028-01-31', 'ativo', NULL, NULL),
(335, 83553, 100, 100, 'AA076', '2026-09-30', 'ativo', NULL, NULL),
(336, 1672, 88, 90, 'ABQ24004A', '2027-07-31', 'ativo', NULL, NULL),
(337, 1362, 90, 90, 'ACC23015A', '2026-09-30', 'ativo', NULL, NULL),
(338, 622, 90, 90, 'ADD24007A', '2027-08-31', 'ativo', NULL, NULL),
(339, 722, 90, 90, 'ACQ2021', '2027-10-31', 'ativo', NULL, NULL),
(340, 1361, 90, 90, 'ADT25001', '2027-10-31', 'ativo', NULL, NULL),
(341, 1486, 100, 100, 'ADS24007', '2027-11-30', 'ativo', NULL, NULL),
(342, 1484, 90, 90, 'AEV24007', '2027-10-30', 'ativo', NULL, NULL),
(343, 797, 90, 90, 'AEA25001', '2027-12-31', 'ativo', NULL, NULL),
(344, 800, 90, 90, 'AFQ25002', '2028-01-31', 'ativo', NULL, NULL),
(345, 83494, 100, 100, 'AFQ25001A', '2028-03-31', 'ativo', NULL, NULL),
(346, 83495, 90, 90, 'AAM24003', '2027-02-28', 'ativo', NULL, NULL),
(347, 896, 90, 90, 'ACG24004', '2027-11-30', 'ativo', NULL, NULL),
(348, 83551, 90, 90, 'AHL24001', '2027-04-30', 'ativo', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tabelas_precos`
--

CREATE TABLE `tabelas_precos` (
  `id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `validade_inicio` date DEFAULT NULL,
  `validade_fim` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_criacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabelas_precos`
--

INSERT INTO `tabelas_precos` (`id`, `empresa_id`, `nome`, `descricao`, `validade_inicio`, `validade_fim`, `ativo`, `data_criacao`, `usuario_criacao`) VALUES
(1, 1, 'Tabela Padrão', NULL, NULL, NULL, 1, '2025-11-22 21:25:53', 6),
(2, 2, 'Tabela Padrão', NULL, NULL, NULL, 1, '2025-11-25 21:08:48', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tabela_precos_servicos`
--

CREATE TABLE `tabela_precos_servicos` (
  `id` int(11) NOT NULL,
  `tabela_precos_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT 0.00,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tabela_precos_servicos`
--

INSERT INTO `tabela_precos_servicos` (`id`, `tabela_precos_id`, `servico_id`, `preco`, `desconto_percentual`, `ativo`, `data_criacao`) VALUES
(1, 1, 2, 2000.00, 5.00, 1, '2025-11-22 21:26:14'),
(2, 1, 1, 1200.00, 5.00, 1, '2025-11-22 21:26:14'),
(3, 1, 3, 1500.00, 5.00, 1, '2025-11-22 21:26:14'),
(4, 1, 4, 800.00, 5.00, 1, '2025-11-22 21:26:14'),
(5, 1, 5, 500.00, 5.00, 1, '2025-11-22 21:26:14'),
(6, 2, 2, 2000.00, 8.00, 1, '2025-11-25 21:08:55'),
(7, 2, 1, 1200.00, 8.00, 1, '2025-11-25 21:08:55'),
(8, 2, 3, 1500.00, 8.00, 1, '2025-11-25 21:08:55'),
(9, 2, 4, 800.00, 8.00, 1, '2025-11-25 21:08:55'),
(10, 2, 5, 500.00, 8.00, 1, '2025-11-25 21:08:55');

-- --------------------------------------------------------

--
-- Table structure for table `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `doc` varchar(255) DEFAULT NULL,
  `n_doc` varchar(255) DEFAULT NULL,
  `cliente` int(11) DEFAULT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `serie` int(11) DEFAULT NULL,
  `ref_factura` int(11) DEFAULT NULL,
  `debito` decimal(10,2) DEFAULT NULL,
  `credito` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `transacoes`
--

INSERT INTO `transacoes` (`id`, `doc`, `n_doc`, `cliente`, `iva`, `serie`, `ref_factura`, `debito`, `credito`, `saldo`, `data`) VALUES
(1, 'Factura', '1', 0, 0.00, 2025, NULL, 97.00, NULL, 97.00, '2025-11-06 12:14:53'),
(2, 'Factura', '1', 1, 0.00, 2025, NULL, 97.00, NULL, 97.00, '2025-11-06 12:14:53'),
(3, 'Factura', '2', 0, 0.00, 2025, NULL, 100.00, NULL, 197.00, '2025-11-06 12:18:39'),
(4, 'Factura', '2', 1, 0.00, 2025, NULL, 100.00, NULL, 197.00, '2025-11-06 12:18:39'),
(5, 'Factura', '3', 0, 0.00, 2025, NULL, 100.00, NULL, 297.00, '2025-11-06 12:19:41'),
(6, 'Factura', '3', 1, 0.00, 2025, NULL, 100.00, NULL, 297.00, '2025-11-06 12:19:41'),
(7, 'Factura', '4', 0, 0.00, 2025, NULL, 60.00, NULL, 357.00, '2025-11-06 12:22:16'),
(8, 'Factura', '4', 1, 0.00, 2025, NULL, 60.00, NULL, 357.00, '2025-11-06 12:22:16'),
(9, 'Factura', '5', 0, 0.00, 2025, NULL, 10.00, NULL, 367.00, '2025-11-06 12:27:19'),
(10, 'Factura', '5', 1, 0.00, 2025, NULL, 10.00, NULL, 367.00, '2025-11-06 12:27:19'),
(11, 'Factura', '6', 0, 0.00, 2025, NULL, 20.00, NULL, 387.00, '2025-11-06 12:28:51'),
(12, 'Factura', '6', 1, 0.00, 2025, NULL, 20.00, NULL, 387.00, '2025-11-06 12:28:51'),
(13, 'Factura', '7', 0, 0.00, 2025, NULL, 10.00, NULL, 397.00, '2025-11-06 12:31:01'),
(14, 'Factura', '7', 1, 0.00, 2025, NULL, 10.00, NULL, 397.00, '2025-11-06 12:31:01'),
(15, 'Factura', '8', 0, 0.00, 2025, NULL, 30.00, NULL, 427.00, '2025-11-06 12:32:49'),
(16, 'Factura', '8', 1, 0.00, 2025, NULL, 30.00, NULL, 427.00, '2025-11-06 12:32:49'),
(17, 'Factura', '9', 0, 0.00, 2025, NULL, 14.00, NULL, 441.00, '2025-11-06 12:34:59'),
(18, 'Factura', '9', 1, 0.00, 2025, NULL, 14.00, NULL, 441.00, '2025-11-06 12:34:59'),
(19, 'Factura', '10', 0, 0.00, 2025, NULL, 30.00, NULL, 471.00, '2025-11-06 12:49:46'),
(20, 'Factura', '10', 1, 0.00, 2025, NULL, 30.00, NULL, 471.00, '2025-11-06 12:49:46'),
(21, 'Factura', '11', 0, 0.00, 2025, NULL, 166.00, NULL, 637.00, '2025-11-06 13:16:29'),
(22, 'Factura', '11', 1, 0.00, 2025, NULL, 166.00, NULL, 637.00, '2025-11-06 13:16:29'),
(23, 'Factura', '12', 0, 0.00, 2025, NULL, 134.00, NULL, 771.00, '2025-11-06 13:18:08'),
(24, 'Factura', '12', 1, 0.00, 2025, NULL, 134.00, NULL, 771.00, '2025-11-06 13:18:08'),
(25, 'Factura', '13', 0, 10.08, 2025, NULL, 73.08, NULL, 844.08, '2025-11-06 15:12:01'),
(26, 'Factura', '13', 1, 10.08, 2025, NULL, 73.08, NULL, 844.08, '2025-11-06 15:12:01'),
(27, 'Venda a Dinheiro', '1', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:28:06'),
(28, 'Venda a Dinheiro', '1', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:28:06'),
(29, 'Venda a Dinheiro', '2', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:38:47'),
(30, 'Venda a Dinheiro', '2', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:38:47'),
(31, 'Venda a Dinheiro', '3', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:47:44'),
(32, 'Venda a Dinheiro', '3', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 15:47:44'),
(33, 'Venda a Dinheiro', '4', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 18:27:33'),
(34, 'Venda a Dinheiro', '4', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-06 18:27:33'),
(35, 'Venda a Dinheiro', '5', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 06:34:59'),
(36, 'Venda a Dinheiro', '5', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 06:34:59'),
(37, 'Venda a Dinheiro', '6', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 10:41:27'),
(38, 'Venda a Dinheiro', '6', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 10:41:27'),
(39, 'Venda a Dinheiro', '7', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 11:13:25'),
(40, 'Venda a Dinheiro', '7', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 11:13:25'),
(41, 'Venda a Dinheiro', '8', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 12:15:06'),
(42, 'Venda a Dinheiro', '8', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 12:15:06'),
(43, 'Venda a Dinheiro', '9', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 13:08:58'),
(44, 'Venda a Dinheiro', '9', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 13:08:58'),
(45, 'Venda a Dinheiro', '10', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 14:37:14'),
(46, 'Venda a Dinheiro', '10', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-07 14:37:14'),
(47, 'Venda a Dinheiro', '11', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:00:24'),
(48, 'Venda a Dinheiro', '11', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:00:24'),
(49, 'Venda a Dinheiro', '12', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:02:19'),
(50, 'Venda a Dinheiro', '12', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:02:19'),
(51, 'Venda a Dinheiro', '13', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:06:42'),
(52, 'Venda a Dinheiro', '13', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 16:06:42'),
(53, 'Venda a Dinheiro', '14', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 17:07:14'),
(54, 'Venda a Dinheiro', '14', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 17:07:14'),
(55, 'Devolucao', '1', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 17:12:53'),
(56, 'Devolucao', '1', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-08 17:12:53'),
(57, 'Venda a Dinheiro', '15', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:22:43'),
(58, 'Venda a Dinheiro', '15', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:22:43'),
(59, 'Venda a Dinheiro', '16', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:46:23'),
(60, 'Venda a Dinheiro', '16', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:46:23'),
(61, 'Venda a Dinheiro', '17', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:58:19'),
(62, 'Venda a Dinheiro', '17', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 08:58:19'),
(63, 'Venda a Dinheiro', '18', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 13:04:17'),
(64, 'Venda a Dinheiro', '18', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 13:04:17'),
(65, 'Venda a Dinheiro', '19', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 16:03:24'),
(66, 'Venda a Dinheiro', '19', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 16:03:24'),
(67, 'Venda a Dinheiro', '20', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 16:08:26'),
(68, 'Venda a Dinheiro', '20', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 16:08:26'),
(69, 'Venda a Dinheiro', '21', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 18:05:09'),
(70, 'Venda a Dinheiro', '21', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-10 18:05:09'),
(71, 'Factura', '14', 0, 0.00, 2025, NULL, 134.00, NULL, 978.08, '2025-11-24 08:30:32'),
(72, 'Factura', '14', 1, 0.00, 2025, NULL, 134.00, NULL, 978.08, '2025-11-24 08:30:32'),
(73, 'Venda a Dinheiro', '22', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-24 08:58:55'),
(74, 'Venda a Dinheiro', '22', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-24 08:58:55'),
(75, 'Recibo', '1', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-24 20:22:38'),
(76, 'Recibo', '1', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-24 20:22:38'),
(77, 'Venda a Dinheiro', '23', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-25 11:09:51'),
(78, 'Venda a Dinheiro', '23', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-25 11:09:51'),
(79, 'Venda a Dinheiro', '24', 0, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-25 15:03:43'),
(80, 'Venda a Dinheiro', '24', 1, NULL, 2025, NULL, NULL, NULL, NULL, '2025-11-25 15:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `treinamentos`
--

CREATE TABLE `treinamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `validade_em_dias` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `codigo_verificacao` varchar(255) DEFAULT NULL,
  `codigo_autenticacao` varchar(255) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nome`, `user`, `pass`, `categoria`, `codigo_verificacao`, `codigo_autenticacao`, `data`) VALUES
(1, 'milagre nicodemos', 'admin', '878848624', 'admin', '', '', '2024-01-10 18:40:05'),
(6, 'Recepcao', 'recepcao', '1234', 'recepcao', '', '', '2025-11-04 10:15:03'),
(7, 'Armazem', 'armazem', '1234', 'recepcao', '', '', '2025-11-04 10:15:07'),
(8, 'Contabilidade', 'contabilidade', '1234', 'contabilidade', '', '', '2025-11-04 10:15:10'),
(22, 'Farmacia Bandula', 'bandula', '1234', 'admin', NULL, NULL, '2025-11-05 07:38:18'),
(23, 'Janete Jasse', 'janete.jasse', 'jasse2025', 'contabilidade', NULL, NULL, '2025-11-06 15:34:09'),
(24, 'Carlota Muaeca', 'carlota.muaeca', '1997', 'contabilidade', NULL, NULL, '2025-11-07 06:29:05'),
(25, 'Emerson Covane', 'emerson', '1234', 'recepcao', NULL, NULL, '2025-11-21 12:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `vagas`
--

CREATE TABLE `vagas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('Aberta','Fechada') DEFAULT 'Aberta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vds_servicos_fact`
--

CREATE TABLE `vds_servicos_fact` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `vds_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vds_servicos_fact`
--

INSERT INTO `vds_servicos_fact` (`id`, `servico`, `qtd`, `preco`, `total`, `user`, `vds_id`) VALUES
(1, 2, 1, 1900.00, 1900.00, 25, 1),
(2, 1, 1, 1200.00, 1200.00, 25, 2),
(3, 1, 1, 1140.00, 1140.00, 25, 3),
(4, 3, 20, 1425.00, 28500.00, 25, 4),
(5, 1, 1, 1200.00, 1200.00, 6, 5),
(6, 2, 1, 2000.00, 2000.00, 6, 6),
(7, 3, 1, 1500.00, 1500.00, 6, 6),
(8, 4, 1, 800.00, 800.00, 6, 6),
(9, 2, 1, 2000.00, 2000.00, 25, 7),
(10, 1, 1, 1200.00, 1200.00, 25, 8);

-- --------------------------------------------------------

--
-- Table structure for table `vds_servicos_temp`
--

CREATE TABLE `vds_servicos_temp` (
  `id` int(11) NOT NULL,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venda_dinheiro_servico`
--

CREATE TABLE `venda_dinheiro_servico` (
  `id` int(11) NOT NULL,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venda_dinheiro_servico`
--

INSERT INTO `venda_dinheiro_servico` (`id`, `n_doc`, `paciente`, `empresa_id`, `valor`, `valor_pago`, `metodo`, `serie`, `usuario`, `dataa`, `data_criacao`) VALUES
(1, 1, 2, 1, 1900.00, 2000.00, 'dinheiro', 2025, 25, '2025-11-25', '2025-11-25 13:59:02'),
(2, 2, 1, 0, 1200.00, 1500.00, 'dinheiro', 2025, 25, '2025-11-25', '2025-11-25 14:43:58'),
(3, 3, 2, 1, 1140.00, 1500.00, 'dinheiro', 2025, 25, '2025-11-25', '2025-11-25 14:44:30'),
(4, 4, 2, 1, 28500.00, 29000.00, 'dinheiro', 2025, 25, '2025-11-25', '2025-11-25 14:45:14'),
(5, 5, 1, 0, 1200.00, 1500.00, 'dinheiro', 2025, 6, '2025-11-25', '2025-11-25 19:05:40'),
(6, 6, 1, 0, 4300.00, 5000.00, 'm_pesa', 2025, 6, '2025-11-25', '2025-11-25 19:13:32'),
(7, 7, 1, 0, 2000.00, 2000.00, 'dinheiro', 2025, 25, '2025-11-26', '2025-11-26 08:08:32'),
(8, 8, 1, 0, 1200.00, 2000.00, 'dinheiro', 2025, 25, '2025-11-26', '2025-11-26 09:55:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aprovacoes`
--
ALTER TABLE `aprovacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ferias_licenca` (`id_ferias_licenca`),
  ADD KEY `id_aprovador` (`id_aprovador`);

--
-- Indexes for table `armazem`
--
ALTER TABLE `armazem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `armazem_movimentos`
--
ALTER TABLE `armazem_movimentos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `armazem_stock`
--
ALTER TABLE `armazem_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artigos_devolvidos`
--
ALTER TABLE `artigos_devolvidos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artigos_devolvidos_temp`
--
ALTER TABLE `artigos_devolvidos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Indexes for table `balanco_saldo`
--
ALTER TABLE `balanco_saldo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beneficios`
--
ALTER TABLE `beneficios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `caixa_recepcao`
--
ALTER TABLE `caixa_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data` (`data`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_vaga` (`id_vaga`);

--
-- Indexes for table `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categoria_produtos`
--
ALTER TABLE `categoria_produtos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `colaborador_beneficios`
--
ALTER TABLE `colaborador_beneficios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_beneficio` (`id_beneficio`);

--
-- Indexes for table `colaborador_cargos`
--
ALTER TABLE `colaborador_cargos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_cargo` (`id_cargo`);

--
-- Indexes for table `colaborador_treinamentos`
--
ALTER TABLE `colaborador_treinamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`),
  ADD KEY `id_treinamento` (`id_treinamento`);

--
-- Indexes for table `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `condicao_pagamento`
--
ALTER TABLE `condicao_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `confirmacao_caixa`
--
ALTER TABLE `confirmacao_caixa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cotacao`
--
ALTER TABLE `cotacao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cotacao_recepcao`
--
ALTER TABLE `cotacao_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- Indexes for table `ct_artigos_cotados`
--
ALTER TABLE `ct_artigos_cotados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ct_artigos_temp`
--
ALTER TABLE `ct_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ct_servicos_fact`
--
ALTER TABLE `ct_servicos_fact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `cotacao_id` (`cotacao_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `ct_servicos_temp`
--
ALTER TABLE `ct_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `user` (`user`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indexes for table `despesas`
--
ALTER TABLE `despesas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devolucao`
--
ALTER TABLE `devolucao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devolucao_recepcao`
--
ALTER TABLE `devolucao_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- Indexes for table `disconto`
--
ALTER TABLE `disconto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dv_servicos_fact`
--
ALTER TABLE `dv_servicos_fact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `devolucao_id` (`devolucao_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `dv_servicos_temp`
--
ALTER TABLE `dv_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `user` (`user`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`);

--
-- Indexes for table `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `empresas_seguros`
--
ALTER TABLE `empresas_seguros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nome` (`nome`),
  ADD KEY `idx_nuit` (`nuit`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Indexes for table `entrada_caixa`
--
ALTER TABLE `entrada_caixa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entrada_stock`
--
ALTER TABLE `entrada_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entrega`
--
ALTER TABLE `entrega`
  ADD PRIMARY KEY (`identrega`);

--
-- Indexes for table `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Indexes for table `es_artigos`
--
ALTER TABLE `es_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `es_artigos_temp`
--
ALTER TABLE `es_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factura_recepcao`
--
ALTER TABLE `factura_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_paciente` (`paciente`),
  ADD KEY `idx_empresa` (`empresa_id`),
  ADD KEY `idx_serie` (`serie`),
  ADD KEY `idx_n_doc` (`n_doc`);

--
-- Indexes for table `familia_artigos`
--
ALTER TABLE `familia_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faturas_atendimento`
--
ALTER TABLE `faturas_atendimento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_fatura` (`numero_fatura`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data` (`data_atendimento`),
  ADD KEY `idx_empresa` (`empresa_id`),
  ADD KEY `idx_tipo` (`tipo_documento`);

--
-- Indexes for table `fatura_servicos`
--
ALTER TABLE `fatura_servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fatura` (`fatura_id`),
  ADD KEY `idx_servico` (`servico_id`);

--
-- Indexes for table `fa_artigos_fact`
--
ALTER TABLE `fa_artigos_fact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_artigos_temp`
--
ALTER TABLE `fa_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fa_servicos_fact_recepcao`
--
ALTER TABLE `fa_servicos_fact_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_servico` (`servico`),
  ADD KEY `idx_factura` (`factura`),
  ADD KEY `idx_user` (`user`);

--
-- Indexes for table `fa_servicos_temp`
--
ALTER TABLE `fa_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user`),
  ADD KEY `idx_servico` (`servico`),
  ADD KEY `idx_empresa` (`empresa_id`);

--
-- Indexes for table `ferias_licencas`
--
ALTER TABLE `ferias_licencas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Indexes for table `filadeespera`
--
ALTER TABLE `filadeespera`
  ADD PRIMARY KEY (`idfiladeespera`),
  ADD UNIQUE KEY `produtofiladeespera` (`produtofiladeespera`,`usuariofiladeespera`,`lote`);

--
-- Indexes for table `fornecedor`
--
ALTER TABLE `fornecedor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`idfuncionario`);

--
-- Indexes for table `grupo_artigos`
--
ALTER TABLE `grupo_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `historico_atendimentos`
--
ALTER TABLE `historico_atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_fatura` (`fatura_id`),
  ADD KEY `idx_data` (`data_atendimento`);

--
-- Indexes for table `historico_equipamentos`
--
ALTER TABLE `historico_equipamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_equipamento` (`id_equipamento`),
  ADD KEY `id_colaborador` (`id_colaborador`);

--
-- Indexes for table `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `itens_comprados`
--
ALTER TABLE `itens_comprados`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `iva`
--
ALTER TABLE `iva`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `licenca`
--
ALTER TABLE `licenca`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `metodo_pagamento`
--
ALTER TABLE `metodo_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nc_artigos`
--
ALTER TABLE `nc_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nc_artigos_temp`
--
ALTER TABLE `nc_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nc_servicos_fact`
--
ALTER TABLE `nc_servicos_fact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `nota_credito_id` (`nota_credito_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `nc_servicos_temp`
--
ALTER TABLE `nc_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `user` (`user`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indexes for table `nd_artigos`
--
ALTER TABLE `nd_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nd_artigos_temp`
--
ALTER TABLE `nd_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nd_servicos_fact`
--
ALTER TABLE `nd_servicos_fact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `nota_debito_id` (`nota_debito_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `nd_servicos_temp`
--
ALTER TABLE `nd_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `user` (`user`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indexes for table `nota_credito_recepcao`
--
ALTER TABLE `nota_credito_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- Indexes for table `nota_debito`
--
ALTER TABLE `nota_debito`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nota_debito_recepcao`
--
ALTER TABLE `nota_debito_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- Indexes for table `nota_de_credito`
--
ALTER TABLE `nota_de_credito`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ordem_compra`
--
ALTER TABLE `ordem_compra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ordem_compra_artigos`
--
ALTER TABLE `ordem_compra_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ordem_compra_artigos_temp`
--
ALTER TABLE `ordem_compra_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_processo` (`numero_processo`),
  ADD KEY `idx_nome` (`nome`,`apelido`),
  ADD KEY `idx_documento` (`documento_numero`),
  ADD KEY `idx_contacto` (`contacto`),
  ADD KEY `idx_empresa` (`empresa_id`);

--
-- Indexes for table `paciente_empresa_historico`
--
ALTER TABLE `paciente_empresa_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_empresa` (`empresa_id`),
  ADD KEY `idx_data` (`data_inicio`);

--
-- Indexes for table `pagamentos_recepcao`
--
ALTER TABLE `pagamentos_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fatura` (`fatura_id`),
  ADD KEY `idx_factura_recepcao` (`factura_recepcao_id`),
  ADD KEY `idx_data` (`data_pagamento`),
  ADD KEY `idx_metodo` (`metodo_pagamento`);

--
-- Indexes for table `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`idpedido`);

--
-- Indexes for table `periodicidade`
--
ALTER TABLE `periodicidade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`idperiodo`);

--
-- Indexes for table `produto`
--
ALTER TABLE `produto`
  ADD PRIMARY KEY (`idproduto`);

--
-- Indexes for table `rc_fact`
--
ALTER TABLE `rc_fact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rc_fact_temp`
--
ALTER TABLE `rc_fact_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rc_faturas_temp_recepcao`
--
ALTER TABLE `rc_faturas_temp_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `recibo`
--
ALTER TABLE `recibo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recibo_factura_recepcao`
--
ALTER TABLE `recibo_factura_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recibo_id` (`recibo_id`),
  ADD KEY `factura_recepcao_id` (`factura_recepcao_id`);

--
-- Indexes for table `recibo_recepcao`
--
ALTER TABLE `recibo_recepcao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- Indexes for table `requisicao_externa`
--
ALTER TABLE `requisicao_externa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requisicao_interna`
--
ALTER TABLE `requisicao_interna`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `re_artigos`
--
ALTER TABLE `re_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `re_artigos_temp`
--
ALTER TABLE `re_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ri_artigos`
--
ALTER TABLE `ri_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ri_artigos_temp`
--
ALTER TABLE `ri_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saida_caixa`
--
ALTER TABLE `saida_caixa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saida_stock`
--
ALTER TABLE `saida_stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `serie_factura`
--
ALTER TABLE `serie_factura`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servicos_clinica`
--
ALTER TABLE `servicos_clinica`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_categoria` (`categoria`);

--
-- Indexes for table `ss_artigos`
--
ALTER TABLE `ss_artigos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ss_artigos_temp`
--
ALTER TABLE `ss_artigos_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Indexes for table `tabelas_precos`
--
ALTER TABLE `tabelas_precos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_empresa` (`empresa_id`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Indexes for table `tabela_precos_servicos`
--
ALTER TABLE `tabela_precos_servicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_tabela_servico` (`tabela_precos_id`,`servico_id`),
  ADD KEY `idx_servico` (`servico_id`);

--
-- Indexes for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treinamentos`
--
ALTER TABLE `treinamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vagas`
--
ALTER TABLE `vagas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vds_servicos_fact`
--
ALTER TABLE `vds_servicos_fact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `vds_id` (`vds_id`),
  ADD KEY `user` (`user`);

--
-- Indexes for table `vds_servicos_temp`
--
ALTER TABLE `vds_servicos_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `servico` (`servico`),
  ADD KEY `user` (`user`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Indexes for table `venda_dinheiro_servico`
--
ALTER TABLE `venda_dinheiro_servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente` (`paciente`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `serie` (`serie`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aprovacoes`
--
ALTER TABLE `aprovacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `armazem`
--
ALTER TABLE `armazem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `armazem_movimentos`
--
ALTER TABLE `armazem_movimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `armazem_stock`
--
ALTER TABLE `armazem_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artigos_devolvidos`
--
ALTER TABLE `artigos_devolvidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `artigos_devolvidos_temp`
--
ALTER TABLE `artigos_devolvidos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `balanco_saldo`
--
ALTER TABLE `balanco_saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `beneficios`
--
ALTER TABLE `beneficios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caixa_recepcao`
--
ALTER TABLE `caixa_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `candidaturas`
--
ALTER TABLE `candidaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categoria_produtos`
--
ALTER TABLE `categoria_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `colaboradores`
--
ALTER TABLE `colaboradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `colaborador_beneficios`
--
ALTER TABLE `colaborador_beneficios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `colaborador_cargos`
--
ALTER TABLE `colaborador_cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `colaborador_treinamentos`
--
ALTER TABLE `colaborador_treinamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `condicao_pagamento`
--
ALTER TABLE `condicao_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `confirmacao_caixa`
--
ALTER TABLE `confirmacao_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cotacao`
--
ALTER TABLE `cotacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cotacao_recepcao`
--
ALTER TABLE `cotacao_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ct_artigos_cotados`
--
ALTER TABLE `ct_artigos_cotados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ct_artigos_temp`
--
ALTER TABLE `ct_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ct_servicos_fact`
--
ALTER TABLE `ct_servicos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ct_servicos_temp`
--
ALTER TABLE `ct_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `despesas`
--
ALTER TABLE `despesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devolucao`
--
ALTER TABLE `devolucao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `devolucao_recepcao`
--
ALTER TABLE `devolucao_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `disconto`
--
ALTER TABLE `disconto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dv_servicos_fact`
--
ALTER TABLE `dv_servicos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dv_servicos_temp`
--
ALTER TABLE `dv_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `empresas_seguros`
--
ALTER TABLE `empresas_seguros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `entrada_caixa`
--
ALTER TABLE `entrada_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entrada_stock`
--
ALTER TABLE `entrada_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;

--
-- AUTO_INCREMENT for table `entrega`
--
ALTER TABLE `entrega`
  MODIFY `identrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `equipamentos`
--
ALTER TABLE `equipamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `es_artigos`
--
ALTER TABLE `es_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=370;

--
-- AUTO_INCREMENT for table `es_artigos_temp`
--
ALTER TABLE `es_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=423;

--
-- AUTO_INCREMENT for table `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `factura_recepcao`
--
ALTER TABLE `factura_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `familia_artigos`
--
ALTER TABLE `familia_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `faturas_atendimento`
--
ALTER TABLE `faturas_atendimento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fatura_servicos`
--
ALTER TABLE `fatura_servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `fa_artigos_fact`
--
ALTER TABLE `fa_artigos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `fa_artigos_temp`
--
ALTER TABLE `fa_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `fa_servicos_fact_recepcao`
--
ALTER TABLE `fa_servicos_fact_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `fa_servicos_temp`
--
ALTER TABLE `fa_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ferias_licencas`
--
ALTER TABLE `ferias_licencas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filadeespera`
--
ALTER TABLE `filadeespera`
  MODIFY `idfiladeespera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `idfuncionario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grupo_artigos`
--
ALTER TABLE `grupo_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `historico_atendimentos`
--
ALTER TABLE `historico_atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `historico_equipamentos`
--
ALTER TABLE `historico_equipamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- AUTO_INCREMENT for table `itens_comprados`
--
ALTER TABLE `itens_comprados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iva`
--
ALTER TABLE `iva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `licenca`
--
ALTER TABLE `licenca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `metodo_pagamento`
--
ALTER TABLE `metodo_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nc_artigos`
--
ALTER TABLE `nc_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nc_artigos_temp`
--
ALTER TABLE `nc_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nc_servicos_fact`
--
ALTER TABLE `nc_servicos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `nc_servicos_temp`
--
ALTER TABLE `nc_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `nd_artigos`
--
ALTER TABLE `nd_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nd_artigos_temp`
--
ALTER TABLE `nd_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nd_servicos_fact`
--
ALTER TABLE `nd_servicos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nd_servicos_temp`
--
ALTER TABLE `nd_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nota_credito_recepcao`
--
ALTER TABLE `nota_credito_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nota_debito`
--
ALTER TABLE `nota_debito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nota_debito_recepcao`
--
ALTER TABLE `nota_debito_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nota_de_credito`
--
ALTER TABLE `nota_de_credito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordem_compra`
--
ALTER TABLE `ordem_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordem_compra_artigos`
--
ALTER TABLE `ordem_compra_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordem_compra_artigos_temp`
--
ALTER TABLE `ordem_compra_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paciente_empresa_historico`
--
ALTER TABLE `paciente_empresa_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pagamentos_recepcao`
--
ALTER TABLE `pagamentos_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idpedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `periodicidade`
--
ALTER TABLE `periodicidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `periodo`
--
ALTER TABLE `periodo`
  MODIFY `idperiodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `produto`
--
ALTER TABLE `produto`
  MODIFY `idproduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83554;

--
-- AUTO_INCREMENT for table `rc_fact`
--
ALTER TABLE `rc_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rc_fact_temp`
--
ALTER TABLE `rc_fact_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rc_faturas_temp_recepcao`
--
ALTER TABLE `rc_faturas_temp_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recibo`
--
ALTER TABLE `recibo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recibo_factura_recepcao`
--
ALTER TABLE `recibo_factura_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recibo_recepcao`
--
ALTER TABLE `recibo_recepcao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisicao_externa`
--
ALTER TABLE `requisicao_externa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requisicao_interna`
--
ALTER TABLE `requisicao_interna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `re_artigos`
--
ALTER TABLE `re_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `re_artigos_temp`
--
ALTER TABLE `re_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ri_artigos`
--
ALTER TABLE `ri_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ri_artigos_temp`
--
ALTER TABLE `ri_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `saida_caixa`
--
ALTER TABLE `saida_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saida_stock`
--
ALTER TABLE `saida_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `serie_factura`
--
ALTER TABLE `serie_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `servicos_clinica`
--
ALTER TABLE `servicos_clinica`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ss_artigos`
--
ALTER TABLE `ss_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ss_artigos_temp`
--
ALTER TABLE `ss_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=349;

--
-- AUTO_INCREMENT for table `tabelas_precos`
--
ALTER TABLE `tabelas_precos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabela_precos_servicos`
--
ALTER TABLE `tabela_precos_servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `treinamentos`
--
ALTER TABLE `treinamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vagas`
--
ALTER TABLE `vagas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vds_servicos_fact`
--
ALTER TABLE `vds_servicos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vds_servicos_temp`
--
ALTER TABLE `vds_servicos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `venda_dinheiro_servico`
--
ALTER TABLE `venda_dinheiro_servico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aprovacoes`
--
ALTER TABLE `aprovacoes`
  ADD CONSTRAINT `aprovacoes_ibfk_1` FOREIGN KEY (`id_ferias_licenca`) REFERENCES `ferias_licencas` (`id`),
  ADD CONSTRAINT `aprovacoes_ibfk_2` FOREIGN KEY (`id_aprovador`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD CONSTRAINT `candidaturas_ibfk_1` FOREIGN KEY (`id_vaga`) REFERENCES `vagas` (`id`);

--
-- Constraints for table `colaborador_beneficios`
--
ALTER TABLE `colaborador_beneficios`
  ADD CONSTRAINT `colaborador_beneficios_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`),
  ADD CONSTRAINT `colaborador_beneficios_ibfk_2` FOREIGN KEY (`id_beneficio`) REFERENCES `beneficios` (`id`);

--
-- Constraints for table `colaborador_cargos`
--
ALTER TABLE `colaborador_cargos`
  ADD CONSTRAINT `colaborador_cargos_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`),
  ADD CONSTRAINT `colaborador_cargos_ibfk_2` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id`);

--
-- Constraints for table `colaborador_treinamentos`
--
ALTER TABLE `colaborador_treinamentos`
  ADD CONSTRAINT `colaborador_treinamentos_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`),
  ADD CONSTRAINT `colaborador_treinamentos_ibfk_2` FOREIGN KEY (`id_treinamento`) REFERENCES `treinamentos` (`id`);

--
-- Constraints for table `equipamentos`
--
ALTER TABLE `equipamentos`
  ADD CONSTRAINT `equipamentos_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `factura_recepcao`
--
ALTER TABLE `factura_recepcao`
  ADD CONSTRAINT `fk_factura_recepcao_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_factura_recepcao_paciente` FOREIGN KEY (`paciente`) REFERENCES `pacientes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `faturas_atendimento`
--
ALTER TABLE `faturas_atendimento`
  ADD CONSTRAINT `fk_faturas_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faturas_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fatura_servicos`
--
ALTER TABLE `fatura_servicos`
  ADD CONSTRAINT `fk_fatura_servicos_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fatura_servicos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos_clinica` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `fa_servicos_fact_recepcao`
--
ALTER TABLE `fa_servicos_fact_recepcao`
  ADD CONSTRAINT `fk_fa_servicos_fact_factura` FOREIGN KEY (`factura`) REFERENCES `factura_recepcao` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fa_servicos_fact_servico` FOREIGN KEY (`servico`) REFERENCES `servicos_clinica` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `ferias_licencas`
--
ALTER TABLE `ferias_licencas`
  ADD CONSTRAINT `ferias_licencas_ibfk_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `historico_atendimentos`
--
ALTER TABLE `historico_atendimentos`
  ADD CONSTRAINT `fk_historico_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historico_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `historico_equipamentos`
--
ALTER TABLE `historico_equipamentos`
  ADD CONSTRAINT `historico_equipamentos_ibfk_1` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`),
  ADD CONSTRAINT `historico_equipamentos_ibfk_2` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id`);

--
-- Constraints for table `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `fk_pacientes_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `paciente_empresa_historico`
--
ALTER TABLE `paciente_empresa_historico`
  ADD CONSTRAINT `fk_hist_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hist_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pagamentos_recepcao`
--
ALTER TABLE `pagamentos_recepcao`
  ADD CONSTRAINT `fk_pagamentos_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pagamentos_factura_recepcao` FOREIGN KEY (`factura_recepcao_id`) REFERENCES `factura_recepcao` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tabelas_precos`
--
ALTER TABLE `tabelas_precos`
  ADD CONSTRAINT `fk_tabela_precos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tabela_precos_servicos`
--
ALTER TABLE `tabela_precos_servicos`
  ADD CONSTRAINT `fk_tabela_precos_servicos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos_clinica` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tabela_precos_servicos_tabela` FOREIGN KEY (`tabela_precos_id`) REFERENCES `tabelas_precos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
