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


-- -----------------------------------------------------
-- Table `netpaper`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`user` (
  `id` INT NOT NULL ,
  `user` VARCHAR(45) NULL ,
  `password` VARCHAR(45) NULL ,
  `email` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NULL ,
  `admin` TINYINT(1) NULL ,
  `is_ldap` TINYINT(1) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`group` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`user_group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`user_group` (
  `id` INT NOT NULL ,
  `user` INT NULL ,
  `group` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usergroup_user_idx` (`user` ASC) ,
  INDEX `fk_usergroup_group_idx` (`group` ASC) ,
  CONSTRAINT `fk_usergroup_user`
    FOREIGN KEY (`user` )
    REFERENCES `netpaper`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usergroup_group`
    FOREIGN KEY (`group` )
    REFERENCES `netpaper`.`group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`access_location`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`access_location` (
  `id` INT NOT NULL ,
  `group` INT NULL ,
  `location` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_accesslocation_group_idx` (`group` ASC) ,
  INDEX `fk_accesslocation_location_idx` (`location` ASC) ,
  CONSTRAINT `fk_accesslocation_group`
    FOREIGN KEY (`group` )
    REFERENCES `netpaper`.`group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accesslocation_location`
    FOREIGN KEY (`location` )
    REFERENCES `netpaper`.`location` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`access_rack`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`access_rack` (
  `id` INT NOT NULL ,
  `group` INT NULL ,
  `rack` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_accessrack_group_idx` (`group` ASC) ,
  INDEX `fk_accessrack_rack_idx` (`rack` ASC) ,
  CONSTRAINT `fk_accessrack_group`
    FOREIGN KEY (`group` )
    REFERENCES `netpaper`.`group` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accessrack_rack`
    FOREIGN KEY (`rack` )
    REFERENCES `netpaper`.`rack` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`language`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`language` (
  `id` INT NOT NULL ,
  `code` VARCHAR(5) NULL ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`connection_type_lang`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`connection_type_lang` (
  `language` INT NOT NULL ,
  `connection_type` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`language`, `connection_type`) ,
  INDEX `fk_connectiontypelang_language_idx` (`language` ASC) ,
  INDEX `fk_connectiontypelang_connectiontype_idx` (`connection_type` ASC) ,
  CONSTRAINT `fk_connectiontypelang_language`
    FOREIGN KEY (`language` )
    REFERENCES `netpaper`.`language` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_connectiontypelang_connectiontype`
    FOREIGN KEY (`connection_type` )
    REFERENCES `netpaper`.`connection_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`device_type_lang`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`device_type_lang` (
  `language` INT NOT NULL ,
  `device_type` INT NOT NULL ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`language`, `device_type`) ,
  INDEX `fk_devicetypelang_language_idx` (`language` ASC) ,
  INDEX `fk_devicetypelang_devicetype_idx` (`device_type` ASC) ,
  CONSTRAINT `fk_devicetypelang_language`
    FOREIGN KEY (`language` )
    REFERENCES `netpaper`.`language` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_devicetypelang_devicetype`
    FOREIGN KEY (`device_type` )
    REFERENCES `netpaper`.`device_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`session`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`session` (
  `id` INT NOT NULL ,
  `user` INT NULL ,
  `auth_token` VARCHAR(255) NULL ,
  `ipaddress` VARCHAR(15) NULL ,
  `ip6address` VARCHAR(39) NULL ,
  `created_at` DATETIME NULL ,
  `updated_at` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_session_user_idx` (`user` ASC) ,
  CONSTRAINT `fk_session_user`
    FOREIGN KEY (`user` )
    REFERENCES `netpaper`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`dbversion`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`dbversion` (
  `value` VARCHAR(9) NOT NULL ,
  PRIMARY KEY (`value`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `netpaper`.`ldap`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `netpaper`.`ldap` (
  `id` INT NOT NULL ,
  `domain_name` VARCHAR(255) NULL ,
  `base_dn` VARCHAR(255) NULL ,
  `servers_name` VARCHAR(255) NULL ,
  `use_ssl` TINYINT(1) NULL ,
  `use_tls` TINYINT(1) NULL ,
  `port` SMALLINT NULL ,
  `filter` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

USE `netpaper` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
