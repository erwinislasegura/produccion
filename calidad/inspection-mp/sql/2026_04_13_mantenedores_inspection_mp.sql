-- Actualización BD para inspección MP (ejecutar manualmente)

-- 1) defects.defect_scope
SET @exists_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'defects'
      AND COLUMN_NAME = 'defect_scope'
);
SET @sql_col := IF(
    @exists_col = 0,
    "ALTER TABLE defects ADD COLUMN defect_scope ENUM('quality','condition') NOT NULL DEFAULT 'quality' AFTER defect_type",
    'SELECT "defects.defect_scope ya existe"'
);
PREPARE stmt FROM @sql_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE defects
SET defect_scope = 'quality'
WHERE defect_scope IS NULL OR defect_scope = '';

-- 2) Índice defects scope/type/active
SET @exists_idx := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'defects'
      AND INDEX_NAME = 'idx_defects_scope_type_active'
);
SET @sql_idx := IF(
    @exists_idx = 0,
    'CREATE INDEX idx_defects_scope_type_active ON defects (defect_scope, defect_type, is_active)',
    'SELECT "idx_defects_scope_type_active ya existe"'
);
PREPARE stmt FROM @sql_idx;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Tablas de mantenedores de defectos
CREATE TABLE IF NOT EXISTS calidad_defectos_calidad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    defect_type ENUM('critical','major','minor') NOT NULL DEFAULT 'minor',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    defect_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS calidad_defectos_condicion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    defect_type ENUM('critical','major','minor') NOT NULL DEFAULT 'minor',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    defect_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4) inspections.reception_id
SET @exists_reception_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND COLUMN_NAME = 'reception_id'
);
SET @sql_reception_col := IF(
    @exists_reception_col = 0,
    'ALTER TABLE inspections ADD COLUMN reception_id BIGINT NULL AFTER product',
    'SELECT "inspections.reception_id ya existe"'
);
PREPARE stmt FROM @sql_reception_col;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5) Índice único para evitar doble inspección por recepción
SET @exists_uq := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND INDEX_NAME = 'uq_inspections_reception_id'
);
SET @sql_uq := IF(
    @exists_uq = 0,
    'CREATE UNIQUE INDEX uq_inspections_reception_id ON inspections (reception_id)',
    'SELECT "uq_inspections_reception_id ya existe"'
);
PREPARE stmt FROM @sql_uq;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
