-- -------------------------------------------------------------
-- /!\ Suppression de BDD formulaire_securise si déjà existante
-- -------------------------------------------------------------
DROP DATABASE IF EXISTS formulaire_securise;

-- -------------------------------------------------------------
-- Création de BDD formulaire_securise
-- -------------------------------------------------------------
CREATE DATABASE formulaire_securise;

-- -------------------------------------------------------------
-- Sélection de BDD formulaire_securise
-- -------------------------------------------------------------
USE formulaire_securise;

-- -------------------------------------------------------------
-- Création de la table datas_form
-- -------------------------------------------------------------
CREATE TABLE `datas_form` (
  `id` INT NOT NULL AUTO_INCREMENT, 
  `civility` VARCHAR(50) NOT NULL,
  `first-name` VARCHAR(50) NOT NULL,
  `last-name` VARCHAR(50) NOT NULL,
  `date-of-birth` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `telephone` VARCHAR(20),
  `password` VARCHAR(50) NOT NULL,
  `adress` VARCHAR(50) NOT NULL,
  `additional-address` VARCHAR(50),
  `zip-code` VARCHAR(5) NOT NULL,
  `city` VARCHAR(50) NOT NULL,
  `country` VARCHAR(50) NOT NULL,
  `image` TEXT,
  PRIMARY KEY (`id`)
);