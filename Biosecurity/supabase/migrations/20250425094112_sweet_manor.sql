-- Create database
CREATE DATABASE IF NOT EXISTS biosecurity_db;
USE biosecurity_db;

-- Registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(20) NOT NULL UNIQUE,
    applicant_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    goods_type ENUM('agricultural', 'animals', 'plants', 'biological', 'other') NOT NULL,
    goods_origin VARCHAR(100) NOT NULL,
    goods_description TEXT NOT NULL,
    supporting_document VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected', 'additional_info') NOT NULL DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reference_number (reference_number),
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Registration logs table
CREATE TABLE IF NOT EXISTS registration_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    status VARCHAR(20),
    user VARCHAR(100) NOT NULL,
    notes TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    INDEX idx_registration_id (registration_id),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB;

-- Admin logs table
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_action (action),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB;

-- Create directories if they don't exist
-- This is only for demo purposes; in production, directories should be created through PHP
-- CREATE TABLE IF NOT EXISTS temp_setup (id INT);
-- INSERT INTO temp_setup VALUES (1);

-- Sample data for testing
INSERT INTO registrations (reference_number, applicant_name, email, phone, goods_type, goods_origin, goods_description, status, created_at)
VALUES 
('BIO-ABC12345', 'John Smith', 'john@example.com', '123-456-7890', 'agricultural', 'Australia', 'Wheat seeds for agricultural research, variety XYZ-123, quantity: 5kg', 'pending', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('BIO-DEF67890', 'Jane Doe', 'jane@example.com', '987-654-3210', 'plants', 'Brazil', 'Rare orchid specimens for botanical garden display, 10 plants of 3 different species', 'approved', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('BIO-GHI12345', 'Robert Johnson', 'robert@example.com', '555-123-4567', 'animals', 'Canada', 'Live fish specimens for aquarium exhibition, freshwater species, quantity: 20', 'rejected', DATE_SUB(NOW(), INTERVAL 7 DAY)),
('BIO-JKL67890', 'Sarah Williams', 'sarah@example.com', '444-555-6666', 'biological', 'Germany', 'Bacterial cultures for scientific research, non-pathogenic strains', 'additional_info', DATE_SUB(NOW(), INTERVAL 3 DAY)),
('BIO-MNO12345', 'Michael Brown', 'michael@example.com', '777-888-9999', 'other', 'Japan', 'Soil samples for environmental analysis, collected from agricultural areas', 'pending', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Add logs for the sample registrations
INSERT INTO registration_logs (registration_id, action, status, user, notes, timestamp)
VALUES 
(1, 'created', 'pending', 'system', 'Registration submitted', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'created', 'pending', 'system', 'Registration submitted', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 'status_change', 'approved', 'admin', 'All documentation is in order', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 'created', 'pending', 'system', 'Registration submitted', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(3, 'status_change', 'rejected', 'admin', 'Missing required import permits for aquatic species', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(4, 'created', 'pending', 'system', 'Registration submitted', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 'status_change', 'additional_info', 'admin', 'Please provide detailed information about bacterial strains and containment measures', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 'created', 'pending', 'system', 'Registration submitted', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Add admin logs
INSERT INTO admin_logs (username, action, ip_address, timestamp)
VALUES 
('admin', 'login', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 7 DAY)),
('admin', 'login', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 5 DAY)),
('admin', 'login', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 3 DAY)),
('admin', 'login', '127.0.0.1', DATE_SUB(NOW(), INTERVAL 1 DAY));