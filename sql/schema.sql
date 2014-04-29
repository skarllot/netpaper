SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `netpaper` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `netpaper` ;

-- -----------------------------------------------------
-- Table `netpaper`.`location`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`location` (
  `idlocation` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`idlocation`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`rack`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`rack` (
  `idrack` INT NOT NULL ,
  `idlocation` INT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`idrack`) ,
  INDEX `fk_rack_1_idx` (`idlocation` ASC) ,
  CONSTRAINT `fk_rack_1`
    FOREIGN KEY (`idlocation` )
    REFERENCES `netpaper`.`location` (`idlocation` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device_type` (
  `iddevice_type` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`iddevice_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device` (
  `iddevice` INT NOT NULL ,
  `idrack` INT NULL ,
  `iddevice_type` INT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`iddevice`) ,
  INDEX `fk_device_1_idx` (`iddevice_type` ASC) ,
  INDEX `fk_device_2_idx` (`idrack` ASC) ,
  CONSTRAINT `fk_device_1`
    FOREIGN KEY (`iddevice_type` )
    REFERENCES `netpaper`.`device_type` (`iddevice_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_device_2`
    FOREIGN KEY (`idrack` )
    REFERENCES `netpaper`.`rack` (`idrack` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device_port`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device_port` (
  `iddevice_port` INT NOT NULL ,
  `iddevice` INT NULL ,
  `number` SMALLINT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`iddevice_port`) ,
  INDEX `fk_device_port_1_idx` (`iddevice` ASC) ,
  CONSTRAINT `fk_device_port_1`
    FOREIGN KEY (`iddevice` )
    REFERENCES `netpaper`.`device` (`iddevice` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`connection_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`connection_type` (
  `idconnection_type` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`idconnection_type`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`connection`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`connection` (
  `idconnection` INT NOT NULL ,
  `idconnection_type` INT NULL ,
  `idrack` INT NULL ,
  `number` SMALLINT NULL ,
  `iddevice_port1` INT NULL ,
  `iddevice_port2` INT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`idconnection`) ,
  INDEX `fk_connection_1_idx` (`idconnection_type` ASC) ,
  INDEX `fk_connection_2_idx` (`idrack` ASC) ,
  INDEX `fk_connection_3_idx` (`iddevice_port1` ASC) ,
  INDEX `fk_connection_4_idx` (`iddevice_port2` ASC) ,
  CONSTRAINT `fk_connection_1`
    FOREIGN KEY (`idconnection_type` )
    REFERENCES `netpaper`.`connection_type` (`idconnection_type` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_2`
    FOREIGN KEY (`idrack` )
    REFERENCES `netpaper`.`rack` (`idrack` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_3`
    FOREIGN KEY (`iddevice_port1` )
    REFERENCES `netpaper`.`device_port` (`iddevice_port` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connection_4`
    FOREIGN KEY (`iddevice_port2` )
    REFERENCES `netpaper`.`device_port` (`iddevice_port` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `netpaper` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
