SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `netpaper` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `netpaper` ;

-- -----------------------------------------------------
-- Table `netpaper`.`location`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`location` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`rack`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`rack` (
  `id` INT NOT NULL ,
  `location` INT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_rack_1_idx` (`location` ASC) ,
  CONSTRAINT `fk_rack_location`
    FOREIGN KEY (`location` )
    REFERENCES `netpaper`.`location` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device_type` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device` (
  `id` INT NOT NULL ,
  `rack` INT NULL ,
  `device_type` INT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_device_1_idx` (`device_type` ASC) ,
  INDEX `fk_device_2_idx` (`rack` ASC) ,
  CONSTRAINT `fk_device_devicetype`
    FOREIGN KEY (`device_type` )
    REFERENCES `netpaper`.`device_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_device_rack`
    FOREIGN KEY (`rack` )
    REFERENCES `netpaper`.`rack` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device_port`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device_port` (
  `id` INT NOT NULL ,
  `device` INT NULL ,
  `number` SMALLINT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_device_port_1_idx` (`device` ASC) ,
  CONSTRAINT `fk_deviceport_device`
    FOREIGN KEY (`device` )
    REFERENCES `netpaper`.`device` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`connection_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`connection_type` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`connection`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`connection` (
  `id` INT NOT NULL ,
  `connection_type` INT NULL ,
  `rack` INT NULL ,
  `number` SMALLINT NULL ,
  `deviceport_1` INT NULL ,
  `deviceport_2` INT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_connection_1_idx` (`connection_type` ASC) ,
  INDEX `fk_connection_2_idx` (`rack` ASC) ,
  INDEX `fk_connection_3_idx` (`deviceport_1` ASC) ,
  INDEX `fk_connection_4_idx` (`deviceport_2` ASC) ,
  CONSTRAINT `fk_connection_connectiontype`
    FOREIGN KEY (`connection_type` )
    REFERENCES `netpaper`.`connection_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_rack`
    FOREIGN KEY (`rack` )
    REFERENCES `netpaper`.`rack` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_deviceport_1`
    FOREIGN KEY (`deviceport_1` )
    REFERENCES `netpaper`.`device_port` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_deviceport_2`
    FOREIGN KEY (`deviceport_2` )
    REFERENCES `netpaper`.`device_port` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `netpaper` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
