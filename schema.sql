-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema yeticave
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema yeticave
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `yeticave` ;
USE `yeticave` ;

-- -----------------------------------------------------
-- Table `yeticave`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `yeticave`.`categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(254) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (name ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yeticave`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `yeticave`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` VARCHAR(254) NOT NULL,
  `name` VARCHAR(254) NOT NULL,
  `password` VARCHAR(64) NOT NULL,
  `avatar` VARCHAR(254) NULL,
  `contact` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yeticave`.`lots`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `yeticave`.`lots` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(254) NOT NULL,
  `description` TEXT NOT NULL,
  `img_url` VARCHAR(254) NOT NULL,
  `price_origin` INT UNSIGNED NOT NULL,
  `date_end` DATE NOT NULL,
  `price_step` INT UNSIGNED NOT NULL,
  `author_id` INT UNSIGNED NOT NULL,
  `winner_id` INT UNSIGNED NULL DEFAULT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_lots_categories_idx` (`category_id` ASC),
  INDEX `fk_lots_users1_idx` (`winner_id` ASC),
  INDEX `fk_lots_users2_idx` (`author_id` ASC),
  INDEX `name_idx` (`name` ASC),
  INDEX `description_idx` (`description`(767) ASC),
  CONSTRAINT `fk_lots_categories`
    FOREIGN KEY (`category_id`)
    REFERENCES `yeticave`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lots_users1`
    FOREIGN KEY (`winner_id`)
    REFERENCES `yeticave`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_lots_users2`
    FOREIGN KEY (`author_id`)
    REFERENCES `yeticave`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `yeticave`.`bets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `yeticave`.`bets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `lot_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_bets_lots1_idx` (`lot_id` ASC),
  INDEX `fk_bets_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_bets_lots1`
    FOREIGN KEY (`lot_id`)
    REFERENCES `yeticave`.`lots` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bets_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `yeticave`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
