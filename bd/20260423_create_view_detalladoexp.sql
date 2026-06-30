-- Script: creación/actualización de la vista view_detalladoexp
-- Base de datos objetivo (ajustar si corresponde)
USE `smartberry`;

DROP VIEW IF EXISTS `view_detalladoexp`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `view_detalladoexp` AS
SELECT
    IFNULL(`car`.`NREFERENCIA_ICARGA`, 'Sin datos') AS `Número Referencia`,
    IFNULL(`cli`.`NOMBRE_BROKER`, 'Sin datos') AS `Cliente`,
    IFNULL(`mer`.`NOMBRE_MERCADO`, 'Sin datos') AS `Mercado`,
    `des`.`NUMERO_CONTENEDOR_DESPACHOEX` AS `Contenedor`,
    'Exportación' AS `Tipo Despacho`,
    `des`.`NUMERO_DESPACHOEX` AS `Número Despacho`,
    DATE_FORMAT(`des`.`FECHA_DESPACHOEX`, '%d-%m-%Y') AS `Fecha Despacho`,
    `des`.`NUMERO_GUIA_DESPACHOEX` AS `Número Guía Despacho`,
    `pais`.`NOMBRE_PAIS` AS `Destino`,
    IFNULL(DATE_FORMAT(`car`.`FECHA_CDOCUMENTAL_ICARGA`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Corte Documental`,
    COALESCE(DATE_FORMAT(`car`.`FECHAETD_ICARGA`, '%d-%m-%Y'), DATE_FORMAT(`des`.`FECHAETD_DESPACHOEX`, '%d-%m-%Y')) AS `Fecha ETD`,
    COALESCE(DATE_FORMAT(`car`.`FECHAETDREAL_ICARGA`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Real ETD`,
    COALESCE(DATE_FORMAT(`car`.`FECHAETA_ICARGA`, '%d-%m-%Y'), DATE_FORMAT(`des`.`FECHAETA_DESPACHOEX`, '%d-%m-%Y')) AS `Fecha ETA`,
    COALESCE(DATE_FORMAT(`car`.`FECHAETAREAL_ICARGA`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Real ETA`,
    COALESCE(`rec`.`NOMBRE_RFINAL`, 'Sin datos') AS `Recibidor Final`,
    CASE
        WHEN `des`.`TEMBARQUE_DESPACHOEX` = 1 THEN 'Terrestre'
        WHEN `des`.`TEMBARQUE_DESPACHOEX` = 2 THEN 'Aéreo'
        WHEN `des`.`TEMBARQUE_DESPACHOEX` = 3 THEN 'Marítimo'
        ELSE 'Sin datos'
    END AS `Tipo Embarque`,
    CASE
        WHEN `des`.`TEMBARQUE_DESPACHOEX` = 1 THEN 'No Aplica'
        ELSE `des`.`NAVE_DESPACHOEX`
    END AS `Nave`,
    CASE
        WHEN `des`.`TEMBARQUE_DESPACHOEX` = 1 THEN 'No Aplica'
        ELSE `des`.`NVIAJE_DESPACHOEX`
    END AS `Número Viaje/Vuelo`,
    CASE
        WHEN `car`.`TEMBARQUE_ICARGA` = 1 THEN `ldest`.`NOMBRE_LDESTINO`
        WHEN `car`.`TEMBARQUE_ICARGA` = 2 THEN `adest`.`NOMBRE_ADESTINO`
        WHEN `car`.`TEMBARQUE_ICARGA` = 3 THEN `pdest`.`NOMBRE_PDESTINO`
        ELSE 'Sin datos'
    END AS `Puerto/Aeropuerto/Lugar Destino`,
    `ddes`.`FOLIO_EXIEXPORTACION` AS `N° Folio Original`,
    `ddes`.`FOLIO_AUXILIAR_EXIEXPORTACION` AS `N° Folio`,
    DATE_FORMAT(`ddes`.`FECHA_EMBALADO_EXIEXPORTACION`, '%d-%m-%Y') AS `Fecha Embalado`,
    CASE
        WHEN `ddes`.`TESTADOSAG` = 0 OR `ddes`.`TESTADOSAG` IS NULL THEN 'Sin Condición'
        WHEN `ddes`.`TESTADOSAG` = 1 THEN 'En Inspección'
        WHEN `ddes`.`TESTADOSAG` = 2 THEN 'Aprobado Origen'
        WHEN `ddes`.`TESTADOSAG` = 3 THEN 'Aprobado USDA'
        WHEN `ddes`.`TESTADOSAG` = 4 THEN 'Fumigado'
        WHEN `ddes`.`TESTADOSAG` = 5 THEN 'Rechazado'
        ELSE 'Sin datos'
    END AS `Condición`,
    `esta`.`CODIGO_ESTANDAR` AS `Código Estandar`,
    `esta`.`NOMBRE_ESTANDAR` AS `Envase/Estandar`,
    `prod`.`CSG_PRODUCTOR` AS `CSG`,
    `prod`.`NOMBRE_PRODUCTOR` AS `Productor`,
    `esp`.`NOMBRE_ESPECIES` AS `Especies`,
    `var`.`NOMBRE_VESPECIES` AS `Variedad`,
    `ddes`.`CANTIDAD_ENVASE_EXIEXPORTACION` AS `Cantidad Envase`,
    `ddes`.`KILOS_NETO_EXIEXPORTACION` AS `Kilos Neto`,
    `ddes`.`PDESHIDRATACION_EXIEXPORTACION` AS `% Deshidratación`,
    `ddes`.`KILOS_DESHIRATACION_EXIEXPORTACION` AS `Kilos Deshidratación`,
    `ddes`.`KILOS_BRUTO_EXIEXPORTACION` AS `Kilos Bruto`,
    IFNULL(`repas`.`NUMERO_REPALETIZAJE`, 'Sin datos') AS `Número Repaletizaje`,
    IFNULL(DATE_FORMAT(`repas`.`INGRESO`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Repaletizaje`,
    IFNULL(`proce`.`NUMERO_PROCESO`, 'Sin datos') AS `Número Proceso`,
    IFNULL(DATE_FORMAT(`proce`.`FECHA_PROCESO`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Proceso`,
    CASE
        WHEN `proce`.`ID_TPROCESO` = 1 THEN 'Óptico'
        WHEN `proce`.`ID_TPROCESO` = 2 THEN 'Convecional'
        WHEN `proce`.`ID_TPROCESO` = 3 THEN 'Bulk'
        ELSE 'Sin datos'
    END AS `Tipo Proceso`,
    IFNULL(`reem`.`NUMERO_REEMBALAJE`, 'Sin datos') AS `Número Reembalaje`,
    IFNULL(DATE_FORMAT(`reem`.`FECHA_REEMBALAJE`, '%d-%m-%Y'), 'Sin datos') AS `Fecha Reembalaje`,
    CASE
        WHEN `reem`.`ID_TREEMBALAJE` = 1 THEN 'Cambio de Embalaje'
        WHEN `reem`.`ID_TREEMBALAJE` = 2 THEN 'Reembalaje por decisión comercial o calidad'
        WHEN `reem`.`ID_TREEMBALAJE` = 3 THEN 'Proceso de Etiquetado'
        ELSE 'Sin datos'
    END AS `Tipo Reembalaje`,
    CASE
        WHEN `ddes`.`ID_TMANEJO` = 1 THEN 'Convencional'
        WHEN `ddes`.`ID_TMANEJO` = 2 THEN 'Orgánico'
        ELSE 'Sin datos'
    END AS `Tipo Manejo`,
    `cal`.`NOMBRE_TCALIBRE` AS `Tipo Calibre`,
    `emb`.`NOMBRE_TEMBALAJE` AS `Tipo Embalaje`,
    IFNULL(`ddes`.`STOCK`, 'Sin datos') AS `Stock`,
    CASE
        WHEN `ddes`.`EMBOLSADO` = 1 THEN 'Si'
        WHEN `ddes`.`EMBOLSADO` = 0 THEN 'No'
        ELSE 'Sin datos'
    END AS `Embolsado`,
    CASE
        WHEN `ddes`.`GASIFICADO` = 1 THEN 'Si'
        WHEN `ddes`.`GASIFICADO` = 0 THEN 'No'
        ELSE 'Sin datos'
    END AS `Gasificación`,
    CASE
        WHEN `ddes`.`PREFRIO` = 1 THEN 'Si'
        WHEN `ddes`.`PREFRIO` = 0 THEN 'No'
        ELSE 'Sin datos'
    END AS `Prefrío`,
    `trans`.`NOMBRE_TRANSPORTE` AS `Transporte`,
    `cond`.`NOMBRE_CONDUCTOR` AS `Nombre Conductor`,
    `des`.`PATENTE_CAMION` AS `Patente Camión`,
    `des`.`PATENTE_CARRO` AS `Patente Carro`,
    WEEK(`des`.`FECHA_DESPACHOEX`, 3) AS `Semana`,
    WEEK(`des`.`FECHA_GUIA_DESPACHOEX`, 3) AS `Semana Guía`,
    `emp`.`NOMBRE_EMPRESA` AS `Empresa`,
    `pla`.`NOMBRE_PLANTA` AS `Planta`,
    `tempo`.`NOMBRE_TEMPORADA` AS `Temporada`,
    `car`.`BOLAWBCRT_ICARGA` AS `Bl/AWB`,
    `recpt`.`NUMERO_RECEPCION` AS `Número Recepción`,
    DATE_FORMAT(`recpt`.`FECHA_RECEPCION`, '%d-%m-%Y') AS `Fecha Recepción`,
    CASE
        WHEN `ddes`.`ID_RECEPCION` IS NOT NULL AND `recpt`.`TRECEPCION` = 1 THEN 'Desde Productor'
        WHEN `ddes`.`ID_RECEPCION` IS NOT NULL AND `recpt`.`TRECEPCION` = 2 THEN 'Planta Externa'
        WHEN `ddes`.`ID_DESPACHO2` IS NOT NULL THEN 'Interplanta'
        ELSE 'Sin datos'
    END AS `Tipo Recepción`,
    CASE
        WHEN `ddes`.`ID_RECEPCION` IS NOT NULL THEN `recpt`.`NUMERO_GUIA_RECEPCION`
        ELSE 'Sin datos'
    END AS `Número Guía Recepción`,
    CASE
        WHEN `ddes`.`ID_RECEPCION` IS NOT NULL THEN DATE_FORMAT(`recpt`.`FECHA_GUIA_RECEPCION`, '%d-%m-%Y')
        ELSE 'Sin datos'
    END AS `Fecha Guía Recepción`,
    `mp`.`NUMERO_RECEPCION` AS `Número Recepción MP`,
    `mp`.`FECHA_RECEPCION` AS `Fecha Recepción MP`,
    CASE
        WHEN `ddes`.`ID_PROCESO` IS NOT NULL AND `mp`.`TRECEPCION` = 1 THEN 'Desde Productor'
        WHEN `ddes`.`ID_PROCESO` IS NOT NULL AND `mp`.`TRECEPCION` = 2 THEN 'Planta Externa'
        ELSE 'Sin datos'
    END AS `Tipo Recepción MP`,
    CASE
        WHEN `ddes`.`ID_PROCESO` IS NOT NULL THEN `mp`.`NUMERO_GUIA_RECEPCION`
        ELSE 'Sin datos'
    END AS `Número Guía Recepción MP`,
    CASE
        WHEN `ddes`.`ID_PROCESO` IS NOT NULL THEN DATE_FORMAT(`mp`.`FECHA_GUIA_RECEPCION`, '%d-%m-%Y')
        ELSE 'Sin datos'
    END AS `Fecha Guía Recepción MP`,
    CASE
        WHEN `ddes`.`ID_PROCESO` IS NOT NULL THEN `pla`.`NOMBRE_PLANTA`
        ELSE 'Sin datos'
    END AS `Planta Recepción MP`,
    `des`.`TERMOGRAFO_DESPACHOEX` AS `Termógrafo Despacho`,
    `ddes`.`N_TERMOGRAFO` AS `Termógrafo Pallet`
FROM `fruta_despachoex` `des`
LEFT JOIN `fruta_icarga` `car`
    ON `des`.`ID_ICARGA` = `car`.`ID_ICARGA`
   AND `des`.`ID_EMPRESA` = `car`.`ID_EMPRESA`
   AND `des`.`ID_TEMPORADA` = `car`.`ID_TEMPORADA`
   AND `car`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_broker` `cli`
    ON `car`.`ID_BROKER` = `cli`.`ID_BROKER`
   AND `cli`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_mercado` `mer`
    ON `car`.`ID_MERCADO` = `mer`.`ID_MERCADO`
LEFT JOIN `ubicacion_pais` `pais`
    ON `car`.`ID_PAIS` = `pais`.`ID_PAIS`
   AND `mer`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_ldestino` `ldest`
    ON `car`.`ID_LDESTINO` = `ldest`.`ID_LDESTINO`
   AND `ldest`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_adestino` `adest`
    ON `car`.`ID_ADESTINO` = `adest`.`ID_ADESTINO`
   AND `adest`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_pdestino` `pdest`
    ON `car`.`ID_PDESTINO` = `pdest`.`ID_PDESTINO`
   AND `pdest`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_rfinal` `rec`
    ON `car`.`ID_RFINAL` = `rec`.`ID_RFINAL`
   AND `rec`.`ESTADO_REGISTRO` = 1
JOIN `fruta_exiexportacion` `ddes`
    ON `des`.`ID_DESPACHOEX` = `ddes`.`ID_DESPACHOEX`
   AND `ddes`.`ESTADO_REGISTRO` = 1
LEFT JOIN `estandar_eexportacion` `esta`
    ON `ddes`.`ID_ESTANDAR` = `esta`.`ID_ESTANDAR`
   AND `esta`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_productor` `prod`
    ON `ddes`.`ID_PRODUCTOR` = `prod`.`ID_PRODUCTOR`
   AND `prod`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_vespecies` `var`
    ON `ddes`.`ID_VESPECIES` = `var`.`ID_VESPECIES`
   AND `var`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_especies` `esp`
    ON `var`.`ID_ESPECIES` = `esp`.`ID_ESPECIES`
   AND `esp`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_repaletizajeex` `repas`
    ON `ddes`.`ID_REPALETIZAJE` = `repas`.`ID_REPALETIZAJE`
   AND `repas`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_proceso` `proce`
    ON `ddes`.`ID_PROCESO` = `proce`.`ID_PROCESO`
   AND `proce`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_reembalaje` `reem`
    ON `ddes`.`ID_REEMBALAJE` = `reem`.`ID_REEMBALAJE`
   AND `reem`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_tcalibre` `cal`
    ON `ddes`.`ID_TCALIBRE` = `cal`.`ID_TCALIBRE`
   AND `cal`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_tembalaje` `emb`
    ON `ddes`.`ID_TEMBALAJE` = `emb`.`ID_TEMBALAJE`
   AND `emb`.`ESTADO_REGISTRO` = 1
LEFT JOIN `transporte_transporte` `trans`
    ON `des`.`ID_TRANSPORTE` = `trans`.`ID_TRANSPORTE`
   AND `trans`.`ESTADO_REGISTRO` = 1
LEFT JOIN `transporte_conductor` `cond`
    ON `des`.`ID_CONDUCTOR` = `cond`.`ID_CONDUCTOR`
   AND `cond`.`ESTADO_REGISTRO` = 1
LEFT JOIN `principal_empresa` `emp`
    ON `des`.`ID_EMPRESA` = `emp`.`ID_EMPRESA`
   AND `emp`.`ESTADO_REGISTRO` = 1
LEFT JOIN `principal_planta` `pla`
    ON `des`.`ID_PLANTA` = `pla`.`ID_PLANTA`
   AND `pla`.`ESTADO_REGISTRO` = 1
LEFT JOIN `principal_temporada` `tempo`
    ON `des`.`ID_TEMPORADA` = `tempo`.`ID_TEMPORADA`
   AND `tempo`.`ESTADO_REGISTRO` = 1
LEFT JOIN `fruta_recepcionpt` `recpt`
    ON `ddes`.`ID_RECEPCION` = `recpt`.`ID_RECEPCION`
   AND `recpt`.`ESTADO_REGISTRO` = 1
LEFT JOIN (
    SELECT
        `fruta_eximateriaprima`.`ID_PROCESO` AS `ID_PROCESO`,
        MIN(`fruta_eximateriaprima`.`ID_RECEPCION`) AS `ID_RECEPCION`
    FROM `fruta_eximateriaprima`
    WHERE `fruta_eximateriaprima`.`ESTADO_REGISTRO` = 1
    GROUP BY `fruta_eximateriaprima`.`ID_PROCESO`
) `exmp`
    ON `ddes`.`ID_PROCESO` = `exmp`.`ID_PROCESO`
LEFT JOIN `fruta_recepcionmp` `mp`
    ON `exmp`.`ID_RECEPCION` = `mp`.`ID_RECEPCION`
   AND `mp`.`ESTADO_REGISTRO` = 1
WHERE `des`.`ID_EMPRESA` <> 5
  AND `des`.`ESTADO_REGISTRO` = 1
  AND `ddes`.`ESTADO` IN (7, 8);
