-- ============================================================
-- PMS — Seed Data
-- ============================================================

-- ROLES
INSERT INTO roles (name, slug, description) VALUES
('Super Admin',       'super_admin',       'Full system control — technical administration'),
('System Auditor',    'system_auditor',    'Read-only compliance and oversight'),
('Parish Priest',     'parish_priest',     'Operational head — leadership and approvals'),
('Parish Secretary',  'parish_secretary',  'Administrative operations'),
('Finance Officer',   'finance_officer',   'Financial record management'),
('Ministry Leader',   'ministry_leader',   'Group and attendance management'),
('Parish Member',     'parish_member',     'Self-service end user');

-- PERMISSIONS (all possible)
INSERT INTO permissions (name, slug, module) VALUES
-- Users
('View Users',          'users.view',       'users'),
('Create Users',        'users.create',     'users'),
('Update Users',        'users.update',     'users'),
('Delete Users',        'users.delete',     'users'),
('Activate Users',      'users.activate',   'users'),
('Deactivate Users',    'users.deactivate', 'users'),
('Full User Access',    'users.*',          'users'),
-- Roles
('View Roles',          'roles.view',       'roles'),
('Manage Roles',        'roles.*',          'roles'),
-- Permissions
('Manage Permissions',  'permissions.*',    'permissions'),
-- Members
('View Members',        'members.view',     'members'),
('Create Members',      'members.create',   'members'),
('Update Members',      'members.update',   'members'),
('Delete Members',      'members.delete',   'members'),
('Full Member Access',  'members.*',        'members'),
-- Sacraments
('View Sacraments',     'sacraments.view',  'sacraments'),
('Create Sacraments',   'sacraments.create','sacraments'),
('Update Sacraments',   'sacraments.update','sacraments'),
('Full Sacrament Access','sacraments.*',    'sacraments'),
-- Finance
('View Finance',        'finance.view',     'finance'),
('Create Transactions', 'finance.create',   'finance'),
('Update Transactions', 'finance.update',   'finance'),
('Approve Transactions','finance.approve',  'finance'),
('Full Finance Access', 'finance.*',        'finance'),
-- Events
('View Events',         'events.view',      'events'),
('Create Events',       'events.create',    'events'),
('Update Events',       'events.update',    'events'),
('Delete Events',       'events.delete',    'events'),
('Full Event Access',   'events.*',         'events'),
-- Groups
('View Groups',         'groups.view',      'groups'),
('Create Groups',       'groups.create',    'groups'),
('Update Groups',       'groups.update',    'groups'),
('Delete Groups',       'groups.delete',    'groups'),
('Full Group Access',   'groups.*',         'groups'),
-- Attendance
('View Attendance',     'attendance.view',  'attendance'),
('Record Attendance',   'attendance.create','attendance'),
('Full Attendance Access','attendance.*',   'attendance'),
-- Reports
('View Reports',        'reports.view',     'reports'),
('Finance Reports',     'reports.finance',  'reports'),
('Full Report Access',  'reports.*',        'reports'),
-- Profile
('View Profile',        'profile.view',     'profile'),
('Update Profile',      'profile.update',   'profile'),
-- Requests
('Create Requests',     'requests.create',  'requests'),
-- System
('System Settings',     'system.settings',  'system'),
('Manage Backups',      'backup.manage',    'system'),
-- Audit
('View Audit Logs',     'audit_logs.view',  'audit');

-- ROLE <-> PERMISSION mappings (using subqueries for IDs)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'super_admin' AND p.slug IN (
    'users.*','users.activate','users.deactivate',
    'roles.*','permissions.*',
    'system.settings','audit_logs.view','backup.manage'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'system_auditor' AND p.slug IN (
    'audit_logs.view','reports.view','finance.view','members.view'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'parish_priest' AND p.slug IN (
    'members.*','sacraments.*','reports.*',
    'finance.view','finance.approve','users.deactivate'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'parish_secretary' AND p.slug IN (
    'members.create','members.update','members.view',
    'sacraments.create','sacraments.update','events.*'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'finance_officer' AND p.slug IN (
    'finance.create','finance.update','finance.view','reports.finance'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'ministry_leader' AND p.slug IN (
    'groups.*','attendance.*','members.view','events.view'
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
WHERE r.slug = 'parish_member' AND p.slug IN (
    'profile.view','profile.update','events.view','requests.create'
);

-- DEFAULT USERS (password: Password@123 — hashed)
-- Super Admin
INSERT INTO users (email, password, role_id, status) VALUES
('admin@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='super_admin'), 'active');

-- Parish Priest
INSERT INTO users (email, password, role_id, status) VALUES
('priest@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='parish_priest'), 'active');

-- Auditor
INSERT INTO users (email, password, role_id, status) VALUES
('auditor@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='system_auditor'), 'active');

-- Secretary
INSERT INTO users (email, password, role_id, status) VALUES
('secretary@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='parish_secretary'), 'active');

-- Finance Officer
INSERT INTO users (email, password, role_id, status) VALUES
('finance@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='finance_officer'), 'active');

-- Ministry Leader
INSERT INTO users (email, password, role_id, status) VALUES
('leader@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='ministry_leader'), 'active');

-- Parish Member
INSERT INTO users (email, password, role_id, status) VALUES
('member@parish.com', '$2y$12$LJ3m4ys3Lk0QITW2v8XW9.j8hMxNvK6sGpEqRzlFBXBTVq7UI0OeG',
 (SELECT id FROM roles WHERE slug='parish_member'), 'active');

-- SYSTEM SETTINGS
INSERT INTO system_settings (setting_key, setting_value) VALUES
('parish_name', 'St. Augustine Catholic Parish'),
('parish_address', '12 Church Street, Ikeja, Lagos'),
('parish_phone', '+234 801 234 5678'),
('parish_email', 'info@staugustineparish.com'),
('currency', 'NGN'),
('date_format', 'd/m/Y'),
('member_prefix', 'PMS');

-- NOTE: The password hash above is for "Password@123"
-- Generate a fresh one for production: php -r "echo password_hash('Password@123', PASSWORD_BCRYPT);"