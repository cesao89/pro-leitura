-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.21-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para proleitura
CREATE DATABASE IF NOT EXISTS `proleitura` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `proleitura`;

-- Copiando estrutura para tabela proleitura.profile
CREATE TABLE IF NOT EXISTS `profile` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Insert dos PROFILES
INSERT INTO `profile` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES (1, 'gestor', 1, NOW(), NOW());
INSERT INTO `profile` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES (2, 'proponente', 1, NOW(), NOW());

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` tinyint(1) unsigned NOT NULL,
  `name` char(200) NOT NULL,
  `email` char(200) NOT NULL,
  `phone` char(15) NOT NULL,
  `num_document` char(15) NOT NULL,
  `password` char(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_user_profile` (`profile_id`),
  CONSTRAINT `FK_user_profile` FOREIGN KEY (`profile_id`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Insert do usuario ADMIN
INSERT INTO `user` (`id`, `profile_id`, `name`, `email`, `phone`, `num_document`, `password`, `status`, `created_at`, `updated_at`) VALUES (1, 1, 'Administrador', 'administrador@prolivro.com.br', '1111111111', '11111111111', '4297f44b13955235245b2497399d7a93', 1, NOW(), NOW());

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_status
CREATE TABLE IF NOT EXISTS `projeto_status` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `status` char(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Insert dos status PROJETO
INSERT INTO `projeto_status` (`id`, `status`) VALUES (1, 'editando');
INSERT INTO `projeto_status` (`id`, `status`) VALUES (2, 'enviado');
INSERT INTO `projeto_status` (`id`, `status`) VALUES (3, 'credenciado');
INSERT INTO `projeto_status` (`id`, `status`) VALUES (4, 'inscrito');

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto
CREATE TABLE IF NOT EXISTS `projeto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `status_id` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `nome` char(100) DEFAULT NULL,
  `diferenciais_experiencia` char(200) DEFAULT NULL,
  `vigencia_inicio` date DEFAULT NULL,
  `vigencia_fim` date DEFAULT NULL,
  `natureza` text,
  `publico_atendido` text,
  `faixa_etaria` char(50) DEFAULT NULL,
  `genero` char(50) DEFAULT NULL,
  `atendidos_total` int(10) unsigned NOT NULL DEFAULT '0',
  `atendidos_ultimo_ano` int(10) unsigned NOT NULL DEFAULT '0',
  `atendidos_por_acao` int(10) unsigned NOT NULL DEFAULT '0',
  `atendidos_detalhes` char(200) DEFAULT NULL,
  `localizacao_territorio` text,
  `localizacao_regional` char(50) DEFAULT NULL,
  `localizacao_estado` text,
  `localizacao_cidade` text,
  `localizacao_outro` text,
  `organizacao_nome` char(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_project_user` (`user_id`),
  KEY `FK_projeto_status_projeto` (`status_id`),
  CONSTRAINT `FK_project_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_projeto_status_projeto` FOREIGN KEY (`status_id`) REFERENCES `projeto_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_categorias
CREATE TABLE IF NOT EXISTS `projeto_categorias` (
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `categoria` char(100) NOT NULL,
  `detalhe` char(50) DEFAULT NULL,
  KEY `FK_organizacao_categoria_projeto` (`project_id`),
  CONSTRAINT `FK_organizacao_categoria_projeto` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_detalhes
CREATE TABLE IF NOT EXISTS `projeto_detalhes` (
  `project_id` int(10) unsigned NOT NULL,
  `sintese` text,
  `caracteristicas` text,
  `objetivos` text,
  `justificativas` text,
  `metodologia_a` text,
  `metodologia_b` text,
  `resultado` text,
  KEY `FK_project_description_project` (`project_id`),
  CONSTRAINT `FK_project_description_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_equipe
CREATE TABLE IF NOT EXISTS `projeto_equipe` (
  `project_id` int(10) unsigned NOT NULL,
  `quantidade` int(10) unsigned NOT NULL DEFAULT '0',
  `equipe` char(50) NOT NULL,
  `detalhe` char(50) DEFAULT NULL,
  KEY `FK_project_team_project` (`project_id`),
  CONSTRAINT `FK_project_team_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_expectativa
CREATE TABLE IF NOT EXISTS `projeto_expectativa` (
  `project_id` int(10) unsigned NOT NULL,
  `expectativa` char(100) NOT NULL,
  `detalhe` char(50) DEFAULT NULL,
  KEY `FK_project_location_project` (`project_id`),
  CONSTRAINT `FK_project_location_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_mais_detalhes
CREATE TABLE IF NOT EXISTS `projeto_mais_detalhes` (
  `project_id` int(10) unsigned NOT NULL,
  `avaliacoes` text,
  `depoimentos` text,
  `premios` text,
  `principais_dificuldades` text,
  `dificuldades_superadas` text,
  `garantir_continuidade` text,
  `site` varchar(500) DEFAULT NULL,
  `redes_sociais` text,
  `fotos_videos` text,
  `adicional` text,
  KEY `FK_projeto_informacoes_project` (`project_id`),
  CONSTRAINT `FK_projeto_informacoes_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_parceiros
CREATE TABLE IF NOT EXISTS `projeto_parceiros` (
  `project_id` int(10) unsigned DEFAULT NULL,
  `patrocinio` char(100) DEFAULT NULL,
  `patrocinio_percentual` tinyint(3) DEFAULT NULL,
  `apoio_tecnico` char(100) DEFAULT NULL,
  `apoio_institucional` char(100) DEFAULT NULL,
  `outros` char(100) DEFAULT NULL,
  KEY `FK_project_partner_project` (`project_id`),
  CONSTRAINT `FK_project_partner_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
-- Copiando estrutura para tabela proleitura.projeto_responsavel
CREATE TABLE IF NOT EXISTS `projeto_responsavel` (
  `project_id` int(10) unsigned NOT NULL,
  `organizacao` char(100) DEFAULT NULL,
  `cnpj` char(100) DEFAULT NULL,
  `cidade` char(100) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `cep` char(10) DEFAULT NULL,
  `email` char(200) DEFAULT NULL,
  `telefone` char(15) DEFAULT NULL,
  `celular` char(15) DEFAULT NULL,
  `site` char(200) DEFAULT NULL,
  `facebook` char(200) DEFAULT NULL,
  `outros_contatos` char(100) DEFAULT NULL,
  `pessoa_responsavel` char(100) DEFAULT NULL,
  `pessoa_cargo` char(100) DEFAULT NULL,
  `pessoa_email` char(200) DEFAULT NULL,
  `pessoa_telefone` char(15) DEFAULT NULL,
  `pessoa_celular` char(15) DEFAULT NULL,
  `pessoa_outros_contatos` char(100) DEFAULT NULL,
  KEY `FK_project_organization_project` (`project_id`),
  CONSTRAINT `FK_project_organization_project` FOREIGN KEY (`project_id`) REFERENCES `projeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportação de dados foi desmarcado.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;