SET @has_col := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND COLUMN_NAME = 'inspection_number'
);

SET @sql_col := IF(
    @has_col = 0,
    'ALTER TABLE inspections ADD COLUMN inspection_number VARCHAR(40) NULL AFTER reception_id',
    'SELECT "inspection_number ya existe"'
);
PREPARE stmt_col FROM @sql_col;
EXECUTE stmt_col;
DEALLOCATE PREPARE stmt_col;

SET @has_idx := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'inspections'
      AND INDEX_NAME = 'uq_inspections_inspection_number'
);

SET @sql_idx := IF(
    @has_idx = 0,
    'CREATE UNIQUE INDEX uq_inspections_inspection_number ON inspections (inspection_number)',
    'SELECT "uq_inspections_inspection_number ya existe"'
);
PREPARE stmt_idx FROM @sql_idx;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;
