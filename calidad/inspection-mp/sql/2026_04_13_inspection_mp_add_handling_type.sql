ALTER TABLE inspection_details
    ADD COLUMN IF NOT EXISTS handling_type ENUM('CONV','ORG') NOT NULL DEFAULT 'CONV' AFTER pallet_number;
