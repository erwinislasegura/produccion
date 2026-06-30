-- Tabla para asociar múltiples variedades a un productor
CREATE TABLE IF NOT EXISTS `fruta_rvespecies` (
  `ID_RVESPECIES` bigint(20) NOT NULL AUTO_INCREMENT,
  `NUMERO_RVESPECIES` int(11) DEFAULT NULL,
  `ID_VESPECIES` bigint(20) NOT NULL,
  `ID_PRODUCTOR` bigint(20) NOT NULL,
  `ID_EMPRESA` bigint(20) NOT NULL,
  `ID_USUARIOI` bigint(20) NOT NULL,
  `ID_USUARIOM` bigint(20) NOT NULL,
  `INGRESO` date DEFAULT NULL,
  `MODIFICACION` date DEFAULT NULL,
  `ESTADO_REGISTRO` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_RVESPECIES`),
  KEY `fk_fruta_rvespecies_fruta_vespecies_idx` (`ID_VESPECIES`),
  KEY `fk_fruta_rvespecies_fruta_productor_idx` (`ID_PRODUCTOR`),
  KEY `fk_fruta_rvespecies_principal_empresa_idx` (`ID_EMPRESA`),
  KEY `fk_fruta_rvespecies_usuario_usuarioi_idx` (`ID_USUARIOI`),
  KEY `fk_fruta_rvespecies_usuario_usuariom_idx` (`ID_USUARIOM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

ALTER TABLE `fruta_rvespecies`
  ADD CONSTRAINT `fk_fruta_rvespecies_fruta_vespecies` FOREIGN KEY (`ID_VESPECIES`) REFERENCES `fruta_vespecies` (`ID_VESPECIES`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fruta_rvespecies_fruta_productor` FOREIGN KEY (`ID_PRODUCTOR`) REFERENCES `fruta_productor` (`ID_PRODUCTOR`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fruta_rvespecies_principal_empresa` FOREIGN KEY (`ID_EMPRESA`) REFERENCES `principal_empresa` (`ID_EMPRESA`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fruta_rvespecies_usuario_usuarioi` FOREIGN KEY (`ID_USUARIOI`) REFERENCES `usuario_usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fruta_rvespecies_usuario_usuariom` FOREIGN KEY (`ID_USUARIOM`) REFERENCES `usuario_usuario` (`ID_USUARIO`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- Evita duplicados por productor + variedad
ALTER TABLE `fruta_rvespecies`
  ADD UNIQUE KEY `uk_fruta_rvespecies_productor_variedad` (`ID_PRODUCTOR`, `ID_VESPECIES`);
