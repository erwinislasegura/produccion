-- Actualización BD para habilitar Inspección MP/PT en tabla compartida inspections

-- 1) Agregar columna de origen (MP/PT)
SET @has_source_type := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND COLUMN_NAME = 'source_type'
);
SET @sql_source_type := IF(
    @has_source_type = 0,
    "ALTER TABLE inspections ADD COLUMN source_type ENUM('MP','PT') NOT NULL DEFAULT 'MP' AFTER reception_id",
    'SELECT "inspections.source_type ya existe"'
);
PREPARE stmt_source_type FROM @sql_source_type;
EXECUTE stmt_source_type;
DEALLOCATE PREPARE stmt_source_type;

-- 2) Quitar índice único antiguo por reception_id si existe
SET @has_old_uq := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND INDEX_NAME = 'uq_inspections_reception_id'
);
SET @sql_drop_old_uq := IF(
    @has_old_uq > 0,
    'DROP INDEX uq_inspections_reception_id ON inspections',
    'SELECT "uq_inspections_reception_id no existe"'
);
PREPARE stmt_drop_old_uq FROM @sql_drop_old_uq;
EXECUTE stmt_drop_old_uq;
DEALLOCATE PREPARE stmt_drop_old_uq;

-- 3) Crear índice único por origen + id de origen
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
