-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 30-Ago-2023 às 14:43
-- Versão do servidor: 10.1.13-MariaDB
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pedaco`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `artigos_devolvidos`
--

CREATE TABLE `artigos_devolvidos` (
  `id` int(11) NOT NULL,
  `produto` varchar(500) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cliente` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `devolucao` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `artigos_devolvidos_temp`
--

CREATE TABLE `artigos_devolvidos_temp` (
  `id` int(11) NOT NULL,
  `artigo` varchar(255) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `balanco_saldo`
--

CREATE TABLE `balanco_saldo` (
  `id` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `data1` varchar(20) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria_produtos`
--

CREATE TABLE `categoria_produtos` (
  `id` int(11) NOT NULL,
  `categoria` varchar(200) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `categoria_produtos`
--

INSERT INTO `categoria_produtos` (`id`, `categoria`, `data`) VALUES
(1, 'Bebidas', '2023-02-24 18:24:00'),
(2, 'Refrigerantes', '2023-02-24 18:24:00'),
(3, 'Comidas', '2023-02-24 18:24:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `nuit` int(11) NOT NULL,
  `apelido` varchar(255) NOT NULL,
  `contacto` int(11) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `iva` int(11) NOT NULL,
  `qtd_factura` int(11) NOT NULL DEFAULT '0',
  `qtd_notas_credito` int(11) NOT NULL DEFAULT '0',
  `qtd_nota_debito` int(11) NOT NULL,
  `qtd_cotacao` int(11) NOT NULL DEFAULT '0',
  `qtd_vds` int(11) NOT NULL DEFAULT '0',
  `qtd_recibo` int(11) NOT NULL DEFAULT '0',
  `qtd_devolucao` int(11) NOT NULL DEFAULT '0',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `cotacao`
--

CREATE TABLE `cotacao` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `disconto` decimal(10,2) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `cliente` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ct_artigos_cotados`
--

CREATE TABLE `ct_artigos_cotados` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `cotacao` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ct_artigos_temp`
--

CREATE TABLE `ct_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `devolucao`
--

CREATE TABLE `devolucao` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text NOT NULL,
  `idpedido` int(11) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idperiodo` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `disconto`
--

CREATE TABLE `disconto` (
  `id` int(11) NOT NULL,
  `percentagem` double NOT NULL,
  `motivo` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `documento`
--

CREATE TABLE `documento` (
  `id` int(11) NOT NULL,
  `documento` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
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
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`id`, `nome`, `nuit`, `endereco`, `provincia`, `pais`, `contacto`, `capital_social`, `email`, `banco`, `conta`, `nib`, `img`, `data`) VALUES
(1, 'iProjects Sociedade Unipessoal Limitada', 401403159, 'Matema, Rua Martires do Colonialismo', 'Tete', 'Mocambique', '878884862/879207381', 'Metical', 'iprojectscompany@gmail.com', 'Moza Banco', '3186551010001', '003400003186551010144', 'iCone.png', '2023-05-10 16:26:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrada_caixa`
--

CREATE TABLE `entrada_caixa` (
  `id` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `caixa` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrada_stock`
--

CREATE TABLE `entrada_stock` (
  `id` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `serie` int(11) NOT NULL,
  `grupo` int(11) NOT NULL,
  `familia` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrega`
--

CREATE TABLE `entrega` (
  `identrega` int(11) NOT NULL,
  `produtoentrega` varchar(255) NOT NULL,
  `qtdentrega` double NOT NULL,
  `precoentrega` decimal(10,2) NOT NULL,
  `totalentrega` decimal(10,2) NOT NULL,
  `clienteentrega` int(11) NOT NULL,
  `usuarioentrega` int(11) NOT NULL,
  `pedidoentrega` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `datavenda` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `es_artigos`
--

CREATE TABLE `es_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `es` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `es_artigos_temp`
--

CREATE TABLE `es_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `factura`
--

CREATE TABLE `factura` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `motivo_iva` text NOT NULL,
  `disconto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `serie` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `statuss` int(11) NOT NULL DEFAULT '0',
  `apolice` varchar(255) DEFAULT NULL,
  `condicoes` varchar(255) NOT NULL,
  `codigo1` varchar(255) DEFAULT NULL,
  `codigo2` varchar(255) DEFAULT NULL,
  `codigo3` varchar(255) DEFAULT NULL,
  `nota_credito` int(11) NOT NULL DEFAULT '0',
  `recibo` int(11) NOT NULL DEFAULT '0',
  `nota_debito` int(11) NOT NULL DEFAULT '0',
  `cotacao` int(11) NOT NULL DEFAULT '0',
  `cliente` int(11) NOT NULL,
  `utente` varchar(500) DEFAULT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `familia_artigos`
--

CREATE TABLE `familia_artigos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `setor` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `familia_artigos`
--

INSERT INTO `familia_artigos` (`id`, `descricao`, `setor`, `data`) VALUES
(1, 'Recepcao', 1, '2023-08-16 05:27:42'),
(2, 'Farmacia', 1, '2023-08-16 05:27:42'),
(3, 'Armazem', 1, '2023-08-16 05:27:42'),
(4, 'Arquivos', 4, '2023-08-17 08:54:03');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fa_artigos_fact`
--

CREATE TABLE `fa_artigos_fact` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fa_artigos_temp`
--

CREATE TABLE `fa_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `filadeespera`
--

CREATE TABLE `filadeespera` (
  `idfiladeespera` int(11) NOT NULL,
  `produtofiladeespera` varchar(255) NOT NULL,
  `qtdfiladeespera` double NOT NULL,
  `precofiladeespera` double NOT NULL,
  `totalfiladeespera` double NOT NULL,
  `usuariofiladeespera` int(11) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `fornecedor`
--

CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL,
  `nome` varchar(500) NOT NULL,
  `nuit` int(11) NOT NULL,
  `contacto` varchar(50) NOT NULL,
  `endereco` text NOT NULL,
  `ordem_compra` int(11) NOT NULL DEFAULT '0',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `idfuncionario` int(11) NOT NULL,
  `nomefuncionario` varchar(255) NOT NULL,
  `apelidofuncionario` varchar(255) NOT NULL,
  `sexofuncionario` varchar(10) NOT NULL,
  `bi` varchar(17) NOT NULL,
  `enderecofuncionario` varchar(255) NOT NULL,
  `contactofuncionario` varchar(20) NOT NULL,
  `nuit` int(20) NOT NULL,
  `emailfuncionario` varchar(50) NOT NULL,
  `empresafuncionario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`idfuncionario`, `nomefuncionario`, `apelidofuncionario`, `sexofuncionario`, `bi`, `enderecofuncionario`, `contactofuncionario`, `nuit`, `emailfuncionario`, `empresafuncionario`, `data`) VALUES
(1, 'consumidor', 'final', 'qualquer', '0', 'cidade de tete', '878884862', 0, 'milagrelazaro1@gmail.com', 1, '2021-07-13 00:41:08'),
(2, 'Nelo ', 'sgfd', 'f', '45', 'fbg', '46', 1, 'gh', 1, '2023-03-20 15:55:55');

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupo_artigos`
--

CREATE TABLE `grupo_artigos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `familia` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `grupo_artigos`
--

INSERT INTO `grupo_artigos` (`id`, `descricao`, `familia`, `data`) VALUES
(1, 'Consultas', 1, '2023-08-16 05:21:28'),
(2, 'Procedimentos', 1, '2023-08-16 05:21:28'),
(3, 'Medicamentos', 2, '2023-08-16 05:21:28'),
(4, 'Material de Escritorio', 3, '2023-08-16 05:22:25'),
(5, 'Material do Laboratorio', 3, '2023-08-16 05:22:25'),
(8, 'Exames Auxiliares de Diagnostico', 1, '2023-08-16 09:44:27');

-- --------------------------------------------------------

--
-- Estrutura da tabela `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `artigo` varchar(500) NOT NULL,
  `1` int(11) NOT NULL,
  `2` int(11) NOT NULL,
  `3` int(11) NOT NULL,
  `4` int(11) NOT NULL,
  `5` int(11) NOT NULL,
  `6` int(11) NOT NULL,
  `7` int(11) NOT NULL,
  `8` int(11) NOT NULL,
  `9` int(11) NOT NULL,
  `10` int(11) NOT NULL,
  `11` int(11) NOT NULL,
  `12` int(11) NOT NULL,
  `13` int(11) NOT NULL,
  `14` int(11) NOT NULL,
  `15` int(11) NOT NULL,
  `16` int(11) NOT NULL,
  `17` int(11) NOT NULL,
  `18` int(11) NOT NULL,
  `19` int(11) NOT NULL,
  `20` int(11) NOT NULL,
  `21` int(11) NOT NULL,
  `22` int(11) NOT NULL,
  `23` int(11) NOT NULL,
  `24` int(11) NOT NULL,
  `25` int(11) NOT NULL,
  `26` int(11) NOT NULL,
  `27` int(11) NOT NULL,
  `28` int(11) NOT NULL,
  `29` int(11) NOT NULL,
  `30` int(11) NOT NULL,
  `31` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens_comprados`
--

CREATE TABLE `itens_comprados` (
  `id` int(11) NOT NULL,
  `id_artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `iva`
--

CREATE TABLE `iva` (
  `id` int(11) NOT NULL,
  `percentagem` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `iva`
--

INSERT INTO `iva` (`id`, `percentagem`, `motivo`, `data`) VALUES
(1, 16, 'Taxa de Iva Normal', '2023-04-30 16:56:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `nc_artigos`
--

CREATE TABLE `nc_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_nota` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nc_artigos_temp`
--

CREATE TABLE `nc_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nd_artigos`
--

CREATE TABLE `nd_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `id_nd` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nd_artigos_temp`
--

CREATE TABLE `nd_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nota_debito`
--

CREATE TABLE `nota_debito` (
  `id` int(11) NOT NULL,
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
  `motivo` varchar(20000) NOT NULL,
  `cliente` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `nota_de_credito`
--

CREATE TABLE `nota_de_credito` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` int(11) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `motivo` varchar(20000) NOT NULL,
  `cliente` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ordem_compra`
--

CREATE TABLE `ordem_compra` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fornecedor` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `serie` varchar(5) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ordem_compra_artigos`
--

CREATE TABLE `ordem_compra_artigos` (
  `id` int(11) NOT NULL,
  `artigo` varchar(500) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `ordem_compra` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ordem_compra_artigos_temp`
--

CREATE TABLE `ordem_compra_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` varchar(500) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido`
--

CREATE TABLE `pedido` (
  `idpedido` int(11) NOT NULL,
  `descpedido` datetime NOT NULL,
  `serie` int(11) NOT NULL,
  `pagamentopedido` decimal(10,2) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `trocopedido` decimal(10,2) NOT NULL,
  `disconto` decimal(10,2) NOT NULL,
  `clientepedido` int(11) NOT NULL,
  `userpedido` int(11) NOT NULL,
  `periodo` int(11) NOT NULL,
  `devolucao` int(11) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `periodicidade`
--

CREATE TABLE `periodicidade` (
  `id` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `periodo`
--

CREATE TABLE `periodo` (
  `idperiodo` int(11) NOT NULL,
  `diaperiodo` varchar(7) NOT NULL DEFAULT 'fechado',
  `serie` int(11) NOT NULL,
  `aberturaperiodo` decimal(10,0) DEFAULT NULL,
  `fechoperiodo` decimal(10,0) DEFAULT NULL,
  `numero_devolucoes` int(11) NOT NULL,
  `dataaberturaperiodo` datetime NOT NULL,
  `datafechoperiodo` datetime NOT NULL,
  `usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produto`
--

CREATE TABLE `produto` (
  `idproduto` int(11) NOT NULL,
  `nomeproduto` varchar(255) NOT NULL,
  `stock` double NOT NULL,
  `stock_min` double NOT NULL,
  `preco_compra` decimal(10,2) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `codbar` text NOT NULL,
  `grupo` int(11) NOT NULL,
  `familia` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `produto`
--

INSERT INTO `produto` (`idproduto`, `nomeproduto`, `stock`, `stock_min`, `preco_compra`, `preco`, `codbar`, `grupo`, `familia`, `data`) VALUES
(1, 'Consulta de Medicina Geral', 24, 0, '0.00', '1200.00', 'abc', 1, 1, '2023-08-28 13:23:59'),
(2, 'Canetas', 36, 0, '15.00', '0.00', '00', 4, 3, '2023-08-28 13:24:00'),
(3, 'Consulta de Cirurgia Geral', 17, 0, '0.00', '1500.00', 'abcd', 1, 1, '2023-08-30 12:35:18'),
(4, '2M TXOTI', 15.010000000000002, 0, '50.00', '60.00', '12345', 4, 4, '2023-08-30 12:35:19'),
(5, 'QUINTA BOLOTA 2', 22, 0, '400.00', '600.00', '321', 2, 3, '2023-08-28 13:24:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `rc_fact`
--

CREATE TABLE `rc_fact` (
  `id` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `id_rc` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `rc_fact_temp`
--

CREATE TABLE `rc_fact_temp` (
  `id` int(11) NOT NULL,
  `factura` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recibo`
--

CREATE TABLE `recibo` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `troco` decimal(10,2) NOT NULL,
  `modo` varchar(255) NOT NULL,
  `observacoes` text,
  `serie` varchar(5) NOT NULL,
  `cliente` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `requisicao_externa`
--

CREATE TABLE `requisicao_externa` (
  `id` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fornecedor` int(11) NOT NULL,
  `sector` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `requisicao_interna`
--

CREATE TABLE `requisicao_interna` (
  `id` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sector` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `serie` int(11) NOT NULL,
  `motivo` varchar(20000) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `re_artigos`
--

CREATE TABLE `re_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `re` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `re_artigos_temp`
--

CREATE TABLE `re_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ri_artigos`
--

CREATE TABLE `ri_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `ri` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ri_artigos_temp`
--

CREATE TABLE `ri_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saida_caixa`
--

CREATE TABLE `saida_caixa` (
  `id` int(11) NOT NULL,
  `serie` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `caixa` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `saida_stock`
--

CREATE TABLE `saida_stock` (
  `id` int(11) NOT NULL,
  `descricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `serie` int(11) NOT NULL,
  `solicitante` varchar(255) NOT NULL,
  `motivo` text NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `responsavel` varchar(255) NOT NULL,
  `qtdrequisicao` int(11) NOT NULL,
  `qtdrequisicaoexterna` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `sector`
--

INSERT INTO `sector` (`id`, `nome`, `responsavel`, `qtdrequisicao`, `qtdrequisicaoexterna`, `data`) VALUES
(1, 'Recepcao', 'Kapa', 0, 0, '2023-08-16 12:44:04'),
(2, 'Laboratorio', 'Kape', 0, 1, '2023-08-16 12:44:04'),
(3, 'Farmacia', 'Kapi', 0, 0, '2023-08-16 12:44:04'),
(4, 'Contabilidades', 'Kapo', 1, 0, '2023-08-16 12:44:04'),
(5, 'Armazem', 'Kapu', 0, 0, '2023-08-16 12:44:04');

-- --------------------------------------------------------

--
-- Estrutura da tabela `serie_factura`
--

CREATE TABLE `serie_factura` (
  `id` int(11) NOT NULL,
  `ano_fiscal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `serie_factura`
--

INSERT INTO `serie_factura` (`id`, `ano_fiscal`) VALUES
(1, 2023);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ss_artigos`
--

CREATE TABLE `ss_artigos` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `ss` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ss_artigos_temp`
--

CREATE TABLE `ss_artigos_temp` (
  `id` int(11) NOT NULL,
  `artigo` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `doc` varchar(255) NOT NULL,
  `n_doc` varchar(255) NOT NULL,
  `cliente` int(11) NOT NULL,
  `iva` decimal(10,2) DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `ref_factura` int(11) NOT NULL,
  `debito` decimal(10,2) DEFAULT NULL,
  `credito` decimal(10,2) DEFAULT NULL,
  `saldo` decimal(10,2) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `user`, `pass`, `categoria`, `data`) VALUES
(1, 'milagre nicodemos', 'admin', '12', 'admin', '2023-03-20 15:47:00'),
(6, 'Luisa', 'luisa', '12345', 'user', '2023-05-26 04:45:44'),
(7, 'Francisco', 'francisco', '12345', 'user', '2023-05-26 04:46:14'),
(8, 'Massalo', 'massalo', '12', 'admin', '2023-08-15 10:23:13'),
(9, 'Joao Lino Phiri', 'joao', '1234', 'user', '2023-08-11 14:33:57'),
(10, 'user2', 'user2', '12', 'admin', '2023-08-20 15:32:40');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `balanco_saldo`
--
ALTER TABLE `balanco_saldo`
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
-- Indexes for table `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cotacao`
--
ALTER TABLE `cotacao`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `devolucao`
--
ALTER TABLE `devolucao`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `familia_artigos`
--
ALTER TABLE `familia_artigos`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `filadeespera`
--
ALTER TABLE `filadeespera`
  ADD PRIMARY KEY (`idfiladeespera`);

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
-- Indexes for table `nota_debito`
--
ALTER TABLE `nota_debito`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`idproduto`),
  ADD UNIQUE KEY `nomeproduto` (`nomeproduto`);

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
-- Indexes for table `recibo`
--
ALTER TABLE `recibo`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artigos_devolvidos`
--
ALTER TABLE `artigos_devolvidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `artigos_devolvidos_temp`
--
ALTER TABLE `artigos_devolvidos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `balanco_saldo`
--
ALTER TABLE `balanco_saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `categoria_produtos`
--
ALTER TABLE `categoria_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `cotacao`
--
ALTER TABLE `cotacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ct_artigos_cotados`
--
ALTER TABLE `ct_artigos_cotados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ct_artigos_temp`
--
ALTER TABLE `ct_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `devolucao`
--
ALTER TABLE `devolucao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `disconto`
--
ALTER TABLE `disconto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `documento`
--
ALTER TABLE `documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `entrada_caixa`
--
ALTER TABLE `entrada_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `entrada_stock`
--
ALTER TABLE `entrada_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `entrega`
--
ALTER TABLE `entrega`
  MODIFY `identrega` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `es_artigos`
--
ALTER TABLE `es_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `es_artigos_temp`
--
ALTER TABLE `es_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `factura`
--
ALTER TABLE `factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `familia_artigos`
--
ALTER TABLE `familia_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `fa_artigos_fact`
--
ALTER TABLE `fa_artigos_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `fa_artigos_temp`
--
ALTER TABLE `fa_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `filadeespera`
--
ALTER TABLE `filadeespera`
  MODIFY `idfiladeespera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `fornecedor`
--
ALTER TABLE `fornecedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `idfuncionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `grupo_artigos`
--
ALTER TABLE `grupo_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `itens_comprados`
--
ALTER TABLE `itens_comprados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;
--
-- AUTO_INCREMENT for table `iva`
--
ALTER TABLE `iva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nc_artigos`
--
ALTER TABLE `nc_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nc_artigos_temp`
--
ALTER TABLE `nc_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `nota_debito`
--
ALTER TABLE `nota_debito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `pedido`
--
ALTER TABLE `pedido`
  MODIFY `idpedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `periodicidade`
--
ALTER TABLE `periodicidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `periodo`
--
ALTER TABLE `periodo`
  MODIFY `idperiodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `produto`
--
ALTER TABLE `produto`
  MODIFY `idproduto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `rc_fact`
--
ALTER TABLE `rc_fact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rc_fact_temp`
--
ALTER TABLE `rc_fact_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `recibo`
--
ALTER TABLE `recibo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `requisicao_externa`
--
ALTER TABLE `requisicao_externa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `requisicao_interna`
--
ALTER TABLE `requisicao_interna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `re_artigos`
--
ALTER TABLE `re_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `re_artigos_temp`
--
ALTER TABLE `re_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ri_artigos`
--
ALTER TABLE `ri_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ri_artigos_temp`
--
ALTER TABLE `ri_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `saida_caixa`
--
ALTER TABLE `saida_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `saida_stock`
--
ALTER TABLE `saida_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `serie_factura`
--
ALTER TABLE `serie_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ss_artigos`
--
ALTER TABLE `ss_artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ss_artigos_temp`
--
ALTER TABLE `ss_artigos_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
