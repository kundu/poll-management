-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS poll_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user if it doesn't exist
CREATE USER IF NOT EXISTS 'poll_user'@'%' IDENTIFIED BY 'poll_password';

-- Grant privileges to the user
GRANT ALL PRIVILEGES ON poll_management.* TO 'poll_user'@'%';

-- Flush privileges
FLUSH PRIVILEGES;

-- Use the database
USE poll_management;
