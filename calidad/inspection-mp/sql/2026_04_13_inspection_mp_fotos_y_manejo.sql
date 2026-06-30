CREATE TABLE IF NOT EXISTS calidad_inspection_detail_photos (
    id BIGINT NOT NULL AUTO_INCREMENT,
    inspection_detail_id BIGINT NOT NULL,
    photo_name VARCHAR(150) NULL,
    photo_comment VARCHAR(500) NULL,
    file_path VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_calidad_photo_detail (inspection_detail_id),
    CONSTRAINT fk_calidad_photo_detail
        FOREIGN KEY (inspection_detail_id)
        REFERENCES inspection_details (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
