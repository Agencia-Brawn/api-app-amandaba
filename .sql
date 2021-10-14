CREATE TABLE `appamandaba`.`pes_registro` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `users_id` BIGINT(20) NOT NULL,
  `pe_sp_esq` VARCHAR(100) NULL,
  `pe_sb_esq` VARCHAR(100) NULL,
  `pe_sp_dir` VARCHAR(100) NULL,
  `pe_sb_dir` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
COMMENT = 'registro semanal dos pes ';
