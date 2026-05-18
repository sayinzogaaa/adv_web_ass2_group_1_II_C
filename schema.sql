-- ============================================================
-- database/schema.sql — Umuganda Smart Service Request Platform
-- Import via phpMyAdmin > Import, or: mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS umuganda_platform
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE umuganda_platform;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id         INT          NOT NULL AUTO_INCREMENT,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    email      VARCHAR(100),
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: username=admin  password=admin123
INSERT INTO admins (username, password)
VALUES ('admin', '1234');

-- Requests table  (ENUM syntax fixed from original)
CREATE TABLE IF NOT EXISTS requests (
    id          INT           NOT NULL AUTO_INCREMENT,
    fullname    VARCHAR(100)  NOT NULL,
    email       VARCHAR(100)  NOT NULL,
    category    VARCHAR(50)   NOT NULL,
    priority    ENUM('Low','Medium','High')               NOT NULL DEFAULT 'Low',
    description TEXT          NOT NULL,
    status      ENUM('Pending','In Progress','Resolved')  NOT NULL DEFAULT 'Pending',
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_status   (status),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample seed data
INSERT INTO requests (fullname, email, category, priority, description, status) VALUES
('Mugisha Eric',    'mugisha@ines.ac.rw',    'Water Issue',  'High',   'Water point near Hostel B broken for 3 days.',       'Pending'),
('Uwase Diane',     'uwase@ines.ac.rw',      'Street Light', 'Medium', 'Street lights on main path off every night.',        'In Progress'),
('Nkurunziza JB',   'nkurunziza@ines.ac.rw', 'Cleaning',     'High',   'Garbage bins near cafeteria unemptied for a week.',  'Pending'),
('Iradukunda Alex', 'alex@ines.ac.rw',       'Road & Paths', 'Low',    'Pothole at main gate damaging vehicles.',            'Resolved'),
('Uwimana Claire',  'claire@ines.ac.rw',     'Security',     'Medium', 'Camera at Gate 2 not functioning.',                 'In Progress');
