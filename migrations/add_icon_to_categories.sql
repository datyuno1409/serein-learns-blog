-- Migration: Add icon column to categories table
-- Date: 2024-01-01

ALTER TABLE categories ADD COLUMN icon VARCHAR(100) DEFAULT NULL AFTER name;

-- Update existing categories with default icons
UPDATE categories SET icon = 'fas fa-folder' WHERE icon IS NULL;