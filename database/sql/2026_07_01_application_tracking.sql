-- EPIM - Suivi avance des inscriptions
-- A executer une seule fois dans phpMyAdmin si php artisan migrate n'est pas disponible.

ALTER TABLE applications
    ADD COLUMN source VARCHAR(255) NOT NULL DEFAULT 'formulaire_complet' AFTER status,
    ADD COLUMN priority VARCHAR(255) NOT NULL DEFAULT 'normale' AFTER source,
    ADD COLUMN assigned_to BIGINT UNSIGNED NULL AFTER priority,
    ADD COLUMN processed_by BIGINT UNSIGNED NULL AFTER assigned_to,
    ADD COLUMN processed_at TIMESTAMP NULL AFTER processed_by,
    ADD COLUMN last_contacted_at TIMESTAMP NULL AFTER processed_at,
    ADD COLUMN next_follow_up_at TIMESTAMP NULL AFTER last_contacted_at;

ALTER TABLE applications
    ADD CONSTRAINT applications_assigned_to_foreign FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    ADD CONSTRAINT applications_processed_by_foreign FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL;

UPDATE applications
SET source = 'preinscription_rapide', priority = 'haute'
WHERE status = 'preinscription_rapide';

CREATE TABLE application_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    body TEXT NOT NULL,
    is_important TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT application_comments_application_id_foreign FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT application_comments_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE application_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT NULL,
    meta JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT application_activities_application_id_foreign FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT application_activities_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
