-- Actualización BD para habilitar Inspección en Destino sobre tabla compartida inspections
-- Ejecutar en ambiente donde ya existen módulos MP/PT.

-- 1) Asegurar columna source_type con valor DESTINO permitido
SET @has_source_type := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND COLUMN_NAME = 'source_type'
);

SET @sql_add_source_type := IF(
    @has_source_type = 0,
    "ALTER TABLE inspections ADD COLUMN source_type ENUM('MP','PT','DESTINO') NOT NULL DEFAULT 'MP' AFTER reception_id",
    'SELECT "inspections.source_type ya existe"'
);
PREPARE stmt_add_source_type FROM @sql_add_source_type;
EXECUTE stmt_add_source_type;
DEALLOCATE PREPARE stmt_add_source_type;

-- Si source_type ya existe, ampliar ENUM para incluir DESTINO
SET @is_enum_with_destino := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND COLUMN_NAME = 'source_type'
      AND COLUMN_TYPE LIKE "%'DESTINO'%"
);

SET @sql_expand_enum := IF(
    @is_enum_with_destino = 0,
    "ALTER TABLE inspections MODIFY COLUMN source_type ENUM('MP','PT','DESTINO') NOT NULL DEFAULT 'MP'",
    'SELECT "source_type ya soporta DESTINO"'
);
PREPARE stmt_expand_enum FROM @sql_expand_enum;
EXECUTE stmt_expand_enum;
DEALLOCATE PREPARE stmt_expand_enum;

-- 2) Reforzar índice único por origen + origen_id (si no existe)
SET @has_new_uq := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND INDEX_NAME = 'uq_inspections_source_reception'
);
SET @sql_new_uq := IF(
    @has_new_uq = 0,
    'CREATE UNIQUE INDEX uq_inspections_source_reception ON inspections (source_type, reception_id)',
    'SELECT "uq_inspections_source_reception ya existe"'
);
PREPARE stmt_new_uq FROM @sql_new_uq;
EXECUTE stmt_new_uq;
DEALLOCATE PREPARE stmt_new_uq;
