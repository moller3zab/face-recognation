-- ============================================================================
-- FACE RECOGNITION ATTENDANCE SYSTEM — IDEMPOTENT SEED DATA
-- Development bootstrap account: admin@gmail.com / admin123
-- ============================================================================

-- Seed System Users
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `department`, `identifier`, `status`) VALUES
(1, 'System Administrator', 'admin@gmail.com', '$2y$10$QFpK43uKvYCHUhFtsa6DOOHiRP2c0FH7ZuFJfZglR2hjFYixshP6K', 'admin', NULL, 'Administration', 'ADMIN-001', 'active');

-- Seed System Settings
INSERT IGNORE INTO `settings` (`key_name`, `value`) VALUES
('system_name', 'Face Recognition Attendance System'),
('working_start', '09:00'),
('working_end', '17:00'),
('late_threshold_minutes', '15'),
('recognition_threshold', '0.60'),
('weekend_days', '5,6'), -- Friday, Saturday
('timezone', 'Africa/Cairo'),
('default_camera', 'user'),
('camera_resolution', '640x480');

-- Seed Official Holidays
INSERT IGNORE INTO `holidays` (`id`, `date`, `name`) VALUES
(1, '2026-10-06', 'Armed Forces Victory Day'),
(2, '2026-01-07', 'Coptic Christmas');

-- Seed Dynamic Self-Referencing Menu Items (SPEC §7)
INSERT INTO `menu_items` (`id`, `parent_id`, `title`, `url`, `icon`, `role`, `sort_order`, `is_active`) VALUES
-- Admin Top-Level Navigation
(1, NULL, 'Dashboard', '/admin/dashboard', 'dashboard', 'admin', 1, 1),
(2, NULL, 'User Management', '/admin/users', 'users', 'admin', 2, 1),
(3, NULL, 'Attendance Records', '/admin/attendance', 'clipboard-list', 'admin', 3, 1),
(4, NULL, 'Reports & Analytics', '/admin/reports', 'chart-bar', 'admin', 4, 1),
(5, NULL, 'Correction Requests', '/admin/corrections', 'pencil-alt', 'admin', 5, 1),
(6, NULL, 'System Alerts', '/admin/alerts', 'bell', 'admin', 6, 1),
(7, NULL, 'Settings & Holidays', '/admin/settings', 'cog', 'admin', 7, 1),
(8, NULL, 'Admin Profile', '/admin/profile', 'user-circle', 'admin', 8, 1),

-- User Top-Level Navigation
(9, NULL, 'Dashboard', '/user/dashboard', 'home', 'user', 1, 1),
(10, NULL, 'Mark Attendance', '/attendance/scan', 'camera', 'user', 2, 1),
(11, NULL, 'Register Face Baseline', '/face/register', 'user-check', 'user', 3, 1),
(12, NULL, 'My Attendance History', '/user/attendance', 'history', 'user', 4, 1),
(13, NULL, 'Correction Requests', '/corrections', 'file-text', 'user', 5, 1),
(14, NULL, 'My Profile', '/user/profile', 'user-circle', 'user', 7, 1),

(15, 2, 'Import HR CSV', '/admin/users?import=1', 'upload', 'admin', 1, 1),
(16, NULL, 'Alerts', '/alerts', 'bell', 'user', 6, 1)
ON DUPLICATE KEY UPDATE
  `parent_id` = VALUES(`parent_id`),
  `title` = VALUES(`title`),
  `url` = VALUES(`url`),
  `icon` = VALUES(`icon`),
  `role` = VALUES(`role`),
  `sort_order` = VALUES(`sort_order`),
  `is_active` = VALUES(`is_active`);

-- No fabricated people, attendance, corrections, or alert records are seeded.
