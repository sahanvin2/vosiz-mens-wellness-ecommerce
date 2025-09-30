-- Initialize Vosiz Database
USE vosiz_db;

-- Set proper charset and collation
ALTER DATABASE vosiz_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create additional user if needed
-- CREATE USER IF NOT EXISTS 'vosiz_readonly'@'%' IDENTIFIED BY 'readonly_password';
-- GRANT SELECT ON vosiz_db.* TO 'vosiz_readonly'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Display success message
SELECT 'Vosiz MySQL Database initialized successfully!' as Status;