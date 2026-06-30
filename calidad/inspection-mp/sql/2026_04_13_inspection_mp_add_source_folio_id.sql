ALTER TABLE inspection_details
    ADD COLUMN IF NOT EXISTS source_folio_id BIGINT NULL AFTER variety_id;
