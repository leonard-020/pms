-- ============================================================
-- Parish Management System — Complete Database Schema
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Drop existing tables (in dependency order)
-- -----------------------------------------------------------
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS group_members;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS finance_transactions;
DROP TABLE IF EXISTS member_requests;
DROP TABLE IF EXISTS sacraments;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS system_settings;

-- -----------------------------------------------------------
-- 1. USERS — authentication accounts
-- -----------------------------------------------------------
CREATE TABLE users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(191)    NOT NULL,
    password        VARCHAR(255)    NOT NULL,
    role_id         BIGINT UNSIGNED NOT NULL,
    status          ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    last_login_at   DATETIME        NULL,
    last_login_ip   VARCHAR(45)     NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME        NULL,

    UNIQUE KEY uk_users_email (email),
    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status),
    INDEX idx_users_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 2. ROLES — RBAC roles
-- -----------------------------------------------------------
CREATE TABLE roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)    NOT NULL,
    slug        VARCHAR(100)    NOT NULL,
    description VARCHAR(255)    NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_roles_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 3. PERMISSIONS — granular permissions
-- -----------------------------------------------------------
CREATE TABLE permissions (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)    NOT NULL COMMENT 'Human-readable name',
    slug        VARCHAR(150)    NOT NULL COMMENT 'Dot-notation: module.action',
    module      VARCHAR(50)     NOT NULL COMMENT 'Grouping module',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_permissions_slug (slug),
    INDEX idx_permissions_module (module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. ROLE_PERMISSIONS — pivot
-- -----------------------------------------------------------
CREATE TABLE role_permissions (
    role_id       BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY pk_role_permissions (role_id, permission_id),

    INDEX idx_rp_permission (permission_id),

    CONSTRAINT fk_rp_role
        FOREIGN KEY (role_id) REFERENCES roles (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_rp_permission
        FOREIGN KEY (permission_id) REFERENCES permissions (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 5. MEMBERS — parishioner records
-- -----------------------------------------------------------
CREATE TABLE members (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL COMMENT 'Linked account (nullable)',
    member_number   VARCHAR(20)     NOT NULL COMMENT 'Auto-generated parish ID',
    first_name      VARCHAR(100)    NOT NULL,
    last_name       VARCHAR(100)    NOT NULL,
    middle_name     VARCHAR(100)    NULL,
    date_of_birth   DATE            NULL,
    gender          ENUM('male','female') NULL,
    phone           VARCHAR(30)     NULL,
    address         VARCHAR(255)    NULL,
    city            VARCHAR(100)    NULL,
    state           VARCHAR(100)    NULL,
    zip_code        VARCHAR(20)     NULL,
    country         VARCHAR(100)    NULL DEFAULT 'Nigeria',
    photo           VARCHAR(255)    NULL,
    occupation      VARCHAR(150)    NULL,
    status          ENUM('active','inactive','deceased','transferred') NOT NULL DEFAULT 'active',
    created_by      BIGINT UNSIGNED NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME        NULL,

    UNIQUE KEY uk_members_number (member_number),
    INDEX idx_members_user (user_id),
    INDEX idx_members_name (last_name, first_name),
    INDEX idx_members_status (status),
    INDEX idx_members_deleted (deleted_at),

    CONSTRAINT fk_members_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE SET NULL,
    CONSTRAINT fk_members_creator
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 6. SACRAMENTS
-- -----------------------------------------------------------
CREATE TABLE sacraments (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id       BIGINT UNSIGNED NOT NULL,
    type            ENUM('baptism','first_communion','confirmation','marriage','holy_orders','anointing_sick') NOT NULL,
    date            DATE            NOT NULL,
    place           VARCHAR(255)    NULL,
    minister        VARCHAR(150)    NULL,
    witness_name    VARCHAR(150)    NULL COMMENT 'Godparent / Sponsor / Spouse',
    notes           TEXT            NULL,
    created_by      BIGINT UNSIGNED NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_sacraments_member (member_id),
    INDEX idx_sacraments_type (type),
    INDEX idx_sacraments_date (date),

    CONSTRAINT fk_sacraments_member
        FOREIGN KEY (member_id) REFERENCES members (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_sacraments_creator
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 7. FINANCE_TRANSACTIONS — with dual-control approval
-- -----------------------------------------------------------
CREATE TABLE finance_transactions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type            ENUM('income','expense') NOT NULL,
    category        VARCHAR(100)    NOT NULL COMMENT 'tithe, offering, donation, salary, maintenance, etc.',
    amount          DECIMAL(15,2)   NOT NULL,
    description     VARCHAR(500)    NULL,
    transaction_date DATE           NOT NULL,
    status          ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    recorded_by     BIGINT UNSIGNED NOT NULL,
    approved_by     BIGINT UNSIGNED NULL,
    approved_at     DATETIME        NULL,
    rejection_note  VARCHAR(500)    NULL,
    notes           TEXT            NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_finance_type (type),
    INDEX idx_finance_category (category),
    INDEX idx_finance_status (status),
    INDEX idx_finance_date (transaction_date),
    INDEX idx_finance_recorded (recorded_by),

    CONSTRAINT fk_finance_recorder
        FOREIGN KEY (recorded_by) REFERENCES users (id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_finance_approver
        FOREIGN KEY (approved_by) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 8. EVENTS
-- -----------------------------------------------------------
CREATE TABLE events (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)    NOT NULL,
    description TEXT            NULL,
    event_date  DATE            NOT NULL,
    start_time  TIME            NULL,
    end_time    TIME            NULL,
    location    VARCHAR(255)    NULL,
    status      ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
    created_by  BIGINT UNSIGNED NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_events_date (event_date),
    INDEX idx_events_status (status),

    CONSTRAINT fk_events_creator
        FOREIGN KEY (created_by) REFERENCES users (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 9. GROUPS — ministries, societies, committees
-- -----------------------------------------------------------
CREATE TABLE groups (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)    NOT NULL,
    description TEXT            NULL,
    leader_id   BIGINT UNSIGNED NULL COMMENT 'Member who leads this group',
    status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_groups_leader (leader_id),

    CONSTRAINT fk_groups_leader
        FOREIGN KEY (leader_id) REFERENCES members (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 10. GROUP_MEMBERS — pivot
-- -----------------------------------------------------------
CREATE TABLE group_members (
    group_id    BIGINT UNSIGNED NOT NULL,
    member_id   BIGINT UNSIGNED NOT NULL,
    role        VARCHAR(50)     NULL DEFAULT 'member' COMMENT 'leader, assistant, member',
    joined_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY pk_group_members (group_id, member_id),

    CONSTRAINT fk_gm_group
        FOREIGN KEY (group_id) REFERENCES groups (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_gm_member
        FOREIGN KEY (member_id) REFERENCES members (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 11. ATTENDANCE
-- -----------------------------------------------------------
CREATE TABLE attendance (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id    BIGINT UNSIGNED NOT NULL,
    member_id   BIGINT UNSIGNED NOT NULL,
    event_id    BIGINT UNSIGNED NULL,
    date        DATE            NOT NULL,
    status      ENUM('present','absent','late') NOT NULL DEFAULT 'present',
    notes       VARCHAR(255)    NULL,
    recorded_by BIGINT UNSIGNED NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_attendance_group_date (group_id, date),
    INDEX idx_attendance_member (member_id),

    CONSTRAINT fk_attendance_group
        FOREIGN KEY (group_id) REFERENCES groups (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_attendance_member
        FOREIGN KEY (member_id) REFERENCES members (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_attendance_event
        FOREIGN KEY (event_id) REFERENCES events (id)
        ON DELETE SET NULL,
    CONSTRAINT fk_attendance_recorder
        FOREIGN KEY (recorded_by) REFERENCES users (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 12. AUDIT_LOGS — immutable, no delete allowed
-- -----------------------------------------------------------
CREATE TABLE audit_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NULL,
    action      VARCHAR(50)     NOT NULL COMMENT 'create, update, delete, login, logout, approve',
    module      VARCHAR(50)     NOT NULL COMMENT 'users, members, finance, etc.',
    description TEXT            NOT NULL,
    ip_address  VARCHAR(45)     NULL,
    user_agent  VARCHAR(500)    NULL,
    old_values  JSON            NULL COMMENT 'Previous state for updates',
    new_values  JSON            NULL COMMENT 'New state for updates/creates',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_module (module),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 13. MEMBER_REQUESTS — parishioner submissions
-- -----------------------------------------------------------
CREATE TABLE member_requests (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id   BIGINT UNSIGNED NOT NULL,
    type        VARCHAR(100)    NOT NULL COMMENT 'certificate, recommendation, transfer, etc.',
    description TEXT            NULL,
    status      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    responded_by BIGINT UNSIGNED NULL,
    response_note TEXT          NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_requests_member (member_id),
    INDEX idx_requests_status (status),

    CONSTRAINT fk_requests_member
        FOREIGN KEY (member_id) REFERENCES members (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_requests_responder
        FOREIGN KEY (responded_by) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 14. SYSTEM_SETTINGS — key-value configuration
-- -----------------------------------------------------------
CREATE TABLE system_settings (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100)    NOT NULL,
    setting_value TEXT          NULL,
    updated_by  BIGINT UNSIGNED NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_settings_key (setting_key),

    CONSTRAINT fk_settings_updater
        FOREIGN KEY (updated_by) REFERENCES users (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;