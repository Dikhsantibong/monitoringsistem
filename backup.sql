-- Membuat tabel untuk menyimpan riwayat perubahan notulen
CREATE TABLE IF NOT EXISTS notulen_revisions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notulen_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED DEFAULT 1,
    field_name VARCHAR(255) NOT NULL,
    old_value TEXT,
    new_value TEXT,
    revision_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notulen_id) REFERENCES notulens(id) ON DELETE CASCADE
);

-- Menambahkan kolom revision_count ke tabel notulens
ALTER TABLE notulens ADD COLUMN IF NOT EXISTS revision_count INT DEFAULT 0;

-- Menambahkan kolom created_by ke tabel notulens dengan default 1 (untuk user default/system)
ALTER TABLE notulens ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED DEFAULT 1;
