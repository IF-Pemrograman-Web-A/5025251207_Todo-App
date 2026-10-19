CREATE DATABASE IF NOT EXISTS todo_db;
USE todo_db;

CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('personal', 'shared') NOT NULL DEFAULT 'personal',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    notif_time TIME,
    image_path VARCHAR(255),
    is_completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO todos (type, title, description, due_date, notif_time, is_completed) 
VALUES 
('personal', 'Olahraga', 'Push Up 179x dalam 25 menit', '2026-10-25', '17:00:00', 0),
('shared', 'Kerja Kelompok', 'Zoom untuk diskusi kelompok', '2026-10-20', '20:00:00', 0),
('shared', 'ETS PWEB', 'Kerjakan latihan soal', '2026-10-19', '07:00:00', 0);